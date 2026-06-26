"""
seed_generator.py — generates data/seed.js for AccessCity.

v2 (Day 4): curated distribution instead of pure random.

Why curated: a fully random seed (v1) could plausibly hand you a demo where
every block is "Some Barriers" and the grid looks visually flat on stage.
For a 5-minute judged demo, we want a grid that visibly shows all four
statuses (green/yellow/red/gray) the second it loads, so the scoring system
is obvious without clicking anything. So this version explicitly assigns:
  - 6 blocks  -> accessible      (green)
  - 6 blocks  -> some-barriers   (yellow)
  - 6 blocks  -> high-barriers   (red)
  - 12 blocks -> unrated         (gray, zero reports)
out of the 30 total blocks (6 neighborhoods x 5 blocks).

The severity combos below are chosen so the resulting average severity
always lands in the intended scoring bucket -- see verify_distribution()
for the assertion-based proof of that before anything is written to disk.

Scoring formula this has to match exactly (kept in sync with app.js):
  score = round(100 - avg_severity * 20)
  score >= 80          -> accessible
  50 <= score < 80      -> some-barriers
  score < 50            -> high-barriers

Run: python3 seed_generator.py
"""

import json
import os
import random
from datetime import datetime, timedelta

random.seed(42)  # fixed seed -> reproducible demo data

GRID_COLS = 6
GRID_ROWS = 5

BARRIER_TYPES = [
    "no-curb-cut",
    "stairs-only-entrance",
    "broken-elevator",
    "no-audible-signal",
    "blocked-sidewalk",
    "uneven-pavement",
    "no-accessible-parking",
    "other",
]

AFFECTED_NEEDS = [
    "wheelchair",
    "stroller",
    "blind-low-vision",
    "deaf-hard-of-hearing",
    "elderly",
    "temporary-injury",
]

DESCRIPTIONS = {
    "no-curb-cut": [
        "Corner has a curb with no ramp down to the crosswalk.",
        "Curb cut exists but is too steep to use safely.",
    ],
    "stairs-only-entrance": [
        "Building entrance is up a short flight of stairs, no ramp.",
        "Only way in is a set of stairs -- no visible ramp or lift.",
    ],
    "broken-elevator": [
        "Elevator has been out of service for weeks.",
        "Elevator door doesn't open fully, hard to get a wheelchair through.",
    ],
    "no-audible-signal": [
        "Crosswalk signal is visual only, no audio cue for crossing.",
        "No tactile paving or sound signal at this intersection.",
    ],
    "blocked-sidewalk": [
        "Construction equipment is blocking the entire sidewalk.",
        "Parked delivery van blocks the curb cut and ramp.",
    ],
    "uneven-pavement": [
        "Large crack and uneven slabs create a trip hazard.",
        "Tree roots have pushed up the sidewalk panels here.",
    ],
    "no-accessible-parking": [
        "No marked accessible parking spot within a block of the entrance.",
        "Accessible spot is there, but the curb cut next to it is broken.",
    ],
    "other": [
        "Bike share dock partially blocks the path of travel.",
        "Uncovered utility access panel sits right in the walkway.",
    ],
}

# Severity combos chosen so the resulting block average always falls in the
# intended status bucket. See verify_distribution() for the proof.
GREEN_COMBOS = [[1], [1, 1], [1, 1, 1]]            # avg = 1   -> score 80 -> accessible
YELLOW_COMBOS = [[2], [2, 2], [1, 2], [2, 3]]       # avg in (1, 2.5] -> score in [50, 80) -> some-barriers
RED_COMBOS = [[4], [5], [3, 4], [4, 4], [3, 5, 4]]  # avg >= 3 -> score <= 40 -> high-barriers


def compute_score(severities):
    if not severities:
        return None
    avg = sum(severities) / len(severities)
    return round(100 - avg * 20)


def status_for_score(score):
    if score is None:
        return "unrated"
    if score >= 80:
        return "accessible"
    if score >= 50:
        return "some-barriers"
    return "high-barriers"


def verify_distribution(assignments):
    """Sanity-check every curated block before we write anything to disk.

    assignments: list of (intended_status, severities) tuples.
    Raises AssertionError immediately if the formula and the combo tables
    have drifted out of sync -- much better to crash here than to discover
    a green block rendering red five minutes before a demo.
    """
    for intended_status, severities in assignments:
        score = compute_score(severities)
        actual_status = status_for_score(score)
        assert actual_status == intended_status, (
            f"Combo {severities} (avg score {score}) resolved to "
            f"'{actual_status}', expected '{intended_status}'. "
            f"Fix the combo tables or the scoring formula."
        )
    print(f"verify_distribution: {len(assignments)} curated blocks all check out.")


def random_needs():
    k = random.choice([1, 1, 2])
    return random.sample(AFFECTED_NEEDS, k)


def random_date():
    days_ago = random.randint(0, 21)
    dt = datetime.now() - timedelta(days=days_ago, hours=random.randint(0, 23))
    return dt.isoformat(timespec="seconds")


def make_report(report_id, block_id, severity):
    barrier = random.choice(BARRIER_TYPES)
    return {
        "id": report_id,
        "blockId": block_id,
        "barrierType": barrier,
        "severity": severity,
        "affectedNeeds": random_needs(),
        "description": random.choice(DESCRIPTIONS[barrier]),
        "reportedAt": random_date(),
    }


def main():
    all_block_ids = [
        f"B-{col}-{row}" for col in range(GRID_COLS) for row in range(GRID_ROWS)
    ]
    random.shuffle(all_block_ids)

    green_ids = all_block_ids[0:6]
    yellow_ids = all_block_ids[6:12]
    red_ids = all_block_ids[12:18]
    # remaining 12 ids (all_block_ids[18:30]) are left unrated -> zero reports

    assignments_for_verify = []
    reports = []
    report_counter = 1

    def assign(block_ids, status, combos):
        nonlocal report_counter
        for block_id in block_ids:
            combo = random.choice(combos)
            assignments_for_verify.append((status, combo))
            for severity in combo:
                report_id = f"r{report_counter:03d}"
                report_counter += 1
                reports.append(make_report(report_id, block_id, severity))

    assign(green_ids, "accessible", GREEN_COMBOS)
    assign(yellow_ids, "some-barriers", YELLOW_COMBOS)
    assign(red_ids, "high-barriers", RED_COMBOS)

    verify_distribution(assignments_for_verify)

    # Shuffle report order so the seed data doesn't read as suspiciously
    # grouped by status when someone pages through it during judging.
    random.shuffle(reports)

    os.makedirs("data", exist_ok=True)
    js_content = (
        "// Auto-generated by seed_generator.py -- regenerate, don't hand-edit.\n"
        "// Curated distribution: 6 accessible / 6 some-barriers / 6 high-barriers / 12 unrated blocks.\n"
        "const SEED_REPORTS = "
        + json.dumps(reports, indent=2)
        + ";\n"
    )
    with open(os.path.join("data", "seed.js"), "w") as f:
        f.write(js_content)

    print(f"Wrote {len(reports)} seed reports to data/seed.js")
    print(f"Blocks: {len(green_ids)} accessible, {len(yellow_ids)} some-barriers, "
          f"{len(red_ids)} high-barriers, {30 - len(green_ids) - len(yellow_ids) - len(red_ids)} unrated")


if __name__ == "__main__":
    main()
