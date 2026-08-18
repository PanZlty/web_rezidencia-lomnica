<?php
/**
 * Rezidencia Lomnica – Google Sheets sync (cena + status jednotiek).
 *
 * Každých 15 minút číta dáta z Google Sheets a zapisuje ich do ACF podľa kódu
 * jednotky (apartment_code pre byty, parking_number pre parkovanie).
 * Frontend naďalej číta z ACF, preto sa zmena v tabuľke po najbližšej
 * synchronizácii prejaví aj na stránke.
 *
 * Stĺpce tabuľky:
 *   A id           – kód jednotky (AP-01, P01, …)
 *   B area_total   – úžitková plocha v m²
 *   C balcony_area – terasa / balkón v m²
 *   D price        – cena
 *   E status       – Dostupný / Rezervovaný / Predaný
 * Poradie aj ďalšie stĺpce definuje rs_gsheets_columns() – mení sa iba tam.
 * Rozhoduje pozícia stĺpca, nie text v hlavičke; prvý riadok sa preskakuje.
 *
 * Tabuľka je zdroj pravdy: prázdna bunka hodnotu v ACF vymaže, ale iba ak má
 * daný stĺpec aspoň jednu vyplnenú bunku. Prázdny stĺpec sa berie ako
 * „zatiaľ sa nepoužíva“ a nechá ACF na pokoji, takže pridanie novej hlavičky
 * nič nezmaže. Mazanie sa dá vypnúť filtrom
 * rezidencia_lomnica_gsheets_clear_empty (dostáva názov poľa).
 * Cena sa na fronte zobrazuje iba pri statuse "available"; toto pravidlo rieši
 * rs_imp_price_display() v image-map-shortcodes.php, nie tento modul.
 *
 * Bez API kľúča použije verejný gviz JSON endpoint, ktorý funguje pre tabuľky
 * zdieľané ako „Verejná – každý s odkazom (Viewer)“. Voliteľne je možné doplniť
 * API kľúč cez konštantu RS_GSHEETS_API_KEY alebo filter
 * rezidencia_lomnica_gsheets_api_key; vtedy sa použije oficiálne Sheets API v4.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('RS_GSHEETS_SPREADSHEET_ID')) {
    define('RS_GSHEETS_SPREADSHEET_ID', '1T6QkwyxRvDkA7kHcJaJyA5DrHPYZBYDTBN6OLSq6IAk');
}

if (!defined('RS_GSHEETS_RANGE')) {
    define('RS_GSHEETS_RANGE', 'Hárok1!A1:E200');
}

if (!defined('RS_GSHEETS_SYNC_INTERVAL')) {
    define('RS_GSHEETS_SYNC_INTERVAL', 15 * MINUTE_IN_SECONDS);
}

if (!function_exists('rs_gsheets_sync_interval')) {
    function rs_gsheets_sync_interval($schedules) {
        $schedules['rs_gsheets_15min'] = array(
            'interval' => (int) RS_GSHEETS_SYNC_INTERVAL,
            'display'  => 'Rezidencia Lomnica: Google Sheets sync (15 min)',
        );
        return $schedules;
    }
}
add_filter('cron_schedules', 'rs_gsheets_sync_interval');

if (!function_exists('rs_gsheets_schedule')) {
    function rs_gsheets_schedule() {
        if (!wp_next_scheduled('rs_gsheets_sync_hook')) {
            wp_schedule_event(time() + 60, 'rs_gsheets_15min', 'rs_gsheets_sync_hook');
        }
    }
}
add_action('init', 'rs_gsheets_schedule');

if (!function_exists('rs_gsheets_normalize_status')) {
    /**
     * Musí ostať zhodné s rs_imp_normalize_status() v image-map-shortcodes.php.
     */
    function rs_gsheets_normalize_status($status) {
        $status = sanitize_text_field((string) $status);
        if (function_exists('remove_accents')) {
            $status = remove_accents($status);
        }
        $status = sanitize_key(str_replace(' ', '-', $status));

        $aliases = array(
            'available' => array('available', 'na-predaj', 'na_predaj', 'dostupne', 'dostupny', 'dostupna', 'volne', 'volny', 'volna', 'free'),
            'reserved'  => array('reserved', 'rezervovane', 'rezervovany', 'rezervovana'),
            'sold'      => array('sold', 'predane', 'predany', 'predana'),
        );

        foreach ($aliases as $normalized => $values) {
            if (in_array($status, $values, true)) {
                return $normalized;
            }
        }

        return '';
    }
}

