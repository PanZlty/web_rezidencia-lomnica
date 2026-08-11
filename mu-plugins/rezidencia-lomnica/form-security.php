<?php

if (!defined('ABSPATH')) {
    exit;
}

function rs_form_spam_guard_get_ip() {
    $candidates = array(
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR',
    );

    foreach ($candidates as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }

        $raw_value = sanitize_text_field(wp_unslash($_SERVER[$key]));
        $ip = trim(explode(',', $raw_value)[0]);

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return '0.0.0.0';
}

function rs_form_spam_guard_flatten_values($value, &$output) {
    if (is_array($value)) {
        foreach ($value as $item) {
            rs_form_spam_guard_flatten_values($item, $output);
        }

        return;
    }

    if (is_object($value)) {
        rs_form_spam_guard_flatten_values((array) $value, $output);
        return;
    }

    $value = trim(wp_strip_all_tags((string) $value));

    if ($value !== '') {
        $output[] = $value;
    }
}

function rs_form_spam_guard_collect_strings($form_data) {
    $strings = array();
    rs_form_spam_guard_flatten_values($form_data, $strings);

    return $strings;
}

function rs_form_spam_guard_count_links($text) {
    preg_match_all('/(?:https?:\/\/|www\.|[a-z0-9\-]+\.[a-z]{2,})(?:[\/?#][^\s]*)?/iu', $text, $matches);
    return count($matches[0]);
}

function rs_form_spam_guard_detect_reason($form_data) {
    $strings = rs_form_spam_guard_collect_strings($form_data);

    if (!$strings) {
        return null;
    }

    $combined = mb_strtolower(implode("\n", $strings), 'UTF-8');
    $link_count = rs_form_spam_guard_count_links($combined);

    if ($link_count > 2) {
        return 'too_many_links';
    }

    if (strlen($combined) > 5000) {
        return 'payload_too_large';
    }

    $spam_patterns = array(
        'viagra',
        'cialis',
        'casino',
        'poker',
        'betting',
        'crypto giveaway',
        'forex',
        'backlinks',
        'buy seo',
        'guest post',
        'telegram',
        'whatsapp',
    );

    foreach ($spam_patterns as $pattern) {
        if (strpos($combined, $pattern) !== false) {
            return 'spam_keyword';
        }
    }

    foreach ($strings as $string) {
        if (mb_strlen($string, 'UTF-8') > 1500 && rs_form_spam_guard_count_links($string) > 0) {
            return 'long_link_message';
        }
    }

    return null;
}

function rs_form_spam_guard_rate_limit_key($channel, $form_id) {
    $ip = rs_form_spam_guard_get_ip();
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';

    return 'rs_fsg_' . md5($channel . '|' . $form_id . '|' . $ip . '|' . $ua);
}

function rs_form_spam_guard_rate_limited($channel, $form_id, $max_submissions = 3, $window = 600) {
    $key = rs_form_spam_guard_rate_limit_key($channel, $form_id);
    $attempts = get_transient($key);

    if (!is_array($attempts)) {
        $attempts = array();
    }

    $now = time();
    $attempts = array_values(array_filter($attempts, function ($timestamp) use ($now, $window) {
        return is_numeric($timestamp) && ((int) $timestamp) > ($now - $window);
    }));

    if (count($attempts) >= $max_submissions) {
        set_transient($key, $attempts, $window);
        return true;
    }

    $attempts[] = $now;
    set_transient($key, $attempts, $window);

    return false;
}

function rs_form_spam_guard_message() {
    return esc_html__('Odoslanie formulara bolo z bezpecnostnych dovodov zablokovane. Skuste to prosim znovu o chvilu.', 'rezidencia-lomnica');
}

add_filter('fluentform/prevent_malicious_attacks', '__return_true', 10, 2);

add_filter('fluentform/min_submission_interval', function ($count) {
    $count = (int) $count;
    return max($count, 20);
}, 10, 2);

add_filter('fluentform/max_submission_count', function ($count) {
    $count = (int) $count;

    if ($count > 0) {
        return min($count, 3);
    }

    return 3;
}, 10, 2);

add_action('fluentform/before_insert_submission', function ($insert_data, $data, $form) {
    $form_id = isset($form->id) ? absint($form->id) : 0;

    if (rs_form_spam_guard_rate_limited('fluentform', $form_id, 3, 600)) {
        wp_die(rs_form_spam_guard_message(), esc_html__('Formular zablokovany', 'rezidencia-lomnica'), array('response' => 429));
    }

    if (rs_form_spam_guard_detect_reason($data) !== null) {
        wp_die(rs_form_spam_guard_message(), esc_html__('Spam zablokovany', 'rezidencia-lomnica'), array('response' => 403));
    }
}, 10, 3);

add_filter('gform_validation', function ($validation_result) {
    if (empty($validation_result['form']) || !is_array($validation_result['form'])) {
        return $validation_result;
    }

    $form = $validation_result['form'];
    $form_id = isset($form['id']) ? absint($form['id']) : 0;
    $posted_values = array();

    foreach ($form['fields'] as $field) {
        if (empty($field->id)) {
            continue;
        }

        $input_name = 'input_' . $field->id;

        if (!isset($_POST[$input_name])) {
            continue;
        }

        $posted_values[$input_name] = wp_unslash($_POST[$input_name]);
    }

    $blocked = rs_form_spam_guard_rate_limited('gravityforms', $form_id, 3, 600);
    $reason = $blocked ? 'rate_limited' : rs_form_spam_guard_detect_reason($posted_values);

    if ($reason === null) {
        return $validation_result;
    }

    $validation_result['is_valid'] = false;
    $message = rs_form_spam_guard_message();
    $field_marked = false;

    foreach ($form['fields'] as &$field) {
        if ($field_marked) {
            break;
        }

        if (!isset($field->type) || !in_array($field->type, array('text', 'textarea', 'email', 'phone'), true)) {
            continue;
        }

        $field->failed_validation = true;
        $field->validation_message = $message;
        $field_marked = true;
    }

    if (!$field_marked && !empty($form['fields'][0])) {
        $form['fields'][0]->failed_validation = true;
        $form['fields'][0]->validation_message = $message;
    }

    $validation_result['form'] = $form;

    return $validation_result;
}, 10, 1);
