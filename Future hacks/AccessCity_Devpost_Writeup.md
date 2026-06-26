# AccessCity — Devpost Write-Up Draft

Copy each section into the matching Devpost field. Edit freely — this is a draft, not a script.

## Inspiration

The theme was "Build the city of tomorrow, today." Most submissions to that prompt default to smart traffic lights or solar-powered benches — futuristic infrastructure that only works if it gets built. We wanted something that's actually useful starting today, with zero infrastructure spend.

About 1 in 7 US adults has a mobility disability, and over a quarter of US adults have some form of disability (CDC). Most of those people navigate cities using accessibility information that barely exists — word of mouth, outdated city PDFs, or just finding out a ramp is broken when they're standing in front of it. A "city of tomorrow" should be one where that information is collected and shared by the people who actually encounter it. That's AccessCity: a crowdsourced accessibility map for a fictional city, Meridian.

## What it does

AccessCity shows Meridian as a 30-block grid across 6 neighborhoods. Anyone can report an accessibility barrier on a block — a broken elevator, a missing curb cut, an obstructed sidewalk — and rate how severe it is. Every block is color-coded by its current accessibility score, computed live from all the reports filed on it:

- Green ("Accessible") — reports average to low severity
- Yellow ("Some Barriers") — moderate severity on average
- Red ("High Barriers") — high severity on average
- Gray ("Unrated") — no reports yet

Click any block to see its full report history. Filter the whole map by barrier type (e.g. "only show blocks with broken elevators") or by who's affected (e.g. "only show barriers that affect wheelchair users") — the grid recolors live to reflect just that slice. A high-contrast mode, full keyboard navigation, and a text-to-speech "read aloud" button on every block let the app practice the same accessibility it's trying to map.

## How we built it

Vanilla HTML, CSS, and JavaScript — no framework, no build step, no backend. The whole app is three files you can open directly in a browser.

That was a deliberate choice, not a shortcut. A hackathon demo gets judged in a five-minute window, often on unfamiliar wifi, sometimes on a different laptop than the one it was built on. Every dependency — a map API key, a database connection, a dev server — is a way for the demo to fail in front of judges. So the build avoids all of them:

- No map API (Google Maps, Leaflet, etc.) — the city is a CSS Grid of buttons. No API keys, no network calls, no rate limits.
- No backend or database — all data lives in a JS array in memory, persisted to `localStorage` so reports survive a page refresh mid-demo.
- Seed data ships as a `<script>`-loaded `.js` file (not fetched JSON), because `fetch()` hits CORS errors when a page is opened directly from disk with no local server.

The scoring formula is intentionally simple and intentionally pessimistic: average the severity of all reports on a block (1–5 scale), convert to a 0–100 score, and bucket it. A single high-severity report is enough to flag a block red rather than washing out in an average — because for an accessibility tool, a false "looks fine" is worse than an overcautious warning.

Seed data (30 reports across the 30 blocks) comes from a small Python script with a fixed random seed, so it's reproducible and regenerable. It's curated rather than purely random — it explicitly guarantees 6 green, 6 yellow, 6 red, and 12 gray blocks, verified by an assertion-based check that re-runs the exact scoring formula before anything gets written to disk. That way the grid visibly shows every status the moment it loads, instead of risking an all-one-color demo.

## Challenges we ran into

Getting the scoring buckets right was trickier than it sounds. A "yellow" block built from two severity-3 reports actually averages out to a score of 40 — which is "red," not "yellow." Picking severity combinations that reliably land in their intended bucket meant working the formula backwards: for each target status, what severity combos are mathematically guaranteed to produce a score in that range, no matter which barrier types or needs get randomly attached to them? That turned into explicit "safe combo" tables in the seed generator, checked by an assertion pass before any data gets written.

The other challenge was resisting the urge to add a real map. A live Google Maps embed looks more impressive in a screenshot, but it's also a network call, an API key, and a CORS policy standing between the judges and a working demo. Betting on a hand-drawn grid instead of a "real" map was a trade of visual polish for reliability — worth it, since a feature that's broken on stage scores zero no matter how good it looked in development.

## What's next

- Real geolocation: let a phone GPS position map to a real city block instead of a fictional grid.
- Photo attachments on reports, so "broken elevator" comes with a photo of the broken elevator.
- A trust/verification layer — upvotes or duplicate-detection so old or disputed reports don't keep dragging a block's score down forever.
- A real backend, so reports persist across devices instead of living in one browser's `localStorage`.
- Route planning: given a start and end block, suggest the path with the fewest high-barrier blocks.

## Built With

`html5` `css3` `javascript` `python` `localstorage` `web-speech-api`
