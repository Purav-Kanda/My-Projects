import type { AnalysisResult } from "../types";

const API_BASE = "/api";

export async function analyzeSwallow(audioBlob: Blob): Promise<AnalysisResult> {
  const formData = new FormData();
  const ext = audioBlob.type.includes("mp4")
    ? "mp4"
    : audioBlob.type.includes("ogg")
      ? "ogg"
      : "webm";
  formData.append("audio", audioBlob, `recording.${ext}`);

  const response = await fetch(`${API_BASE}/analyze`, {
    method: "POST",
    body: formData,
  });

  if (!response.ok) {
    const errorBody = await response.json().catch(() => null);
    throw new Error(errorBody?.detail || `Server error: ${response.status}`);
  }

  return response.json();
}

export async function healthCheck(): Promise<boolean> {
  try {
    const res = await fetch(`${API_BASE}/health`);
    return res.ok;
  } catch {
    return false;
  }
}
