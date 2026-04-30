import type { HistoryEntry } from "../types";
import { Disclaimer } from "./Disclaimer";
import { HistoryPanel } from "./HistoryPanel";

interface Props {
  onStart: () => void;
  history: HistoryEntry[];
  onClearHistory: () => void;
}

export function WelcomeScreen({ onStart, history, onClearHistory }: Props) {
  return (
    <div className="welcome-layout">
      <div className="welcome-main">
        <div className="hero-section">
          <div className="hero-badge">AI-Powered Screening</div>
          <h1 className="hero-title">
            Swallowing Difficulty
            <span className="gradient-text"> Screening</span>
          </h1>
          <p className="hero-subtitle">
            Record a short swallow audio clip and get an instant risk
            assessment powered by advanced acoustic analysis and AI.
          </p>

          <button className="btn-primary btn-large" onClick={onStart}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="20" height="20">
              <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z" />
              <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
              <line x1="12" x2="12" y1="19" y2="22" />
            </svg>
            Start Screening
          </button>

          <Disclaimer compact />
        </div>

        <div className="features-grid">
          <div className="feature-card glass-card">
            <div className="feature-icon" style={{ background: "rgba(99, 102, 241, 0.15)", color: "#6366f1" }}>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="24" height="24">
                <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z" />
                <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
              </svg>
            </div>
            <h3>Record</h3>
            <p>Quick audio recording of your swallowing sounds</p>
          </div>

          <div className="feature-card glass-card">
            <div className="feature-icon" style={{ background: "rgba(16, 185, 129, 0.15)", color: "#10b981" }}>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="24" height="24">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
              </svg>
            </div>
            <h3>Analyze</h3>
            <p>12+ acoustic features extracted and scored</p>
          </div>

          <div className="feature-card glass-card">
            <div className="feature-icon" style={{ background: "rgba(245, 158, 11, 0.15)", color: "#f59e0b" }}>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="24" height="24">
                <path d="M9 11l3 3L22 4" />
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
              </svg>
            </div>
            <h3>Results</h3>
            <p>AI-powered explanations with actionable guidance</p>
          </div>
        </div>

        <div className="instructions-card glass-card">
          <h3>How to Get the Best Results</h3>
          <div className="instruction-steps">
            <div className="instruction-step">
              <div className="step-num">1</div>
              <div>
                <strong>Prepare</strong>
                <p>Find a quiet room and have a glass of water ready</p>
              </div>
            </div>
            <div className="instruction-step">
              <div className="step-num">2</div>
              <div>
                <strong>Position</strong>
                <p>Hold your device 2-3 inches from your throat</p>
              </div>
            </div>
            <div className="instruction-step">
              <div className="step-num">3</div>
              <div>
                <strong>Record</strong>
                <p>Take a sip of water and swallow naturally while recording</p>
              </div>
            </div>
            <div className="instruction-step">
              <div className="step-num">4</div>
              <div>
                <strong>Review</strong>
                <p>Get your instant screening results with detailed analysis</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="welcome-sidebar">
        <HistoryPanel history={history} onClear={onClearHistory} />
      </div>
    </div>
  );
}
