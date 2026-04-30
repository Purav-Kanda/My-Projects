import {
  RadarChart,
  PolarGrid,
  PolarAngleAxis,
  Radar,
  ResponsiveContainer,
} from "recharts";
import type { FeatureData } from "../types";

interface Props {
  features: FeatureData;
}

function normalize(value: number, min: number, max: number): number {
  return Math.min(100, Math.max(0, ((value - min) / (max - min)) * 100));
}

export function FeatureRadarChart({ features }: Props) {
  const data = [
    {
      feature: "Energy",
      value: normalize(features.rms_energy_mean, 0, 0.3),
    },
    {
      feature: "Variability",
      value: normalize(features.rms_energy_std, 0, 0.2),
    },
    {
      feature: "Spectral",
      value: normalize(features.spectral_centroid_mean, 0, 5000),
    },
    {
      feature: "Bandwidth",
      value: normalize(features.spectral_bandwidth_mean, 0, 4000),
    },
    {
      feature: "ZCR",
      value: normalize(features.zero_crossing_rate_mean, 0, 0.3),
    },
    {
      feature: "SNR",
      value: normalize(features.signal_to_noise_ratio, 0, 30),
    },
  ];

  return (
    <div className="radar-chart-container">
      <h3 className="card-title">Acoustic Profile</h3>
      <ResponsiveContainer width="100%" height={250}>
        <RadarChart data={data} cx="50%" cy="50%" outerRadius="70%">
          <PolarGrid stroke="rgba(148, 163, 184, 0.2)" />
          <PolarAngleAxis
            dataKey="feature"
            tick={{ fill: "#94a3b8", fontSize: 12 }}
          />
          <Radar
            dataKey="value"
            stroke="#6366f1"
            fill="#6366f1"
            fillOpacity={0.25}
            strokeWidth={2}
          />
        </RadarChart>
      </ResponsiveContainer>
    </div>
  );
}
