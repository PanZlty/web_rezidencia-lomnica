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
  --primary: #142335;
  --primary-dark: #0e1928;
  --tertiary: #caad99;
  --tertiary-d-1: #a88873;
  --tertiary-d-2: #876956;
  --neutral-dark: #142335;
  --white: #fff;
  --text-dark: #263446;
  --heading: #142335;
  --text: #5d6874;
  --muted: #7d858d;
  --bg-muted: #f7f8ee;
  --surface-stone: #ebe4dc;
  --sandstone: #caad99;
  --status-available: #3c776b;
  --status-reserved: #a8762f;
  --status-sold: #9d5252;
  --icon-accent: #caad99;
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