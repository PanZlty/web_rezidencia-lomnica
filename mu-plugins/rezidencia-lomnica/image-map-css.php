<?php
/**
 * Optional Image Map Pro CSS module kept separate from the data logic.
 * The module is intentionally loaded through WordPress enqueue hooks.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', static function () {
    if (is_admin()) {
        return;
    }

    wp_register_style('rezidencia-lomnica-image-map-css', false, [], null);
    wp_enqueue_style('rezidencia-lomnica-image-map-css');
    wp_add_inline_style('rezidencia-lomnica-image-map-css', <<<'CSS'
/* The source module is intentionally empty; Image Map Pro owns its hover UI. */
CSS
    );
}, 100);
