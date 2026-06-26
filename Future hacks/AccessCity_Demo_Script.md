# AccessCity — Demo Script & Submission Checklist (Day 5)

Deadline: **Sun Jun 28, 8:00 PM ET, Intermediate track.** Today is Thu Jun 25 — 3 days out. Record and submit early; don't wait for the last hour.

Recording, deploying, and clicking "Submit" on Devpost are all things only you can do (they require your screen, your GitHub/Devpost accounts). Everything below is written so you can execute it without needing anything else built.

---

## 1. Before you hit record

Run through this once, in order, so the recording is clean on the first take:

1. **Regenerate fresh seed data**: `python3 seed_generator.py` — guarantees the curated 6/6/6/12 split instead of whatever state you left it in while testing.
2. **Clear stray test reports**: open the app in an Incognito/Private window (or clear `localStorage` for the page) so none of your own test submissions show up on top of the seed data.
3. **Re-run the test suite once more**: `npm test` from `accesscity/` — confirm 7/7 still pass before you film. A green test suite the morning of recording is a good sign nothing's broken.
4. **Deploy first, demo from the live URL** — not `localhost` or a local file. Judges will click your Devpost link; if you only ever tested locally, deploy-only bugs (wrong file paths, case-sensitivity on Linux servers, etc.) will surprise you on stage instead of beforehand. GitHub Pages: push the `accesscity/` contents to a repo, then Settings → Pages → deploy from `main` branch, root folder. You'll get a URL like `https://yourusername.github.io/accesscity/`.
5. **Browser setup**: maximize the window, zoom to 100-110% so text is readable in a recording, close devtools/notifications/other tabs.
6. **Pick a recorder**: Mac — Cmd+Shift+5 (or QuickTime). Windows — Win+G (Xbox Game Bar) or ScreenPad. Decide now whether you're narrating live or recording silently and voicing over after — silent-then-voiceover is more forgiving if you flub a line.

---

## 2. Demo script (target: 2:15–2:30 total)

Devpost wants 2-3 minutes. Shorter and tight beats longer and padded — judges watch dozens of these. Each beat below lists what to click and roughly what to say; the wording is a starting point, not a script to memorize word-for-word.

| Time | What's on screen | What you say |
|---|---|---|
| 0:00–0:15 | App loaded, full grid visible | "The theme this year is 'build the city of tomorrow.' About 1 in 7 US adults has a mobility disability — so I built AccessCity: a crowdsourced accessibility map that scores every block of a city based on barriers people actually report." |
| 0:15–0:35 | Point at stats bar / color legend, slowly pan grid | "Every block is colored live from real reports — green is accessible, yellow has some barriers, red has high barriers, gray means nobody's reported on it yet. No map API, no backend — this is reading real crowdsourced data and scoring it on the fly." |
| 0:35–1:00 | Click a red or yellow block, modal opens | "Clicking a block shows its full report history — what was reported, how severe, who it affects. This one's flagged because of [whatever the report says]." Click **Read aloud** briefly to show it. |
| 1:00–1:40 | Close modal, click "Report a Barrier," fill the form on a gray block, submit | "Now watch what happens when I report something new." Pick a gray block, set barrier type, drag severity to 4-5, submit. "That block just went from unrated to high-barriers, live — no refresh, no waiting." (This live color-flip is your best "wow" beat — don't rush it.) |
| 1:40–2:05 | Open filter panel, pick a barrier type or need | "You can also filter the whole map — show me only blocks with broken elevators, or only barriers that affect wheelchair users. The grid recolors to just that slice." |
| 2:05–2:20 | Toggle high contrast | "And the app practices the accessibility it's mapping — high-contrast mode, full keyboard navigation, screen-reader announcements on every update." |
| 2:20–2:30 | Cut back to grid or a title card | "Vanilla HTML, CSS, and JavaScript, no framework, no backend — built so the demo can't fail on bad wifi. That's AccessCity." |

Notes:
- Say the CDC stat once, near the top — it's your real-world-impact line, don't bury it.
- The live color-flip (1:00-1:40) is the single most important 30 seconds. If you're short on time, cut the filter section before you cut this.
- If narrating live feels stressful, record the clicking silently first, then add voiceover in a second pass — much easier to get clean audio that way.

---

## 3. Screenshots to grab (for the Devpost gallery)

Grab these from the deployed (not local) version, after step 1's reset:

- Full grid view showing all four status colors at once
- A block modal open, showing a report list with the "matches filter" tag visible
- The report form mid-fill (severity slider somewhere in the middle, a need checked)
- High-contrast mode toggled on

---

## 4. Final Devpost submission checklist

- [ ] Project name + one-line pitch ("A crowdsourced accessibility map for the city of tomorrow.")
- [ ] Live demo link — the deployed GitHub Pages URL, tested in an incognito window right before submitting
- [ ] "Built With" tags: `html5` `css3` `javascript` `python` `localstorage` `web-speech-api`
- [ ] Write-up pasted in from `AccessCity_Devpost_Writeup.md` (Inspiration / What it does / How we built it / Challenges / What's next)
- [ ] 4 screenshots from section 3 above
- [ ] Demo video (2-3 min) uploaded to YouTube as unlisted, link pasted into Devpost
- [ ] Submitted to the **Intermediate** track
- [ ] Submitted before **Sun Jun 28, 8:00 PM ET** — submit a few hours early in case Devpost's upload is slow near the deadline
- [ ] Double-check the live link still works after submitting (judges will click it, not your local copy)
