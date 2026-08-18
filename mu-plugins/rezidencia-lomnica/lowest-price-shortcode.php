<?php
/**
 * Rezidencia Lomnica – shortcode [rs_lowest_available_price].
 *
 * Vypíše najnižšiu cenu spomedzi dostupných jednotiek, napr. "185 000 €".
 * Používa ho hero sekcia („Už od …“).
 *
 * Zdroj dát je Google Sheets. Frontend z tabuľky nečíta priamo – hodnoty do ACF
 * zapisuje google-sheets-sync.php každých 15 minút a tento modul číta tie isté
 * ACF polia (price / parking_price, status / parking_status) ako tooltipy mapy
 * aj [rs_apartment_price]. Priame volanie Sheets pri každom zobrazení stránky by
 * znamenalo externý HTTP request v render ceste a rozchádzajúce sa ceny na
 * jednej stránke; jediný zdroj pravdy tak ostáva tabuľka, cesta k nej je sync.
 *
 * Do úvahy sa berú iba jednotky so statusom "available" a vyplnenou cenou –
 * rovnaké pravidlo ako rs_imp_price_display(). Rezervované a predané byty cenu
 * na fronte neukazujú, takže nesmú ovplyvniť ani "už od".
 *
 * Atribúty:
 *   type  – apartment (predvolené), parking alebo all
 *   empty – čo vypísať, keď nie je dostupná ani jedna jednotka s cenou
 *
 * Výsledok je v transiente; maže ho sync po zmene (rezidencia_lomnica_units_synced)
 * aj uloženie bytu či parkovania v administrácii.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('rl_lowest_price_cache_key')) {
    function rl_lowest_price_cache_key($type) {
        return 'rl_lowest_price_' . sanitize_key($type);
    }
}

if (!function_exists('rl_lowest_price_types')) {
    /**
     * Vráti definície jednotiek, ktoré sa majú prehľadať.
     */
    function rl_lowest_price_types($type) {
        $definitions = function_exists('rs_imp_unit_definitions') ? rs_imp_unit_definitions() : array();
        if (empty($definitions)) {
            return array();
        }

        if ($type === 'all') {
            return $definitions;
        }

        if (function_exists('rs_imp_normalize_unit_type')) {
            $type = rs_imp_normalize_unit_type($type);
        }

        return isset($definitions[$type]) ? array($type => $definitions[$type]) : array();
    }
}

if (!function_exists('rl_lowest_price_value')) {
    /**
     * Najnižšia cena dostupnej jednotky ako float, alebo 0 keď taká nie je.
     */
    function rl_lowest_price_value($type = 'apartment') {
        $type = sanitize_key((string) $type);
        if ($type === '') {
            $type = 'apartment';
        }

        $cache_key = rl_lowest_price_cache_key($type);
        $cached    = get_transient($cache_key);
        if ($cached !== false) {
            return (float) $cached;
        }

        $lowest = 0.0;

        foreach (rl_lowest_price_types($type) as $definition) {
            $post_types = rs_imp_existing_post_types($definition['post_types']);
            if (empty($post_types)) {
                continue;
            }

            $posts = get_posts(array(
                'post_type'        => $post_types,
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
                'fields'           => 'ids',
                'suppress_filters' => false,
                'no_found_rows'    => true,
            ));

            foreach ($posts as $post_id) {
                $status = rs_imp_normalize_status(rs_imp_field($definition['status_field'], $post_id));
                if ($status !== 'available') {
                    continue;
                }

                $price = rs_imp_field($definition['price_field'], $post_id);
                if (is_array($price) || is_object($price)) {
                    continue;
                }

                $price = str_replace(',', '.', preg_replace('/[^0-9,.]/', '', (string) $price));
                if ($price === '' || !is_numeric($price)) {
                    continue;
                }

                $price = (float) $price;
                if ($price <= 0) {
                    continue;
                }

                if ($lowest === 0.0 || $price < $lowest) {
                    $lowest = $price;
                }
            }
        }

        $lowest = (float) apply_filters('rezidencia_lomnica_lowest_price', $lowest, $type);

        set_transient($cache_key, $lowest, 15 * MINUTE_IN_SECONDS);

        return $lowest;
    }
}

if (!function_exists('rl_lowest_price_flush')) {
    function rl_lowest_price_flush() {
        $types = array('all', 'apartment', 'parking');
        foreach ($types as $type) {
            delete_transient(rl_lowest_price_cache_key($type));
        }
    }
}
add_action('rezidencia_lomnica_units_synced', 'rl_lowest_price_flush');

if (!function_exists('rl_lowest_price_flush_on_save')) {
    function rl_lowest_price_flush_on_save($post_id) {
        if (!function_exists('rs_imp_unit_definition_for_post_type')) {
            return;
        }
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }
        if (rs_imp_unit_definition_for_post_type(get_post_type($post_id))) {
            rl_lowest_price_flush();
        }
    }
}
add_action('save_post', 'rl_lowest_price_flush_on_save');
add_action('deleted_post', 'rl_lowest_price_flush');

add_shortcode('rs_lowest_available_price', function ($atts) {
    if (!function_exists('rs_imp_unit_definitions') || !function_exists('rs_imp_price_text')) {
        return '';
    }

    $atts = shortcode_atts(
        array(
            'type'  => 'apartment',
            'empty' => '',
        ),
        $atts,
        'rs_lowest_available_price'
    );

    $lowest = rl_lowest_price_value($atts['type']);

    if ($lowest <= 0) {
        $fallback = (string) $atts['empty'];
        if ($fallback === '' && function_exists('rs_imp_price_placeholder')) {
            $fallback = rs_imp_price_placeholder();
        }

        return esc_html($fallback);
    }

    return esc_html(rs_imp_price_text($lowest));
});
