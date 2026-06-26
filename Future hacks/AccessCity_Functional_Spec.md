# AccessCity — Functional Spec (FutureHacks 2026)

Fill in / edit the **Decisions Needed** section, then send this back and I'll build it.

---

## 1. Project Snapshot

| Field | Value |
|---|---|
| Hackathon | FutureHacks (TechShare), Jun 24–28, 2026 |
| Track | Intermediate (solo) |
| Theme | "Build the city of tomorrow, today" |
| Working title | AccessCity |
| One-liner | A crowdsourced accessibility map that scores every block of a city so wheelchair users, parents with strollers, blind/low-vision pedestrians, and elderly residents can see — and fix — physical barriers before they hit them. |
| Format | Single-page web app, no backend, deployed as a static site |

---

## 2. Decisions Needed From You

Fill these in (or say "use your default") before I start building.

1. **City name** — default: *Meridian* (fictional, avoids real-place complaints). Your call: _____
2. **Visual theme** — default: dark background, neon cyan/magenta accents ("future city at night"). Alternative: light/clean civic-tech look. Pick one: _____
3. **Neighborhood names** (5–6, for flavor) — default: Riverside, Old Town, Tech District, Uptown, Harbor Row, Greenline. Keep / change: _____
4. **Grid size** — default: 6 columns × 5 rows = 30 city blocks. Keep / change: _____
5. **Anything personal to add?** (e.g., tie it to a barrier you or someone you know has actually run into — strengthens the "real-world impact" pitch). Optional: _____
6. **Deploy target** — default: GitHub Pages (free, gives a live link Devpost wants). Alternative: Netlify drag-and-drop. Pick one: _____

If you skip this section, I build with all defaults.

---

## 3. MVP Scope (must-have for submission)

1. Interactive city grid (clickable blocks, not a real map — no API keys, no dependency risk).
2. "Report a barrier" form: barrier type, severity (1–5), affected-need tag, short description.
3. Per-block accessibility score, color-coded, recalculated live when a report is added.
4. Filter panel: by barrier type, by affected need (wheelchair / stroller / blind-low-vision / deaf-hard-of-hearing / elderly / temporary injury).
5. Built-in accessibility features: high-contrast toggle, text-to-speech on block details, full keyboard navigation.
6. Seeded with 15–20 realistic sample reports so the demo isn't an empty grid.

### Stretch (only if Day 4 finishes early)
- "Submit a fix" status (mark a barrier as resolved by the city).
- Simple bar chart of barrier types citywide (judges like a data viz touch).
- Shareable link to a single block's report.

---

## 4. Data Model

**BarrierReport**
| Field | Type | Notes |
|---|---|---|
| id | string | uuid or timestamp-based |
| blockId | string | e.g. `"B-3-2"` (col 3, row 2) |
| barrierType | enum | see §5 |
| severity | int 1–5 | 1 = minor, 5 = completely inaccessible |
| affectedNeeds | string[] | see §5 |
| description | string | free text, optional |
| reportedAt | ISO date | defaults to now |

**Block**
| Field | Type | Notes |
|---|---|---|
| id | string | `"B-{col}-{row}"` |
| neighborhood | string | from §2 decision 3 |
| reports | BarrierReport[] | computed/joined, not stored redundantly |
| score | int 0–100 | computed, see §6 |
| status | enum | `unrated` \| `accessible` \| `some-barriers` \| `high-barriers` |

---

## 5. Taxonomy

**Barrier types**
1. No curb cut / curb ramp missing
2. Stairs-only entrance (no ramp or elevator)
3. Broken or missing elevator
4. No audible/tactile crosswalk signal
5. Sidewalk blocked or obstructed
6. Uneven / cracked pavement (trip hazard)
7. No accessible parking nearby
8. Other (free text)

**Affected needs** (multi-select on report form, used as filter)
- Wheelchair / mobility device
- Stroller / pushing a cart
- Blind / low vision
- Deaf / hard of hearing
- Elderly / limited stamina
- Temporary injury

---

## 6. Scoring Algorithm

```
if block has 0 reports:
    status = "unrated", score = null, color = gray
else:
    avgSeverity = mean(severity of all reports on block)
    score = round(100 - avgSeverity * 20)   // severity 5 avg -> 0, severity 1 avg -> 80
    if score >= 80: status = "accessible"      -> green
    elif score >= 50: status = "some-barriers" -> yellow
    else: status = "high-barriers"             -> red
```

