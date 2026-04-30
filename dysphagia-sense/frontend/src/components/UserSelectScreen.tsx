import { useState } from "react";
import type { User } from "../types";
import { Disclaimer } from "./Disclaimer";

interface Props {
  users: User[];
  onSelect: (id: string) => void;
  onCreate: (name: string) => void;
}

export function UserSelectScreen({ users, onSelect, onCreate }: Props) {
  const [newName, setNewName] = useState("");

  const handleCreate = () => {
    if (newName.trim()) {
      onCreate(newName.trim());
      setNewName("");
    }
  };

  return (
    <div className="user-select-screen">
      <div className="user-select-card glass-card">
        <div className="user-select-header">
          <div className="logo-icon logo-icon-lg">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
              <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z" />
              <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
              <line x1="12" x2="12" y1="19" y2="22" />
            </svg>
          </div>
          <h1>DysphagiaSense</h1>
          <p className="text-muted">Select or create a profile to get started</p>
        </div>

        {users.length > 0 && (
          <div className="user-select-list">
            <div className="user-select-label">Existing Profiles</div>
            {users.map((u) => (
              <button
                key={u.id}
                className="user-select-item"
                onClick={() => onSelect(u.id)}
              >
                <div className="avatar" style={{ background: u.color }}>
                  {u.name.charAt(0).toUpperCase()}
                </div>
                <div className="user-select-item-info">
                  <span className="user-select-item-name">{u.name}</span>
                  <span className="user-select-item-date">
                    Created {new Date(u.createdAt).toLocaleDateString()}
                  </span>
                </div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="16" height="16" className="text-muted">
                  <path d="M9 18l6-6-6-6" />
                </svg>
              </button>
            ))}
          </div>
        )}

        <div className="user-select-create">
          <div className="user-select-label">
            {users.length > 0 ? "Or create a new profile" : "Create your profile"}
          </div>
          <div className="user-create-form">
            <input
              type="text"
              className="user-input"
              placeholder="Enter your name"
              value={newName}
              onChange={(e) => setNewName(e.target.value)}
              onKeyDown={(e) => e.key === "Enter" && handleCreate()}
              maxLength={30}
              autoFocus
            />
            <button
              className="btn-primary"
              onClick={handleCreate}
              disabled={!newName.trim()}
            >
              Continue
            </button>
          </div>
        </div>

        <Disclaimer compact />
      </div>
    </div>
  );
}
