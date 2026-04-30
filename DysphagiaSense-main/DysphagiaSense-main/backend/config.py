import os
from dotenv import load_dotenv

load_dotenv()

# Server
HOST = "0.0.0.0"
PORT = 8000
ALLOWED_ORIGINS = ["*"]  # Open for local dev; restrict in production

# Audio processing
SAMPLE_RATE = 22050
MAX_AUDIO_DURATION_SEC = 15
MIN_AUDIO_DURATION_SEC = 1
MAX_FILE_SIZE_MB = 10

# Gemini
GEMINI_API_KEY = os.getenv("GEMINI_API_KEY", "")
GEMINI_MODEL = "gemini-2.5-flash"

# Disclaimer
MEDICAL_DISCLAIMER = (
    "This is a screening tool only, not a medical diagnosis. "
    "It does not replace professional medical evaluation. "
    "If you have concerns about swallowing difficulties, "
    "please consult a healthcare professional or speech-language pathologist."
)
