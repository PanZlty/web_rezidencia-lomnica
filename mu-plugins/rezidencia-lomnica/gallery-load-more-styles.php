<?php
/**
 * Gallery load-more styles loaded as a MU-plugin module.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', static function () {
    if (is_admin()) {
        return;
    }

    wp_register_style('rezidencia-lomnica-gallery-load-more-styles', false, [], null);
    wp_enqueue_style('rezidencia-lomnica-gallery-load-more-styles');
    wp_add_inline_style('rezidencia-lomnica-gallery-load-more-styles', <<<'CSS'
.rs-gallery-hidden {
  display: none !important;
}

.rs-gallery-loadmore {
  width: 100%;
}

.rs-gallery-loadmore-btn {
  margin-top: 3.2rem;
  margin-left: auto;
  margin-right: auto;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
CSS
    );
}, 100);
