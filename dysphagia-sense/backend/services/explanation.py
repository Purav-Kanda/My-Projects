from typing import Literal

from google import genai
from config import GEMINI_API_KEY, GEMINI_MODEL
from services.audio_processor import SwallowFeatures

_client = None


def _get_client() -> genai.Client:
    global _client
    if _client is None:
        _client = genai.Client(api_key=GEMINI_API_KEY)
    return _client


async def generate_explanation(
    risk_level: Literal["Low", "Medium", "High"],
    features: SwallowFeatures,
    feature_flags: list[tuple[str, int, str]],
) -> tuple[str, str]:
    """
    Generate human-readable explanation and guidance.
    Returns (reason_summary, guidance).
    Falls back to templates if Gemini is unavailable.
    """
    try:
        if not GEMINI_API_KEY:
            raise ValueError("No Gemini API key configured")

        flag_descriptions = "\n".join(
            f"- {name}: {desc}" for name, _, desc in feature_flags
        )

        prompt = f"""You are a health screening assistant for a swallowing difficulty (dysphagia) screening tool called DysphagiaSense. Based on the acoustic analysis of a recorded swallowing sound, generate two pieces of text.

RISK LEVEL: {risk_level}

ACOUSTIC OBSERVATIONS:
{flag_descriptions}

AUDIO METRICS:
- Duration: {features.duration_sec:.1f} seconds
- Number of sound events: {features.num_energy_peaks}
- Post-swallow activity: {"Yes" if features.has_post_swallow_activity else "No"}

Generate EXACTLY two sections:

SUMMARY: A 2-3 sentence plain-language explanation of what the screening found. Use simple, non-alarming language appropriate for older adults. Do not use technical jargon. Be honest but gentle.

GUIDANCE: 2-3 specific, actionable next steps appropriate for the risk level:
- Low: General swallowing health tips
- Medium: Suggest monitoring and considering a professional consultation
- High: Strongly recommend consulting a speech-language pathologist or doctor

CRITICAL RULES:
- Never claim this is a diagnosis
- Never use the word "diagnosis" or "diagnose"
- Always frame as "screening suggests" or "screening indicates"
- Keep language at a 6th-grade reading level
- Be warm, clear, and reassuring in tone

Format your response exactly as:
SUMMARY: <your summary text>
GUIDANCE: <your guidance text>"""

        client = _get_client()
        response = client.models.generate_content(
            model=GEMINI_MODEL,
            contents=prompt,
        )

        text = response.text
        return _parse_gemini_response(text, risk_level)

    except Exception:
        return _fallback_explanation(risk_level, feature_flags)


def _parse_gemini_response(text: str, risk_level: str) -> tuple[str, str]:
    """Parse SUMMARY: / GUIDANCE: format from Gemini."""
    summary = ""
    guidance = ""

    if "SUMMARY:" in text and "GUIDANCE:" in text:
        parts = text.split("GUIDANCE:")
        summary = parts[0].replace("SUMMARY:", "").strip()
        guidance = parts[1].strip()
    else:
        summary = text.strip()
        guidance = _fallback_guidance(risk_level)

    return (summary, guidance)


def _fallback_explanation(
    risk_level: str,
    feature_flags: list[tuple[str, int, str]],
) -> tuple[str, str]:
    """Template-based fallback when Gemini is unavailable."""
    descriptions = [desc for _, _, desc in feature_flags]
    observations = " ".join(descriptions) if descriptions else "No notable observations."

    summaries = {
        "Low": (
            f"Your swallowing sound screening did not detect notable concerns. "
            f"{observations}"
        ),
        "Medium": (
            f"Your swallowing sound screening detected some features worth noting. "
            f"{observations} This does not mean there is a problem, but monitoring "
            f"may be helpful."
        ),
        "High": (
            f"Your swallowing sound screening detected several features that suggest "
            f"further evaluation may be beneficial. {observations}"
        ),
    }

    summary = summaries.get(risk_level, summaries["Medium"])
    guidance = _fallback_guidance(risk_level)
    return (summary, guidance)


def _fallback_guidance(risk_level: str) -> str:
    """Static guidance text by risk level."""
    guidance_map = {
        "Low": (
            "Continue to eat and drink normally. Stay hydrated and eat slowly. "
            "If you ever experience coughing, choking, or a wet-sounding voice "
            "during or after meals, consider re-screening or consulting a professional."
        ),
        "Medium": (
            "Consider paying closer attention to how you feel when eating and drinking. "
            "You may want to eat more slowly and take smaller sips. "
            "If symptoms persist or worsen, we recommend scheduling a visit "
            "with your doctor or a speech-language pathologist for a professional evaluation."
        ),
        "High": (
            "We recommend scheduling an appointment with a speech-language pathologist "
            "or your doctor for a professional swallowing evaluation. "
            "In the meantime, consider eating soft foods, taking small bites, "
            "and sitting upright during and after meals. "
            "If you experience choking or difficulty breathing while eating, "
            "seek immediate medical attention."
        ),
    }
    return guidance_map.get(risk_level, guidance_map["Medium"])