if (!function_exists('rs_gsheets_normalize_price')) {
    function rs_gsheets_normalize_price($price) {
        $price = sanitize_text_field((string) $price);
        $price = preg_replace('/[^0-9,.]/', '', $price);
        $price = str_replace(',', '.', $price);
        if ($price === '' || !is_numeric($price)) {
            return '';
        }
        return (string) floatval($price);
    }
}

if (!function_exists('rs_gsheets_normalize_number')) {
    /**
     * Plochy: "62,5", "62.5 m2", "62,5 m²" aj číslo z gviz -> "62.5".
     */
    function rs_gsheets_normalize_number($value) {
        $value = sanitize_text_field((string) $value);
        // Jednotku odstrániť skôr, než sa vyhadzujú nečíselné znaky – inak by
        // sa dvojka z "m2" prilepila k číslu ("62.5 m2" -> 62.52).
        $value = preg_replace('/\s*(m2|m²|m\^2|sqm)\.?\s*$/iu', '', $value);
        $value = preg_replace('/[^0-9,.]/', '', $value);
        $value = str_replace(',', '.', $value);
        if ($value === '' || !is_numeric($value)) {
            return '';
        }
        return (string) floatval($value);
    }
}

if (!function_exists('rs_gsheets_columns')) {
    /**
     * Mapa stĺpcov tabuľky. Kľúč je index stĺpca (0 = A, 1 = B, …).
     *
     * role:
     *   code   – kód jednotky, podľa neho sa hľadá post
     *   price  – cena, pole sa berie z definície jednotky
     *   status – status, neznáma hodnota sa neprepisuje
     *   number – číselná hodnota do ACF poľa uvedeného v 'field'
     *
     * Poradie stĺpcov v tabuľke sa mení tu, nikde inde.
     */
    function rs_gsheets_columns() {
        return apply_filters('rezidencia_lomnica_gsheets_columns', array(
            0 => array('role' => 'code'),
            1 => array('role' => 'number', 'field' => 'area_total'),   // B – úžitková plocha
            2 => array('role' => 'number', 'field' => 'balcony_area'), // C – terasa / balkón
            3 => array('role' => 'price'),                             // D – cena
            4 => array('role' => 'status'),                            // E – status
        ));
    }
}

if (!function_exists('rs_gsheets_column_letter')) {
    function rs_gsheets_column_letter($index) {
        $index  = (int) $index;
        $letter = '';
        do {
            $letter = chr(65 + ($index % 26)) . $letter;
            $index  = intdiv($index, 26) - 1;
        } while ($index >= 0);

        return $letter;
    }
}

if (!function_exists('rs_gsheets_column_label')) {
    function rs_gsheets_column_label($index, $definition) {
        if (!empty($definition['field'])) {
            return $definition['field'];
        }
        return isset($definition['role']) ? $definition['role'] : (string) $index;
    }
}

if (!function_exists('rs_gsheets_clear_empty')) {
    /**
     * Tabuľka je zdroj pravdy: prázdna bunka hodnotu v ACF vymaže.
     * Dá sa vypnúť pre konkrétne pole cez filter.
     */
    function rs_gsheets_clear_empty($field) {
        return (bool) apply_filters('rezidencia_lomnica_gsheets_clear_empty', true, $field);
    }
}

if (!function_exists('rs_gsheets_columns_in_use')) {
    /**
     * Vráti indexy stĺpcov, ktoré majú aspoň jednu vyplnenú bunku.
     *
     * Rozlišuje „stĺpec sa zatiaľ nepoužíva“ od „hodnota bola zámerne
     * vymazaná“. Bez toho by pridanie prázdnej hlavičky do tabuľky zmazalo
     * celé pole vo všetkých bytoch pri najbližšej synchronizácii.
     */
    function rs_gsheets_columns_in_use($rows) {
        $in_use = array();

        foreach ((array) $rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            foreach ($row as $index => $cell) {
                if (is_array($cell) || is_object($cell)) {
                    continue;
                }
                if (trim((string) $cell) !== '') {
                    $in_use[(int) $index] = true;
                }
            }
        }

        return $in_use;
    }
}

