from pydantic import BaseModel
from typing import Literal


class FeatureData(BaseModel):
    duration_sec: float
    rms_energy_mean: float
    rms_energy_std: float
    spectral_centroid_mean: float
    spectral_bandwidth_mean: float
    zero_crossing_rate_mean: float
    num_energy_peaks: int
    peak_to_peak_time: float
    has_post_swallow_activity: bool
    signal_to_noise_ratio: float


class FlagData(BaseModel):
    name: str
    points: int
    description: str


class AnalysisResponse(BaseModel):
    risk_level: Literal["Low", "Medium", "High"]
    risk_score: int
    confidence_note: str
    reason_summary: str
    guidance: str
    disclaimer: str
    features: FeatureData
    flags: list[FlagData]


class HealthResponse(BaseModel):
    status: str
    version: str


class ErrorResponse(BaseModel):
    detail: str
    disclaimer: str
