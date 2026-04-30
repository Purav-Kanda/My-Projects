<div align="center">

# 🫁 DysphagiaSense

### AI-powered acoustic screening for swallowing difficulties

**Record a swallow. Get an instant, AI-backed risk assessment.**

---

*Built at **MacHack** · 12 hours · 2025*

**Team:** Harsifat Singh · Ali Fadoo · Purav Kanda · Pratik Surana

---

[![FastAPI](https://img.shields.io/badge/FastAPI-0.115-009688?style=flat-square&logo=fastapi)](https://fastapi.tiangolo.com)
[![React](https://img.shields.io/badge/React-18-61DAFB?style=flat-square&logo=react)](https://react.dev)
[![Gemini](https://img.shields.io/badge/Gemini-2.5_Flash-4285F4?style=flat-square&logo=google)](https://ai.google.dev)
[![Python](https://img.shields.io/badge/Python-3.11+-3776AB?style=flat-square&logo=python)](https://python.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?style=flat-square&logo=typescript)](https://typescriptlang.org)

</div>

---

## The Problem

**Dysphagia** — the medical term for difficulty swallowing — affects an estimated **1 in 25 adults** worldwide, with rates rising sharply in older populations, stroke survivors, Parkinson's patients, and people with neurological conditions. It is:

- **Underdiagnosed.** Many people dismiss swallowing difficulty as normal aging, not realizing it is a clinically significant symptom.
- **Dangerous when missed.** Aspiration — food or liquid entering the airway — is a leading cause of aspiration pneumonia, which kills tens of thousands of people annually. Silent aspiration (no cough reflex) is particularly insidious.
- **Expensive to screen for.** The gold standard is a videofluoroscopic swallow study (VFSS) conducted by a speech-language pathologist — requiring clinical equipment, a specialist referral, and significant cost. Many at-risk individuals never get screened.

### Who This Affects

| Population | Dysphagia Prevalence |
|---|---|
| Community-dwelling older adults | ~15% |
| Nursing home residents | 40–60% |
| Stroke survivors | 40–78% (acute phase) |
| Parkinson's disease | 52–82% |
| Dementia patients | Up to 84% |
| Post-intubation ICU patients | 30–62% |

### The Gap DysphagiaSense Addresses

There is no accessible, low-cost, at-home tool that can flag potential swallowing difficulty early — before it progresses to aspiration pneumonia or significant quality-of-life impact.

**DysphagiaSense is a first-of-its-kind mobile acoustic screening tool** that uses the microphone on any smartphone to analyze the sound of a swallow and provide an instant preliminary risk assessment. No clinical equipment. No referral. No cost.

> ⚠️ DysphagiaSense is a **screening tool only**, not a diagnostic device. It does not replace a clinical evaluation.

---

## What It Does

```
Record a short swallow sound
         ↓
Acoustic feature extraction (librosa)
  · Duration · Energy peaks · Spectral profile
  · Zero-crossing rate · Post-swallow activity · SNR
         ↓
Rule-based risk classifier
  · 8 acoustic rules · Point-based scoring
  · Low / Medium / High
         ↓
Gemini 2.5 Flash explanation
  · Plain-language summary
  · Actionable next steps
         ↓
Instant result with full breakdown
  · Animated risk gauge
  · Acoustic radar chart
  · Detailed flag analysis
  · Per-user history tracking
```

### Key Features

- **Live mic recording** — works directly in the browser, including iPhone Safari via HTTPS
- **File upload** — analyze existing `.mp3`, `.wav`, `.m4a`, `.webm`, `.ogg` recordings
- **Real-time waveform** — live visualization using Web Audio API
- **Risk gauge** — animated SVG arc gauge (Low / Medium / High) with smooth needle animation
- **Acoustic radar chart** — visualizes 6 key features against typical baselines
- **AI-generated explanations** — Gemini 2.5 Flash writes a warm, jargon-free summary with actionable guidance
- **Graceful fallback** — full template-based explanations if Gemini is unavailable
- **Multi-user profiles** — per-user history, avatar colors, stored locally in the browser
- **Screening history** — area chart showing risk trends over time per user

---

## Team

| | Name | Role |
|---|---|---|
| 👤 | **Harsifat Singh** | Full-stack, backend audio pipeline, frontend architecture |
| 👤 | **Ali Fadoo** | Frontend UI/UX, component design, visualization |
| 👤 | **Purav Kanda** | Risk classification engine, acoustic feature research |
| 👤 | **Pratik Surana** | AI integration, Gemini prompting, explanation system |

*Built in ~12 hours at MacHack.*

---

## Architecture

```
┌──────────────────────────────────────────────────────┐
│                    Browser / iPhone                   │
│                                                        │
│  React + Vite (HTTPS, port 5173)                      │
│  ┌────────────┐  ┌───────────────┐  ┌──────────────┐ │
│  │ UserSelect │  │  RecordScreen │  │ ResultScreen │ │
│  │  Screen    │  │  + Waveform   │  │ + Radar/Gauge│ │
│  └────────────┘  └──────┬────────┘  └──────────────┘ │
│                          │ FormData (audio blob)       │
└──────────────────────────┼─────────────────────────────┘
                           │ POST /api/analyze
                           ▼
┌──────────────────────────────────────────────────────┐
│                  FastAPI (port 8000)                  │
│                                                        │
│  ┌─────────────────┐                                  │
│  │  audio_processor│  pydub format retry loop         │
│  │                 │  → librosa feature extraction    │
│  └────────┬────────┘                                  │
│           │ SwallowFeatures (12+ acoustic metrics)    │
│  ┌────────▼────────┐                                  │
│  │ risk_classifier │  8 acoustic rules, point scoring │
│  └────────┬────────┘                                  │
│           │ (risk_level, score, flags)                 │
│  ┌────────▼────────┐                                  │
│  │  explanation    │  Gemini 2.5 Flash + fallback     │
│  └────────┬────────┘                                  │
│           │ AnalysisResponse (JSON)                    │
└──────────────────────────────────────────────────────┘
```

---

## Tech Stack

| Layer | Technology | Why |
|---|---|---|
| Frontend framework | React 18 + TypeScript (Vite) | Fast dev, strong typing |
| Charts & visualization | recharts (RadarChart, AreaChart) | Flexible, React-native charts |
| Audio capture | MediaRecorder + Web Audio API | Browser-native, no native app needed |
| Frontend HTTPS | `@vitejs/plugin-basic-ssl` | Required for mic on network URLs |
| Backend framework | FastAPI + uvicorn | Fast async Python API |
| Audio decoding | pydub + ffmpeg | Handles all browser MIME types including Safari MP4/AAC |
| Audio analysis | librosa + NumPy | Industry-standard audio feature extraction |
| AI explanations | Google Gemini 2.5 Flash | Fast, warm natural language with low latency |
| Local persistence | localStorage | Zero-dependency demo-ready storage |

---

## Project Structure

```
DysphagiaSense/
├── backend/
│   ├── .env                        # GEMINI_API_KEY (not committed)
│   ├── .env.example
│   ├── requirements.txt
│   ├── config.py                   # All settings and constants
│   ├── main.py                     # FastAPI app + CORS
│   ├── routers/
│   │   └── analyze.py              # GET /api/health · POST /api/analyze
│   ├── schemas/
│   │   └── response.py             # Pydantic response models
│   └── services/
│       ├── audio_processor.py      # Format conversion + feature extraction
│       ├── risk_classifier.py      # 8-rule point-based scorer
│       └── explanation.py          # Gemini + template fallback
└── frontend/
    ├── vite.config.ts              # HTTPS, proxy /api → backend
    ├── index.html
    └── src/
        ├── App.tsx                 # Screen state machine
        ├── App.css                 # Dark glassmorphism design system
        ├── types/index.ts
        ├── services/api.ts
        ├── hooks/
        │   ├── useAudioRecorder.ts # MediaRecorder + Safari MIME fallback
        │   ├── useWaveform.ts      # Web Audio AnalyserNode → canvas
        │   ├── useHistory.ts       # Per-user localStorage history
        │   └── useUsers.ts         # User CRUD + active session
        └── components/
            ├── UserSelectScreen.tsx
            ├── UserMenu.tsx
            ├── Header.tsx
            ├── WelcomeScreen.tsx
            ├── RecordScreen.tsx
            ├── ResultScreen.tsx
            ├── RiskGauge.tsx           # SVG gauge, animated needle
            ├── FeatureRadarChart.tsx   # 6-axis acoustic radar
            ├── HistoryPanel.tsx        # Area chart + entry list
            ├── WaveformVisualizer.tsx
            └── Disclaimer.tsx
```

---

## How It Works

### 1. Audio Pipeline

The backend receives audio as a multipart upload. Because browsers — especially Safari/iOS — often send MP4/AAC with misleading or absent MIME types, we never trust the content type. Temp files are written **without an extension** so ffmpeg uses magic-byte detection. A retry loop then attempts every common format:

```python
_FORMAT_FALLBACKS = [None, "mp4", "webm", "ogg", "mp3", "wav", "aac"]

for fmt in _FORMAT_FALLBACKS:
    try:
        audio_segment = AudioSegment.from_file(file_path, format=fmt)
        break
    except Exception:
        continue
```

After decoding, audio is validated (≥ 1 s, not silent), clamped to 15 s, and normalized to `[-1, 1]`.

### 2. Feature Extraction

We extract 12+ acoustic features using librosa — each chosen for clinical relevance to swallowing dysfunction:

| Feature | Clinical Relevance |
|---|---|
| `duration_sec` | Prolonged swallows indicate reduced efficiency |
| `rms_energy_mean / std` | Energy level and consistency across the swallow |
| `spectral_centroid_mean` | High centroid may reflect unusual airway vibrations |
| `spectral_bandwidth_mean` | Broadband noise can signal turbulence or coughing |
| `zero_crossing_rate_mean` | High ZCR correlates with turbulent, cough-like sounds |
| `mfcc_means` (×13) | Timbral fingerprint — MFCCs capture overall vocal tract shape |
| `num_energy_peaks` | Multiple peaks suggest coughing or repeated swallow attempts |
| `peak_to_peak_time` | Long span between events may indicate difficulty |
| `has_post_swallow_activity` | Post-swallow energy = residue, wet voice, or throat clearing |
| `signal_to_noise_ratio` | Low SNR reduces reliability of other features |

### 3. Risk Classifier

A transparent, point-based rule engine — no black-box ML. Every point is auditable, every rule has a clinical motivation.

| Rule | Threshold | Points |
|---|---|---|
| Prolonged duration | > 8 s | 3 |
| Moderate duration | > 5 s | 1 |
| Many energy peaks | ≥ 5 | 4 |
| Some energy peaks | ≥ 3 | 2 |
| Post-swallow activity | detected | 3 |
| High zero-crossing rate | ZCR > 0.15 | 3 |
| Moderate ZCR | ZCR > 0.10 | 1 |
| Highly irregular energy | CV > 1.5 | 3 |
| Moderately irregular | CV > 1.0 | 1 |
| Weak signal | RMS < 0.02 | 2 |
| Elevated spectral centroid | > 3000 Hz | 2 |
| Low SNR | < 5 dB | 1 |

**Risk thresholds:** Low (0–4) · Medium (5–10) · High (≥ 11)

### 4. AI-Powered Explanation

Triggered flags and audio metrics are sent to **Gemini 2.5 Flash** with a structured prompt:
- **6th-grade reading level** — accessible to older adults and caregivers
- **Warm, non-alarming tone** — designed to inform, not frighten
- **Never claims diagnosis** — always frames as "screening suggests"
- **Actionable guidance** — specific next steps tailored to the risk level

If Gemini is unavailable, a full **template fallback** generates explanations from the flag descriptions — the app never leaves a user without guidance.

---

## API Reference

### `GET /api/health`
```json
{ "status": "healthy", "version": "0.1.0" }
```

### `POST /api/analyze`

**Request:** `multipart/form-data`, field `audio` — any audio file up to 10 MB.

**Response:**
```json
{
  "risk_level": "Medium",
  "risk_score": 6,
  "confidence_note": "Based on acoustic screening analysis",
  "reason_summary": "Your swallowing sound screening detected some features worth noting...",
  "guidance": "Consider paying closer attention to how you feel when eating...",
  "disclaimer": "This is a screening tool only, not a medical diagnosis...",
  "features": {
    "duration_sec": 3.2,
    "rms_energy_mean": 0.18,
    "rms_energy_std": 0.09,
    "spectral_centroid_mean": 1842.5,
    "spectral_bandwidth_mean": 1205.3,
    "zero_crossing_rate_mean": 0.11,
    "num_energy_peaks": 3,
    "peak_to_peak_time": 1.8,
    "has_post_swallow_activity": true,
    "signal_to_noise_ratio": 12.4
  },
  "flags": [
    { "name": "some_peaks",   "points": 2, "description": "Several distinct sound events detected" },
    { "name": "post_swallow", "points": 3, "description": "Significant sound activity after the main swallow" }
  ]
}
```

**Errors:** `413` file too large · `500` decode/analysis failure

---

## Setup & Running Locally

### Prerequisites

```bash
brew install ffmpeg        # Required for pydub audio decoding
# Python 3.11+, Node.js 18+
```

### Backend

```bash
cd backend

python3 -m venv .venv && source .venv/bin/activate

pip install -r requirements.txt

cp .env.example .env
# Edit .env → set GEMINI_API_KEY=your-key-here

python main.py
# API live at http://localhost:8000
# Swagger UI at http://localhost:8000/docs
```

Verify:
```bash
curl localhost:8000/api/health
# {"status":"healthy","version":"0.1.0"}
```

### Frontend

```bash
cd frontend

npm install

npm run dev
# https://localhost:5173
```

> The dev server runs on **HTTPS** (self-signed cert). Your browser will show a security warning — click **Advanced → Proceed**. This is required for microphone access.

All `/api/*` requests are proxied to `http://localhost:8000` — no CORS setup needed.

### iPhone / Network Testing

```bash
# 1. Get your Mac's local IP
ipconfig getifaddr en0
# → e.g. 192.168.1.42

# 2. Open on iPhone
# https://192.168.1.42:5173

# 3. Safari: tap Advanced → visit this website (certificate warning)
# 4. Allow microphone access when prompted
```

---

## Configuration

`backend/config.py`:

| Setting | Default | Description |
|---|---|---|
| `HOST` | `0.0.0.0` | Bind address |
| `PORT` | `8000` | API port |
| `ALLOWED_ORIGINS` | `["*"]` | CORS (open for local dev) |
| `SAMPLE_RATE` | `22050` | librosa target sample rate (Hz) |
| `MAX_AUDIO_DURATION_SEC` | `15` | Clips trimmed to this |
| `MIN_AUDIO_DURATION_SEC` | `1` | Shorter clips rejected |
| `MAX_FILE_SIZE_MB` | `10` | Upload limit |
| `GEMINI_MODEL` | `gemini-2.5-flash` | Gemini model ID |

`.env`:
```bash
GEMINI_API_KEY=your-gemini-api-key-here
```

---

## Design Decisions

**Rule-based classifier over ML** — With no labeled clinical training data and a 12-hour timeline, rule-based scoring was the right call. Every decision is transparent and tunable. Each rule maps to a published acoustic indicator of dysphagia.

**pydub + ffmpeg for format conversion** — librosa cannot decode AAC/MP4 natively. Safari records in MP4/AAC. The no-extension temp file + format retry loop was the key insight that solved the browser compatibility problem.

**Gemini for explanations only, not classification** — Risk scoring is deterministic and auditable. Gemini's job is to make the output human — warm, readable, and actionable. Using AI only for communication (not clinical scoring) keeps the system trustworthy and debuggable.

**Vite proxy for CORS** — All API traffic goes through the Vite dev server's `/api` proxy to `localhost:8000`. Zero CORS configuration, zero exposure of the backend to the network.

**localStorage for user data** — Right-sized for a hackathon demo. Zero infrastructure, instant persistence, per-user isolation via keyed entries (`dysphagiasense_history_{userId}`, `dysphagiasense_users`, `dysphagiasense_active_user`).

**HTTPS for microphone access** — `getUserMedia()` requires a secure context on non-localhost origins. `@vitejs/plugin-basic-ssl` injects a self-signed cert so the dev server serves HTTPS to any device on the network.

---

## Future Directions

- **Clinical validation** — Work with speech-language pathologists to validate thresholds against VFSS ground truth
- **ML model** — Train a supervised classifier on labeled swallow audio to replace the rule engine
- **Longitudinal tracking** — Detect gradual changes in swallowing function over weeks/months
- **Caregiver portal** — Let family members or nurses monitor multiple patients remotely
- **EHR integration** — Export screening results in FHIR-compatible format for clinical systems
- **Native mobile app** — iOS/Android with background noise cancellation and guided recording prompts

---

## Disclaimer

DysphagiaSense is a **screening tool only**. It is not a medical device, does not constitute a diagnosis, and has not been clinically validated. The acoustic analysis is based on general signal processing heuristics. If you or someone you care for has concerns about swallowing difficulties, please consult a qualified healthcare professional or speech-language pathologist.

---

<div align="center">

*Made with care at MacHack by Harsifat Singh, Ali Fadoo, Purav Kanda & Pratik Surana*

</div>
