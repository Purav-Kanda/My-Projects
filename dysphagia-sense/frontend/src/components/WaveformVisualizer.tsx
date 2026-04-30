import type { RefObject } from "react";

interface Props {
  canvasRef: RefObject<HTMLCanvasElement | null>;
  isRecording: boolean;
}

export function WaveformVisualizer({ canvasRef, isRecording }: Props) {
  return (
    <div className={`waveform-container ${isRecording ? "active" : ""}`}>
      <canvas
        ref={canvasRef}
        width={600}
        height={200}
        className="waveform-canvas"
      />
      {!isRecording && (
        <div className="waveform-placeholder">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" width="32" height="32">
            <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z" />
            <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
            <line x1="12" x2="12" y1="19" y2="22" />
          </svg>
          <span>Ready to record</span>
        </div>
      )}
    </div>
  );
}
