/* AccessCity — app.js
 * Day 1-5 build. See README.md and the functional spec for context.
 *
 * Scoring is intentionally conservative: a single moderate-severity report
 * is enough to flag a block as "High Barriers" rather than "Some Barriers".
 * For a safety-relevant tool, a false "looks fine" is worse than an
 * over-cautious flag, so the formula leans pessimistic on purpose.
 * See computeScore() / statusForScore() below.
 */

// ---- Config -----------------------------------------------------------

const GRID_COLS = 6;
const GRID_ROWS = 5;

const NEIGHBORHOODS = [
  "Riverside",
  "Old Town",
  "Tech District",
  "Uptown",
  "Harbor Row",
  "Greenline",
];

const BARRIER_TYPES = [
  { value: "no-curb-cut", label: "No curb cut / curb ramp missing" },
  { value: "stairs-only-entrance", label: "Stairs-only entrance (no ramp/elevator)" },
  { value: "broken-elevator", label: "Broken or missing elevator" },
  { value: "no-audible-signal", label: "No audible/tactile crosswalk signal" },
  { value: "blocked-sidewalk", label: "Sidewalk blocked or obstructed" },
  { value: "uneven-pavement", label: "Uneven / cracked pavement" },
  { value: "no-accessible-parking", label: "No accessible parking nearby" },
  { value: "other", label: "Other" },
];

const AFFECTED_NEEDS = [
  { value: "wheelchair", label: "Wheelchair / mobility device" },
  { value: "stroller", label: "Stroller / pushing a cart" },
  { value: "blind-low-vision", label: "Blind / low vision" },
  { value: "deaf-hard-of-hearing", label: "Deaf / hard of hearing" },
  { value: "elderly", label: "Elderly / limited stamina" },
  { value: "temporary-injury", label: "Temporary injury" },
];

const STATUS_META = {
  accessible: { label: "Accessible", className: "status-accessible" },
  "some-barriers": { label: "Some Barriers", className: "status-some-barriers" },
  "high-barriers": { label: "High Barriers", className: "status-high-barriers" },
  unrated: { label: "Unrated", className: "status-unrated" },
};

const LOCAL_STORAGE_REPORTS_KEY = "accesscity_user_reports";
const LOCAL_STORAGE_CONTRAST_KEY = "accesscity_high_contrast";

// ---- State ----------------------------------------------------------------

const state = {
  blocks: [],
  reports: [],
  currentBlockId: null,
  filters: {
    barrierType: "all",
    needs: [],
  },
};

// ---- Setup ------------------------------------------------------------------

function buildBlocks() {
  const blocks = [];
  for (let col = 0; col < GRID_COLS; col++) {
    for (let row = 0; row < GRID_ROWS; row++) {
      blocks.push({ id: "B-" + col + "-" + row, col: col, row: row, neighborhood: NEIGHBORHOODS[col] });
    }
  }
  return blocks;
}

