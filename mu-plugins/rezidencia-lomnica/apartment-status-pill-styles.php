<?php
/**
 * Project status pill styles loaded as a MU-plugin module.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', static function () {
    if (is_admin()) {
        return;
    }

    wp_register_style('rezidencia-lomnica-apartment-status-pill-styles', false, [], null);
    wp_enqueue_style('rezidencia-lomnica-apartment-status-pill-styles');
    wp_add_inline_style('rezidencia-lomnica-apartment-status-pill-styles', <<<'CSS'
.rs-status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: fit-content;
  min-height: 3.2rem;
  padding: 0.7rem 1.2rem;
  border-radius: 999rem;
  color: var(--white, #ffffff);
  font-size: var(--text-s, 1.4rem);
  font-weight: 600;
  line-height: 1;
  white-space: nowrap;
}

.rs-status-pill--available,
.rs-status-pill[data-status="available"],
.status-available {
  background: var(--status-available, var(--available, #2f7d72));
}

.rs-status-pill--reserved,
.rs-status-pill[data-status="reserved"],
.status-reserved {
  background: var(--status-reserved, var(--reserved, #b97828));
}

.rs-status-pill--sold,
.rs-status-pill[data-status="sold"],
.status-sold {
  background: var(--status-sold, var(--sold, #a94f50));
}
CSS
    );
}, 100);