if (!function_exists('rs_gsheets_is_header_row')) {
    function rs_gsheets_is_header_row($row) {
        $first = isset($row[0]) ? strtolower(trim((string) $row[0])) : '';

        return in_array($first, array('id', 'kod', 'kód', 'code', 'apartment_code', 'byt', 'jednotka'), true);
    }
}

if (!function_exists('rs_gsheets_resolve_unit')) {
    /**
     * Vráti pole s post ID a názvami ACF polí pre daný kód jednotky.
     * Primárne využíva definície z image-map-shortcodes.php, takže sync
     * funguje aj pre parkovanie, ak sa doplní do tabuľky.
     */
    function rs_gsheets_resolve_unit($code) {
        $code = sanitize_text_field((string) $code);
        if ($code === '') {
            return array();
        }

        if (function_exists('rs_imp_unit_id_by_code')) {
            $unit = rs_imp_unit_id_by_code($code);
            if (!empty($unit['id']) && !empty($unit['definition'])) {
                return array(
                    'id'           => absint($unit['id']),
                    'price_field'  => $unit['definition']['price_field'],
                    'status_field' => $unit['definition']['status_field'],
                );
            }

            return array();
        }

        $posts = get_posts(array(
            'post_type'      => 'byty',
            'post_status'    => array('publish', 'draft', 'pending', 'private'),
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'   => 'apartment_code',
                    'value' => $code,
                ),
            ),
        ));

        if (empty($posts)) {
            return array();
        }

        return array(
            'id'           => absint($posts[0]),
            'price_field'  => 'price',
            'status_field' => 'status',
        );
    }
}

if (!function_exists('rs_gsheets_find_by_code')) {
    function rs_gsheets_find_by_code($code) {
        $unit = rs_gsheets_resolve_unit($code);

        return empty($unit['id']) ? 0 : absint($unit['id']);
    }
}

if (!function_exists('rs_gsheets_read_field')) {
    function rs_gsheets_read_field($post_id, $field) {
        if (function_exists('get_field')) {
            return get_field($field, absint($post_id));
        }
        return get_post_meta(absint($post_id), $field, true);
    }
}

if (!function_exists('rs_gsheets_write_field')) {
    /**
     * Zapíše hodnotu iba ak sa naozaj líši. Vracia true, ak nastala zmena.
     */
    function rs_gsheets_write_field($post_id, $field, $value) {
        $post_id = absint($post_id);
        if (!$post_id || !$field) {
            return false;
        }

        $current = rs_gsheets_read_field($post_id, $field);
        if (is_array($current) || is_object($current)) {
            $current = '';
        }

        if ((string) $current === (string) $value) {
            return false;
        }

        if (function_exists('update_field')) {
            update_field($field, $value, $post_id);
        } else {
            update_post_meta($post_id, $field, $value);
        }

        return true;
    }
}