function loadUserReports() {
  try {
    var raw = localStorage.getItem(LOCAL_STORAGE_REPORTS_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch (err) {
    console.warn("Couldn't read saved reports, starting fresh.", err);
    return [];
  }
}

function saveUserReports(userReports) {
  localStorage.setItem(LOCAL_STORAGE_REPORTS_KEY, JSON.stringify(userReports));
}

function init() {
  state.blocks = buildBlocks();

  var seed = (typeof SEED_REPORTS !== "undefined") ? SEED_REPORTS : [];
  var userReports = loadUserReports();
  state.reports = seed.concat(userReports);

  populateBlockSelect();
  populateBarrierTypeSelect();
  populateNeedsCheckboxes("needsCheckboxes");
  populateFilterBarrierTypeSelect();
  populateNeedsCheckboxes("filterNeedsCheckboxes", { isFilter: true });
  restoreContrastPreference();
  disableSpeechIfUnsupported();
  renderGrid();
  attachEventListeners();
}

// ---- Data helpers -----------------------------------------------------------

function getReportsForBlock(blockId) {
  return state.reports.filter(function (r) { return r.blockId === blockId; });
}

function reportMatchesFilters(report) {
  var typeMatch = state.filters.barrierType === "all" || report.barrierType === state.filters.barrierType;
  var needsMatch =
    state.filters.needs.length === 0 ||
    report.affectedNeeds.some(function (n) { return state.filters.needs.indexOf(n) !== -1; });
  return typeMatch && needsMatch;
}

function getFilteredReportsForBlock(blockId) {
  return getReportsForBlock(blockId).filter(reportMatchesFilters);
}

function computeScore(reports) {
  if (reports.length === 0) return null;
  var total = 0;
  for (var i = 0; i < reports.length; i++) total += reports[i].severity;
  var avgSeverity = total / reports.length;
  return Math.round(100 - avgSeverity * 20);
}

function statusForScore(score) {
  if (score === null) return "unrated";
  if (score >= 80) return "accessible";
  if (score >= 50) return "some-barriers";
  return "high-barriers";
}

function findBlock(blockId) {
  return state.blocks.find(function (b) { return b.id === blockId; });
}

function barrierLabel(value) {
  var found = BARRIER_TYPES.find(function (b) { return b.value === value; });
  return found ? found.label : value;
}

function needLabel(value) {
  var found = AFFECTED_NEEDS.find(function (n) { return n.value === value; });
  return found ? found.label : value;
}

function escapeHtml(str) {
  var div = document.createElement("div");
  div.textContent = str;
  return div.innerHTML;
}

function formatDate(iso) {
  var d = new Date(iso);
  if (isNaN(d.getTime())) return "";
  return d.toLocaleDateString(undefined, { month: "short", day: "numeric", year: "numeric" });
}

function generateId() {
  return "r-" + Date.now() + "-" + Math.floor(Math.random() * 10000);
}

function announce(message) {
  var region = document.getElementById("liveRegion");
  if (region) region.textContent = message;
}

// ---- Rendering: grid + stats bar -------------------------------------------

function renderGrid() {
  var grid = document.getElementById("grid");
  grid.innerHTML = "";
  grid.style.gridTemplateColumns = "repeat(" + GRID_COLS + ", 1fr)";

  var counts = { accessible: 0, "some-barriers": 0, "high-barriers": 0, unrated: 0 };

  state.blocks.forEach(function (block) {
    var filtered = getFilteredReportsForBlock(block.id);
    var score = computeScore(filtered);
    var status = statusForScore(score);
    var meta = STATUS_META[status];
    counts[status]++;

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "block " + meta.className;
    btn.dataset.blockId = block.id;

    var ariaLabel = block.neighborhood + ", block " + block.col + "-" + block.row + ", " + meta.label;
    if (score !== null) ariaLabel += ", score " + score;
    btn.setAttribute("aria-label", ariaLabel);

    var countText = filtered.length + " report" + (filtered.length === 1 ? "" : "s");

    var elNeighborhood = document.createElement("span");
    elNeighborhood.className = "block-neighborhood";
    elNeighborhood.textContent = block.neighborhood;

    var elStatus = document.createElement("span");
    elStatus.className = "block-status";
    elStatus.textContent = meta.label;

    var elCount = document.createElement("span");
    elCount.className = "block-count";
    elCount.textContent = countText;

    btn.appendChild(elNeighborhood);
    btn.appendChild(elStatus);
    btn.appendChild(elCount);

    btn.addEventListener("click", function () { openBlockModal(block.id); });
    grid.appendChild(btn);
  });

  renderStatsBar(counts);
}

function renderStatsBar(counts) {
  var bar = document.getElementById("statsBar");
  if (!bar) return;
  bar.innerHTML = "";

  Object.keys(STATUS_META).forEach(function (key) {
    var meta = STATUS_META[key];
    var chip = document.createElement("span");
    chip.className = "stat-chip " + meta.className;

    var swatch = document.createElement("span");
    swatch.className = "stat-swatch";
    swatch.setAttribute("aria-hidden", "true");

    chip.appendChild(swatch);
    chip.appendChild(document.createTextNode(meta.label + ": " + counts[key]));
    bar.appendChild(chip);
  });
}

function refreshBlock(blockId) {
  renderGrid();
  var btn = document.querySelector('.block[data-block-id="' + blockId + '"]');
  if (btn) {
    btn.classList.add("just-updated");
    setTimeout(function () { btn.classList.remove("just-updated"); }, 1200);
  }
}

// ---- Rendering: block detail modal -----------------------------------------

function openBlockModal(blockId) {
  state.currentBlockId = blockId;
  var block = findBlock(blockId);
  var allReports = getReportsForBlock(blockId);
  var filteredReports = getFilteredReportsForBlock(blockId);
  var filteredIds = {};
  filteredReports.forEach(function (r) { filteredIds[r.id] = true; });
  var filteredScore = computeScore(filteredReports);
  var filteredStatus = statusForScore(filteredScore);
  var statusMeta = STATUS_META[filteredStatus];

  document.getElementById("blockTitle").textContent =
    block.neighborhood + " — Block " + block.col + "-" + block.row;

  var metaEl = document.getElementById("blockMeta");
  metaEl.innerHTML = "";

  var pill = document.createElement("span");
  pill.className = "status-pill " + statusMeta.className;
  pill.textContent = statusMeta.label;
  metaEl.appendChild(pill);

  var scoreText = filteredScore !== null ? " score " + filteredScore + "/100. " : " ";
  var totalText = allReports.length
    ? allReports.length + " report" + (allReports.length === 1 ? "" : "s") + " total on file."
    : "No reports on this block yet. Be the first.";

  metaEl.appendChild(document.createTextNode(scoreText + "under the current filter. " + totalText));

  var list = document.getElementById("reportList");
  list.innerHTML = "";

  var sorted = allReports.slice().sort(function (a, b) {
    return new Date(b.reportedAt) - new Date(a.reportedAt);
  });

  sorted.forEach(function (r) {
    var li = document.createElement("li");
    li.className = "report-item" + (filteredIds[r.id] ? " matches-filter" : "");

    var html = "";
    if (filteredIds[r.id]) {
      html += '<span class="filter-tag">matches filter</span>';
    }
    html += "<strong>" + escapeHtml(barrierLabel(r.barrierType)) + "</strong> - severity " + r.severity + "/5<br>";
    html += "Affects: " + escapeHtml(r.affectedNeeds.map(needLabel).join(", ") || "Not specified") + "<br>";
    if (r.description) {
      html += "<em>" + escapeHtml(r.description) + "</em><br>";
    }
    html += '<span class="report-date">' + formatDate(r.reportedAt) + "</span>";

    li.innerHTML = html;
    list.appendChild(li);
  });

  showModal("blockModal");
}

function readBlockAloud() {
  if (!("speechSynthesis" in window)) return;
  var block = findBlock(state.currentBlockId);
  if (!block) return;

  var filtered = getFilteredReportsForBlock(block.id);
  var score = computeScore(filtered);
  var status = STATUS_META[statusForScore(score)].label;
  var all = getReportsForBlock(block.id);

  var text = block.neighborhood + ", block " + block.col + " dash " + block.row + ". Status: " + status + ".";
  if (score !== null) text += " Score " + score + " out of 100.";
  if (all.length === 0) {
    text += " No reports on file yet.";
  } else {
    text += " " + all.length + " report" + (all.length === 1 ? "" : "s") + " on file.";
    all.forEach(function (r, i) {
      text += " Report " + (i + 1) + ": " + barrierLabel(r.barrierType) + ", severity " + r.severity + " out of 5.";
    });
  }

  window.speechSynthesis.cancel();
  window.speechSynthesis.speak(new SpeechSynthesisUtterance(text));
}

function disableSpeechIfUnsupported() {
  if ("speechSynthesis" in window) return;
  var btn = document.getElementById("readAloudBtn");
  if (btn) {
    btn.disabled = true;
    btn.title = "Text-to-speech isn't supported in this browser.";
  }
}

// ---- Rendering: report form + filter form inputs ---------------------------

function populateBlockSelect() {
  var select = document.getElementById("blockSelect");
  select.innerHTML = "";
  state.blocks.forEach(function (block) {
    var opt = document.createElement("option");
    opt.value = block.id;
    opt.textContent = block.neighborhood + " — Block " + block.col + "-" + block.row;
    select.appendChild(opt);
  });
}

function populateBarrierTypeSelect() {
  var select = document.getElementById("barrierType");
  select.innerHTML = "";
  BARRIER_TYPES.forEach(function (b) {
    var opt = document.createElement("option");
    opt.value = b.value;
    opt.textContent = b.label;
    select.appendChild(opt);
  });
}

function populateFilterBarrierTypeSelect() {
  var select = document.getElementById("filterBarrierType");
  select.innerHTML = "";
  var allOpt = document.createElement("option");
  allOpt.value = "all";
  allOpt.textContent = "All barrier types";
  select.appendChild(allOpt);
  BARRIER_TYPES.forEach(function (b) {
    var opt = document.createElement("option");
    opt.value = b.value;
    opt.textContent = b.label;
    select.appendChild(opt);
  });
}

function populateNeedsCheckboxes(containerId, opts) {
  opts = opts || {};
  var container = document.getElementById(containerId);
  if (!container) return;
  container.innerHTML = "";
  AFFECTED_NEEDS.forEach(function (n) {
    var prefix = opts.isFilter ? "filter-need" : "need";
    var id = prefix + "-" + n.value;

    var wrapper = document.createElement("label");
    wrapper.className = "checkbox-label";
    wrapper.setAttribute("for", id);

    var input = document.createElement("input");
    input.type = "checkbox";
    input.id = id;
    input.value = n.value;

    wrapper.appendChild(input);
    wrapper.appendChild(document.createTextNode(" " + n.label));
    container.appendChild(wrapper);
  });
}

function openReportModal(presetBlockId) {
  if (presetBlockId) {
    document.getElementById("blockSelect").value = presetBlockId;
  }
  showModal("reportModal");
}

// ---- Modal helpers -----------------------------------------------------------

function showModal(id) {
  document.getElementById(id).classList.remove("hidden");
}

function hideModal(id) {
  document.getElementById(id).classList.add("hidden");
}

// ---- Filters -------------------------------------------------------------------

function readFiltersFromUI() {
  state.filters.barrierType = document.getElementById("filterBarrierType").value;
  var checked = document.querySelectorAll("#filterNeedsCheckboxes input:checked");
  state.filters.needs = Array.prototype.map.call(checked, function (cb) { return cb.value; });
}

function clearFilters() {
  document.getElementById("filterBarrierType").value = "all";
  var checked = document.querySelectorAll("#filterNeedsCheckboxes input:checked");
  Array.prototype.forEach.call(checked, function (cb) { cb.checked = false; });
  state.filters = { barrierType: "all", needs: [] };
  renderGrid();
  announce("Filters cleared. Showing all reports.");
}

function applyFiltersAndRerender() {
  readFiltersFromUI();
  renderGrid();
  var typeLabel = state.filters.barrierType === "all" ? "all barrier types" : barrierLabel(state.filters.barrierType);
  var needsLabel = state.filters.needs.length === 0 ? "all needs" : state.filters.needs.map(needLabel).join(", ");
  announce("Showing scores for " + typeLabel + ", " + needsLabel + ".");
}

// ---- Accessibility: high contrast -----------------------------------------------

function restoreContrastPreference() {
  var saved = localStorage.getItem(LOCAL_STORAGE_CONTRAST_KEY);
  if (saved === "true") {
    document.body.classList.add("high-contrast");
    var btn = document.getElementById("contrastToggle");
    if (btn) btn.setAttribute("aria-pressed", "true");
  }
}

function toggleContrast() {
  var isOn = document.body.classList.toggle("high-contrast");
  localStorage.setItem(LOCAL_STORAGE_CONTRAST_KEY, String(isOn));
  document.getElementById("contrastToggle").setAttribute("aria-pressed", String(isOn));
  announce(isOn ? "High contrast mode on." : "High contrast mode off.");
}

// ---- Form submit -----------------------------------------------------------------

function handleReportSubmit(e) {
  e.preventDefault();

  var blockId = document.getElementById("blockSelect").value;
  var barrierType = document.getElementById("barrierType").value;
  var severity = Number(document.getElementById("severity").value);
  var description = document.getElementById("description").value.trim();
  var checked = document.querySelectorAll("#needsCheckboxes input:checked");
  var affectedNeeds = Array.prototype.map.call(checked, function (cb) { return cb.value; });

  var report = {
    id: generateId(),
    blockId: blockId,
    barrierType: barrierType,
    severity: severity,
    affectedNeeds: affectedNeeds,
    description: description,
    reportedAt: new Date().toISOString(),
  };

  state.reports.push(report);

  var userReports = loadUserReports();
  userReports.push(report);
  saveUserReports(userReports);

  refreshBlock(blockId);
  if (state.currentBlockId === blockId) {
    openBlockModal(blockId);
  }

  var block = findBlock(blockId);
  var newStatus = STATUS_META[statusForScore(computeScore(getFilteredReportsForBlock(blockId)))].label;
  announce("New report submitted. " + block.neighborhood + " block " + block.col + "-" + block.row + " is now rated " + newStatus + ".");

  e.target.reset();
  document.getElementById("severityOutput").textContent = "3";
  hideModal("reportModal");
}

// ---- Event wiring -----------------------------------------------------------------

function attachEventListeners() {
  document.getElementById("reportBtn").addEventListener("click", function () { openReportModal(); });

  document.getElementById("addReportHereBtn").addEventListener("click", function () {
    hideModal("blockModal");
    openReportModal(state.currentBlockId);
  });

  document.getElementById("readAloudBtn").addEventListener("click", readBlockAloud);

  var closeButtons = document.querySelectorAll("[data-close]");
  Array.prototype.forEach.call(closeButtons, function (btn) {
    btn.addEventListener("click", function (e) {
      var modal = e.target.closest(".modal");
      hideModal(modal.id);
    });
  });

  document.getElementById("severity").addEventListener("input", function (e) {
    document.getElementById("severityOutput").textContent = e.target.value;
  });

  document.getElementById("reportForm").addEventListener("submit", handleReportSubmit);

  document.getElementById("filterBarrierType").addEventListener("change", applyFiltersAndRerender);
  var filterNeedInputs = document.querySelectorAll("#filterNeedsCheckboxes input");
  Array.prototype.forEach.call(filterNeedInputs, function (cb) {
    cb.addEventListener("change", applyFiltersAndRerender);
  });
  document.getElementById("clearFiltersBtn").addEventListener("click", clearFilters);

  document.getElementById("contrastToggle").addEventListener("click", toggleContrast);

  var modals = document.querySelectorAll(".modal");
  Array.prototype.forEach.call(modals, function (modal) {
    modal.addEventListener("click", function (e) {
      if (e.target === modal) hideModal(modal.id);
    });
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      var openModals = document.querySelectorAll(".modal:not(.hidden)");
      Array.prototype.forEach.call(openModals, function (m) { hideModal(m.id); });
    }
  });
}

document.addEventListener("DOMContentLoaded", init);
