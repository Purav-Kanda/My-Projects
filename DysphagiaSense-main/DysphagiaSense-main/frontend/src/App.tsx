import { useState, useCallback } from "react";
import type { Screen, AnalysisResult } from "./types";
import { Header } from "./components/Header";
import { UserSelectScreen } from "./components/UserSelectScreen";
import { WelcomeScreen } from "./components/WelcomeScreen";
import { RecordScreen } from "./components/RecordScreen";
import { ResultScreen } from "./components/ResultScreen";
import { useAudioRecorder } from "./hooks/useAudioRecorder";
import { useHistory } from "./hooks/useHistory";
import { useUsers } from "./hooks/useUsers";
import { useWaveform } from "./hooks/useWaveform";
import { analyzeSwallow } from "./services/api";
import "./App.css";

export default function App() {
  const [screen, setScreen] = useState<Screen>("welcome");
  const [result, setResult] = useState<AnalysisResult | null>(null);
  const [uploadedFileName, setUploadedFileName] = useState<string | null>(null);
  const [isRecordingActive, setIsRecordingActive] = useState(false);

  const { users, activeUser, createUser, switchUser, deleteUser, logout } =
    useUsers();
  const { history, addEntry, clearHistory } = useHistory(activeUser?.id ?? null);

  const { canvasRef, connectStream, disconnect } =
    useWaveform(isRecordingActive);

  const onStreamReady = useCallback(
    (stream: MediaStream) => connectStream(stream),
    [connectStream],
  );

  const { state, startRecording, stopRecording, reset, setState } =
    useAudioRecorder(onStreamReady);

  if (isRecordingActive !== state.isRecording) {
    setIsRecordingActive(state.isRecording);
  }

  const handleFileSelected = (blob: Blob, name: string) => {
    reset();
    disconnect();
    setUploadedFileName(name);
    setState({
      isRecording: false,
      isProcessing: false,
      audioBlob: blob,
      error: null,
    });
  };

  const handleSubmit = async () => {
    if (!state.audioBlob) return;
    setState((prev) => ({ ...prev, isProcessing: true, error: null }));
    try {
      const analysisResult = await analyzeSwallow(state.audioBlob);
      setResult(analysisResult);
      addEntry(analysisResult);
      setScreen("results");
    } catch (err) {
      setState((prev) => ({
        ...prev,
        isProcessing: false,
        error:
          err instanceof Error
            ? err.message
            : "Analysis failed. Please try again.",
      }));
    }
  };

  const handleStartOver = () => {
    reset();
    disconnect();
    setResult(null);
    setUploadedFileName(null);
    setScreen("welcome");
  };

  const handleSwitchUser = (id: string) => {
    handleStartOver();
    switchUser(id);
  };

  const handleLogout = () => {
    handleStartOver();
    logout();
  };

  // No active user → show user select screen
  if (!activeUser) {
    return (
      <div className="app-shell">
        <UserSelectScreen
          users={users}
          onSelect={switchUser}
          onCreate={(name) => createUser(name)}
        />
      </div>
    );
  }

  return (
    <div className="app-shell">
      <Header
        onLogoClick={handleStartOver}
        activeUser={activeUser}
        users={users}
        onSwitchUser={handleSwitchUser}
        onLogout={handleLogout}
        onDeleteUser={deleteUser}
      />
      <main className="app-main">
        <div className={`screen-container ${screen}`}>
          {screen === "welcome" && (
            <WelcomeScreen
              onStart={() => setScreen("recording")}
              history={history}
              onClearHistory={clearHistory}
            />
          )}
          {screen === "recording" && (
            <RecordScreen
              isRecording={state.isRecording}
              audioBlob={state.audioBlob}
              uploadedFileName={uploadedFileName}
              error={state.error}
              isProcessing={state.isProcessing}
              canvasRef={canvasRef}
              onStartRecording={() => {
                setUploadedFileName(null);
                startRecording();
              }}
              onStopRecording={() => {
                stopRecording();
                disconnect();
              }}
              onFileSelected={handleFileSelected}
              onSubmit={handleSubmit}
              onBack={() => {
                reset();
                disconnect();
                setUploadedFileName(null);
                setScreen("welcome");
              }}
            />
          )}
          {screen === "results" && result && (
            <ResultScreen
              result={result}
              history={history}
              onClearHistory={clearHistory}
              onStartOver={handleStartOver}
            />
          )}
        </div>
      </main>
    </div>
  );
}
