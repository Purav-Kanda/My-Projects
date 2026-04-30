import io
from dataclasses import dataclass, asdict
from typing import Optional

import librosa
import numpy as np
from pydub import AudioSegment


@dataclass
class SwallowFeatures:
    duration_sec: float
    peak_amplitude: float
    rms_energy_mean: float
    rms_energy_std: float
    spectral_centroid_mean: float
    spectral_bandwidth_mean: float
    zero_crossing_rate_mean: float
    mfcc_means: list[float]  # 13 values
    num_energy_peaks: int
    peak_to_peak_time: float
    has_post_swallow_activity: bool
    signal_to_noise_ratio: float
    is_valid: bool
    quality_issue: Optional[str]

    def to_dict(self) -> dict:
        return asdict(self)


_FORMAT_FALLBACKS = [None, "mp4", "webm", "ogg", "mp3", "wav", "aac"]


def process_audio(file_path: str, target_sr: int = 22050) -> SwallowFeatures:
    """Full pipeline: convert format -> normalize -> extract features."""

    # Step 1: Convert to WAV via pydub.
    # Try auto-detection first (no format hint), then explicit formats as
    # fallback. Browsers and uploaded files can have MIME/content mismatches
    # so we never rely solely on the content_type header.
    audio_segment = None
    last_error: Exception | None = None
    for fmt in _FORMAT_FALLBACKS:
        try:
            audio_segment = AudioSegment.from_file(file_path, format=fmt)
            break
        except Exception as e:
            last_error = e
            continue

    if audio_segment is None:
        raise ValueError(f"Could not decode audio file: {last_error}")

    wav_buffer = io.BytesIO()
    audio_segment.export(wav_buffer, format="wav")
    wav_buffer.seek(0)

    # Step 2: Load with librosa
    y, sr = librosa.load(wav_buffer, sr=target_sr, mono=True)

    # Step 3: Validate
    duration = librosa.get_duration(y=y, sr=sr)
    if duration < 1.0:
        return _invalid_features(duration, "Audio too short (under 1 second)")
    if duration > 15.0:
        y = y[: int(15.0 * sr)]
        duration = 15.0

    # Step 4: Normalize amplitude to [-1, 1]
    peak = np.max(np.abs(y))
    if peak < 1e-6:
        return _invalid_features(duration, "Audio is silent or near-silent")
    y = y / peak

    # Step 5: Extract features
    return _extract_features(y, sr, duration)


def _extract_features(y: np.ndarray, sr: int, duration: float) -> SwallowFeatures:
    """Extract acoustic features relevant to swallow screening."""

    # RMS energy
    rms = librosa.feature.rms(y=y)[0]
    rms_mean = float(np.mean(rms))
    rms_std = float(np.std(rms))

    # Spectral centroid
    cent = librosa.feature.spectral_centroid(y=y, sr=sr)[0]
    cent_mean = float(np.mean(cent))

    # Spectral bandwidth
    bw = librosa.feature.spectral_bandwidth(y=y, sr=sr)[0]
    bw_mean = float(np.mean(bw))

    # Zero crossing rate
    zcr = librosa.feature.zero_crossing_rate(y=y)[0]
    zcr_mean = float(np.mean(zcr))

    # MFCCs (13 coefficients)
    mfccs = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=13)
    mfcc_means = [float(np.mean(mfccs[i])) for i in range(13)]

    # Energy peaks via onset detection (proxy for swallow events / coughs)
    onset_env = librosa.onset.onset_strength(y=y, sr=sr)
    peaks = librosa.util.peak_pick(
        onset_env, pre_max=3, post_max=3, pre_avg=3, post_avg=5, delta=0.3, wait=10
    )
    num_peaks = len(peaks)

    # Peak-to-peak timing
    if num_peaks >= 2:
        hop_length = 512
        peak_times = librosa.frames_to_time(peaks, sr=sr, hop_length=hop_length)
        p2p_time = float(peak_times[-1] - peak_times[0])
    else:
        p2p_time = 0.0

    # Post-swallow activity: significant energy in last 30% of clip
    last_30_idx = int(len(rms) * 0.7)
    rms_last_30 = rms[last_30_idx:]
    rms_first_70 = rms[:last_30_idx]
    has_post_activity = bool(
        np.mean(rms_last_30) > 0.5 * np.mean(rms_first_70)
        if len(rms_first_70) > 0
        else False
    )

    # SNR estimate
    threshold = np.percentile(rms, 20)
    noise_frames = rms[rms <= threshold]
    signal_frames = rms[rms > threshold]
    noise_power = float(np.mean(noise_frames**2)) if len(noise_frames) > 0 else 1e-10
    signal_power = float(np.mean(signal_frames**2)) if len(signal_frames) > 0 else 0
    snr = float(10 * np.log10(signal_power / noise_power)) if noise_power > 0 else 0.0

    return SwallowFeatures(
        duration_sec=duration,
        peak_amplitude=float(np.max(np.abs(y))),
        rms_energy_mean=rms_mean,
        rms_energy_std=rms_std,
        spectral_centroid_mean=cent_mean,
        spectral_bandwidth_mean=bw_mean,
        zero_crossing_rate_mean=zcr_mean,
        mfcc_means=mfcc_means,
        num_energy_peaks=num_peaks,
        peak_to_peak_time=p2p_time,
        has_post_swallow_activity=has_post_activity,
        signal_to_noise_ratio=snr,
        is_valid=True,
        quality_issue=None,
    )


def _invalid_features(duration: float, reason: str) -> SwallowFeatures:
    """Return a features object marked as invalid."""
    return SwallowFeatures(
        duration_sec=duration,
        peak_amplitude=0.0,
        rms_energy_mean=0.0,
        rms_energy_std=0.0,
        spectral_centroid_mean=0.0,
        spectral_bandwidth_mean=0.0,
        zero_crossing_rate_mean=0.0,
        mfcc_means=[0.0] * 13,
        num_energy_peaks=0,
        peak_to_peak_time=0.0,
        has_post_swallow_activity=False,
        signal_to_noise_ratio=0.0,
        is_valid=False,
        quality_issue=reason,
    )