if (!function_exists('rs_gsheets_fetch_rows')) {
    function rs_gsheets_fetch_rows() {
        $spreadsheet_id = defined('RS_GSHEETS_SPREADSHEET_ID') ? RS_GSHEETS_SPREADSHEET_ID : '';
        $range          = defined('RS_GSHEETS_RANGE') ? RS_GSHEETS_RANGE : 'Hárok1!A1:E200';
        $api_key        = apply_filters('rezidencia_lomnica_gsheets_api_key', defined('RS_GSHEETS_API_KEY') ? RS_GSHEETS_API_KEY : '');

        if ($spreadsheet_id === '') {
            return new WP_Error('no_id', 'Chýba spreadsheet ID');
        }

        if ($api_key !== '') {
            $url = add_query_arg(
                array(
                    'key'               => $api_key,
                    'majorDimension'    => 'ROWS',
                    'valueRenderOption' => 'UNFORMATTED_VALUE',
                ),
                'https://sheets.googleapis.com/v4/spreadsheets/' . rawurlencode($spreadsheet_id) . '/values/' . rawurlencode($range)
            );
        } else {
            $url = 'https://docs.google.com/spreadsheets/d/' . rawurlencode($spreadsheet_id) . '/gviz/tq?tqx=out:json';
        }

        // Cache-busting, aby sa čerstvá zmena prejavila hneď po zápise.
        $url = add_query_arg('_', time(), $url);

        $response = wp_remote_get($url, array(
            'timeout'    => 20,
            'user-agent' => 'RezidenciaLomnicaSync/1.0',
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            return new WP_Error('http_' . $code, 'Google Sheets vrátil HTTP ' . $code);
        }

        $body = wp_remote_retrieve_body($response);
        $rows = array();

        if ($api_key !== '') {
            $data   = json_decode($body, true);
            $values = isset($data['values']) && is_array($data['values']) ? $data['values'] : array();
            foreach ($values as $value_row) {
                $rows[] = is_array($value_row) ? $value_row : array();
            }
        } else {
            $body = trim($body);
            $body = preg_replace('/^[^(]*\(/', '', $body);
            $body = preg_replace('/\\);?\s*$/', '', $body);
            $data = json_decode($body, true);

            if (!is_array($data) || !isset($data['table']['rows'])) {
                return new WP_Error('bad_payload', 'Odpoveď z Google Sheets sa nepodarilo spracovať. Skontroluj, či je tabuľka zdieľaná ako „Každý, kto má odkaz“.');
            }

            foreach ($data['table']['rows'] as $table_row) {
                $cells = isset($table_row['c']) && is_array($table_row['c']) ? $table_row['c'] : array();
                $row   = array();
                foreach ($cells as $cell) {
                    // Prázdna bunka príde ako null; poradie stĺpcov musí ostať zachované.
                    $row[] = (is_array($cell) && isset($cell['v']) && $cell['v'] !== null) ? $cell['v'] : '';
                }
                $rows[] = $row;
            }
        }

        // Hlavičkový riadok gviz podľa formátovania buď vráti, alebo nie.
        if (!empty($rows) && rs_gsheets_is_header_row($rows[0])) {
            array_shift($rows);
        }

        return $rows;
    }
}

if (!function_exists('rs_gsheets_purge_caches')) {
    function rs_gsheets_purge_caches() {
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        if (has_action('litespeed_purge_all')) {
            do_action('litespeed_purge_all');
        }

        do_action('rezidencia_lomnica_units_synced');
    }
}

if (!function_exists('rs_gsheets_sync_run')) {
    function rs_gsheets_sync_run() {
        if (get_transient('rs_gsheets_sync_running')) {
            return array(
                'success' => false,
                'skipped_run' => true,
                'errors'  => array('Predchádzajúca synchronizácia ešte beží.'),
            );
        }
        set_transient('rs_gsheets_sync_running', 1, 5 * MINUTE_IN_SECONDS);

        $result = array(
            'success'         => false,
            'rows'            => 0,
            'changed'         => 0,
            'unchanged'       => 0,
            'unknown_codes'   => array(),
            'unknown_status'  => array(),
            'errors'          => array(),
        );

        $rows = rs_gsheets_fetch_rows();
        if (is_wp_error($rows)) {
            $result['errors'][] = $rows->get_error_message();
            update_option('rs_gsheets_last_sync', array('time' => time(), 'result' => $result), false);
            delete_transient('rs_gsheets_sync_running');
            return $result;
        }

        if (empty($rows)) {
            $result['success']  = true;
            $result['errors'][] = 'Tabuľka neobsahuje žiadne dátové riadky.';
            update_option('rs_gsheets_last_sync', array('time' => time(), 'result' => $result), false);
            delete_transient('rs_gsheets_sync_running');
            return $result;
        }

        $columns        = rs_gsheets_columns();
        $columns_in_use = rs_gsheets_columns_in_use($rows);

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $code = isset($row[0]) ? sanitize_text_field((string) $row[0]) : '';
            if ($code === '') {
                continue;
            }

            $result['rows']++;

            $unit = rs_gsheets_resolve_unit($code);
            if (empty($unit['id'])) {
                $result['unknown_codes'][] = $code;
                continue;
            }

            $post_id = absint($unit['id']);
            $changed = false;

            foreach ($columns as $column => $definition) {
                $column = (int) $column;
                $role   = isset($definition['role']) ? $definition['role'] : '';

                // Ak stĺpec v riadku úplne chýba, hodnotu nechávame nedotknutú.
                if ($role === 'code' || $role === '' || !array_key_exists($column, $row)) {
                    continue;
                }

                $raw    = sanitize_text_field((string) $row[$column]);
                $letter = rs_gsheets_column_letter($column);

                // Status: neznámu hodnotu radšej ignorujeme, aby sa byt
                // nepreklopil omylom kvôli preklepu v tabuľke.
                if ($role === 'status') {
                    $status = rs_gsheets_normalize_status($raw);

                    if ($status !== '') {
                        $changed = rs_gsheets_write_field($post_id, $unit['status_field'], $status) || $changed;
                    } elseif ($raw !== '') {
                        $result['unknown_status'][] = $code . ': ' . $raw;
                    }
                    continue;
                }

                if ($role === 'price') {
                    $field = $unit['price_field'];
                    $value = rs_gsheets_normalize_price($raw);
                } elseif ($role === 'number') {
                    $field = sanitize_key((string) (isset($definition['field']) ? $definition['field'] : ''));
                    $value = rs_gsheets_normalize_number($raw);
                } else {
                    continue;
                }

                if ($field === '') {
                    continue;
                }

                // Prázdna bunka maže iba v stĺpci, ktorý sa reálne používa.
                $may_clear = !empty($columns_in_use[$column]) && rs_gsheets_clear_empty($field);

                if ($value !== '' || ($raw === '' && $may_clear)) {
                    $changed = rs_gsheets_write_field($post_id, $field, $value) || $changed;
                } elseif ($raw !== '') {
                    $result['errors'][] = sprintf('%s: hodnotu „%s“ v stĺpci %s sa nepodarilo prečítať.', $code, $raw, $letter);
                }
            }

            if ($changed) {
                $result['changed']++;
            } else {
                $result['unchanged']++;
            }
        }

        $result['success'] = true;

        if ($result['changed'] > 0) {
            rs_gsheets_purge_caches();
        }

        update_option('rs_gsheets_last_sync', array('time' => time(), 'result' => $result), false);
        delete_transient('rs_gsheets_sync_running');

        return $result;
    }
}
add_action('rs_gsheets_sync_hook', 'rs_gsheets_sync_run');

