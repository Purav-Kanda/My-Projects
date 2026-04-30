import { useState, useRef, useEffect } from "react";
import type { User } from "../types";

interface Props {
  activeUser: User;
  users: User[];
  onSwitch: (id: string) => void;
  onLogout: () => void;
  onDelete: (id: string) => void;
}

export function UserMenu({ activeUser, users, onSwitch, onLogout, onDelete }: Props) {
  const [open, setOpen] = useState(false);
  const menuRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handler = (e: MouseEvent) => {
      if (menuRef.current && !menuRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    };
    document.addEventListener("mousedown", handler);
    return () => document.removeEventListener("mousedown", handler);
  }, []);

  return (
    <div className="user-menu" ref={menuRef}>
      <button className="user-menu-trigger" onClick={() => setOpen(!open)}>
        <div
          className="avatar"
          style={{ background: activeUser.color }}
        >
          {activeUser.name.charAt(0).toUpperCase()}
        </div>
        <span className="user-menu-name">{activeUser.name}</span>
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          width="14"
          height="14"
          style={{
            transition: "transform 0.2s",
            transform: open ? "rotate(180deg)" : "none",
          }}
        >
          <path d="M6 9l6 6 6-6" />
        </svg>
      </button>

      {open && (
        <div className="user-menu-dropdown">
          <div className="user-menu-section-label">Switch user</div>
          {users.map((u) => (
            <div key={u.id} className="user-menu-item-row">
              <button
                className={`user-menu-item ${u.id === activeUser.id ? "active" : ""}`}
                onClick={() => {
                  onSwitch(u.id);
                  setOpen(false);
                }}
              >
                <div className="avatar avatar-sm" style={{ background: u.color }}>
                  {u.name.charAt(0).toUpperCase()}
                </div>
                <span>{u.name}</span>
                {u.id === activeUser.id && (
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" width="14" height="14" className="check-icon">
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                )}
              </button>
              {u.id !== activeUser.id && (
                <button
                  className="user-delete-btn"
                  onClick={(e) => {
                    e.stopPropagation();
                    onDelete(u.id);
                  }}
                  title="Remove user"
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="14" height="14">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                  </svg>
                </button>
              )}
            </div>
          ))}

          <div className="user-menu-divider" />

          <button
            className="user-menu-item"
            onClick={() => {
              onLogout();
              setOpen(false);
            }}
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="16" height="16">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
              <polyline points="16 17 21 12 16 7" />
              <line x1="21" y1="12" x2="9" y2="12" />
            </svg>
            <span>Switch Account</span>
          </button>
        </div>
      )}
    </div>
  );
}
