# AccessCity

A crowdsourced accessibility map for Meridian, "the city of tomorrow" — built for FutureHacks 2026 (Intermediate track).

## Run it

No build step, no server required. Just open `index.html` in a browser (double-click it, or drag it into a browser tab).

Seed data loads from `data/seed.js` (a curated 30-report demo dataset — see below). New reports you submit are kept in memory and saved to `localStorage`, so they survive a page refresh during a demo.

## Status: Day 1-5 of the build plan complete (see the functional spec)

Done:
- City grid (6 neighborhoods x 5 blocks = 30 blocks)
- Click a block -> see every report filed on it
- "Report a Barrier" form -> add a new report, grid updates immediately
- Per-block accessibility score + color coding (green/yellow/red/gray), computed live from reports
- Citywide stats bar (doubles as the color legend)
- Filters by barrier type and by affected need -- grid recolors live, block modal tags which reports match the active filter
- Accessibility toggles: high-contrast mode (persisted), full keyboard navigation, `aria-live` status announcements, text-to-speech "read aloud" per block
- Curated seed data generator (`seed_generator.py`) -- guarantees 6 accessible / 6 some-barriers / 6 high-barriers / 12 unrated blocks on load, verified by an assertion pass against the scoring formula before writing
- Automated DOM test suite (`tests/dom.test.js`, jsdom) -- 7/7 passing, run with `npm test`
- Devpost write-up draft (`../AccessCity_Devpost_Writeup.md`)
- Demo script + final submission checklist (`../AccessCity_Demo_Script.md`)

Not yet (these need you, not more code -- see `../AccessCity_Demo_Script.md`):
- Deploy to GitHub Pages (or Netlify) for a live demo link
- Record the 2-3 min demo video
- Submit on Devpost before Sun Jun 28, 8:00 PM ET

## Scoring, in one paragraph

Each block's score is `round(100 - average_severity * 20)` across the reports that match the current filter. Score >= 80 is "Accessible" (green), 50-79 is "Some Barriers" (yellow), under 50 is "High Barriers" (red), and a block with zero matching reports is "Unrated" (gray). The formula is deliberately conservative: a single high-severity report is enough to flag a block red rather than averaging out, because a false "looks fine" is worse than an overcautious warning for a tool like this.

## Regenerating seed data

```
python3 seed_generator.py
```

Writes a fresh `data/seed.js` with 30 reports curated across a fixed 6/6/6/12 status split. Uses a fixed random seed (42) so output is reproducible — change `random.seed(...)` if you want different sample data. The script asserts the curated severity combos resolve to the intended status before writing anything to disk, so a broken combo table fails loudly instead of silently shipping a wrong-colored demo.

## File structure

```
accesscity/
  index.html
  styles.css
  app.js
  seed_generator.py
  data/
    seed.js
  README.md
```
