/* AccessCity -- automated DOM test pass (Day 4/5).
 *
 * Uses jsdom directly with plain Node `assert`, no test framework. The app
 * has no build step, so the tests load the real index.html/app.js/seed.js
 * straight off disk the same way a browser would -- this is the closest
 * thing to an end-to-end check without a real browser.
 *
 * Run: npm install && npm test   (from the accesscity/ directory)
 */

const fs = require("fs");
const path = require("path");
const assert = require("assert");
const { JSDOM } = require("jsdom");

const ROOT = __dirname + "/..";

function loadApp() {
  const html = fs.readFileSync(path.join(ROOT, "index.html"), "utf8");
  const dom = new JSDOM(html, {
    runScripts: "dangerously",
    // jsdom only implements localStorage for non-opaque origins, and
    // file:// URLs are treated as opaque -- use a fake https URL instead
    // so app.js's localStorage calls behave the same as in a real browser.
    // (resources: "usable" is intentionally omitted -- we inject the app's
    // scripts manually below instead of letting jsdom fetch the <script
    // src> / <link> tags over the network, which would just fail against
    // this fake hostname.)
    url: "https://accesscity.test/",
  });

  // jsdom doesn't implement <script src="..."> relative-file loading the
  // way a real browser does in this mode, so we read and run the two
  // scripts manually, in the same order index.html loads them. They're
  // eval'd as ONE combined call (not two separate calls) because each
  // top-level const/let in a script only stays alive for the duration of
  // its own eval -- a separate eval() for seed.js would let SEED_REPORTS
  // vanish before app.js's eval() ever ran, silently leaving every block
  // unrated. Concatenating them mirrors how two <script> tags in a real
  // page share one global scope.
  const seedSrc = fs.readFileSync(path.join(ROOT, "data", "seed.js"), "utf8");
  const appSrc = fs.readFileSync(path.join(ROOT, "app.js"), "utf8");

  dom.window.eval(seedSrc + "\n" + appSrc);

  // app.js's own DOMContentLoaded listener fires init(); jsdom dispatches
  // DOMContentLoaded automatically once parsing completes, but since we
  // injected the script via eval() after that point, call init() directly
  // to be explicit and avoid relying on event-timing.
  dom.window.init();

  return dom;
}

function test(name, fn) {
  try {
    fn();
    console.log("  PASS - " + name);
    return true;
  } catch (err) {
    console.log("  FAIL - " + name);
    console.log("         " + err.message);
    return false;
  }
}

let passed = 0;
let failed = 0;

function run(name, fn) {
  if (test(name, fn)) passed++; else failed++;
}

console.log("AccessCity DOM tests\n");

// ---- Test 1: grid renders all 30 blocks ------------------------------------
(function () {
  const dom = loadApp();
  run("renders 30 blocks", function () {
    const blocks = dom.window.document.querySelectorAll(".block");
    assert.strictEqual(blocks.length, 30, "expected 30 .block elements, got " + blocks.length);
  });
})();

// ---- Test 2: status distribution matches the curated 6/6/6/12 split -------
(function () {
  const dom = loadApp();
  run("status distribution is 6 accessible / 6 some-barriers / 6 high-barriers / 12 unrated", function () {
    const doc = dom.window.document;
    const counts = {
      accessible: doc.querySelectorAll(".block.status-accessible").length,
      someBarriers: doc.querySelectorAll(".block.status-some-barriers").length,
      highBarriers: doc.querySelectorAll(".block.status-high-barriers").length,
      unrated: doc.querySelectorAll(".block.status-unrated").length,
    };
    assert.strictEqual(counts.accessible, 6, "accessible count: " + counts.accessible);
    assert.strictEqual(counts.someBarriers, 6, "some-barriers count: " + counts.someBarriers);
    assert.strictEqual(counts.highBarriers, 6, "high-barriers count: " + counts.highBarriers);
    assert.strictEqual(counts.unrated, 12, "unrated count: " + counts.unrated);
  });
})();

