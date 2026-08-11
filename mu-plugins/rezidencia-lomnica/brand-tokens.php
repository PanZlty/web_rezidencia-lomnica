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
  --primary: #556b6d;
  --primary-dark: #354447;
  --tertiary: #a3654f;
  --tertiary-d-1: #854d3c;
  --tertiary-d-2: #6a3a2f;
  --neutral-dark: #222624;
  --white: #fff;
  --text-dark: #303432;
  --heading: #222624;
  --text: #626865;
  --muted: #8c8c83;
  --bg-muted: #f6f2ea;
  --surface-stone: #e6ded2;
  --sandstone: #b8aa99;
  --status-available: #3c776b;
  --status-reserved: #a8762f;
  --status-sold: #9d5252;
  --icon-accent: #b8aa99;
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