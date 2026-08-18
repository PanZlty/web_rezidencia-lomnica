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
        if ($filename === '') {
            return '';
        }

        $url = rl_asset_attachment_url($filename);
        if ($url === '') {
            $url = home_url('/wp-content/uploads/rezidencia-lomnica/' . $filename);
        }

        return apply_filters('rezidencia_lomnica_asset_url', $url, $filename);
    }
}

if (!function_exists('rl_asset_attachment_url')) {
    /**
     * Ikonky sa nahrávajú do štandardnej mediatéky (dátumový priečinok
     * /uploads/rok/mesiac/), nie do /uploads/rezidencia-lomnica/, preto sa
     * URL dohľadáva podľa názvu súboru priamo v mediatéke.
     */
    function rl_asset_attachment_url($filename)
    {
        $cache_key = 'rl_asset_url_' . md5($filename);
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }

        global $wpdb;
        $guid = $wpdb->get_var($wpdb->prepare(
            "SELECT guid FROM {$wpdb->posts} WHERE post_type = 'attachment' AND guid LIKE %s ORDER BY ID DESC LIMIT 1",
            '%' . $wpdb->esc_like($filename)
        ));

        $url = $guid ? esc_url_raw($guid) : '';
        set_transient($cache_key, $url, DAY_IN_SECONDS);

        return $url;
    }
}

$rezidencia_lomnica_modules = [
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
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/contact-shortcodes.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/google-sheets-sync.php',
    WPMU_PLUGIN_DIR . '/rezidencia-lomnica/legacy-storage-cleanup.php',
];

foreach ($rezidencia_lomnica_modules as $rezidencia_lomnica_module) {
    if (file_exists($rezidencia_lomnica_module)) {
        require_once $rezidencia_lomnica_module;
    }
}

