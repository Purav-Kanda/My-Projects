import { useState, useCallback, useEffect } from "react";
import type { AnalysisResult, HistoryEntry } from "../types";

const MAX_ENTRIES = 50;

function storageKey(userId: string) {
  return `dysphagiasense_history_${userId}`;
}

function loadHistory(userId: string | null): HistoryEntry[] {
  if (!userId) return [];
  try {
    const raw = localStorage.getItem(storageKey(userId));
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

export function useHistory(userId: string | null) {
  const [history, setHistory] = useState<HistoryEntry[]>(() =>
    loadHistory(userId),
  );

  // Reload history when user changes
  useEffect(() => {
    setHistory(loadHistory(userId));
  }, [userId]);

  // Persist on change
  useEffect(() => {
    if (userId) {
      localStorage.setItem(storageKey(userId), JSON.stringify(history));
    }
  }, [history, userId]);

  const addEntry = useCallback(
    (result: AnalysisResult) => {
      if (!userId) return;
      const entry: HistoryEntry = {
        id: crypto.randomUUID(),
        timestamp: Date.now(),
        risk_level: result.risk_level,
        risk_score: result.risk_score,
        reason_summary: result.reason_summary,
        features: result.features,
        flags: result.flags,
      };
      setHistory((prev) => [entry, ...prev].slice(0, MAX_ENTRIES));
    },
    [userId],
  );

  const clearHistory = useCallback(() => {
    setHistory([]);
  }, []);

  return { history, addEntry, clearHistory };
}