/* =========================================
   Admin: stav synchronizácie + manuálne spustenie
========================================= */

if (!function_exists('rs_gsheets_admin_menu')) {
    function rs_gsheets_admin_menu() {
        add_submenu_page(
            'options-general.php',
            'Google Sheets sync',
            'Google Sheets sync',
            'manage_options',
            'rs-gsheets-sync',
            'rs_gsheets_admin_page'
        );
    }
}
add_action('admin_menu', 'rs_gsheets_admin_menu');

if (!function_exists('rs_gsheets_admin_page')) {
    function rs_gsheets_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Nemáš oprávnenie na túto stránku.');
        }

        $last      = get_option('rs_gsheets_last_sync', array());
        $result    = isset($last['result']) && is_array($last['result']) ? $last['result'] : array();
        $timestamp = isset($last['time']) ? (int) $last['time'] : 0;
        $next      = wp_next_scheduled('rs_gsheets_sync_hook');
        $format    = get_option('date_format') . ' ' . get_option('time_format');

        echo '<div class="wrap"><h1>Google Sheets sync</h1>';

        if (isset($_GET['rs_synced'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Synchronizácia bola spustená.</p></div>';
        }

        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
            echo '<div class="notice notice-warning"><p><strong>DISABLE_WP_CRON je zapnuté.</strong> WP-Cron sa nespustí sám – na serveri musí bežať reálny cron, ktorý volá <code>wp-cron.php</code>, inak sa dáta nebudú aktualizovať.</p></div>';
        }

        echo '<table class="widefat striped" style="max-width:900px"><tbody>';
        printf(
            '<tr><th style="width:220px">Tabuľka</th><td><a href="%1$s" target="_blank" rel="noopener">%1$s</a></td></tr>',
            esc_url('https://docs.google.com/spreadsheets/d/' . RS_GSHEETS_SPREADSHEET_ID . '/edit')
        );
        printf(
            '<tr><th>Interval</th><td>%s min</td></tr>',
            esc_html((string) round(RS_GSHEETS_SYNC_INTERVAL / MINUTE_IN_SECONDS))
        );
        printf(
            '<tr><th>Posledný beh</th><td>%s</td></tr>',
            $timestamp ? esc_html(wp_date($format, $timestamp)) : '<em>zatiaľ nikdy</em>'
        );
        printf(
            '<tr><th>Najbližší beh</th><td>%s</td></tr>',
            $next ? esc_html(wp_date($format, $next)) : '<em>nie je naplánovaný</em>'
        );
        printf(
            '<tr><th>Spracované riadky</th><td>%s</td></tr>',
            esc_html((string) (isset($result['rows']) ? $result['rows'] : 0))
        );
        printf(
            '<tr><th>Zmenené / bez zmeny</th><td>%s / %s</td></tr>',
            esc_html((string) (isset($result['changed']) ? $result['changed'] : 0)),
            esc_html((string) (isset($result['unchanged']) ? $result['unchanged'] : 0))
        );

        $lists = array(
            'unknown_codes'  => 'Kódy bez zhody v WordPresse',
            'unknown_status' => 'Nerozpoznané statusy',
            'errors'         => 'Chyby',
        );
        foreach ($lists as $key => $label) {
            if (empty($result[$key])) {
                continue;
            }
            printf(
                '<tr><th>%s</th><td>%s</td></tr>',
                esc_html($label),
                esc_html(implode(', ', array_map('strval', (array) $result[$key])))
            );
        }

        echo '</tbody></table>';

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="margin-top:20px">';
        echo '<input type="hidden" name="action" value="rs_gsheets_sync_now">';
        wp_nonce_field('rs_gsheets_sync_now');
        submit_button('Synchronizovať teraz', 'primary', 'submit', false);
        echo '</form>';

        $column_list = array();
        foreach (rs_gsheets_columns() as $column => $definition) {
            $label = rs_gsheets_column_label($column, $definition);
            if (isset($definition['role']) && $definition['role'] === 'code') {
                $label = 'id (kód jednotky)';
            }
            $column_list[] = '<code>' . esc_html(rs_gsheets_column_letter($column) . ' = ' . $label) . '</code>';
        }

        echo '<p style="margin-top:24px;max-width:900px"><strong>Stĺpce tabuľky:</strong> ' . implode(', ', $column_list) . '</p>';
        echo '<p style="max-width:900px"><em>Cena sa na webe zobrazuje iba pri statuse „Dostupný / Na predaj“. Pri rezervovanom a predanom sa zobrazí „-“. Prázdna bunka hodnotu v ACF vymaže – vrátane plôch, takže stĺpce nechávaj vyplnené.</em></p>';
        echo '</div>';
    }
}

