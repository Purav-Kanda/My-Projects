import { useState, useRef, useCallback } from "react";
import type { RecorderState } from "../types";

function getSupportedMimeType(): string {
  const types = [
    "audio/webm;codecs=opus",
    "audio/webm",
    "audio/mp4",
    "audio/ogg;codecs=opus",
  ];
  for (const type of types) {
    if (MediaRecorder.isTypeSupported(type)) {
      return type;
    }
  }
  return "";
}

export function useAudioRecorder(onStreamReady?: (stream: MediaStream) => void) {
  const [state, setState] = useState<RecorderState>({
    isRecording: false,
    isProcessing: false,
    audioBlob: null,
    error: null,
  });

  const mediaRecorderRef = useRef<MediaRecorder | null>(null);
  const chunksRef = useRef<Blob[]>([]);
  const streamRef = useRef<MediaStream | null>(null);

  const startRecording = useCallback(async () => {
    try {
      setState({ isRecording: true, isProcessing: false, audioBlob: null, error: null });
      chunksRef.current = [];

      const stream = await navigator.mediaDevices.getUserMedia({
        audio: {
          echoCancellation: true,
          noiseSuppression: true,
          sampleRate: 44100,
        },
      });
      streamRef.current = stream;
      onStreamReady?.(stream);

      const mimeType = getSupportedMimeType();
      const options: MediaRecorderOptions = mimeType ? { mimeType } : {};
      const recorder = new MediaRecorder(stream, options);
      mediaRecorderRef.current = recorder;

      recorder.ondataavailable = (event) => {
        if (event.data.size > 0) {
          chunksRef.current.push(event.data);
        }
      };

      recorder.onstop = () => {
        const blob = new Blob(chunksRef.current, {
          type: recorder.mimeType || "audio/webm",
        });
        setState((prev) => ({ ...prev, isRecording: false, audioBlob: blob }));

        stream.getTracks().forEach((track) => track.stop());
        streamRef.current = null;
      };

      recorder.onerror = () => {
        setState((prev) => ({
          ...prev,
          isRecording: false,
          error: "Recording failed. Please try again.",
        }));
      };

      recorder.start(250);
    } catch (err) {
      const message =
        err instanceof DOMException && err.name === "NotAllowedError"
          ? "Microphone access was denied. Please allow microphone access in your browser settings."
          : "Could not start recording. Please ensure your device has a microphone.";
      setState({ isRecording: false, isProcessing: false, audioBlob: null, error: message });
    }
  }, [onStreamReady]);

  const stopRecording = useCallback(() => {
    if (mediaRecorderRef.current && mediaRecorderRef.current.state === "recording") {
      mediaRecorderRef.current.stop();
    }
  }, []);

  const reset = useCallback(() => {
    setState({ isRecording: false, isProcessing: false, audioBlob: null, error: null });
    if (streamRef.current) {
      streamRef.current.getTracks().forEach((track) => track.stop());
      streamRef.current = null;
    }
  }, []);

  return { state, startRecording, stopRecording, reset, setState };
}
