# AccessCity — Project Story & Interview Prep

This is the "why" document. The functional spec tells you *what* to build; this tells you how to *talk about* it once it's built — for the Devpost write-up, and for internship interviews afterward.

---

## 1. The 10-second pitch

"AccessCity is a crowdsourced accessibility map — people report physical barriers like missing curb cuts or stairs-only entrances, and the app turns those reports into a live, color-coded accessibility score for every block of a city, so people with mobility, vision, or hearing needs can see problem areas before they hit them."

Memorize this. Every interview answer below expands on it.

---

## 2. The problem it solves

Mobility disability (serious difficulty walking or climbing stairs) affects 12.2% of US adults — about 1 in 7. Across all disability types, 28.7% of US adults, roughly 61 million people, live with a disability (CDC). Cities aren't built with this in mind by default: curb cuts get skipped, elevators break and stay broken, crosswalk signals are visual-only. There's no easy way for a resident to know which blocks are actually accessible before they leave the house, and no easy way for a city to see where its real gaps are without sending out a survey crew.

That's the gap AccessCity targets: turn individual, scattered reports into a map anyone can read at a glance.

---

## 3. What it does (user-facing)

- Shows a city as a grid of blocks, each colored by an accessibility score computed from resident reports.
- Lets anyone report a specific barrier on a specific block: type of barrier, how severe, who it affects, and a description.
- Recalculates that block's score and color the moment a report comes in — the map updates live, not after a manual review.
- Lets users filter the whole map by barrier type or by affected need (wheelchair, stroller, blind/low-vision, deaf/hard-of-hearing, elderly, temporary injury) — so a wheelchair user can hide everything irrelevant to them and see only what matters.
- Includes accessibility features in the tool itself: a high-contrast mode, text-to-speech on block details, and full keyboard navigation — so the app practices what it's advocating for.

---

## 4. What it achieves

- **Makes an invisible problem visible.** Accessibility barriers are usually only discovered by the person who gets stuck on them. Aggregating reports turns isolated bad experiences into a pattern anyone — a resident, a city planner, an advocacy group — can see.
- **Lowers the bar to contribute.** Reporting takes under a minute: pick a block, pick a barrier type, rate severity, submit. No account, no app download.
- **Treats "no data" honestly.** A block with zero reports shows up gray/unrated, not green. The system never implies a place is accessible just because no one has complained yet — it only claims what it actually knows.

---

## 5. How it works (technical)

**Data layer.** Every report is a small record: which block, what type of barrier (from a fixed list — no curb cut, stairs-only entrance, broken elevator, no audible crosswalk signal, blocked sidewalk, uneven pavement, no accessible parking, other), a severity rating from 1–5, which needs it affects, and an optional description. Reports are seeded from a JSON file on load and held in memory plus `localStorage` for the session, so new reports survive a refresh without needing a server.

**Scoring.** Each block's score is derived from the average severity of its reports:

```
no reports        -> status = unrated (gray)
score = 100 - (average severity x 20)
score >= 80        -> accessible (green)
50 <= score < 80   -> some barriers (yellow)
score < 50         -> high barriers (red)
```

This is a deliberately simple, explainable formula — anyone can verify a block's color by checking its reports, which matters for trust in a civic tool. It's also why "no reports" is its own gray state instead of defaulting to green: defaulting unreported blocks to "accessible" would let the system claim something it has no evidence for.

**Rendering.** The city is a custom-drawn grid (HTML/CSS/JS), not a real map API. That was an explicit architecture choice — see §6.

**Accessibility features.** Text-to-speech uses the browser's built-in `SpeechSynthesis` API — no external service, no API key, works offline. High-contrast mode is a CSS class swap on the root element. Keyboard navigation relies on native focusable elements and visible focus outlines rather than custom key-handling, which is both simpler and more reliable across browsers.

---

## 6. Why these choices — the part interviewers actually care about

