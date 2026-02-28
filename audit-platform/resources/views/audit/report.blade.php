@extends('layouts.app')
@section('title', 'Raport Audit — ' . parse_url($audit->url, PHP_URL_HOST))

@push('styles')
<style>
/* HEADER */
.rh { border-bottom: 1px solid var(--rule); padding: 44px 24px 36px; background: var(--paper); position: relative; overflow: hidden; }
.rh::before { content: ''; position: absolute; top: 0; right: 0; width: 500px; height: 260px; background: radial-gradient(ellipse at 100% 0%, rgba(45,145,206,.06) 0%, transparent 60%); pointer-events: none; }
.rh-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr auto; gap: 40px; align-items: start; position: relative; z-index: 1; }
.rh-bread { font-size: 12px; color: var(--ink-5); display: flex; align-items: center; gap: 6px; margin-bottom: 12px; }
.rh-bread a { color: var(--ink-5); text-decoration: none; } .rh-bread a:hover { color: var(--ink-3); }
.rh-url { font-size: 26px; font-weight: 800; letter-spacing: -1.5px; color: var(--ink); margin-bottom: 14px; line-height: 1; }
.rh-tags { display: flex; gap: 7px; flex-wrap: wrap; margin-bottom: 18px; }
.rh-meta { font-size: 12px; color: var(--ink-5); display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.rh-meta-dot { width: 3px; height: 3px; border-radius: 50%; background: var(--ink-6); }

/* Score widget */
.score-w { text-align: center; flex-shrink: 0; }
.score-lbl { font-size: 11px; color: var(--ink-5); font-weight: 600; letter-spacing: .3px; text-transform: uppercase; margin-bottom: 8px; }
.score-ring { position: relative; width: 110px; height: 110px; margin: 0 auto 10px; }
.score-ring svg { width: 110px; height: 110px; transform: rotate(-90deg); }
.score-track { fill: none; stroke: var(--rule); stroke-width: 6; }
.score-fill  { fill: none; stroke-width: 6; stroke-linecap: round; stroke-dasharray: 314; transition: stroke-dashoffset 1.2s var(--ease); }
.score-num { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.score-n { font-size: 30px; font-weight: 800; letter-spacing: -2px; line-height: 1; }
.score-d { font-size: 11px; color: var(--ink-5); }
.btn-pdf { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 7px 14px; border: 1px solid var(--rule); border-radius: 7px; background: var(--paper); color: var(--ink-3); text-decoration: none; cursor: pointer; transition: all .15s; }
.btn-pdf svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.btn-pdf:hover { border-color: var(--blue); color: var(--blue); background: var(--blue-bg); }

/* BODY */
.rb { max-width: 1200px; margin: 0 auto; padding: 44px 24px 80px; }

/* CAT SCORES */
.cat-row { display: grid; grid-template-columns: repeat(6,1fr); border: 1px solid var(--rule); border-radius: 12px; overflow: hidden; margin-bottom: 48px; }
.cs { padding: 20px 16px; border-right: 1px solid var(--rule); transition: background .15s; }
.cs:last-child { border-right: none; }
.cs:hover { background: var(--paper-2); }
.cs-lbl { font-size: 11px; font-weight: 500; color: var(--ink-5); margin-bottom: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cs-n { font-size: 24px; font-weight: 800; letter-spacing: -1.5px; line-height: 1; margin-bottom: 8px; }
.cs-bar { height: 3px; background: var(--paper-3); border-radius: 2px; overflow: hidden; }
.cs-fill { height: 100%; border-radius: 2px; transition: width 1s var(--ease); }

/* AI SUMMARY SECTION */
.ai-summary {
    background: var(--paper-2);
    border: 1px solid var(--rule);
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 48px;
}
.ai-summary-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
.ai-summary-icon { width: 32px; height: 32px; background: var(--blue-bg); border: 1px solid var(--blue-bd); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ai-summary-icon svg { width: 14px; height: 14px; stroke: var(--blue); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.ai-summary-title { font-size: 15px; font-weight: 700; letter-spacing: -.3px; color: var(--ink); }
.ai-summary-sub { font-size: 12px; color: var(--ink-5); margin-top: 2px; }
.ai-summary-body { font-size: 14px; color: var(--ink-3); line-height: 1.8; }
.ai-summary-body.loading { display: flex; align-items: center; gap: 10px; color: var(--ink-5); font-size: 13px; }
.ai-loading-spin { width: 16px; height: 16px; border: 2px solid var(--rule); border-top-color: var(--blue); border-radius: 50%; animation: spin-ai .8s linear infinite; flex-shrink: 0; }
@keyframes spin-ai { to { transform: rotate(360deg); } }

/* ISSUES */
.issue-group { margin-bottom: 44px; }
.ig-head { display: flex; align-items: center; gap: 10px; padding-bottom: 12px; border-bottom: 1px solid var(--rule); margin-bottom: 10px; }
.ig-ico { width: 26px; height: 26px; background: var(--paper-3); border: 1px solid var(--rule); border-radius: 6px; display: flex; align-items: center; justify-content: center; }
.ig-ico svg { width: 12px; height: 12px; stroke: var(--ink-4); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.ig-name { font-size: 13px; font-weight: 700; letter-spacing: -.2px; color: var(--ink); }
.ig-cnt { font-size: 11px; color: var(--ink-5); font-weight: 500; }
.ig-score { margin-left: auto; font-size: 13px; font-weight: 800; letter-spacing: -.5px; }
.issues { display: flex; flex-direction: column; gap: 5px; }
.issue { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border: 1px solid var(--rule); border-radius: 8px; background: var(--paper); transition: all .15s; position: relative; }
.issue.sev-critical { border-left: 3px solid var(--red); }
.issue.sev-warning  { border-left: 3px solid var(--amber); }
.issue.sev-info     { border-left: 3px solid var(--green); }
.issue:hover { border-color: var(--rule-2); box-shadow: 0 2px 8px rgba(0,0,0,.04); }
.issue-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; }
.d-critical { background: var(--red); box-shadow: 0 0 5px rgba(239,68,68,.4); }
.d-warning  { background: var(--amber); }
.d-info     { background: var(--green); }
.issue-body { flex: 1; min-width: 0; }
.issue-title { font-size: 13px; font-weight: 700; letter-spacing: -.2px; color: var(--ink); margin-bottom: 4px; }
.issue-desc  { font-size: 12px; color: var(--ink-5); line-height: 1.65; margin-bottom: 5px; }
.issue-fix { font-size: 12px; color: var(--blue); display: flex; gap: 5px; line-height: 1.55; }
.issue-fix-arr { flex-shrink: 0; }
.issue-url { font-size: 10px; color: var(--ink-5); background: var(--paper-3); border: 1px solid var(--rule); padding: 2px 7px; border-radius: 4px; font-family: var(--mono); max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; align-self: flex-start; flex-shrink: 0; }

/* CTA */
.report-cta { background: var(--ink); border-radius: 20px; padding: 56px 48px; text-align: center; position: relative; overflow: hidden; margin-top: 48px; }
.report-cta::before { content: ''; position: absolute; top: -150px; left: 50%; transform: translateX(-50%); width: 500px; height: 350px; background: radial-gradient(ellipse at 50% 0%, rgba(45,145,206,.18) 0%, transparent 65%); pointer-events: none; }
.report-cta-inner { position: relative; z-index: 1; }
.report-cta h3 { font-size: 28px; font-weight: 800; letter-spacing: -1.5px; color: white; margin-bottom: 10px; }
.report-cta p  { font-size: 13px; color: rgba(255,255,255,.4); margin-bottom: 28px; }
.report-cta-btns { display: flex; align-items: center; justify-content: center; gap: 10px; flex-wrap: wrap; }

/* ── CORE WEB VITALS SECTION ─────────────────────────── */
.cwv-section {
    border: 1px solid var(--rule);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 48px;
}
.cwv-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid var(--rule);
    background: var(--paper);
}
.cwv-header-left { display: flex; align-items: center; gap: 10px; }
.cwv-header-icon {
    width: 30px; height: 30px;
    background: var(--blue-bg); border: 1px solid var(--blue-bd);
    border-radius: 8px; display: flex; align-items: center; justify-content: center;
}
.cwv-header-icon svg { width: 14px; height: 14px; stroke: var(--blue); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.cwv-title { font-size: 14px; font-weight: 700; letter-spacing: -.3px; color: var(--ink); }
.cwv-sub   { font-size: 11px; color: var(--ink-5); margin-top: 1px; }
.cwv-tabs  { display: flex; gap: 4px; }
.cwv-tab {
    padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;
    cursor: pointer; border: 1px solid var(--rule); background: var(--paper-2);
    color: var(--ink-4); transition: all .15s;
}
.cwv-tab.active { background: var(--ink); color: white; border-color: var(--ink); }

/* Metrics grid */
.cwv-metrics { display: grid; grid-template-columns: repeat(6,1fr); padding: 0; }
.cwv-metric {
    padding: 20px 16px; border-right: 1px solid var(--rule);
    text-align: center; position: relative;
}
.cwv-metric:last-child { border-right: none; }
.cwv-metric-name {
    font-size: 10px; font-weight: 700; letter-spacing: .5px;
    text-transform: uppercase; color: var(--ink-5); margin-bottom: 10px;
}
.cwv-metric-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: 700;
    margin-bottom: 8px;
}
.cwv-metric-badge.good    { background: #dcfce7; color: #15803d; }
.cwv-metric-badge.needs   { background: #fef9c3; color: #a16207; }
.cwv-metric-badge.poor    { background: #fee2e2; color: #b91c1c; }
.cwv-metric-badge.na      { background: var(--paper-3); color: var(--ink-5); }
.cwv-metric-badge svg     { width: 10px; height: 10px; fill: currentColor; flex-shrink: 0; }
.cwv-metric-val  { font-size: 11px; color: var(--ink-4); font-weight: 500; }
.cwv-metric-threshold { font-size: 10px; color: var(--ink-6); margin-top: 4px; }
.cwv-core-label {
    position: absolute; top: 8px; right: 8px;
    font-size: 9px; font-weight: 700; letter-spacing: .3px;
    color: var(--blue); background: var(--blue-bg);
    padding: 1px 5px; border-radius: 3px; text-transform: uppercase;
}

/* Strategy comparison bar */
.cwv-compare {
    border-top: 1px solid var(--rule);
    padding: 16px 24px;
    display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
    background: var(--paper-2);
}
.cwv-strategy { display: flex; align-items: center; gap: 12px; }
.cwv-strategy-label { font-size: 11px; color: var(--ink-5); font-weight: 600; min-width: 60px; }
.cwv-score-bar-wrap { flex: 1; height: 6px; background: var(--paper-3); border-radius: 3px; overflow: hidden; }
.cwv-score-bar { height: 100%; border-radius: 3px; transition: width 1s var(--ease); }
.cwv-strategy-score { font-size: 13px; font-weight: 800; letter-spacing: -.5px; min-width: 36px; text-align: right; }

/* Opportunities accordion */
.cwv-opps {
    border-top: 1px solid var(--rule);
}
.cwv-opps-toggle {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 24px; cursor: pointer; user-select: none;
    transition: background .15s;
}
.cwv-opps-toggle:hover { background: var(--paper-2); }
.cwv-opps-toggle-left { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--ink-3); }
.cwv-opps-toggle-left svg { width: 14px; height: 14px; stroke: var(--ink-4); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.cwv-opps-count { background: var(--paper-3); border: 1px solid var(--rule); border-radius: 10px; font-size: 10px; font-weight: 700; color: var(--ink-4); padding: 1px 7px; }
.cwv-chevron { width: 14px; height: 14px; stroke: var(--ink-5); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; transition: transform .2s; }
.cwv-opps-body { display: none; padding: 0 24px 16px; }
.cwv-opps-body.open { display: block; }
.cwv-opp-row {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 10px 0; border-bottom: 1px solid var(--rule);
}
.cwv-opp-row:last-child { border-bottom: none; }
.cwv-opp-sev { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; margin-top: 6px; }
.cwv-opp-sev.critical { background: var(--red); }
.cwv-opp-sev.warning  { background: var(--amber); }
.cwv-opp-sev.info     { background: var(--green); }
.cwv-opp-label { font-size: 12px; font-weight: 600; color: var(--ink-3); flex: 1; }
.cwv-opp-savings { font-size: 11px; color: var(--green-dark, #15803d); font-weight: 600; white-space: nowrap; }

@media (max-width: 1024px) {
    .cwv-metrics { grid-template-columns: repeat(3,1fr); }
    .cwv-metric:nth-child(3n) { border-right: none; }
    .cwv-metric:nth-child(n+4) { border-top: 1px solid var(--rule); }
}
@media (max-width: 600px) {
    .cwv-metrics { grid-template-columns: repeat(2,1fr); }
    .cwv-compare { grid-template-columns: 1fr; }
    .cwv-metric:nth-child(2n) { border-right: none; }
    .cwv-metric:nth-child(n+3) { border-top: 1px solid var(--rule); }
}

/* ── PAGES SCANNED SECTION ────────────────────────────── */
.pages-section {
    border: 1px solid var(--rule); border-radius: 16px;
    overflow: hidden; margin-bottom: 48px;
}
.pages-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px; border-bottom: 1px solid var(--rule); background: var(--paper);
}
.pages-header-left { display: flex; align-items: center; gap: 10px; }
.pages-header-icon {
    width: 30px; height: 30px; background: var(--paper-3);
    border: 1px solid var(--rule); border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
}
.pages-header-icon svg { width: 14px; height: 14px; stroke: var(--ink-4); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.pages-title { font-size: 14px; font-weight: 700; letter-spacing: -.3px; color: var(--ink); }
.pages-sub   { font-size: 11px; color: var(--ink-5); margin-top: 1px; }

/* Type pills summary */
.pages-types {
    display: flex; flex-wrap: wrap; gap: 6px;
    padding: 14px 24px; border-bottom: 1px solid var(--rule);
    background: var(--paper-2);
}
.type-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 600;
    background: var(--paper); border: 1px solid var(--rule); color: var(--ink-4);
}
.type-pill-count {
    background: var(--ink); color: white;
    border-radius: 10px; padding: 0 5px; font-size: 10px; font-weight: 700;
}

/* Table */
.pages-table-wrap { overflow-x: auto; }
.pages-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.pages-table th {
    text-align: left; padding: 10px 16px;
    font-size: 10px; font-weight: 700; letter-spacing: .4px;
    text-transform: uppercase; color: var(--ink-5);
    border-bottom: 1px solid var(--rule); background: var(--paper-2);
    white-space: nowrap;
}
.pages-table td { padding: 12px 16px; border-bottom: 1px solid var(--rule); vertical-align: middle; }
.pages-table tr:last-child td { border-bottom: none; }
.pages-table tr:hover td { background: var(--paper-2); }

.pt-url { font-size: 12px; font-weight: 600; color: var(--ink-3); max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; font-family: var(--mono, monospace); }
.pt-title { font-size: 11px; color: var(--ink-5); margin-top: 2px; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.pt-type { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 500; color: var(--ink-4); white-space: nowrap; }

.status-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; font-family: var(--mono, monospace); }
.status-good     { background: #dcfce7; color: #15803d; }
.status-redirect { background: #fef9c3; color: #a16207; }
.status-error    { background: #fee2e2; color: #b91c1c; }
.status-na       { background: var(--paper-3); color: var(--ink-5); }

.load-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
.load-good  { background: #dcfce7; color: #15803d; }
.load-needs { background: #fef9c3; color: #a16207; }
.load-poor  { background: #fee2e2; color: #b91c1c; }
.load-na    { background: var(--paper-3); color: var(--ink-5); }

.pt-issues { display: flex; gap: 4px; flex-wrap: wrap; }
.pt-issue-dot { width: 6px; height: 6px; border-radius: 50%; }
.pt-issue-dot.critical { background: var(--red); }
.pt-issue-dot.warning  { background: var(--amber); }

@media(max-width: 768px) {
    .pages-table th:nth-child(n+4), .pages-table td:nth-child(n+4) { display: none; }
}

/* ── ISSUE FORMAT K — Premium ─────────────────────────── */
.issue {
    border: 1px solid var(--rule); border-radius: 10px;
    background: var(--paper); overflow: hidden;
    transition: box-shadow .15s; margin-bottom: 6px;
}
.issue:hover { box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.issue.sev-critical { border-left: 3px solid var(--red); }
.issue.sev-warning  { border-left: 3px solid var(--amber); }
.issue.sev-info     { border-left: 3px solid var(--green); }

/* Issue header row — always visible */
.issue-head {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 14px 16px; cursor: pointer; user-select: none;
}
.issue-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; }
.d-critical { background: var(--red);   box-shadow: 0 0 5px rgba(239,68,68,.4); }
.d-warning  { background: var(--amber); }
.d-info     { background: var(--green); }
.issue-head-body { flex: 1; min-width: 0; }
.issue-title { font-size: 13px; font-weight: 700; letter-spacing: -.2px; color: var(--ink); margin-bottom: 5px; }

/* Meta row: impact tags + time estimate + URL */
.issue-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.impact-tag {
    font-size: 10px; font-weight: 700; letter-spacing: .3px;
    padding: 2px 7px; border-radius: 4px; text-transform: uppercase;
}
.impact-seo      { background: #dbeafe; color: #1d4ed8; }
.impact-ux       { background: #f3e8ff; color: #7c3aed; }
.impact-legal    { background: #fef9c3; color: #a16207; }
.impact-conversie{ background: #dcfce7; color: #15803d; }
.impact-security { background: #fee2e2; color: #b91c1c; }

.issue-time {
    font-size: 10px; color: var(--ink-5); font-weight: 500;
    display: flex; align-items: center; gap: 3px;
}
.issue-time svg { width: 10px; height: 10px; stroke: var(--ink-6); fill: none; stroke-width: 2; stroke-linecap: round; }
.issue-url-tag {
    font-size: 10px; color: var(--ink-5); background: var(--paper-3);
    border: 1px solid var(--rule); padding: 1px 6px; border-radius: 3px;
    font-family: var(--mono, monospace); max-width: 180px;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

/* Expand toggle */
.issue-toggle {
    flex-shrink: 0; width: 22px; height: 22px;
    border: 1px solid var(--rule); border-radius: 5px;
    background: var(--paper-2); display: flex; align-items: center; justify-content: center;
    transition: all .15s; margin-top: 1px;
}
.issue-toggle svg { width: 11px; height: 11px; stroke: var(--ink-5); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; transition: transform .2s; }
.issue.open .issue-toggle { background: var(--ink); border-color: var(--ink); }
.issue.open .issue-toggle svg { stroke: white; transform: rotate(180deg); }

/* Issue body — collapsible */
.issue-body {
    display: none; padding: 0 16px 16px 35px;
    border-top: 1px solid var(--rule);
}
.issue.open .issue-body { display: block; }

.issue-desc {
    font-size: 12px; color: var(--ink-4); line-height: 1.7;
    padding: 12px 0 14px;
}

/* Steps */
.issue-steps { margin-bottom: 14px; }
.issue-steps-title {
    font-size: 10px; font-weight: 700; letter-spacing: .4px;
    text-transform: uppercase; color: var(--ink-5); margin-bottom: 8px;
}
.issue-step {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 7px 0; border-bottom: 1px solid var(--rule);
}
.issue-step:last-child { border-bottom: none; }
.issue-step-num {
    width: 20px; height: 20px; border-radius: 50%; flex-shrink: 0;
    background: var(--ink); color: white;
    font-size: 10px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}
.issue-step-txt { font-size: 12px; color: var(--ink-3); line-height: 1.6; }

/* Verify */
.issue-verify {
    display: flex; align-items: flex-start; gap: 8px;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: 7px; padding: 10px 12px;
    font-size: 12px; color: #15803d; line-height: 1.6;
}
.issue-verify svg { width: 13px; height: 13px; stroke: #16a34a; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; margin-top: 1px; }

/* Steps loading state */
.issue-steps-loading {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; color: var(--ink-5); padding: 8px 0;
}
.steps-spin {
    width: 14px; height: 14px; border: 2px solid var(--rule);
    border-top-color: var(--blue); border-radius: 50%;
    animation: spin-steps .7s linear infinite; flex-shrink: 0;
}
@keyframes spin-steps { to { transform: rotate(360deg); } }


@media (max-width: 1024px) { .cat-row { grid-template-columns: repeat(3,1fr); } .cs:nth-child(3n){border-right:none} .cs:nth-child(n+4){border-top:1px solid var(--rule)} }
@media (max-width: 768px) {
    .rh-inner { grid-template-columns: 1fr; gap: 24px; }
    .cat-row { grid-template-columns: repeat(2,1fr); }
    .cs:nth-child(2n){border-right:none} .cs:nth-child(3n){border-right:1px solid var(--rule)} .cs:nth-child(4n){border-right:none} .cs:nth-child(n+3){border-top:1px solid var(--rule)}
    .report-cta { padding: 40px 20px; }
    .issue-url { display: none; }
    .rb { padding: 32px 16px 60px; }
    .rh { padding: 32px 16px 28px; }
    .ai-summary { padding: 24px 18px; }
}
@media (max-width: 480px) { .cat-row { grid-template-columns: 1fr 1fr; } }

/* ── QUICK WINS ───────────────────────────────────────── */
.qw-section { margin-bottom: 48px; }
.qw-header  { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
.qw-header-icon {
    width: 30px; height: 30px; background: #fef9c3;
    border: 1px solid #fde68a; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.qw-title { font-size: 14px; font-weight: 700; letter-spacing: -.3px; color: var(--ink); }
.qw-sub   { font-size: 11px; color: var(--ink-5); margin-top: 1px; }
.qw-grid  { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }

.qw-card {
    border: 1px solid var(--rule); border-radius: 12px;
    background: var(--paper); overflow: hidden;
    display: flex; flex-direction: column;
    transition: box-shadow .15s, transform .15s; position: relative;
}
.qw-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); transform: translateY(-1px); }
.qw-card-rank {
    position: absolute; top: 12px; right: 12px;
    width: 22px; height: 22px; border-radius: 50%;
    background: var(--ink); color: white;
    font-size: 11px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}
.qw-card-top { padding: 16px 16px 12px; border-bottom: 1px solid var(--rule); flex: 1; }
.qw-card-sev {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 10px; font-weight: 700; letter-spacing: .3px;
    text-transform: uppercase; margin-bottom: 8px;
}
.qw-sev-critical { color: var(--red); }
.qw-sev-warning  { color: var(--amber); }
.qw-card-title {
    font-size: 13px; font-weight: 700; color: var(--ink);
    letter-spacing: -.2px; line-height: 1.4; margin-bottom: 8px; padding-right: 24px;
}
.qw-card-desc { font-size: 11px; color: var(--ink-5); line-height: 1.65; }
.qw-card-bottom {
    padding: 10px 16px;
    display: flex; align-items: center; justify-content: space-between;
    background: var(--paper-2); gap: 8px; flex-wrap: wrap;
}
.qw-effort { display: flex; align-items: center; gap: 5px; font-size: 10px; color: var(--ink-5); }
.qw-effort-dots { display: flex; gap: 3px; }
.qw-effort-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--rule); border: 1px solid var(--rule-2); }
.qw-effort-dot.filled { background: #f59e0b; border-color: #f59e0b; }
.qw-fix-link {
    font-size: 11px; font-weight: 700; color: var(--blue);
    cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
}
.qw-fix-link:hover { text-decoration: underline; }
.qw-fix-link svg { width: 11px; height: 11px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; }
@media (max-width: 768px) { .qw-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

@php
use Illuminate\Support\Str;
$score = $audit->score_total ?? 0;
$circ = 314;
$offset = $circ - ($score / 100 * $circ);
$scoreColor = $score >= 80 ? '#16a34a' : ($score >= 50 ? '#d97706' : '#dc2626');
$categories = [
    'technical' => ['label'=>'Tehnic','score'=>$audit->score_technical??0,'icon'=>'<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>'],
    'seo'       => ['label'=>'SEO','score'=>$audit->score_seo??0,'icon'=>'<circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/>'],
    'legal'     => ['label'=>'Legal','score'=>$audit->score_legal??0,'icon'=>'<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
    'eeeat'     => ['label'=>'E-E-A-T','score'=>$audit->score_eeeat??0,'icon'=>'<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>'],
    'content'   => ['label'=>'Continut','score'=>$audit->score_content??0,'icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
    'ux'        => ['label'=>'UX','score'=>$audit->score_ux??0,'icon'=>'<rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>'],
];

// ── PageSpeed / Core Web Vitals ───────────────────────────
$rawData   = $audit->raw_data ?? [];
$psData    = $rawData['pagespeed'] ?? [];
$psMobile  = $psData['mobile']  ?? [];
$psDesktop = $psData['desktop'] ?? [];

$cwvClass = function(?float $val, string $metric): string {
    if ($val === null) return 'na';
    return match($metric) {
        'lcp'  => $val < 2500 ? 'good' : ($val < 4000 ? 'needs' : 'poor'),
        'cls'  => $val < 0.1  ? 'good' : ($val < 0.25 ? 'needs' : 'poor'),
        'inp'  => $val < 200  ? 'good' : ($val < 500  ? 'needs' : 'poor'),
        'fcp'  => $val < 1800 ? 'good' : ($val < 3000 ? 'needs' : 'poor'),
        'ttfb' => $val < 800  ? 'good' : ($val < 1800 ? 'needs' : 'poor'),
        'tbt'  => $val < 200  ? 'good' : ($val < 600  ? 'needs' : 'poor'),
        default => 'na',
    };
};

$cwvLabel = ['good'=>'Bun','needs'=>'Mediu','poor'=>'Slab','na'=>'N/A'];
$cwvDot   = ['good'=>'✓','needs'=>'!','poor'=>'✕','na'=>'—'];

$metrics = [
    ['key'=>'lcp',  'label'=>'LCP',   'core'=>true,  'val'=>$psMobile['lcp_ms']??null,  'disp'=>$psMobile['lcp']??null,  'thresh'=>'Bun <2.5s · Mediu <4s'],
    ['key'=>'cls',  'label'=>'CLS',   'core'=>true,  'val'=>$psMobile['cls_raw']??null, 'disp'=>$psMobile['cls']??null,  'thresh'=>'Bun <0.1 · Mediu <0.25'],
    ['key'=>'inp',  'label'=>'INP',   'core'=>true,  'val'=>$psMobile['inp_ms']??null,  'disp'=>$psMobile['inp']??null,  'thresh'=>'Bun <200ms · Mediu <500ms'],
    ['key'=>'fcp',  'label'=>'FCP',   'core'=>false, 'val'=>$psMobile['fcp_ms']??null,  'disp'=>$psMobile['fcp']??null,  'thresh'=>'Bun <1.8s · Mediu <3s'],
    ['key'=>'ttfb', 'label'=>'TTFB',  'core'=>false, 'val'=>$psMobile['ttfb_ms']??null, 'disp'=>$psMobile['ttfb']??null, 'thresh'=>'Bun <800ms · Mediu <1.8s'],
    ['key'=>'tbt',  'label'=>'TBT',   'core'=>false, 'val'=>$psMobile['tbt_ms']??null,  'disp'=>$psMobile['tbt']??null,  'thresh'=>'Bun <200ms · Mediu <600ms'],
];

$mobileScore  = $psMobile['score']  ?? null;
$desktopScore = $psDesktop['score'] ?? null;
$msColor = $mobileScore  === null ? '#9ca3af' : ($mobileScore  >= 90 ? '#16a34a' : ($mobileScore  >= 50 ? '#d97706' : '#dc2626'));
$dsColor = $desktopScore === null ? '#9ca3af' : ($desktopScore >= 90 ? '#16a34a' : ($desktopScore >= 50 ? '#d97706' : '#dc2626'));

$lighthouseIssues = $audit->issues
    ->where('category', 'technical')
    ->filter(fn($i) => str_contains($i->description ?? '', 'PageSpeed Lighthouse'))
    ->sortBy(fn($i) => match($i->severity) { 'critical'=>0,'warning'=>1,default=>2 });
@endphp

{{-- HEADER --}}
<div class="rh">
    <div class="rh-inner">
        <div>
            <div class="rh-bread"><a href="{{ route('home') }}">Inovex Audit</a><span>/</span><span>Raport</span></div>
            <h1 class="rh-url">{{ $audit->url }}</h1>
            <div class="rh-tags">
                @if($critical>0)<span class="chip chip-red">{{ $critical }} {{ $critical===1?'problema critica':'probleme critice' }}</span>@endif
                @if($warnings>0)<span class="chip chip-amber">{{ $warnings }} {{ $warnings===1?'avertisment':'avertismente' }}</span>@endif
                @if($info>0)<span class="chip chip-green">{{ $info }} {{ $info===1?'observatie':'observatii' }}</span>@endif
            </div>
            <div class="rh-meta">
                <span>Generat: {{ $audit->completed_at ? $audit->completed_at->format('d.m.Y, H:i') : 'acum' }}</span>
                <div class="rh-meta-dot"></div><span>Disponibil 30 de zile</span>
                <div class="rh-meta-dot"></div><span>{{ $audit->email }}</span>
            </div>
        </div>
        <div class="score-w">
            <div class="score-lbl">Scor general</div>
            <div class="score-ring">
                <svg viewBox="0 0 110 110">
                    <circle class="score-track" cx="55" cy="55" r="50"/>
                    <circle class="score-fill" cx="55" cy="55" r="50" stroke="{{ $scoreColor }}" stroke-dashoffset="{{ number_format($offset,2) }}"/>
                </svg>
                <div class="score-num">
                    <span class="score-n" style="color:{{ $scoreColor }}">{{ $score }}</span>
                    <span class="score-d">/100</span>
                </div>
            </div>
            <a href="{{ route('audit.pdf', $audit->public_token) }}" class="btn-pdf">
                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Descarca PDF
            </a>
        </div>
    </div>
</div>

{{-- BODY --}}
<div class="rb">
    {{-- Category scores --}}
    <div class="cat-row" data-reveal>
        @foreach($categories as $key => $cat)
            @php $s=$cat['score']; $cc=$s>=80?'c-green':($s>=50?'c-amber':'c-red'); $bc=$s>=80?'b-green':($s>=50?'b-amber':'b-red'); @endphp
            <div class="cs">
                <div class="cs-lbl">{{ $cat['label'] }}</div>
                <div class="cs-n {{ $cc }}">{{ $s }}</div>
                <div class="cs-bar"><div class="cs-fill {{ $bc }}" style="width:{{ $s }}%"></div></div>
            </div>
        @endforeach
    </div>

    {{-- ── QUICK WINS ── --}}
    @if($quickWins->count() > 0)
    @php
        $effortLabels = [1=>'Foarte ușor',2=>'Ușor',3=>'Mediu',4=>'Dificil'];
        $effortMax    = 4;
        $impactCssQw  = ['SEO'=>'impact-seo','UX'=>'impact-ux','Legal'=>'impact-legal','Conversie'=>'impact-conversie','Security'=>'impact-security'];
    @endphp
    <div class="qw-section" data-reveal>
        <div class="qw-header">
            <div class="qw-header-icon">⚡</div>
            <div>
                <div class="qw-title">Quick Wins — rezolvă acum, impact imediat</div>
                <div class="qw-sub">Top {{ $quickWins->count() }} probleme cu efort mic și impact mare, identificate automat</div>
            </div>
        </div>
        <div class="qw-grid">
            @foreach($quickWins as $i => $qw)
            @php
                $issue   = $qw['issue'];
                $effort  = $qw['effort'];
                $impacts = array_filter(array_map('trim', explode(',', $issue->impact ?? '')));
                $effortLbl = $effortLabels[$effort] ?? 'Mediu';
            @endphp
            <div class="qw-card">
                <div class="qw-card-rank">{{ $i + 1 }}</div>
                <div class="qw-card-top">
                    <div class="qw-card-sev qw-sev-{{ $issue->severity }}">
                        <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;flex-shrink:0"></span>
                        {{ $issue->severity === 'critical' ? 'Critic' : 'Avertisment' }}
                    </div>
                    <div class="qw-card-title">{{ $issue->title }}</div>
                    <div class="qw-card-desc">{{ Str::limit($issue->description, 110) }}</div>
                </div>
                <div class="qw-card-bottom">
                    <div class="qw-effort">
                        <div class="qw-effort-dots">
                            @for($d = 1; $d <= $effortMax; $d++)
                                <div class="qw-effort-dot {{ $d <= $effort ? 'filled' : '' }}"></div>
                            @endfor
                        </div>
                        <span>{{ $effortLbl }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        @foreach($impacts as $imp)
                            @php $cls = $impactCssQw[$imp] ?? 'impact-seo'; @endphp
                            <span class="impact-tag {{ $cls }}">{{ $imp }}</span>
                        @endforeach
                    </div>
                    <a class="qw-fix-link" onclick="scrollToIssue('issue-{{ $issue->id }}')">
                        Vezi fix
                        <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── PAGINI SCANATE ── --}}
    @if($audit->pageData->count() > 0)
    @php
        $pages      = $audit->pageData;
        $typeCounts = $pages->groupBy('page_type')->map->count();
        $typeOrder  = ['home','contact','about','services','blog','category','product','checkout','faq','legal','other'];
        $avgLoad    = $pages->avg('load_time_ms');
        $errorPages = $pages->filter(fn($p) => $p->status_code >= 400)->count();
    @endphp
    <div class="pages-section" data-reveal>
        <div class="pages-header">
            <div class="pages-header-left">
                <div class="pages-header-icon">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                    <div class="pages-title">Pagini scanate</div>
                    <div class="pages-sub">{{ $pages->count() }} pagini analizate · timp mediu de încărcare {{ $avgLoad ? round($avgLoad).'ms' : 'N/A' }}{{ $errorPages > 0 ? ' · '.$errorPages.' erori' : '' }}</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:24px;font-weight:800;letter-spacing:-1px;color:var(--ink)">{{ $pages->count() }}</span>
                <span style="font-size:11px;color:var(--ink-5);font-weight:500">pagini</span>
            </div>
        </div>

        {{-- Type pills --}}
        <div class="pages-types">
            @foreach($typeOrder as $type)
                @if(isset($typeCounts[$type]))
                @php
                    $icons = ['home'=>'🏠','contact'=>'📞','about'=>'👥','services'=>'⚙️','blog'=>'📝','category'=>'📂','product'=>'🛍️','checkout'=>'💳','faq'=>'❓','legal'=>'⚖️','other'=>'📄'];
                    $labels = ['home'=>'Home','contact'=>'Contact','about'=>'Despre','services'=>'Servicii','blog'=>'Blog','category'=>'Categorii','product'=>'Produse','checkout'=>'Checkout','faq'=>'FAQ','legal'=>'Legal','other'=>'Alte pagini'];
                @endphp
                <div class="type-pill">
                    <span>{{ $icons[$type] ?? '📄' }}</span>
                    <span>{{ $labels[$type] ?? $type }}</span>
                    <span class="type-pill-count">{{ $typeCounts[$type] }}</span>
                </div>
                @endif
            @endforeach
        </div>

        {{-- Table --}}
        <div class="pages-table-wrap">
            <table class="pages-table">
                <thead>
                    <tr>
                        <th>Pagină</th>
                        <th>Tip</th>
                        <th>Status</th>
                        <th>Viteză</th>
                        <th>Imagini fără ALT</th>
                        <th>Probleme</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages->sortBy(fn($p) => array_search($p->page_type, $typeOrder)) as $page)
                    @php
                        $pageIssues = $audit->issues->filter(fn($i) =>
                            $i->affected_url && str_contains($i->affected_url, $page->url)
                        );
                        $criticalCount = $pageIssues->where('severity','critical')->count();
                        $warningCount  = $pageIssues->where('severity','warning')->count();
                    @endphp
                    <tr>
                        <td>
                            <span class="pt-url" title="{{ $page->url }}">{{ $page->url }}</span>
                            @if($page->title)
                                <span class="pt-title">{{ $page->title }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="pt-type">
                                {{ $page->pageTypeIcon() }} {{ $page->pageTypeLabel() }}
                            </span>
                        </td>
                        <td>
                            @php $sc = $page->status_code ?? 0; @endphp
                            @if($sc)
                                @php
                                    $sCls = $sc < 300 ? 'status-good' : ($sc < 400 ? 'status-redirect' : 'status-error');
                                @endphp
                                <span class="status-badge {{ $sCls }}">{{ $sc }}</span>
                            @else
                                <span class="status-badge status-na">N/A</span>
                            @endif
                        </td>
                        <td>
                            @php $ltCls = $page->loadTimeClass(); @endphp
                            <span class="load-badge load-{{ $ltCls }}">{{ $page->formattedLoadTime() }}</span>
                        </td>
                        <td>
                            @if($page->images_missing_alt > 0)
                                <span style="color:var(--amber);font-weight:700;font-size:12px;">{{ $page->images_missing_alt }}/{{ $page->images_total }}</span>
                            @else
                                <span style="color:var(--ink-6);font-size:12px;">{{ $page->images_total > 0 ? '✓ '.$page->images_total : '—' }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="pt-issues">
                                @for($i=0; $i<min($criticalCount,5); $i++)
                                    <div class="pt-issue-dot critical" title="Problemă critică"></div>
                                @endfor
                                @for($i=0; $i<min($warningCount,5); $i++)
                                    <div class="pt-issue-dot warning" title="Avertisment"></div>
                                @endfor
                                @if($criticalCount === 0 && $warningCount === 0)
                                    <span style="font-size:11px;color:var(--ink-6);">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── CORE WEB VITALS ── --}}
    <div class="cwv-section" data-reveal>
        <div class="cwv-header">
            <div class="cwv-header-left">
                <div class="cwv-header-icon">
                    <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div>
                    <div class="cwv-title">Performanță & Core Web Vitals</div>
                    <div class="cwv-sub">Date reale Google PageSpeed Insights · Mobile</div>
                </div>
            </div>
            @if($mobileScore !== null || $desktopScore !== null)
            <div style="display:flex;align-items:center;gap:16px;">
                @if($mobileScore !== null)
                <div style="text-align:center;">
                    <div style="font-size:10px;color:var(--ink-5);font-weight:600;text-transform:uppercase;margin-bottom:3px;">Mobile</div>
                    <div style="font-size:22px;font-weight:800;letter-spacing:-1px;color:{{ $msColor }};">{{ $mobileScore }}</div>
                </div>
                @endif
                @if($desktopScore !== null)
                <div style="text-align:center;">
                    <div style="font-size:10px;color:var(--ink-5);font-weight:600;text-transform:uppercase;margin-bottom:3px;">Desktop</div>
                    <div style="font-size:22px;font-weight:800;letter-spacing:-1px;color:{{ $dsColor }};">{{ $desktopScore }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Metrics grid --}}
        <div class="cwv-metrics" id="cwvMetrics">
            @foreach($metrics as $m)
                @php
                    $cls  = $cwvClass($m['val'], $m['key']);
                    $lbl  = $cwvLabel[$cls];
                    $disp = $m['disp'] ?? ($m['val'] !== null ? $m['val'] : null);
                @endphp
                <div class="cwv-metric">
                    @if($m['core'])<div class="cwv-core-label">CWV</div>@endif
                    <div class="cwv-metric-name">{{ $m['label'] }}</div>
                    <div class="cwv-metric-badge {{ $cls }}">
                        @if($cls === 'good')
                            <svg viewBox="0 0 10 10" style="width:10px;height:10px;fill:none;stroke:currentColor;stroke-width:1.5;stroke-linecap:round"><path d="M2 5l2 2 4-4"/></svg>
                        @elseif($cls === 'needs')
                            <svg viewBox="0 0 10 10" style="width:10px;height:10px;fill:none;stroke:currentColor;stroke-width:1.5;stroke-linecap:round"><path d="M5 2v3.5M5 7h.01"/></svg>
                        @elseif($cls === 'poor')
                            <svg viewBox="0 0 10 10" style="width:10px;height:10px;fill:none;stroke:currentColor;stroke-width:1.5;stroke-linecap:round"><path d="M2 2l6 6M8 2L2 8"/></svg>
                        @endif
                        {{ $lbl }}
                    </div>
                    <div class="cwv-metric-val">{{ $disp ?? 'N/A' }}</div>
                    <div class="cwv-metric-threshold">{{ $m['thresh'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- Mobile vs Desktop score bars --}}
        @if($mobileScore !== null || $desktopScore !== null)
        <div class="cwv-compare">
            @if($mobileScore !== null)
            <div class="cwv-strategy">
                <span class="cwv-strategy-label">📱 Mobile</span>
                <div class="cwv-score-bar-wrap">
                    <div class="cwv-score-bar" style="width:{{ $mobileScore }}%;background:{{ $msColor }};"></div>
                </div>
                <span class="cwv-strategy-score" style="color:{{ $msColor }}">{{ $mobileScore }}/100</span>
            </div>
            @endif
            @if($desktopScore !== null)
            <div class="cwv-strategy">
                <span class="cwv-strategy-label">🖥 Desktop</span>
                <div class="cwv-score-bar-wrap">
                    <div class="cwv-score-bar" style="width:{{ $desktopScore }}%;background:{{ $dsColor }};"></div>
                </div>
                <span class="cwv-strategy-score" style="color:{{ $dsColor }}">{{ $desktopScore }}/100</span>
            </div>
            @endif
        </div>
        @endif

        {{-- Opportunities accordion --}}
        @if($lighthouseIssues->count() > 0)
        <div class="cwv-opps">
            <div class="cwv-opps-toggle" onclick="toggleOpps(this)">
                <div class="cwv-opps-toggle-left">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                    Oportunități de îmbunătățire Lighthouse
                    <span class="cwv-opps-count">{{ $lighthouseIssues->count() }}</span>
                </div>
                <svg class="cwv-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="cwv-opps-body" id="cwvOppsBody">
                @foreach($lighthouseIssues as $issue)
                <div class="cwv-opp-row">
                    <div class="cwv-opp-sev {{ $issue->severity }}"></div>
                    <div class="cwv-opp-label">{{ $issue->title }}</div>
                    @if($issue->suggestion)
                        <div class="cwv-opp-savings" style="color:var(--ink-4);font-weight:400;font-size:11px;max-width:300px;text-align:right;">{{ Str::limit($issue->suggestion, 80) }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Issues per category --}}
    @php
    // Map impact string → CSS class
    $impactCss = ['SEO'=>'impact-seo','UX'=>'impact-ux','Legal'=>'impact-legal','Conversie'=>'impact-conversie','Security'=>'impact-security'];

    // Timp estimat per severity
    $timeEst = ['critical'=>'30–120 min','warning'=>'15–60 min','info'=>'5–20 min'];

    // Pași de rezolvare predefiniti per issue type (mapare după cuvinte cheie din titlu)
    // Cheia = substring din $issue->title (lowercase), valoarea = ['steps'=>[], 'verify'=>'']
    $stepsMap = [
        'ssl invalid'         => ['steps'=>['Accesează cPanel → SSL/TLS → "Let\'s Encrypt"','Instalează certificatul pentru domeniu și www','Forțează HTTPS în .htaccess: RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]','Testează cu: https://www.ssllabs.com/ssltest/'],'verify'=>'Accesează site-ul cu http:// — trebuie să redirecționeze automat la https://'],
        'ssl expiră'          => ['steps'=>['Loghează-te în cPanel → SSL/TLS','Apasă "Reînnoire" sau "Renew" pentru certificatul existent','Dacă folosești Let\'s Encrypt, reînnoirea este automată — verifică că auto-renew e activ'],'verify'=>'Verifică data expirării pe ssllabs.com — trebuie să fie cel puțin 60+ zile'],
        'link-uri rupte'      => ['steps'=>['Instalează pluginul Broken Link Checker (WordPress) sau folosește ahrefs.com/broken-link-checker','Identifică toate URL-urile cu 404','Redirecționează cu 301 sau actualizează link-urile care duc spre pagini inexistente','Șterge link-urile externe care nu mai funcționează'],'verify'=>'Rulează din nou un scan — nicio pagină nu trebuie să returneze 404'],
        'meta description lipsă'=> ['steps'=>['Accesează pagina respectivă în admin WordPress','Instalează RankMath sau Yoast SEO dacă nu ai','Completează câmpul "Meta Description" cu 140–155 caractere','Include cuvântul cheie principal în primele 50 de caractere'],'verify'=>'Verifică în Google Search Console → URL Inspection că meta description apare corect'],
        'h1 lipsă'            => ['steps'=>['Identifică pagina fără H1 din lista de mai sus','În editorul WordPress/Elementor adaugă un heading de tip H1','H1-ul trebuie să conțină cuvântul cheie principal și să descrie exact pagina','O pagină = un singur H1'],'verify'=>'Inspectează pagina (F12 → Ctrl+F → <h1) — trebuie să existe exact un element H1'],
        'h1 duplicat'         => ['steps'=>['Identifică și șterge H1-ul în plus (adesea generat de tema sau widget)','Verifică dacă titlul paginii și un heading se dublează','Păstrează doar H1-ul principal relevant pentru pagină'],'verify'=>'Inspectează codul sursă — caută <h1 și confirmă că apare o singură dată'],
        'sitemap'             => ['steps'=>['Instalează RankMath sau Yoast SEO','Activează generarea automată de sitemap.xml','Accesează yoursite.ro/sitemap.xml și confirmă că e generat','Submitează URL-ul sitemapului în Google Search Console → Sitemaps'],'verify'=>'Accesează yoursite.ro/sitemap.xml — trebuie să returneze un XML valid cu paginile tale'],
        'robots.txt'          => ['steps'=>['Creează fișierul /robots.txt în rădăcina site-ului','Adaugă conținut minim: User-agent: * / Allow: /','Adaugă linia Sitemap: https://yoursite.ro/sitemap.xml','Nu bloca paginile importante cu Disallow'],'verify'=>'Accesează yoursite.ro/robots.txt — trebuie să returneze conținut text valid'],
        'mobile'              => ['steps'=>['Verifică tema actuală dacă e responsive (afișează corect pe telefon)','Adaugă meta viewport în <head>: <meta name="viewport" content="width=device-width, initial-scale=1">','Testează cu Google Mobile Friendly Test: search.google.com/test/mobile-friendly','Consultă un developer pentru CSS media queries dacă tema nu e responsive'],'verify'=>'Testează pe un telefon real sau în Chrome DevTools (F12 → Toggle device toolbar)'],
        'anpc'                => ['steps'=>['Accesează anpc.ro și descarcă bannerul oficial SAL','Adaugă imaginea în footer cu link spre https://anpc.ro/ce-este-sal/','Alternativ adaugă textul "Soluționarea alternativă a litigiilor" cu link-ul corespunzător'],'verify'=>'Inspectează footer-ul site-ului — textul/imaginea ANPC trebuie să fie vizibile și clicabile'],
        'cui'                 => ['steps'=>['Adaugă în footer: Denumire firmă, CUI (Cod Unic de Înregistrare), număr Registrul Comerțului (J__)','Acestea sunt obligatorii conform Legii 365/2002 pentru comerțul electronic','Poți adăuga și adresa sediului social'],'verify'=>'Verifică footer-ul — CUI-ul (format RO + cifre) și J__/___/___ trebuie să fie vizibile'],
        'gdpr'                => ['steps'=>['Creează o pagină nouă numită "Politică de Confidențialitate"','Include: ce date colectezi, scopul procesării, durata păstrării, drepturile utilizatorilor (acces, ștergere, portabilitate)','Adaugă link spre această pagină în footer și în orice formular de pe site'],'verify'=>'Accesează pagina și verifică că include baza legală de procesare și datele de contact DPO/responsabil'],
        'cookie'              => ['steps'=>['Instalează un plugin de cookie consent (ex: Complianz, CookieYes pentru WordPress)','Configurează categorii: necesare, analitice, marketing','Asigură-te că GA4/Meta Pixel nu se încarcă până utilizatorul nu acceptă','Adaugă buton de "Refuz" separat vizibil'],'verify'=>'Accesează site-ul în incognito — bannerul trebuie să apară înainte de orice tracking'],
        'termeni'             => ['steps'=>['Creează pagina "Termeni și Condiții"','Include: obiectul contractului, prețuri și modalități de plată, livrare și retur (dacă e eCommerce), limitarea răspunderii, legea aplicabilă (drept român)','Adaugă link în footer și la checkout'],'verify'=>'Verifică că pagina e accesibilă din footer și că link-ul de la checkout funcționează'],
        'open graph'          => ['steps'=>['Instalează RankMath sau Yoast SEO','În setările Social → Facebook, activează Open Graph','Setează og:title, og:description și og:image (1200×630px) pentru homepage și pagini cheie','Testează cu: developers.facebook.com/tools/debug/'],'verify'=>'Distribuie un link pe Facebook/WhatsApp — trebuie să apară preview cu imagine și titlu corect'],
        'twitter'             => ['steps'=>['Adaugă în <head>: <meta name="twitter:card" content="summary_large_image">','Adaugă twitter:title, twitter:description, twitter:image','Sau activează din setările Yoast/RankMath → Twitter'],'verify'=>'Testează cu: cards-dev.twitter.com/validator (sau cards.twitter.com)'],
        'structured data'     => ['steps'=>['Instalează RankMath SEO — are JSON-LD automat pentru Organization, WebSite, BreadcrumbList','Configurează Schema → Local Business dacă ai locație fizică','Adaugă FAQ Schema pe paginile cu întrebări frecvente','Testează cu: search.google.com/test/rich-results'],'verify'=>'Instrumentul Google Rich Results Test trebuie să detecteze schemele fără erori'],
        'json-ld invalid'     => ['steps'=>['Copiază blocul JSON-LD din sursa paginii','Validează-l pe jsonlint.com — caută virgule în plus sau ghilimele greșite','Corectează eroarea și retestează pe search.google.com/test/rich-results'],'verify'=>'Rich Results Test nu trebuie să mai afișeze erori de parsare JSON'],
        'google analytics'    => ['steps'=>['Creează cont pe analytics.google.com dacă nu ai','Adaugă o proprietate GA4 și copiază ID-ul G-XXXXXXXX','Instalează Google Tag Manager (gtm.js) pe site','În GTM creează un tag GA4 Configuration cu ID-ul tău și publicăl','Verifică în GA4 → Realtime că datele apar'],'verify'=>'Accesează site-ul și verifică în GA4 → Realtime → Overview că apari ca utilizator activ'],
        'universal analytics' => ['steps'=>['Caută în codul site-ului sau în GTM tagul UA-XXXXXX','Șterge sau dezactivează orice tag/script care conține UA-','Asigură-te că ai GA4 activ înainte de a șterge UA','Publică modificările în GTM'],'verify'=>'Inspectează sursa paginii (Ctrl+U) — nu trebuie să mai apară UA- nicăieri în cod'],
        'ga4 instalat de mai' => ['steps'=>['Deschide Google Tag Manager → Tags','Caută toate tagurile de tip "GA4 Configuration"','Dezactivează sau șterge duplicatele — păstrează un singur tag GA4','Dacă ai și tag hardcodat în HTML și unul în GTM, elimină unul dintre ele','Publică modificările'],'verify'=>'În GA4 → Admin → Data Streams verifică că primești date dintr-o singură sursă'],
        'hsts'                => ['steps'=>['Adaugă în .htaccess (Apache): Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"','Sau în nginx.conf: add_header Strict-Transport-Security "max-age=31536000" always;','Asigură-te că HTTPS funcționează corect ÎNAINTE de a activa HSTS'],'verify'=>'Testează pe securityheaders.com — HSTS trebuie să apară ca prezent'],
        'x-frame'             => ['steps'=>['Adaugă în .htaccess: Header always set X-Frame-Options "SAMEORIGIN"','Sau în nginx: add_header X-Frame-Options "SAMEORIGIN" always;','WordPress: poți folosi pluginul "HTTP Security Headers"'],'verify'=>'Verifică pe securityheaders.com — X-Frame-Options trebuie să fie SAMEORIGIN sau DENY'],
        'x-content'           => ['steps'=>['Adaugă în .htaccess: Header always set X-Content-Type-Options "nosniff"','Sau în nginx: add_header X-Content-Type-Options "nosniff" always;'],'verify'=>'Verifică pe securityheaders.com — X-Content-Type-Options: nosniff trebuie să fie prezent'],
        'content security'    => ['steps'=>['Analizează site-ul cu: csp-evaluator.withgoogle.com','Creează o politică CSP permisivă inițial: default-src \'self\'; script-src \'self\' cdn.jsdelivr.net','Adaugă în .htaccess: Header always set Content-Security-Policy "..."','Testează că site-ul funcționează — ajustează dacă blochează resurse legitime'],'verify'=>'Verifică consola browser (F12) — nu trebuie să apară erori CSP după implementare'],
        'lcp'                 => ['steps'=>['Identifică elementul LCP (de obicei imaginea hero sau H1) cu Chrome DevTools → Performance','Adaugă fetchpriority="high" pe imaginea hero: <img fetchpriority="high" ...>','Preîncarcă fontul principal: <link rel="preload" href="font.woff2" as="font">','Activează CDN pentru livrare mai rapidă a resurselor statice','Optimizează imaginea hero: WebP, dimensiune corectă, compresie'],'verify'=>'Rulează din nou PageSpeed Insights — LCP trebuie să scadă sub 2.5s'],
        'cls'                 => ['steps'=>['Identifică elementele care se mișcă la încărcare (de obicei imagini fără dimensiuni sau reclame)','Adaugă width și height explicit pe toate imaginile: <img width="800" height="600" ...>','Rezervă spațiu pentru reclame/bannere din CSS (min-height)','Evită inserarea de conținut deasupra conținutului existent după încărcare'],'verify'=>'Rulează Lighthouse — CLS trebuie să fie sub 0.1'],
        'inp'                 => ['steps'=>['Identifică scripturile care blochează thread-ul principal (Google Tag Manager, widget-uri terțe)','Adaugă defer sau async pe toate scripturile non-critice','Optimizează event listeners — evită calcule grele în onClick/onInput','Ia în considerare Web Workers pentru operații intensive'],'verify'=>'Testează în Chrome DevTools → Performance — INP trebuie să fie sub 200ms'],
        'ttfb'                => ['steps'=>['Verifică planul de hosting — shared hosting lent cauzează TTFB mare','Activează caching la nivel de server (Redis, Memcached sau plugin WP Rocket)','Activează un CDN (Cloudflare gratuit) care să servească din locații apropiate de utilizator','Optimizează query-urile de baze de date dacă site-ul e dinamic'],'verify'=>'Testează TTFB pe web.dev/measure — trebuie să fie sub 800ms'],
        'imagini fără'        => ['steps'=>['Exportă lista de imagini fără ALT din raport','Pentru fiecare imagine, adaugă un atribut alt descriptiv: <img alt="descriere relevantă">','Imaginile decorative pot avea alt gol: alt=""','În WordPress: Media Library → editează imaginea → completează câmpul "Alternative Text"'],'verify'=>'Rulează WAVE (wave.webaim.org) pe pagină — nu trebuie să apară erori "Missing alternative text"'],
        'compresie'           => ['steps'=>['Activează Gzip în .htaccess (Apache): AddOutputFilterByType DEFLATE text/html text/css application/javascript','Sau activează din cPanel → Optimize Website','Pentru Nginx: gzip on; gzip_types text/plain application/json text/css application/javascript;','Alternativ: plugin WP Rocket sau Autoptimize activează automat'],'verify'=>'Testează pe gtmetrix.com — GZIP/Brotli trebuie să fie activ (vei vedea dimensiunea comprimată vs. originală)'],
        'cache'               => ['steps'=>['În WordPress: instalează WP Rocket sau W3 Total Cache','Configurează Browser Caching pentru fișiere statice (1 an pentru imagini/CSS/JS)','Adaugă în .htaccess: <FilesMatch "\\.(css|js|jpg|png|woff2)$"> ExpiresDefault "access plus 1 year"','Activează și cache la nivel de pagină pentru pagini statice'],'verify'=>'Verifică pe gtmetrix.com → Waterfall — coloanele de cache trebuie să fie verzi pentru resurse statice'],
        'canonical'           => ['steps'=>['Adaugă în <head> al fiecărei pagini: <link rel="canonical" href="URL_COMPLET_PAGINA">','În WordPress: Yoast/RankMath adaugă canonical automat — asigură-te că e activat','Verifică că URL-ul canonical include sau exclude www consistent','Nu adăuga canonical care pointează spre altă pagină decât cea curentă (excepție: pagini duplicate)'],'verify'=>'Inspectează sursa paginii (Ctrl+U) → caută rel="canonical" — trebuie să apară cu URL-ul corect'],
        'title prea lung'     => ['steps'=>['Identifică titlul paginii principale (cel din <title>, nu H1)','Scurtează-l la maxim 60 de caractere','Structura ideală: Cuvânt cheie principal | Nume brand','Evită cuvintele redundante: "bun venit la", "site-ul nostru", etc.'],'verify'=>'Verifică pe Google Search Console → URL Inspection → Test Live URL că titlul nu e trunchiat'],
        'title prea scurt'    => ['steps'=>['Extinde titlul paginii la 50–60 de caractere','Structura: Cuvânt cheie | Beneficiu cheie | Brand','Exemplu: "Audit SEO Automat | Raport Complet în 5 Minute | Inovex"'],'verify'=>'Verifică pe seoptimer.com sau browseo.net că titlul complet apare fără trunchiere'],
        'meta description prea'=> ['steps'=>['Reduce meta description la 140–155 de caractere','Pune CTA sau diferențiatorul în primele 120 de caractere','Elimină informațiile redundante sau puțin relevante de la final'],'verify'=>'Verifică în Google Search Console că descrierea nu e trunchiată în previzualizare'],
        'număr de telefon'    => ['steps'=>['Adaugă numărul de telefon în header sau footer','Folosește format clicabil: <a href="tel:+40700000000">0700 000 000</a>','Pe mobil, clicul pe număr deschide automat telefonul','Adaugă și pe pagina de Contact'],'verify'=>'Pe telefon, apasă pe număr — trebuie să deschidă aplicația de telefon automat'],
    ];

    // Funcție helper: găsește pașii potriviți pentru un issue
    $findSteps = function(string $title) use ($stepsMap): ?array {
        $titleLower = mb_strtolower($title);
        foreach ($stepsMap as $keyword => $data) {
            if (str_contains($titleLower, $keyword)) return $data;
        }
        return null;
    };
    @endphp

    @foreach($categories as $key => $cat)
        @if(isset($issuesByCategory[$key]) && $issuesByCategory[$key]->count() > 0)
            @php $s=$cat['score']; $cc=$s>=80?'c-green':($s>=50?'c-amber':'c-red'); @endphp
            <div class="issue-group" data-reveal>
                <div class="ig-head">
                    <div class="ig-ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">{!! $cat['icon'] !!}</svg></div>
                    <span class="ig-name">{{ $cat['label'] }}</span>
                    <span class="ig-cnt">{{ $issuesByCategory[$key]->count() }} {{ $issuesByCategory[$key]->count() === 1 ? 'problemă' : 'probleme' }}</span>
                    <span class="ig-score {{ $cc }}">{{ $s }}/100</span>
                </div>
                <div class="issues">
                    @foreach($issuesByCategory[$key]->sortBy(fn($i)=>match($i->severity){'critical'=>0,'warning'=>1,default=>2}) as $issue)
                        @php
                            $issueId  = 'issue-' . $issue->id;
                            $impacts  = array_filter(array_map('trim', explode(',', $issue->impact ?? '')));
                            $stepData = $findSteps($issue->title);
                            $estTime  = $timeEst[$issue->severity] ?? '15–30 min';
                        @endphp
                        <div class="issue sev-{{ $issue->severity }}" id="{{ $issueId }}">
                            {{-- Header — mereu vizibil, click expandează --}}
                            <div class="issue-head" onclick="toggleIssue('{{ $issueId }}')">
                                <div class="issue-dot d-{{ $issue->severity }}"></div>
                                <div class="issue-head-body">
                                    <div class="issue-title">{{ $issue->title }}</div>
                                    <div class="issue-meta">
                                        {{-- Impact tags --}}
                                        @foreach($impacts as $imp)
                                            @php $cls = $impactCss[$imp] ?? 'impact-seo'; @endphp
                                            <span class="impact-tag {{ $cls }}">{{ $imp }}</span>
                                        @endforeach
                                        {{-- Timp estimat --}}
                                        <span class="issue-time">
                                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            {{ $estTime }}
                                        </span>
                                        {{-- URL afectat --}}
                                        @if($issue->affected_url)
                                            <span class="issue-url-tag" title="{{ $issue->affected_url }}">{{ Str::limit($issue->affected_url, 35) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="issue-toggle">
                                    <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            {{-- Body — expandabil --}}
                            <div class="issue-body">
                                {{-- Descriere problemă --}}
                                <div class="issue-desc">{{ $issue->description }}</div>

                                {{-- Pași de rezolvare --}}
                                @if($stepData)
                                    <div class="issue-steps">
                                        <div class="issue-steps-title">Cum rezolvi</div>
                                        @foreach($stepData['steps'] as $si => $step)
                                            <div class="issue-step">
                                                <div class="issue-step-num">{{ $si + 1 }}</div>
                                                <div class="issue-step-txt">{{ $step }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($issue->suggestion)
                                    <div class="issue-steps">
                                        <div class="issue-steps-title">Cum rezolvi</div>
                                        <div class="issue-step">
                                            <div class="issue-step-num">1</div>
                                            <div class="issue-step-txt">{{ $issue->suggestion }}</div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Verificare --}}
                                @if($stepData && isset($stepData['verify']))
                                    <div class="issue-verify">
                                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span><strong>Cum verifici că e rezolvat:</strong> {{ $stepData['verify'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    {{-- CTA --}}
    <div class="report-cta" data-reveal>
        <div class="report-cta-inner">
            <h3>Vrei sa rezolvam noi toate problemele?</h3>
            <p>Echipa Inovex.ro implementeaza toate imbunatatirile in 5-7 zile lucratoare, cu garantie.</p>
            <div class="report-cta-btns">
                <a href="https://inovex.ro/contact" target="_blank" class="btn btn-on-dark btn-lg">
                    Contacteaza Inovex.ro
                    <svg class="arrow" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="tel:+40750456096" class="btn btn-ghost-on-dark btn-lg">0750 456 096</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Scroll la issue și deschide-l ────────────────────────
function scrollToIssue(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('open');
    setTimeout(() => {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.style.outline = '2px solid var(--blue)';
        el.style.outlineOffset = '2px';
        setTimeout(() => { el.style.outline = ''; el.style.outlineOffset = ''; }, 2000);
    }, 100);
}

// ── Toggle issue expand/collapse ──────────────────────────
function toggleIssue(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle('open');
    // Scroll ușor în view dacă se deschide
    if (el.classList.contains('open')) {
        const rect = el.getBoundingClientRect();
        if (rect.top < 80) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Tastatura: Enter/Space pe issue-head deschide/închide
document.addEventListener('keydown', function(e) {
    if ((e.key === 'Enter' || e.key === ' ') && e.target.classList.contains('issue-head')) {
        e.preventDefault();
        const issue = e.target.closest('.issue');
        if (issue) issue.classList.toggle('open');
    }
});

// Accesibilitate: issue-head devine focusabil
document.querySelectorAll('.issue-head').forEach(el => {
    el.setAttribute('tabindex', '0');
    el.setAttribute('role', 'button');
});

// ── CWV Opportunities accordion ───────────────────────────
function toggleOpps(btn) {
    const body    = document.getElementById('cwvOppsBody');
    const chevron = btn.querySelector('.cwv-chevron');
    const open    = body.classList.toggle('open');
    chevron.style.transform = open ? 'rotate(180deg)' : '';
}
</script>
@endpush