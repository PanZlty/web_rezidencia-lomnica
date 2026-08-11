<?php
/**
 * Project logo and favicon assets stored in the WordPress Media Library.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Use the selected Lomnica favicon for WordPress core's site-icon output.
 */
add_filter('get_site_icon_url', static function ($url) {
    $rl_favicon_url = wp_get_attachment_url(2005);

    return $rl_favicon_url ?: $url;
}, 10, 1);

/**
 * Keep a direct favicon fallback for themes that do not render core site-icon tags.
 */
add_action('wp_head', static function () {
    if (is_admin()) {
        return;
    }

    $rl_favicon_url = wp_get_attachment_url(2005);

    if (!$rl_favicon_url) {
        return;
    }

    printf(
        '<link rel="icon" href="%1$s" type="image/jpeg" sizes="1600x1600" />%2$s',
        esc_url($rl_favicon_url),
        "\n"
    );
}, 1);