**Why an accessibility map and not a smarter traffic light / parking app?**
Almost every "smart city" hackathon submission clusters around the same handful of ideas — traffic, parking, recycling, EV charging. Those ideas also often imply hardware/IoT that can't really be demonstrated in a short virtual hackathon, so the demos end up unconvincing. Accessibility reporting is software-only, fully demonstrable, and underrepresented at this kind of event, which made it both more original and lower-risk to execute well.

**Why no real map API (Google Maps / Leaflet)?**
Two reasons, both about risk management under a tight deadline. First, external APIs introduce failure points outside my control — rate limits, key issues, network problems — exactly the kind of thing that breaks a live demo at the worst moment. Second, a custom grid gave full control over the visual style, which mattered for making the "city of tomorrow" theme land visually instead of looking like a generic map mockup. This is a scope decision, not a limitation — worth saying explicitly in an interview as an example of choosing reliability over polish-by-default.

**Why no backend or real database?**
Given a five-day solo build, every hour spent on server setup, auth, and deployment is an hour not spent on the actual feature set being judged. In-memory data plus `localStorage` is enough to run a convincing live demo and is something I fully control end to end. The trade-off — reports don't persist across different users or devices — is real, and I can name it directly as a "what I'd do next" item rather than pretend it doesn't exist.

**Why vanilla HTML/CSS/JS instead of React?**
No build step, no dependency install, no risk of a broken `npm install` right before judging. A judge can open one file and it works. For a project this size, a framework would add process overhead without adding capability.

**Why build accessibility features into the tool itself, not just write about accessibility?**
A tool about accessibility that isn't itself accessible would undercut its own premise. High-contrast mode, screen-reader-friendly text-to-speech, and full keyboard navigation are both genuinely useful and a direct, visible demonstration of UX thinking — better than describing good UX in a write-up.

---

## 7. Challenges and how I'd talk about them

- **Scoping under a hard deadline.** The honest challenge in any hackathon is saying no to ideas. The discipline here was cutting anything that depended on something I couldn't fully control (a live API, a backend, real geocoding) and keeping the feature set to what one person could finish *and* polish in five days.
- **Designing a scoring formula that's simple but not naive.** The interesting design problem wasn't "can you average some numbers" but "what should the system claim when it doesn't have enough information" — which led to the unrated/gray state instead of a default score.
- **Balancing breadth vs. depth.** It would have been easy to add more barrier types, more filters, more screens. I prioritized making the core loop (report -> recalculate -> see it change) feel immediate and correct over adding more surface area.

---

## 8. What I'd do next (the "what would you improve" answer)

- Real persistence (a lightweight backend/database) so reports aren't tied to one browser session.
- Real geolocation, so reports map to actual addresses instead of an abstract grid.
- Photo attachments on reports, since a picture of a broken curb cut is more convincing than a text description.
- A simple moderation or confidence mechanism (e.g., multiple independent reports needed before a block's status changes), to make the data harder to game.
- A public API so city governments or accessibility advocacy groups could pull the data into their own tools.

---

## 9. Skills this project demonstrates (for resume/interview framing)

- Problem scoping and requirements definition under a fixed deadline.
- Front-end development (DOM manipulation, event-driven UI updates, state management without a framework).
- Designing and justifying a small algorithm (the scoring formula) rather than just calling a library.
- Accessibility-aware UI design (contrast, screen-reader support via Web Speech API, keyboard navigation).
- Making and explaining architecture trade-offs (no backend, no external API) based on project constraints, not just defaults.
- Translating a real-world problem (backed by a real statistic) into a concrete technical solution.

---

## 10. Likely interview questions, with where to point

| Question | Answer lives in |
|---|---|
| "Walk me through a project you built." | §1, §3 |
| "What problem were you solving and why does it matter?" | §2 |
| "Why did you build it this way?" | §6 |
| "What was the hardest technical decision?" | §6 (scoring formula / no-backend) |
| "What would you do differently with more time?" | §8 |
| "What's a challenge you ran into and how did you handle it?" | §7 |
| "What did you learn?" | §6 + §9, in your own words |

Practice saying §6 out loud — it's the section that separates "I built an app" from "I made deliberate engineering decisions," which is what internship interviewers are actually listening for.