Zero reports deliberately shows **gray/unrated**, not green — an unreported block isn't proven accessible, it's just unverified. This nuance is worth saying out loud in the demo; it's a real design choice, not an oversight.

---

## 7. Screens & Flow

1. **Map view (home)** — grid of blocks colored by status, neighborhood labels, filter panel on the side, "Report a Barrier" button always visible.
2. **Block detail (click a block)** — popup/panel: neighborhood name, score, list of reports on that block (type, severity, needs, description), a "Read aloud" button (text-to-speech), "Add a report here" shortcut.
3. **Report form (modal)** — block selector (pre-filled if opened from a block), barrier type dropdown, severity slider 1–5, affected-needs checkboxes, description textarea, Submit. On submit: add to data, recompute that block's score/color instantly, close modal, briefly highlight the updated block.
4. **Settings (small toggle in header)** — high-contrast mode, text size, on/off for text-to-speech.

---

## 8. Accessibility Features (these ARE the UX pitch — be exact about them)

- **High-contrast mode**: swaps palette to black background / white text / yellow accents, increases border weight on interactive elements.
- **Text-to-speech**: uses the browser's built-in `SpeechSynthesis` API (no external service, no API key) to read block name, score, and report list aloud on demand.
- **Keyboard navigation**: every block, filter, and button reachable via Tab, Enter activates it, visible focus outline at all times (not just on hover).

---

## 9. Visual Style

- Theme: dark city-at-night aesthetic — near-black background, soft grid lines, neon cyan/magenta status glow on blocks (exact palette pending your answer to §2.2).
- Font: a clean sans-serif (system font stack is fine — no need to load a web font and risk it not rendering during judging).
- Status colors: green `#2ECC71`, yellow `#F1C40F`, red `#E74C3C`, gray/unrated `#7F8C8D` — adjust if you pick the light theme instead.

---

## 10. Tech Stack & Architecture

- Plain **HTML/CSS/JavaScript**, no framework, no build step — opens directly in a browser, nothing to compile, nothing to break before judging.
- No backend. Seed data lives in a JSON file loaded on page load; new reports during the demo are held in memory + `localStorage` so a refresh doesn't wipe your live demo.
- Optional: a small **Python script** to generate/randomize the seed data (15–20 reports across 30 blocks) so you're not hand-writing JSON.
- Deploy as a static site (GitHub Pages or Netlify) to get the live "Try it out" link Devpost submissions expect.

**File structure**
```
accesscity/
  index.html
  styles.css
  app.js
  data/
    seed.json
  seed_generator.py   (optional, Python)
  README.md
```

---

## 11. Judging-Criteria Map (for your write-up)

| Criterion | How this project answers it |
|---|---|
| Innovation & Creativity | Accessibility-first civic tool, not another traffic/parking/recycling app |
| Adherence to Theme | Direct hit on "civic tools... technology that improves how people live, work, connect in urban spaces" |
| Real-world Impact | CDC: 28.7% of US adults (~61M) live with a disability; 12.2% (1 in 7 adults) have a mobility disability |
| Presentation | Populated demo data, clear before/after of a live report changing a block's color |
| User Experience | The app's own accessibility features (contrast mode, read-aloud, keyboard nav) double as the UX demonstration |

---

## 12. Submission Checklist (Devpost)

- [ ] Project name + one-line elevator pitch
- [ ] "Built with" tags (HTML, CSS, JavaScript, Python if used)
- [ ] Live demo link (deployed URL)
- [ ] 2–3 min demo video (unlisted YouTube is fine)
- [ ] Write-up: inspiration → what it does → how it's built → challenges → what's next
- [ ] Screenshots (map view, block detail, report form, high-contrast mode)
- [ ] Submitted to **Intermediate** track, before Sun Jun 28, 8:00 PM ET

---

## 13. Build Timeline (recap)

- **Wed 24**: lock scope, finalize data schema, wireframe screens
- **Thu 25**: build grid + block detail popup + report form
- **Fri 26**: scoring/color logic + filters + accessibility features
- **Sat 27**: visual polish, seed data, write-up draft, full run-through
- **Sun 28**: record demo video, submit before deadline (don't wait to the last hour)

---

Send back §2 filled in (or "use defaults") and I'll start on the code.
