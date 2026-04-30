import math
from typing import Literal

from services.audio_processor import SwallowFeatures
import random

# Each flag: (flag_name, points, human_description)
FlagType = tuple[str, int, str]


def classify_risk(
    features: SwallowFeatures,
) -> tuple[Literal["Low", "Medium", "High"], int, list[FlagType]]:
    """
    Score swallow features and classify into risk levels.
    Returns (risk_level, total_score, triggered_flags).
    """
    if not features.is_valid:
        return (
            "Medium",
            0,
            [("invalid_audio", 0, f"Audio quality issue: {features.quality_issue}")],
        )

    flags: list[FlagType] = []
    score = 0

    # 1. Duration: normal swallow ~0.5-1.5s of active sound
    if features.duration_sec > 8.0:
        flags.append(("long_duration", 3, "Swallow duration appears prolonged"))
        score += 3
    elif features.duration_sec > 5.0:
        flags.append(("mod_duration", 1, "Swallow duration is moderately long"))
        score += 1

    # 2. Multiple energy peaks suggest coughing or repeated swallow attempts
    if features.num_energy_peaks >= 5:
        flags.append((
            "many_peaks",
            4,
            "Multiple sound events detected (possible coughing or repeated swallowing)",
        ))
        score += 4
    elif features.num_energy_peaks >= 3:
        flags.append(("some_peaks", 2, "Several distinct sound events detected"))
        score += 2

    # 3. Post-swallow activity (residue, wet voice, cough)
    if features.has_post_swallow_activity:
        flags.append((
            "post_swallow",
            3,
            "Significant sound activity detected after the main swallow",
        ))
        score += 3

    # 4. High zero-crossing rate (turbulent, cough-like sounds)
    if features.zero_crossing_rate_mean > 0.15:
        flags.append(("high_zcr", 3, "High-frequency turbulent sounds detected"))
        score += 3
    elif features.zero_crossing_rate_mean > 0.10:
        flags.append(("mod_zcr", 1, "Moderate high-frequency content detected"))
        score += 1

    # 5. Energy irregularity (high coefficient of variation)
    if features.rms_energy_mean > 0:
        cv = features.rms_energy_std / features.rms_energy_mean
        if cv > 1.5:
            flags.append((
                "irregular_energy",
                3,
                "Highly irregular swallowing sound pattern",
            ))
            score += 3
        elif cv > 1.0:
            flags.append(("mod_irregular", 1, "Moderately irregular sound pattern"))
            score += 1

    # 6. Weak overall signal
    if features.rms_energy_mean < 0.02:
        flags.append(("weak_signal", 2, "Swallow sound is very weak"))
        score += 2

    # 7. High spectral centroid (possible aspiration sounds)
    if features.spectral_centroid_mean > 3000:
        flags.append(("high_centroid", 2, "Unusual spectral profile detected"))
        score += 2

    # 8. Low SNR
    if features.signal_to_noise_ratio < 5.0:
        flags.append(("low_snr", 1, "Low signal-to-noise ratio"))
        score += 1

    if score >= 11:
        risk_level: Literal["Low", "Medium", "High"] = "High"
    elif score > 7:
        risk_level = "Medium"
    else:
        risk_level = "Low"

    
    # If no flags triggered, note normalcy
    if not flags:
        flags.append(("normal", 0, "Swallow sound appears within typical parameters"))

    return (risk_level, score, flags)
