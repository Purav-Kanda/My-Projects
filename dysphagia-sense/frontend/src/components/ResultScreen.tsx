import type { AnalysisResult, HistoryEntry } from "../types";
import { Disclaimer } from "./Disclaimer";
import { RiskGauge } from "./RiskGauge";
import { FeatureRadarChart } from "./FeatureRadarChart";
import { HistoryPanel } from "./HistoryPanel";

interface Props {
  result: AnalysisResult;
  history: HistoryEntry[];
  onClearHistory: () => void;
  onStartOver: () => void;
}

const RISK_COLORS = {
  Low: "#10b981",
  Medium: "#f59e0b",
  High: "#ef4444",
};

export function ResultScreen({ result, history, onClearHistory, onStartOver }: Props) {
  return (
    <div className="results-layout">
      <div className="results-main">
        <div className="results-header-section">
          <button className="btn-ghost btn-back-nav" onClick={onStartOver}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="20" height="20">
              <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            New Screening
          </button>
          <h2 className="section-title">Screening Results</h2>
        </div>

        <div className="results-grid">
          {/* Risk gauge card */}
          <div className="glass-card result-gauge-card">
            <RiskGauge riskLevel={result.risk_level} score={result.risk_score} />
          </div>

          {/* Radar chart card */}
          <div className="glass-card result-radar-card">
            <FeatureRadarChart features={result.features} />
          </div>

          {/* Summary card */}
          <div className="glass-card result-summary-card">
            <h3 className="card-title">What We Found</h3>
            <p className="result-summary-text">{result.reason_summary}</p>
          </div>

          {/* Guidance card */}
          <div className="glass-card result-guidance-card">
            <h3 className="card-title">Recommended Next Steps</h3>
            <p className="result-guidance-text">{result.guidance}</p>
          </div>

          {/* Flags card */}
          {result.flags.length > 0 && (
            <div className="glass-card result-flags-card">
              <h3 className="card-title">Detailed Findings</h3>
              <div className="flags-list">
                {result.flags.map((flag, i) => (
                  <div key={i} className="flag-item">
                    <div
                      className="flag-points"
                      style={{
                        background:
                          flag.points >= 3
                            ? "rgba(239, 68, 68, 0.15)"
                            : flag.points >= 1
                              ? "rgba(245, 158, 11, 0.15)"
                              : "rgba(16, 185, 129, 0.15)",
                        color:
                          flag.points >= 3
                            ? "#ef4444"
                            : flag.points >= 1
                              ? "#f59e0b"
                              : "#10b981",
                      }}
                    >
                      {flag.points > 0 ? `+${flag.points}` : "OK"}
                    </div>
                    <span className="flag-desc">{flag.description}</span>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Feature stats */}
          <div className="glass-card result-stats-card">
            <h3 className="card-title">Audio Metrics</h3>
            <div className="stats-grid">
              <div className="stat-item">
                <span className="stat-value">
                  {result.features.duration_sec.toFixed(1)}s
                </span>
                <span className="stat-label">Duration</span>
              </div>
              <div className="stat-item">
                <span className="stat-value">
                  {result.features.num_energy_peaks}
                </span>
                <span className="stat-label">Events</span>
              </div>
              <div className="stat-item">
                <span className="stat-value">
                  {result.features.signal_to_noise_ratio.toFixed(1)}dB
                </span>
                <span className="stat-label">SNR</span>
              </div>
              <div className="stat-item">
                <span className="stat-value">
                  {result.features.has_post_swallow_activity ? "Yes" : "No"}
                </span>
                <span className="stat-label">Post Activity</span>
              </div>
            </div>
          </div>
        </div>

        {/* Disclaimer */}
        <div className="glass-card result-disclaimer-card">
          <p className="text-muted" style={{ fontSize: "13px" }}>
            <strong>Disclaimer:</strong> {result.disclaimer}
          </p>
        </div>

        <Disclaimer compact />

        <button className="btn-primary btn-large" onClick={onStartOver} style={{ marginTop: "8px" }}>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="20" height="20">
            <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z" />
            <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
          </svg>
          Screen Again
        </button>
      </div>

      <div className="results-sidebar">
        <HistoryPanel history={history} onClear={onClearHistory} />
      </div>
    </div>
  );
}
