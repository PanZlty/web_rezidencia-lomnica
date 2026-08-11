<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_head', function () {
    if (is_admin()) {
        return;
    }

    $website_id = (string) apply_filters('rezidencia_lomnica_analytics_website_id', '');
    $script_src = (string) apply_filters('rezidencia_lomnica_analytics_script_src', 'https://analytics.monaviza.com/script.js');

    if ('' === $website_id || '' === $script_src) {
        return;
    }
    ?>
    <script
        id="rl-analytics"
        defer
        src="<?php echo esc_url($script_src); ?>"
        data-website-id="<?php echo esc_attr($website_id); ?>"
    ></script>
    <?php
}, 99);
