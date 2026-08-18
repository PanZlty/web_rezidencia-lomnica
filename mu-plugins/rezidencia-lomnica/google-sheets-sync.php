<?php
/**
 * Rezidencia Lomnica – Google Sheets sync (cena + status bytov).
 *
 * Každých 15 minút číta dáta z Google Sheets a zapisuje cenu a status do ACF
 * bytov podľa kódu bytu (apartment_code). Frontend naďalej číta z ACF, preto
 * sa zmena v tabuľke po najbližšej synchronizácii prejaví aj na stránke.
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
    define('RS_GSHEETS_RANGE', 'Hárok1!A1:C200');
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
    function rs_gsheets_normalize_status($status) {
        $status = sanitize_text_field((string) $status);
        if (function_exists('remove_accents')) {
            $status = remove_accents($status);
        }
        $status = sanitize_key(str_replace(' ', '-', $status));

        $aliases = array(
            'available' => array('available', 'na-predaj', 'na_predaj', 'dostupne', 'dostupny', 'dostupna', 'volne', 'volny', 'free'),
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

if (!function_exists('rs_gsheets_find_by_code')) {
    function rs_gsheets_find_by_code($code) {
        $code = sanitize_text_field((string) $code);
        if ($code === '') {
            return 0;
        }

        if (function_exists('rs_imp_apartment_id_by_code')) {
            $post_id = rs_imp_apartment_id_by_code($code);
            if ($post_id) {
                return absint($post_id);
            }
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

        return empty($posts) ? 0 : absint($posts[0]);
    }
}

if (!function_exists('rs_gsheets_write_field')) {
    function rs_gsheets_write_field($post_id, $field, $value) {
        if (!absint($post_id) || !$field) {
            return;
        }
        if (function_exists('update_field')) {
            update_field($field, $value, absint($post_id));
        } else {
            update_post_meta(absint($post_id), $field, $value);
        }
    }
}

if (!function_exists('rs_gsheets_fetch_rows')) {
    function rs_gsheets_fetch_rows() {
        $spreadsheet_id = defined('RS_GSHEETS_SPREADSHEET_ID') ? RS_GSHEETS_SPREADSHEET_ID : '';
        $range          = defined('RS_GSHEETS_RANGE') ? RS_GSHEETS_RANGE : 'Hárok1!A1:C200';
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

        if ($api_key !== '') {
            $data = json_decode($body, true);
            $values = isset($data['values']) && is_array($data['values']) ? $data['values'] : array();
            if (!empty($values)) {
                array_shift($values);
            }
            return $values;
        }

        $body = trim($body);
        $body = preg_replace('/^[^(]*\(/', '', $body);
        $body = preg_replace('/\\);?\s*$/', '', $body);
        $data = json_decode($body, true);

        $rows = array();
        if (is_array($data) && isset($data['table']['rows'])) {
            foreach ($data['table']['rows'] as $table_row) {
                $cells = isset($table_row['c']) && is_array($table_row['c']) ? $table_row['c'] : array();
                $row = array();
                foreach ($cells as $cell) {
                    $row[] = (isset($cell['v']) && $cell['v'] !== null) ? $cell['v'] : '';
                }
                $rows[] = $row;
            }
        }

        return $rows;
    }
}

if (!function_exists('rs_gsheets_sync_run')) {
    function rs_gsheets_sync_run() {
        if (get_transient('rs_gsheets_sync_running')) {
            return array('skipped' => true);
        }
        set_transient('rs_gsheets_sync_running', 1, 10 * MINUTE_IN_SECONDS);

        $result = array(
            'success' => false,
            'updated' => 0,
            'skipped' => 0,
            'errors'  => array(),
        );

        $rows = rs_gsheets_fetch_rows();
        if (is_wp_error($rows)) {
            $result['errors'][] = $rows->get_error_message();
            update_option('rs_gsheets_last_sync', array('time' => time(), 'result' => $result));
            delete_transient('rs_gsheets_sync_running');
            return $result;
        }

        if (empty($rows)) {
            $result['success'] = true;
            update_option('rs_gsheets_last_sync', array('time' => time(), 'result' => $result));
            delete_transient('rs_gsheets_sync_running');
            return $result;
        }

        foreach ($rows as $row) {
            if (!is_array($row) || count($row) < 2) {
                $result['skipped']++;
                continue;
            }

            $code = isset($row[0]) ? sanitize_text_field((string) $row[0]) : '';
            if ($code === '') {
                $result['skipped']++;
                continue;
            }

            $post_id = rs_gsheets_find_by_code($code);
            if (!$post_id) {
                $result['skipped']++;
                continue;
            }

            $price = isset($row[1]) ? rs_gsheets_normalize_price($row[1]) : '';
            if ($price !== '') {
                rs_gsheets_write_field($post_id, 'price', $price);
                $result['updated']++;
            }

            $status = isset($row[2]) ? rs_gsheets_normalize_status($row[2]) : '';
            if ($status !== '') {
                rs_gsheets_write_field($post_id, 'status', $status);
                $result['updated']++;
            }

            if ($price === '' && $status === '') {
                $result['skipped']++;
            }
        }

        $result['success'] = true;
        update_option('rs_gsheets_last_sync', array('time' => time(), 'result' => $result));
        delete_transient('rs_gsheets_sync_running');

        return $result;
    }
}
add_action('rs_gsheets_sync_hook', 'rs_gsheets_sync_run');