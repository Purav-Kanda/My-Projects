import { useState, useCallback, useEffect } from "react";
import type { User } from "../types";

const USERS_KEY = "dysphagiasense_users";
const ACTIVE_KEY = "dysphagiasense_active_user";

const AVATAR_COLORS = [
  "#6366f1",
  "#8b5cf6",
  "#ec4899",
  "#f43f5e",
  "#f59e0b",
  "#10b981",
  "#06b6d4",
  "#3b82f6",
];

function loadUsers(): User[] {
  try {
    const raw = localStorage.getItem(USERS_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

function loadActiveId(): string | null {
  return localStorage.getItem(ACTIVE_KEY);
}

export function useUsers() {
  const [users, setUsers] = useState<User[]>(loadUsers);
  const [activeUserId, setActiveUserId] = useState<string | null>(loadActiveId);

  useEffect(() => {
    localStorage.setItem(USERS_KEY, JSON.stringify(users));
  }, [users]);

  useEffect(() => {
    if (activeUserId) {
      localStorage.setItem(ACTIVE_KEY, activeUserId);
    } else {
      localStorage.removeItem(ACTIVE_KEY);
    }
  }, [activeUserId]);

  const activeUser = users.find((u) => u.id === activeUserId) ?? null;

  const createUser = useCallback(
    (name: string) => {
      const color = AVATAR_COLORS[users.length % AVATAR_COLORS.length];
      const user: User = {
        id: crypto.randomUUID(),
        name: name.trim(),
        color,
        createdAt: Date.now(),
      };
      setUsers((prev) => [...prev, user]);
      setActiveUserId(user.id);
      return user;
    },
    [users.length],
  );

  const switchUser = useCallback((id: string) => {
    setActiveUserId(id);
  }, []);

  const deleteUser = useCallback(
    (id: string) => {
      setUsers((prev) => prev.filter((u) => u.id !== id));
      if (activeUserId === id) {
        setActiveUserId(null);
      }
      // Clean up that user's history
      localStorage.removeItem(`dysphagiasense_history_${id}`);
    },
    [activeUserId],
  );

  const logout = useCallback(() => {
    setActiveUserId(null);
  }, []);

  return { users, activeUser, createUser, switchUser, deleteUser, logout };
}
