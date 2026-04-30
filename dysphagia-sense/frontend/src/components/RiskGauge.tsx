import { useEffect, useState } from "react";

interface Props {
  riskLevel: "Low" | "Medium" | "High";
  score: number;
}

const RISK_CONFIG = {
  Low: { color: "#10b981", angle: -60, label: "Low Risk" },
  Medium: { color: "#f59e0b", angle: 0, label: "Medium Risk" },
  High: { color: "#ef4444", angle: 60, label: "High Risk" },
};

export function RiskGauge({ riskLevel, score }: Props) {
  const [animated, setAnimated] = useState(false);
  const config = RISK_CONFIG[riskLevel];

  useEffect(() => {
    const t = setTimeout(() => setAnimated(true), 100);
    return () => clearTimeout(t);
  }, []);

  const needleAngle = animated ? config.angle : -90;

  return (
    <div className="risk-gauge">
      <svg viewBox="0 0 200 120" className="gauge-svg">
        {/* Background arc segments */}
        <path
          d="M 20 100 A 80 80 0 0 1 100 20"
          fill="none"
          stroke="#10b981"
          strokeWidth="12"
          strokeLinecap="round"
          opacity="0.3"
        />
        <path
          d="M 100 20 A 80 80 0 0 1 140 30"
          fill="none"
          stroke="#f59e0b"
          strokeWidth="12"
          strokeLinecap="round"
          opacity="0.3"
        />
        <path
          d="M 140 30 A 80 80 0 0 1 180 100"
          fill="none"
          stroke="#ef4444"
          strokeWidth="12"
          strokeLinecap="round"
          opacity="0.3"
        />

        {/* Needle */}
        <g
          style={{
            transform: `rotate(${needleAngle}deg)`,
            transformOrigin: "100px 100px",
            transition: "transform 1.2s cubic-bezier(0.34, 1.56, 0.64, 1)",
          }}
        >
          <line
            x1="100"
            y1="100"
            x2="100"
            y2="30"
            stroke={config.color}
            strokeWidth="3"
            strokeLinecap="round"
          />
          <circle cx="100" cy="100" r="6" fill={config.color} />
        </g>

        {/* Center dot */}
        <circle cx="100" cy="100" r="3" fill="white" />
      </svg>

      <div className="gauge-label" style={{ color: config.color }}>
        {config.label}
      </div>
      <div className="gauge-score">Score: {score}/20+</div>
    </div>
  );
}
