import type { User } from "../types";
import { UserMenu } from "./UserMenu";

interface Props {
  onLogoClick: () => void;
  activeUser: User | null;
  users: User[];
  onSwitchUser: (id: string) => void;
  onLogout: () => void;
  onDeleteUser: (id: string) => void;
}

export function Header({ onLogoClick, activeUser, users, onSwitchUser, onLogout, onDeleteUser }: Props) {
  return (
    <header className="header">
      <div className="header-inner">
        <button className="header-logo" onClick={onLogoClick}>
          <div className="logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
              <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z" />
              <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
              <line x1="12" x2="12" y1="19" y2="22" />
            </svg>
          </div>
          <span className="logo-text">DysphagiaSense</span>
        </button>

        <div className="header-right">
          <div className="header-badge">
            <span className="badge-dot" />
            Screening Tool
          </div>
          {activeUser && (
            <UserMenu
              activeUser={activeUser}
              users={users}
              onSwitch={onSwitchUser}
              onLogout={onLogout}
              onDelete={onDeleteUser}
            />
          )}
        </div>
      </div>
    </header>
  );
}