if (!function_exists('rs_gsheets_handle_sync_now')) {
    function rs_gsheets_handle_sync_now() {
        if (!current_user_can('manage_options')) {
            wp_die('Nemáš oprávnenie na túto akciu.');
        }
        check_admin_referer('rs_gsheets_sync_now');

        delete_transient('rs_gsheets_sync_running');
        rs_gsheets_sync_run();

        wp_safe_redirect(add_query_arg('rs_synced', '1', admin_url('options-general.php?page=rs-gsheets-sync')));
        exit;
    }
}
add_action('admin_post_rs_gsheets_sync_now', 'rs_gsheets_handle_sync_now');

if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('rs-gsheets sync', function () {
        delete_transient('rs_gsheets_sync_running');
        $result = rs_gsheets_sync_run();

        if (!empty($result['errors'])) {
            foreach ((array) $result['errors'] as $error) {
                WP_CLI::warning($error);
            }
        }

        WP_CLI::success(sprintf(
            'Riadky: %d, zmenené: %d, bez zmeny: %d, neznáme kódy: %d',
            isset($result['rows']) ? $result['rows'] : 0,
            isset($result['changed']) ? $result['changed'] : 0,
            isset($result['unchanged']) ? $result['unchanged'] : 0,
            isset($result['unknown_codes']) ? count($result['unknown_codes']) : 0
        ));
    });
}