// ---- Test 3: clicking a block opens the modal with the right neighborhood -
(function () {
  const dom = loadApp();
  run("clicking a block opens the modal showing its neighborhood", function () {
    const doc = dom.window.document;
    const firstBlock = doc.querySelector('.block[data-block-id="B-0-0"]');
    assert.ok(firstBlock, "B-0-0 should exist in the grid");

    firstBlock.click();

    const modal = doc.getElementById("blockModal");
    assert.ok(!modal.classList.contains("hidden"), "blockModal should be visible after click");

    const title = doc.getElementById("blockTitle").textContent;
    assert.ok(title.indexOf("Riverside") !== -1, "B-0-0 is in Riverside (col 0), got title: " + title);
  });
})();

// ---- Test 4: submitting a report flips a gray block's status --------------
(function () {
  const dom = loadApp();
  run("submitting a severity-5 report turns an unrated block high-barriers", function () {
    const doc = dom.window.document;

    // Query dynamically rather than hardcoding a block ID -- which specific
    // blocks land in "unrated" depends on the curated seed's random shuffle,
    // so picking the first one at test time keeps this robust against seed
    // regeneration.
    const grayBlock = doc.querySelector(".block.status-unrated");
    assert.ok(grayBlock, "expected at least one unrated block to exist");
    const blockId = grayBlock.dataset.blockId;

    doc.getElementById("blockSelect").value = blockId;
    doc.getElementById("barrierType").value = "broken-elevator";
    doc.getElementById("severity").value = "5";

    const submitEvent = new dom.window.Event("submit", { bubbles: true, cancelable: true });
    doc.getElementById("reportForm").dispatchEvent(submitEvent);

    const updatedBlock = doc.querySelector('.block[data-block-id="' + blockId + '"]');
    assert.ok(
      updatedBlock.classList.contains("status-high-barriers"),
      "block " + blockId + " should be high-barriers after a single severity-5 report, classes: " + updatedBlock.className
    );
  });
})();

// ---- Test 5: filtering by barrier type changes rendered report counts -----
(function () {
  const dom = loadApp();
  run("filtering by barrier type updates block report counts", function () {
    const doc = dom.window.document;

    const totalCountsBefore = Array.prototype.map.call(doc.querySelectorAll(".block-count"), function (el) { return el.textContent; });

    doc.getElementById("filterBarrierType").value = "broken-elevator";
    const changeEvent = new dom.window.Event("change", { bubbles: true });
    doc.getElementById("filterBarrierType").dispatchEvent(changeEvent);

    const totalCountsAfter = Array.prototype.map.call(doc.querySelectorAll(".block-count"), function (el) { return el.textContent; });

    assert.notDeepStrictEqual(
      totalCountsBefore,
      totalCountsAfter,
      "expected report counts to change after filtering to a single barrier type"
    );
  });
})();

// ---- Test 6: high-contrast toggle adds the class and persists -------------
(function () {
  const dom = loadApp();
  run("toggling high contrast adds .high-contrast to body and sets aria-pressed", function () {
    const doc = dom.window.document;
    const toggle = doc.getElementById("contrastToggle");

    assert.ok(!doc.body.classList.contains("high-contrast"), "should start without high-contrast");

    toggle.click();

    assert.ok(doc.body.classList.contains("high-contrast"), "body should have .high-contrast after toggle");
    assert.strictEqual(toggle.getAttribute("aria-pressed"), "true", "aria-pressed should be true after toggle");
  });
})();

// ---- Test 7: clearing filters resets the grid to the unfiltered view ------
(function () {
  const dom = loadApp();
  run("clear filters resets barrier type filter and re-renders", function () {
    const doc = dom.window.document;

    doc.getElementById("filterBarrierType").value = "broken-elevator";
    doc.getElementById("filterBarrierType").dispatchEvent(new dom.window.Event("change", { bubbles: true }));

    doc.getElementById("clearFiltersBtn").click();

    assert.strictEqual(doc.getElementById("filterBarrierType").value, "all", "filter select should reset to 'all'");

    const counts = {
      accessible: doc.querySelectorAll(".block.status-accessible").length,
      unrated: doc.querySelectorAll(".block.status-unrated").length,
    };
    assert.strictEqual(counts.accessible, 6, "distribution should return to the curated baseline after clearing filters");
    assert.strictEqual(counts.unrated, 12, "distribution should return to the curated baseline after clearing filters");
  });
})();

console.log("\n" + passed + " passed, " + failed + " failed");
process.exit(failed > 0 ? 1 : 0);
