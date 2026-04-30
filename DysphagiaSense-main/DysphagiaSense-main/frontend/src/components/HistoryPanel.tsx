import {
  AreaChart,
  Area,
  XAxis,
  YAxis,
  Tooltip,
  ResponsiveContainer,
} from "recharts";
import type { HistoryEntry } from "../types";

interface Props {
  history: HistoryEntry[];
  onClear: () => void;
}

const RISK_COLORS = {
  Low: "#10b981",
  Medium: "#f59e0b",
  High: "#ef4444",
};

function formatTime(ts: number): string {
  return new Date(ts).toLocaleTimeString([], {
    hour: "2-digit",
    minute: "2-digit",
  });
}

function formatDate(ts: number): string {
  const d = new Date(ts);
  const today = new Date();
  if (d.toDateString() === today.toDateString()) return "Today";
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);
  if (d.toDateString() === yesterday.toDateString()) return "Yesterday";
  return d.toLocaleDateString([], { month: "short", day: "numeric" });
}

export function HistoryPanel({ history, onClear }: Props) {
  if (history.length === 0) {
    return (
      <div className="history-panel glass-card">
        <h3 className="card-title">Screening History</h3>
        <div className="history-empty">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" width="48" height="48" opacity="0.3">
            <path d="M12 8v4l3 3" strokeLinecap="round" />
            <circle cx="12" cy="12" r="10" />
          </svg>
          <p>No screenings yet</p>
          <p className="text-muted">Results will appear here</p>
        </div>
      </div>
    );
  }

  const chartData = [...history]
    .reverse()
    .map((e) => ({
      time: formatTime(e.timestamp),
      score: e.risk_score,
      level: e.risk_level,
    }));

  return (
    <div className="history-panel glass-card">
      <div className="history-header">
        <h3 className="card-title">Screening History</h3>
        <button className="btn-ghost" onClick={onClear}>
          Clear
        </button>
      </div>

      {history.length >= 2 && (
        <div className="history-chart">
          <ResponsiveContainer width="100%" height={120}>
            <AreaChart data={chartData}>
              <defs>
                <linearGradient id="scoreGrad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stopColor="#6366f1" stopOpacity={0.3} />
                  <stop offset="100%" stopColor="#6366f1" stopOpacity={0} />
                </linearGradient>
              </defs>
              <XAxis
                dataKey="time"
                tick={{ fill: "#64748b", fontSize: 10 }}
                axisLine={false}
                tickLine={false}
              />
              <YAxis hide domain={[0, "auto"]} />
              <Tooltip
                contentStyle={{
                  background: "rgba(15, 23, 42, 0.9)",
                  border: "1px solid rgba(148, 163, 184, 0.2)",
                  borderRadius: "8px",
                  color: "#e2e8f0",
                  fontSize: "12px",
                }}
              />
              <Area
                type="monotone"
                dataKey="score"
                stroke="#6366f1"
                fill="url(#scoreGrad)"
                strokeWidth={2}
              />
            </AreaChart>
          </ResponsiveContainer>
        </div>
      )}

      <div className="history-list">
        {history.map((entry) => (
          <div key={entry.id} className="history-item">
            <div
              className="history-dot"
              style={{ background: RISK_COLORS[entry.risk_level] }}
            />
            <div className="history-item-content">
              <div className="history-item-top">
                <span className="history-risk" style={{ color: RISK_COLORS[entry.risk_level] }}>
                  {entry.risk_level}
                </span>
                <span className="history-score">Score: {entry.risk_score}</span>
              </div>
              <div className="history-time">
                {formatDate(entry.timestamp)} at {formatTime(entry.timestamp)}
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
