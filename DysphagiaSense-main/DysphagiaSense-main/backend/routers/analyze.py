import os
import tempfile

from fastapi import APIRouter, File, HTTPException, UploadFile
from config import MAX_FILE_SIZE_MB, MEDICAL_DISCLAIMER
from schemas.response import AnalysisResponse, FeatureData, FlagData, HealthResponse
from services.audio_processor import process_audio
from services.explanation import generate_explanation
from services.risk_classifier import classify_risk

router = APIRouter()


@router.get("/health", response_model=HealthResponse)
async def health_check():
    return HealthResponse(status="healthy", version="0.1.0")


@router.post("/analyze", response_model=AnalysisResponse)
async def analyze_swallow(audio: UploadFile = File(...)):
    tmp_path = None
    try:
        # 1. Validate file size
        contents = await audio.read()
        if len(contents) > MAX_FILE_SIZE_MB * 1024 * 1024:
            raise HTTPException(status_code=413, detail="File too large")

        # 2. Write to temp file with no extension so ffmpeg uses magic-byte
        #    detection instead of guessing from the extension (avoids webm/mp4
        #    mismatch that breaks Safari recordings and uploaded files).
        with tempfile.NamedTemporaryFile(delete=False, suffix="") as tmp:
            tmp.write(contents)
            tmp_path = tmp.name

        # 3. Process audio -> extract features
        features = process_audio(tmp_path)

        # 4. Classify risk
        risk_level, score, feature_flags = classify_risk(features)

        # 5. Generate explanation via Gemini (with fallback)
        reason_summary, guidance = await generate_explanation(
            risk_level, features, feature_flags
        )

        return AnalysisResponse(
            risk_level=risk_level,
            risk_score=score,
            confidence_note="Based on acoustic screening analysis",
            reason_summary=reason_summary,
            guidance=guidance,
            disclaimer=MEDICAL_DISCLAIMER,
            features=FeatureData(
                duration_sec=features.duration_sec,
                rms_energy_mean=features.rms_energy_mean,
                rms_energy_std=features.rms_energy_std,
                spectral_centroid_mean=features.spectral_centroid_mean,
                spectral_bandwidth_mean=features.spectral_bandwidth_mean,
                zero_crossing_rate_mean=features.zero_crossing_rate_mean,
                num_energy_peaks=features.num_energy_peaks,
                peak_to_peak_time=features.peak_to_peak_time,
                has_post_swallow_activity=features.has_post_swallow_activity,
                signal_to_noise_ratio=features.signal_to_noise_ratio,
            ),
            flags=[
                FlagData(name=name, points=pts, description=desc)
                for name, pts, desc in feature_flags
            ],
        )

    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Analysis failed: {str(e)}")
    finally:
        # 6. Always clean up temp file
        if tmp_path and os.path.exists(tmp_path):
            os.unlink(tmp_path)

