<?php
/**
 * Project-wide design tokens and status pill styles.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', static function () {
    if (is_admin()) {
        return;
    }

    wp_register_style('rezidencia-lomnica-brand-tokens', false, [], null);
    wp_enqueue_style('rezidencia-lomnica-brand-tokens');

    wp_add_inline_style('rezidencia-lomnica-brand-tokens', <<<'CSS'
:root {
  --primary: #1f4d3a;
  --primary-dark: #15382b;
  --tertiary: #c49a58;
  --tertiary-d-1: #a77a3d;
  --tertiary-d-2: #8f642f;
  --neutral-dark: #162520;
  --white: #fff;
  --text-dark: #24312c;
  --heading: #162520;
  --text: #4f5d56;
  --muted: #6b746f;
  --bg-muted: #f2f4f0;
  --status-available: #2e7d32;
  --status-reserved: #f59e0b;
  --status-sold: #d32f2f;
  --icon-gold: #c49a58;
  --radius-l: 2rem;
}

html,
body {
  background-color: var(--bg-muted);
  color: var(--text-dark);
}

.rs-status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2.4rem;
  padding: .55rem 1rem;
  border-radius: 999px;
  font-size: .85em;
  line-height: 1;
  white-space: nowrap;
}

.rs-status-pill[data-status="available"],
.rs-status-pill--available,
.status-available {
  color: #fff;
  background: var(--status-available);
}

.rs-status-pill[data-status="reserved"],
.rs-status-pill--reserved,
.status-reserved {
  color: #201600;
  background: var(--status-reserved);
}

.rs-status-pill[data-status="sold"],
.rs-status-pill--sold,
.status-sold {
  color: #fff;
  background: var(--status-sold);
}

@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    scroll-behavior: auto !important;
    transition-duration: .01ms !important;
    animation-duration: .01ms !important;
  }
}
CSS
    );
}, 110);
