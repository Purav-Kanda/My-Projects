export interface FeatureData {
  duration_sec: number;
  rms_energy_mean: number;
  rms_energy_std: number;
  spectral_centroid_mean: number;
  spectral_bandwidth_mean: number;
  zero_crossing_rate_mean: number;
  num_energy_peaks: number;
  peak_to_peak_time: number;
  has_post_swallow_activity: boolean;
  signal_to_noise_ratio: number;
}

export interface FlagData {
  name: string;
  points: number;
  description: string;
}

export interface AnalysisResult {
  risk_level: "Low" | "Medium" | "High";
  risk_score: number;
  confidence_note: string;
  reason_summary: string;
  guidance: string;
  disclaimer: string;
  features: FeatureData;
  flags: FlagData[];
}

export interface HistoryEntry {
  id: string;
  timestamp: number;
  risk_level: "Low" | "Medium" | "High";
  risk_score: number;
  reason_summary: string;
  features: FeatureData;
  flags: FlagData[];
}

export interface User {
  id: string;
  name: string;
  color: string;
  createdAt: number;
}

export type Screen = "welcome" | "recording" | "results";

export interface RecorderState {
  isRecording: boolean;
  isProcessing: boolean;
  audioBlob: Blob | null;
  error: string | null;
}
