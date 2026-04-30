import { useRef, useState, useEffect } from "react";
import { Disclaimer } from "./Disclaimer";
import { WaveformVisualizer } from "./WaveformVisualizer";
import type { RefObject } from "react";

interface Props {
  isRecording: boolean;
  audioBlob: Blob | null;
  uploadedFileName: string | null;
  error: string | null;
  isProcessing: boolean;
  canvasRef: RefObject<HTMLCanvasElement | null>;
  onStartRecording: () => void;
  onStopRecording: () => void;
  onFileSelected: (blob: Blob, name: string) => void;
  onSubmit: () => void;
  onBack: () => void;
}

const ACCEPTED_TYPES = "audio/*,.mp3,.wav,.ogg,.webm,.m4a,.mp4,.flac,.aac";

export function RecordScreen({
  isRecording,
  audioBlob,
  uploadedFileName,
  error,
  isProcessing,
  canvasRef,
  onStartRecording,
  onStopRecording,
  onFileSelected,
  onSubmit,
  onBack,
}: Props) {
  const fileInputRef = useRef<HTMLInputElement>(null);
  const [elapsed, setElapsed] = useState(0);

  useEffect(() => {
    if (!isRecording) {
      setElapsed(0);
      return;
    }
    const interval = setInterval(() => setElapsed((e) => e + 1), 1000);
    return () => clearInterval(interval);
  }, [isRecording]);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) onFileSelected(file, file.name);
    e.target.value = "";
  };

  const formatTime = (s: number) => {
    const mins = Math.floor(s / 60);
    const secs = s % 60;
    return `${mins}:${secs.toString().padStart(2, "0")}`;
  };

  return (
    <div className="record-layout">
      <div className="record-main">
        <div className="record-header-section">
          <button className="btn-ghost btn-back-nav" onClick={onBack}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="20" height="20">
              <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            Back
          </button>
          <h2 className="section-title">Record Swallow</h2>
        </div>

        <div className="record-center glass-card">
          <WaveformVisualizer canvasRef={canvasRef} isRecording={isRecording} />

          {isRecording && (
            <div className="recording-timer">
              <span className="timer-dot" />
              {formatTime(elapsed)}
            </div>
          )}

          {uploadedFileName && !isRecording && (
            <div className="upload-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="16" height="16">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
              </svg>
              {uploadedFileName}
            </div>
          )}

          {error && (
            <div className="error-banner">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="16" height="16">
                <circle cx="12" cy="12" r="10" />
                <line x1="15" y1="9" x2="9" y2="15" />
                <line x1="9" y1="9" x2="15" y2="15" />
              </svg>
              {error}
            </div>
          )}

          <div className="record-controls">
            {!isRecording && !audioBlob && !isProcessing && (
              <>
                <button className="btn-record-large" onClick={onStartRecording}>
                  <div className="record-btn-inner">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32">
                      <circle cx="12" cy="12" r="8" />
                    </svg>
                  </div>
                  <span>Start Recording</span>
                </button>

                <div className="controls-divider">
                  <span>or</span>
                </div>

                <button
                  className="btn-upload"
                  onClick={() => fileInputRef.current?.click()}
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="20" height="20">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="17 8 12 3 7 8" />
                    <line x1="12" y1="3" x2="12" y2="15" />
                  </svg>
                  Upload Audio File
                </button>
              </>
            )}

            {isRecording && (
              <button className="btn-stop-large" onClick={onStopRecording}>
                <div className="stop-btn-inner">
                  <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
                    <rect x="6" y="6" width="12" height="12" rx="2" />
                  </svg>
                </div>
                <span>Stop Recording</span>
              </button>
            )}

            {audioBlob && !isProcessing && (
              <div className="post-record-controls">
                <button className="btn-primary btn-large" onClick={onSubmit}>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="20" height="20">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                  </svg>
                  Analyze Swallow
                </button>
                <div className="secondary-actions">
                  <button className="btn-ghost" onClick={onStartRecording}>
                    Record Again
                  </button>
                  <button
                    className="btn-ghost"
                    onClick={() => fileInputRef.current?.click()}
                  >
                    Upload Different File
                  </button>
                </div>
              </div>
            )}

            {isProcessing && (
              <div className="processing-state">
                <div className="processing-spinner" />
                <h3>Analyzing your swallow...</h3>
                <p className="text-muted">
                  Extracting acoustic features and generating insights
                </p>
              </div>
            )}
          </div>
        </div>

        <Disclaimer compact />
      </div>

      <input
        ref={fileInputRef}
        type="file"
        accept={ACCEPTED_TYPES}
        style={{ display: "none" }}
        onChange={handleFileChange}
      />
    </div>
  );
}
