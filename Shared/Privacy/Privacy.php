<?php
$effectiveDate = 'May 15, 2025';
$lastUpdated = 'May 15, 2025';
$siteUrl = 'www.hontoriaprinting.site';

if (!function_exists('hps_privacy_icon')) {
    function hps_privacy_icon(string $name): string
    {
        $icons = [
            'printer' => '<path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v7H6z"/><path d="M8 17h8"/>',
            'link' => '<path d="M10 13a5 5 0 0 0 7.1 0l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"/><path d="M14 11a5 5 0 0 0-7.1 0l-2 2a5 5 0 0 0 7.1 7.1l1.1-1.1"/>',
            'calendar' => '<path d="M8 2v4M16 2v4M3 10h18"/><path d="M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/>',
            'refresh' => '<path d="M21 12a9 9 0 0 1-15.3 6.4L3 16"/><path d="M3 21v-5h5"/><path d="M3 12A9 9 0 0 1 18.3 5.6L21 8"/><path d="M21 3v5h-5"/>',
            'globe' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/>',
            'shield-check' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-5"/>',
            'cookie' => '<path d="M21 12.8A9 9 0 1 1 11.2 3 4 4 0 0 0 16 8a4 4 0 0 0 5 4.8z"/><circle cx="8" cy="10" r="1"/><circle cx="12" cy="16" r="1"/><circle cx="8" cy="16" r="1"/>',
            'badge' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10M15 9.5A3 3 0 0 0 12 8c-1.7 0-3 1-3 2.3 0 3.4 6 1.4 6 5 0 1.2-1.3 2.2-3 2.2a3.5 3.5 0 0 1-3.3-1.8"/>',
            'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
            'clipboard' => '<path d="M9 4h6a2 2 0 0 1 2 2v1H7V6a2 2 0 0 1 2-2z"/><path d="M7 5H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><path d="M8 12h8M8 16h8"/>',
            'message' => '<path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.9 8.9 0 0 1-4-.9L3 21l1.8-4.6A8.5 8.5 0 1 1 21 11.5z"/>',
            'lock' => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/><path d="M12 15v2"/>',
            'scale' => '<path d="M12 3v18M5 6h14M6 6l-3 7h6L6 6zM18 6l-3 7h6l-3-7z"/><path d="M8 21h8"/>',
            'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.3 19.3 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2z"/>',
            'map' => '<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
            'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        ];

        $path = $icons[$name] ?? $icons['shield'];
        return '<svg class="icon" aria-hidden="true" viewBox="0 0 24 24">' . $path . '</svg>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy | Hontoria Printing Services</title>
  <meta name="description" content="Privacy Policy for Hontoria Printing Services.">
  <style>
    :root {
      --yellow-500: #ffcc00;
      --yellow-600: #f6b900;
      --red-600: #ef233c;
      --red-700: #df1d12;
      --red-800: #b60d00;
      --dark: #0a0a0a;
      --brown: #1a0700;
      --ink: #160700;
      --muted: #6b7280;
      --line: #e5e7eb;
      --paper: #ffffff;
      --soft: #f8fafc;
      --shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
      --radius: 8px;
      font-family: "Montserrat", "Poppins", "Arial", sans-serif;
      color: var(--ink);
      background: var(--paper);
    }

    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      margin: 0;
      background:
        linear-gradient(90deg, rgba(15, 23, 42, 0.06) 1px, transparent 1px),
        linear-gradient(180deg, rgba(15, 23, 42, 0.06) 1px, transparent 1px),
        var(--paper);
      background-size: 48px 48px;
      color: var(--ink);
    }
    body::before {
      position: fixed;
      inset: 0;
      z-index: -1;
      background:
        radial-gradient(circle at 5% 85%, rgba(255, 204, 0, 0.14), transparent 24rem),
        radial-gradient(circle at 92% 20%, rgba(223, 29, 18, 0.1), transparent 26rem);
      content: "";
    }
    a { color: var(--red-700); text-underline-offset: 0.18em; }
    a:hover { color: var(--red-800); }
    button, a { -webkit-tap-highlight-color: transparent; }
    code {
      border-radius: 4px;
      background: #fff3bf;
      color: var(--red-800);
      font-size: 0.95em;
      padding: 0.1rem 0.28rem;
    }
    .icon {
      width: 1.15em;
      height: 1.15em;
      fill: none;
      stroke: currentColor;
      stroke-linecap: round;
      stroke-linejoin: round;
      stroke-width: 2;
    }
    .skip-link {
      position: absolute;
      left: 1rem;
      top: 0.75rem;
      z-index: 20;
      border-radius: 6px;
      background: var(--ink);
      color: #fff;
      padding: 0.7rem 1rem;
      transform: translateY(-140%);
      transition: transform 160ms ease;
    }
    .skip-link:focus { transform: translateY(0); }
    .privacy-hero {
      position: relative;
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(300px, 0.78fr);
      align-items: center;
      gap: clamp(2rem, 5vw, 4rem);
      width: 100%;
      min-height: 340px;
      margin: 0;
      padding: clamp(3rem, 6vw, 5rem) max(32px, calc((100vw - 1180px) / 2));
      overflow: hidden;
      background:
        linear-gradient(90deg, rgba(255, 204, 0, 0.08) 1px, transparent 1px),
        linear-gradient(180deg, rgba(255, 204, 0, 0.08) 1px, transparent 1px),
        #060606;
      background-size: 40px 40px;
    }
    .privacy-hero::after {
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 82% 24%, rgba(223, 29, 18, 0.18), transparent 22rem);
      content: "";
      pointer-events: none;
    }
    .privacy-hero-copy,
    .privacy-hero-panel {
      position: relative;
      z-index: 1;
    }
    .privacy-eyebrow {
      display: inline-flex;
      align-items: center;
      width: fit-content;
      margin: 0 0 1rem;
      color: var(--yellow-500);
      font-size: 0.8rem;
      font-weight: 900;
      letter-spacing: 0.36em;
      line-height: 1;
      padding: 0;
      text-transform: uppercase;
    }
    .privacy-hero h1 {
      max-width: 700px;
      margin: 0;
      color: #fff;
      font-family: "Arial Black", "Montserrat", sans-serif;
      font-size: clamp(3rem, 6vw, 5.2rem);
      line-height: 0.92;
      text-transform: uppercase;
    }
    .privacy-hero h1 span { color: var(--red-700); }
    .privacy-hero-text {
      max-width: 670px;
      margin: 1.35rem 0 0;
      color: #d1d5db;
      font-size: clamp(1rem, 1.35vw, 1.16rem);
      line-height: 1.72;
    }
    .policy-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      margin: 1.55rem 0 0;
    }
    .policy-meta span {
      display: inline-flex;
      align-items: center;
      gap: 0.48rem;
      min-height: 36px;
      border: 1px solid rgba(255, 204, 0, 0.3);
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      color: #fff7d1;
      font-size: 0.88rem;
      font-weight: 800;
      padding: 0.45rem 0.76rem;
    }
    .privacy-hero-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      margin-top: 1.5rem;
    }
    .icon-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.48rem;
      min-height: 46px;
      border: 2px solid #050505;
      border-radius: 8px;
      background: var(--yellow-500);
      color: #050505;
      cursor: pointer;
      font: inherit;
      font-size: 0.86rem;
      font-weight: 950;
      letter-spacing: 0.04em;
      padding: 0.72rem 1rem;
      text-transform: uppercase;
      transition: transform 160ms ease, box-shadow 160ms ease, background 160ms ease;
    }
    .icon-button:hover {
      background: #ffe066;
      box-shadow: 6px 6px 0 #050505;
      transform: translateY(-1px);
    }
    .icon-button--dark {
      border-color: #fff;
      background: var(--red-700);
      color: #fff;
    }
    .icon-button--dark:hover { background: #ff2d1d; }
    .summary-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 0.85rem;
      margin-top: 0;
    }
    .summary-item {
      min-height: auto;
      border: 1px solid rgba(255, 204, 0, 0.24);
      border-left: 6px solid var(--yellow-500);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.06);
      padding: 1rem;
    }
    .summary-item .icon {
      width: 25px;
      height: 25px;
      color: var(--yellow-500);
    }
    .summary-item h2 {
      margin: 0.7rem 0 0.3rem;
      color: #fff;
      font-size: 0.98rem;
      line-height: 1.18;
      text-transform: uppercase;
    }
    .summary-item p {
      margin: 0;
      color: #d1d5db;
      font-size: 0.9rem;
      line-height: 1.45;
    }
    .purpose-grid .icon { width: 25px; height: 25px; color: var(--yellow-500); }
    .policy-layout {
      display: grid;
      grid-template-columns: 245px minmax(0, 1fr);
      gap: clamp(1.8rem, 4vw, 3.25rem);
      width: min(1360px, calc(100% - 32px));
      margin: 0 auto;
      padding: clamp(2.4rem, 5vw, 4.4rem) 0;
    }
    .policy-sidebar {
      position: sticky;
      top: 1rem;
      align-self: start;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: #fff;
      box-shadow: none;
      padding: 1rem;
    }
    .policy-sidebar p {
      margin: 0 0 0.85rem;
      color: #64748b;
      font-size: 0.78rem;
      font-weight: 900;
      letter-spacing: 0.32em;
      text-transform: uppercase;
    }
    .policy-sidebar ol {
      display: grid;
      gap: 0.25rem;
      margin: 0;
      padding: 0;
      list-style: none;
    }
    .policy-sidebar a {
      display: block;
      border-radius: 8px;
      color: #111827;
      font-size: 0.84rem;
      font-weight: 900;
      line-height: 1.2;
      padding: 0.68rem 0.78rem;
      text-decoration: none;
      transition: background 160ms ease, color 160ms ease, transform 160ms ease;
    }
    .policy-sidebar a:hover, .policy-sidebar a.is-active {
      outline: 2px solid #050505;
      background: #fff0ed;
      color: var(--red-700);
      transform: translateX(2px);
    }
    .policy-document {
      min-width: 0;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: #fff;
      box-shadow: var(--shadow);
    }
    .policy-section {
      scroll-margin-top: 1.25rem;
      border-bottom: 1px solid var(--line);
      padding: clamp(1.35rem, 4vw, 2.4rem);
    }
    .policy-section:last-child { border-bottom: 0; }
    .section-heading {
      display: flex;
      align-items: flex-start;
      gap: 0.95rem;
      margin-bottom: 1.2rem;
    }
    .section-heading > span {
      display: inline-grid;
      width: 48px;
      height: 48px;
      flex: 0 0 auto;
      place-items: center;
      border: 3px solid var(--red-800);
      border-radius: 8px;
      background: var(--yellow-500);
      color: var(--ink);
      font-weight: 950;
    }
    .section-heading p {
      margin: 0 0 0.2rem;
      color: var(--red-700);
      font-size: 0.78rem;
      font-weight: 900;
      line-height: 1.2;
      text-transform: uppercase;
    }
    .section-heading h2 {
      margin: 0;
      color: var(--ink);
      font-family: "Arial Black", "Montserrat", sans-serif;
      font-size: clamp(1.55rem, 3vw, 2.25rem);
      line-height: 1.08;
      text-transform: uppercase;
    }
    .policy-section h3 {
      margin: 1.45rem 0 0.65rem;
      color: var(--red-800);
      font-size: 1.15rem;
      line-height: 1.2;
    }
    .policy-section h4 {
      margin: 0 0 0.5rem;
      color: var(--ink);
      font-size: 1rem;
    }
    .policy-section p, .policy-section li, .policy-section td {
      color: #5f6b7a;
      font-size: 1rem;
      line-height: 1.75;
    }
    .policy-section p { margin: 0.8rem 0 0; }
    .check-list {
      display: grid;
      gap: 0.75rem;
      margin: 0.95rem 0 0;
      padding: 0;
      list-style: none;
    }
    .check-list li {
      position: relative;
      border-left: 5px solid var(--yellow-500);
      border-radius: 6px;
      background: #fff9df;
      padding: 0.8rem 0.95rem 0.8rem 1rem;
    }
    .code-block {
      overflow-x: auto;
      margin: 1rem 0 0;
      border-left: 6px solid var(--red-700);
      border-radius: 8px;
      background: #0a0a0a;
      color: #ffefba;
      padding: 1rem;
    }
    .code-block code {
      background: transparent;
      color: inherit;
      font-family: "Cascadia Mono", "SFMono-Regular", Consolas, monospace;
      font-size: 0.92rem;
      line-height: 1.65;
      padding: 0;
    }
    .purpose-grid, .retention-list, .contact-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 0.9rem;
      margin-top: 1rem;
    }
    .purpose-grid article, .retention-list article, .contact-grid a, .contact-grid div {
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: #fff;
      padding: 1rem;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    }
    .purpose-grid article { min-height: 162px; }
    .purpose-grid h3 { margin-top: 0.65rem; }
    .purpose-grid p, .retention-list p { margin-top: 0.35rem; }
    .rights-table-wrap {
      overflow-x: auto;
      margin-top: 1rem;
      border: 1px solid var(--line);
      border-radius: var(--radius);
    }
    table {
      width: 100%;
      min-width: 760px;
      border-collapse: collapse;
      background: var(--paper);
    }
    th, td {
      border-bottom: 1px solid var(--line);
      padding: 0.95rem;
      text-align: left;
      vertical-align: top;
    }
    th {
      background: var(--brown);
      color: var(--yellow-500);
      font-size: 0.88rem;
      text-transform: uppercase;
    }
    tbody tr:nth-child(even) { background: #fff9df; }
    tbody tr:last-child td { border-bottom: 0; }
    td:first-child {
      color: var(--ink);
      font-weight: 900;
    }
    .contact-section { background: linear-gradient(135deg, #fff9df, #ffffff); }
    .contact-grid a, .contact-grid div {
      display: flex;
      align-items: flex-start;
      gap: 0.8rem;
      color: var(--ink);
      text-decoration: none;
    }
    .contact-grid .icon {
      width: 24px;
      height: 24px;
      flex: 0 0 auto;
      color: var(--red-700);
    }
    .contact-grid strong {
      display: block;
      margin-bottom: 0.2rem;
      color: var(--ink);
    }
    .consent-section { background: var(--brown); }
    .consent-section .section-heading h2, .consent-section .section-heading p, .consent-section p { color: #fff8df; }
    .consent-section .section-heading > span {
      border-color: var(--yellow-500);
      background: var(--yellow-500);
    }
    .final-note {
      display: inline-flex;
      align-items: center;
      gap: 0.7rem;
      margin-top: 1.1rem;
      border: 1px solid rgba(255, 255, 255, 0.28);
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.1);
      color: #fff8df;
      font-weight: 850;
      padding: 0.85rem 1rem;
    }
    .final-note .icon {
      width: 22px;
      height: 22px;
      color: var(--yellow-500);
    }
    .toast {
      position: fixed;
      right: 1rem;
      bottom: 1rem;
      z-index: 30;
      border-radius: 8px;
      background: var(--ink);
      color: #fff8df;
      font-weight: 800;
      padding: 0.8rem 1rem;
      box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
      opacity: 0;
      pointer-events: none;
      transform: translateY(18px);
      transition: opacity 180ms ease, transform 180ms ease;
    }
    .toast.is-visible {
      opacity: 1;
      transform: translateY(0);
    }
    @media (max-width: 980px) {
      .privacy-hero,
      .policy-layout { grid-template-columns: 1fr; }
      .policy-sidebar { position: relative; top: auto; }
      .policy-sidebar ol {
        display: flex;
        overflow-x: auto;
        padding-bottom: 0.2rem;
        scrollbar-width: thin;
      }
      .policy-sidebar li { flex: 0 0 auto; }
      .policy-sidebar a { white-space: nowrap; }
    }
    @media (max-width: 720px) {
      .privacy-hero {
        padding-left: 20px;
        padding-right: 20px;
      }
      .privacy-hero-actions { width: 100%; }
      .icon-button { flex: 1 1 150px; }
      .purpose-grid, .retention-list, .contact-grid { grid-template-columns: 1fr; }
      .purpose-grid article { min-height: auto; }
      .section-heading { gap: 0.7rem; }
      .section-heading > span { width: 42px; height: 42px; }
      .policy-section p, .policy-section li, .policy-section td { font-size: 0.97rem; }
    }
    @media print {
      body { background: #fff; }
      body::before, .privacy-hero, .policy-sidebar, .toast { display: none; }
      .policy-document, .policy-section { border: 0; box-shadow: none; }
      .policy-layout {
        display: block;
        width: 100%;
        padding: 0;
      }
      .purpose-grid, .retention-list, .contact-grid { grid-template-columns: 1fr; }
      .policy-section { break-inside: avoid; }
    }
  </style>
</head>
<body>
  <a class="skip-link" href="#policy">Skip to policy</a>

  <section class="privacy-hero" aria-labelledby="page-title">
    <div class="privacy-hero-copy">
      <p class="privacy-eyebrow">Data Privacy Notice</p>
      <h1 id="page-title">Privacy <span>Policy</span></h1>
      <p class="privacy-hero-text">
        Hontoria Printing Services explains how personal information is collected, used, protected, retained,
        and handled under the Philippine Data Privacy Act of 2012.
      </p>

      <div class="policy-meta" aria-label="Policy dates and website">
        <span><?= hps_privacy_icon('calendar'); ?> Effective <?= htmlspecialchars($effectiveDate, ENT_QUOTES, 'UTF-8'); ?></span>
        <span><?= hps_privacy_icon('refresh'); ?> Last updated <?= htmlspecialchars($lastUpdated, ENT_QUOTES, 'UTF-8'); ?></span>
        <span><?= hps_privacy_icon('globe'); ?> <?= htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?></span>
      </div>

      <div class="privacy-hero-actions">
        <button class="icon-button" type="button" data-print-policy>
          <?= hps_privacy_icon('printer'); ?>
          <span>Print</span>
        </button>
        <button class="icon-button icon-button--dark" type="button" data-copy-link>
          <?= hps_privacy_icon('link'); ?>
          <span>Copy Link</span>
        </button>
      </div>
    </div>

    <div class="privacy-hero-panel" aria-label="Privacy highlights">
      <div class="summary-grid">
        <article class="summary-item">
          <?= hps_privacy_icon('shield-check'); ?>
          <h2>RA 10173 Aligned</h2>
          <p>Policy terms are framed around the Philippine Data Privacy Act of 2012.</p>
        </article>
        <article class="summary-item">
          <?= hps_privacy_icon('cookie'); ?>
          <h2>Essential Cookies</h2>
          <p>Session cookies support secure login and expire when the browser closes.</p>
        </article>
        <article class="summary-item">
          <?= hps_privacy_icon('badge'); ?>
          <h2>No Data Sales</h2>
          <p>Personal information is not sold, rented, leased, or traded to third parties.</p>
        </article>
      </div>
    </div>
  </section>

  <main id="policy" class="policy-layout">
    <aside class="policy-sidebar" aria-label="Privacy policy sections">
      <p>Policy Sections</p>
      <ol>
        <li><a href="#introduction">Introduction</a></li>
        <li><a href="#information">Information We Collect</a></li>
        <li><a href="#purposes">Purposes of Processing</a></li>
        <li><a href="#security">Data Storage &amp; Security</a></li>
        <li><a href="#sharing">Sharing &amp; Disclosure</a></li>
        <li><a href="#rights">Your Rights</a></li>
        <li><a href="#cookies">Cookies</a></li>
        <li><a href="#children">Children's Privacy</a></li>
        <li><a href="#contact">Contact</a></li>
        <li><a href="#changes">Changes</a></li>
        <li><a href="#consent">Consent</a></li>
      </ol>
    </aside>

    <article class="policy-document" aria-label="Privacy Policy">
      <section id="introduction" class="policy-section">
        <div class="section-heading"><span>01</span><div><p>Overview</p><h2>Introduction</h2></div></div>
        <p>
          Hontoria Printing Services ("we", "us", or "our") operates the websites
          <a href="https://www.hontoriaprinting.site">https://www.hontoriaprinting.site</a> and
          <a href="https://internal.hontoriaprinting.site">https://internal.hontoriaprinting.site</a>, including
          all related pages, features, and services (collectively, the "Websites"). This Privacy Policy explains how
          we collect, use, disclose, and protect your personal information in accordance with the Philippine Data
          Privacy Act of 2012 (Republic Act No. 10173) and its implementing rules and regulations.
        </p>
        <p>
          By accessing or using our Websites, you acknowledge that you have read, understood, and agree to the terms
          of this Privacy Policy. If you do not agree, please discontinue use of our Websites.
        </p>
      </section>

      <section id="information" class="policy-section">
        <div class="section-heading"><span>02</span><div><p>Collection</p><h2>Personal Information We Collect</h2></div></div>
        <h3>2.1 Information You Provide Voluntarily</h3>
        <ul class="check-list">
          <li><strong>Account Creation (Staff/Internal Users):</strong> When an administrator creates a user account for you, we collect your first name, middle name (optional), last name, phone number, and email address.</li>
          <li><strong>Order Placement:</strong> When an order is created, whether by staff or through a customer's request, we collect a customer identifier. This can be a real name, nickname, group or institution name, or any label the customer chooses to identify themselves. We also collect a messenger group chat invite link, typically a Facebook Messenger link, to facilitate communication about the order or service.</li>
          <li><strong>Profile Image (optional):</strong> Users may upload a photo of themselves or another image to be associated with their account. This image may or may not depict the user.</li>
        </ul>
        <h3>2.2 Information Collected Automatically</h3>
        <p>Our Websites use session cookies, which are essential cookies, to maintain your login session and ensure the security of your browsing experience. These cookies are strictly necessary for the proper functioning of the Websites and do not track you across other sites.</p>
        <pre class="code-block"><code>session_set_cookie_params([
  'lifetime' =&gt; 0,
  'path' =&gt; '/',
  'domain' =&gt; '.hontoriaprinting.site',
  'secure' =&gt; true,
  'httponly' =&gt; true,
  'samesite' =&gt; 'Lax',
]);</code></pre>
        <p>We do not use tracking cookies, analytics cookies, or third-party advertising cookies at this time. However, our hosting provider Hostinger and security/CDN provider Cloudflare may automatically log certain technical information such as your IP address, browser type, and access times for security and performance monitoring. This information is not used by us to identify individual visitors.</p>
        <h3>2.3 Sensitive Personal Information</h3>
        <p>We do not knowingly collect sensitive personal information, such as government-issued IDs, health data, or financial information, through our Websites. The user-uploaded profile image is not intended to convey sensitive data, and you should avoid uploading images that contain such information.</p>
      </section>

      <section id="purposes" class="policy-section">
        <div class="section-heading"><span>03</span><div><p>Use</p><h2>Purposes of Processing</h2></div></div>
        <p>We process the personal information we collect for the following purposes:</p>
        <div class="purpose-grid">
          <article><?= hps_privacy_icon('users'); ?><h3>Account Administration</h3><p>To create, manage, and maintain user accounts for staff, including role and permission assignments.</p></article>
          <article><?= hps_privacy_icon('clipboard'); ?><h3>Order Management</h3><p>To identify customer orders, communicate through messenger links, and maintain order records.</p></article>
          <article><?= hps_privacy_icon('message'); ?><h3>Communication</h3><p>To contact staff or customers for order-related or service-related matters.</p></article>
          <article><?= hps_privacy_icon('lock'); ?><h3>Security &amp; Compliance</h3><p>To protect the Websites and users by detecting unauthorized access or illegal activities.</p></article>
          <article><?= hps_privacy_icon('scale'); ?><h3>Legal Obligations</h3><p>To comply with applicable laws, regulations, and lawful requests from authorities.</p></article>
        </div>
        <p>We do not use personal information for secondary purposes such as marketing or advertising, and we do not sell, rent, or trade personal information to third parties.</p>
      </section>

      <section id="security" class="policy-section">
        <div class="section-heading"><span>04</span><div><p>Protection</p><h2>Data Storage &amp; Security</h2></div></div>
        <h3>4.1 Storage</h3>
        <p>All personal information is stored on secure servers provided by Hostinger, our web hosting provider. Our Websites are further protected by Cloudflare for DNS management, SSL/TLS encryption, and DDoS mitigation.</p>
        <h3>4.2 Security Measures</h3>
        <ul class="check-list">
          <li><strong>Encryption:</strong> Passwords are hashed using <code>password_hash()</code> with the bcrypt algorithm. All data transmitted between your browser and our servers is encrypted over HTTPS.</li>
          <li><strong>Access Controls:</strong> Role-based permission systems restrict access to personal data to authorized staff members only.</li>
          <li><strong>Regular Backups:</strong> Hostinger performs weekly backups of our data to prevent loss.</li>
          <li><strong>Software Protections:</strong> We have implemented protections against common web vulnerabilities such as Cross-Site Scripting (XSS), Cross-Site Request Forgery (CSRF), and SQL injection.</li>
        </ul>
        <h3>4.3 Retention</h3>
        <div class="retention-list">
          <article><h4>User Account Information</h4><p>Retained indefinitely while the account is active. Upon deletion of the account, the associated personal information is removed from the live system, though residual copies may persist in encrypted backups until they are rotated.</p></article>
          <article><h4>Order Information</h4><p>Customer names or identifiers and associated messenger links are retained indefinitely, including after order completion or deletion, as part of the permanent order archive. If you wish to have your data removed from the archive, please contact us using the information in Section 9.</p></article>
        </div>
      </section>

      <section id="sharing" class="policy-section">
        <div class="section-heading"><span>05</span><div><p>Disclosure</p><h2>Data Sharing &amp; Disclosure</h2></div></div>
        <p>We do not sell, rent, or lease your personal information to third parties. We may share your information only in limited circumstances:</p>
        <ul class="check-list">
          <li><strong>Service Providers:</strong> Hostinger and Cloudflare process data on our behalf as part of the services they provide. These providers are contractually obligated to protect your data and process it only according to our instructions.</li>
          <li><strong>Legal Requirements:</strong> We may disclose personal information if required by law or in response to valid requests by public authorities, such as a court or government agency.</li>
          <li><strong>Business Transfers:</strong> In the unlikely event of a merger, acquisition, or sale of all or a portion of our assets, your personal information may be transferred as part of that transaction. You will be notified via a prominent notice on our Websites.</li>
        </ul>
        <h3>5.1 Cross-Border Data Transfer</h3>
        <p>Our Websites are hosted on servers that may be located within or outside the Philippines. While we have no intention of transferring personal data outside the country, the use of Hostinger and Cloudflare means that data may be processed in jurisdictions where these providers maintain data centers. We take steps to ensure that any such transfer complies with the Data Privacy Act and that your information receives an adequate level of protection.</p>
      </section>

      <section id="rights" class="policy-section">
        <div class="section-heading"><span>06</span><div><p>Data Subjects</p><h2>Your Rights Under the Data Privacy Act</h2></div></div>
        <p>As a data subject, you have the following rights under Philippine law. To exercise any of these rights, please reach out using the contact information in Section 9. We will respond within the timeframes required by law.</p>
        <div class="rights-table-wrap">
          <table>
            <thead><tr><th>Right</th><th>Description</th><th>How to Exercise</th></tr></thead>
            <tbody>
              <tr><td>Right to be informed</td><td>You have the right to know what personal data we collect, how we use it, and your rights.</td><td>This Privacy Policy serves to inform you.</td></tr>
              <tr><td>Right to access</td><td>You may request access to the personal data we hold about you.</td><td>Staff may log in to view account information. Customers may view order details on the Order Tracking page.</td></tr>
              <tr><td>Right to object</td><td>You may object to processing if you believe it is no longer necessary or is being misused.</td><td>Contact us via the details in Section 9.</td></tr>
              <tr><td>Right to erasure or blocking</td><td>You may request deletion or blocking of your personal data from our systems.</td><td>Staff accounts may be deleted by an administrator. For order data, contact us; order archives may still retain the customer name.</td></tr>
              <tr><td>Right to damages</td><td>You have the right to claim damages if you suffer harm due to a Data Privacy Act violation.</td><td>Contact us and consult legal counsel.</td></tr>
              <tr><td>Right to data portability</td><td>You may request a copy of your personal data in a commonly used electronic format.</td><td>Staff may view account data; customers may view order details. Contact us for a portable copy.</td></tr>
              <tr><td>Right to rectification</td><td>You may request correction of inaccurate or incomplete personal data.</td><td>Staff may update their own name, phone, and email. Customers may contact us for assistance with an existing order.</td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <section id="cookies" class="policy-section">
        <div class="section-heading"><span>07</span><div><p>Technology</p><h2>Cookies and Similar Technologies</h2></div></div>
        <p>Our Websites use session cookies, which are essential cookies, to maintain your login session and ensure the security of your browsing experience. These cookies are placed on your device when you log in and are automatically deleted when you close your browser. They do not track your activity across other websites and are not used for advertising or analytics.</p>
        <p>We do not currently use third-party analytics services such as Google Analytics or Facebook Pixel, but we reserve the right to do so in the future. Should we implement such services, we will update this Privacy Policy and, where required, obtain your consent.</p>
        <p>You can configure your browser to reject cookies, but this may affect Website functionality, such as staying logged in.</p>
      </section>

      <section id="children" class="policy-section">
        <div class="section-heading"><span>08</span><div><p>Age</p><h2>Children's Privacy</h2></div></div>
        <p>Our internal staff website is intended for use only by employees of Hontoria Printing Services who are verified to be of legal working age. We do not knowingly collect personal information from individuals under 18 years of age through the public website. If we become aware that a child under 18 has provided us with personal information without verifiable parental consent, we will take steps to delete such information.</p>
      </section>

      <section id="contact" class="policy-section contact-section">
        <div class="section-heading"><span>09</span><div><p>Support</p><h2>Contact Information</h2></div></div>
        <p>If you have questions, concerns, or requests regarding this Privacy Policy or your personal data, you may contact us through the options below.</p>
        <div class="contact-grid">
          <a href="https://www.facebook.com/jhong.hontoria.3"><?= hps_privacy_icon('message'); ?><span><strong>Facebook Page</strong>facebook.com/jhong.hontoria.3</span></a>
          <div><?= hps_privacy_icon('phone'); ?><span><strong>Phone</strong>Available upon request via the Facebook page</span></div>
          <div><?= hps_privacy_icon('map'); ?><span><strong>In Person</strong>Feeder Road 2, Brgy. Tibal-og, Santo Tomas, Davao del Norte</span></div>
        </div>
        <p>While we do not yet have a dedicated Data Protection Officer (DPO), all privacy-related inquiries will be handled by the business owner. We are committed to addressing your concerns in a timely and transparent manner.</p>
      </section>

      <section id="changes" class="policy-section">
        <div class="section-heading"><span>10</span><div><p>Updates</p><h2>Changes to This Privacy Policy</h2></div></div>
        <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page with a revised effective date. We encourage you to review this Privacy Policy periodically. Your continued use of our Websites after any modifications indicates your acceptance of the updated terms.</p>
      </section>

      <section id="consent" class="policy-section consent-section">
        <div class="section-heading"><span>11</span><div><p>Agreement</p><h2>Consent</h2></div></div>
        <p>By using our Websites, you consent to the collection, use, and disclosure of your personal information as described in this Privacy Policy.</p>
        <div class="final-note">
          <?= hps_privacy_icon('shield'); ?>
          <span>Last Updated: <?= htmlspecialchars($lastUpdated, ENT_QUOTES, 'UTF-8'); ?> | <?= htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
      </section>
    </article>
  </main>

  <div class="toast" role="status" aria-live="polite" data-toast>Link copied</div>

  <script>
    const printButton = document.querySelector("[data-print-policy]");
    const copyButton = document.querySelector("[data-copy-link]");
    const toast = document.querySelector("[data-toast]");
    const navLinks = Array.from(document.querySelectorAll(".policy-sidebar a"));
    const sections = navLinks
      .map((link) => document.querySelector(link.getAttribute("href")))
      .filter(Boolean);

    function setActiveLink() {
      const activeSection = sections.reduce((current, section) => {
        const top = section.getBoundingClientRect().top;
        return top <= 140 ? section : current;
      }, sections[0]);

      navLinks.forEach((link) => {
        link.classList.toggle("is-active", link.getAttribute("href") === `#${activeSection.id}`);
      });
    }

    function showToast(message) {
      if (!toast) return;
      toast.textContent = message;
      toast.classList.add("is-visible");
      window.clearTimeout(showToast.timeout);
      showToast.timeout = window.setTimeout(() => {
        toast.classList.remove("is-visible");
      }, 2200);
    }

    printButton?.addEventListener("click", () => window.print());

    copyButton?.addEventListener("click", async () => {
      try {
        await navigator.clipboard.writeText(window.location.href);
        showToast("Policy link copied");
      } catch {
        showToast("Copy unavailable");
      }
    });

    window.addEventListener("scroll", setActiveLink, { passive: true });
    window.addEventListener("resize", setActiveLink);
    window.addEventListener("load", setActiveLink);
  </script>
</body>
</html>
