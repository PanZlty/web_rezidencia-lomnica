<?php
/**
 * Plugin Name: Rezidencia Lomnica Loader
 * Description: Boots the project's custom WordPress functionality from MU plugins.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('rl_asset_url')) {
    function rl_asset_url($filename)
    {
        $filename = sanitize_file_name((string) $filename);
        $url = home_url('/wp-content/uploads/rezidencia-lomnica/' . $filename);

        return apply_filters('rezidencia_lomnica_asset_url', $url, $filename);
    }
}

$rezidencia_lomnica_modules = [
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/brand-tokens.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/image-map-shortcodes.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/image-map-list-pills.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/image-map-mobile.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/apartment-status-pills.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/apartment-status-pill-styles.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/construction-progress.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/termin-vystavby-styles.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/gallery-load-more.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/gallery-load-more-styles.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/gallery-load-more-script.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/form-security.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/disable-comments.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/analytics.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/image-map-css.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/image-map-styles.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/image-map-hover-guard.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/google-sheets-sync.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/legacy-storage-cleanup.php',
];

foreach ($rezidencia_lomnica_modules as $rezidencia_lomnica_module) {
    if (file_exists($rezidencia_lomnica_module)) {
        require_once $rezidencia_lomnica_module;
    }
}

