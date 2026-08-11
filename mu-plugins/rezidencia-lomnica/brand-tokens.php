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
  --primary: #4e707a;
  --primary-dark: #2e414a;
  --tertiary: #b86b50;
  --tertiary-d-1: #9a5641;
  --tertiary-d-2: #7f4637;
  --neutral-dark: #202a2f;
  --white: #fff;
  --text-dark: #2b3438;
  --heading: #202a2f;
  --text: #596469;
  --muted: #7d8788;
  --bg-muted: #f3f1eb;
  --surface-stone: #d9d9d2;
  --sandstone: #b9aa91;
  --status-available: #2f7d72;
  --status-reserved: #b97828;
  --status-sold: #a94f50;
  --icon-accent: #b9aa91;
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