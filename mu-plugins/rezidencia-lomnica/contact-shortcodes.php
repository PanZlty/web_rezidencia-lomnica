<?php
/**
 * Rezidencia Lomnica – shortcodes pre centrálne kontaktné údaje.
 *
 * Zdroj hodnôt je ACF Options stránka „Nastavenia projektu“ (slug
 * project-settings), field group „Globálne nastavenia projektu“. Tento modul
 * žiadne polia nedefinuje, iba ich číta – definícia ostáva v ACF.
 *
 * Prečo to existuje: šablóna Bricks „CTA“ (id 658) volá [rs_contact_email]
 * a [rs_contact_phone] po migrácii zo Štúrovej. Tie shortcodes v projekte
 * chýbali, takže sa v CTA sekcii vypisoval doslovný text „[rs_contact_phone]“.
 * Do tejto šablóny sa navyše nedá zapísať cez Bricks REST/MCP – obsahuje
 * element typu next-arrow-button-v2, ktorý sa mimo builderu neregistruje,
 * a Bricks preto odmietne uložiť celý strom.
 *
 * Čistejšie riešenie je prepnúť CTA v builderi na dynamické tagy
 * {acf_agent_phone} a {acf_site_email}, rovnako ako to má FOOTER a HEADER.
 * Potom sa tieto shortcodes stanú nepoužívanými a modul sa dá zmazať.
 *
 * Detaily: docs/ACF-KONTAKTNE-UDAJE.md
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('rl_contact_option')) {
    /**
     * Prečíta pole z ACF options stránky. Bez ACF PRO vracia prázdny reťazec.
     */
    function rl_contact_option($name) {
        $name = sanitize_key((string) $name);
        if ($name === '') {
            return '';
        }

        $value = '';
        if (function_exists('get_field')) {
            $value = get_field($name, 'option');
        }

        if ($value === null || $value === false) {
            $value = '';
        }
        if (is_array($value) || is_object($value)) {
            $value = '';
        }

        return apply_filters('rezidencia_lomnica_contact_option', (string) $value, $name);
    }
}

if (!function_exists('rl_contact_tel_href')) {
    /**
     * "0948 757 959" -> "+421948757959" pre atribút href.
     * Vedúca nula sa nahradí slovenskou predvoľbou, medzery a pomlčky padajú.
     */
    function rl_contact_tel_href($phone = '') {
        $phone = (string) ($phone !== '' ? $phone : rl_contact_option('agent_phone'));
        if ($phone === '') {
            return '';
        }

        $digits = preg_replace('/[^0-9+]/', '', $phone);
        $digits = preg_replace('/(?!^)\+/', '', $digits);

        if (strpos($digits, '+') !== 0) {
            if (strpos($digits, '00') === 0) {
                $digits = '+' . substr($digits, 2);
            } elseif (strpos($digits, '0') === 0) {
                $digits = '+421' . substr($digits, 1);
            }
        }

        return $digits;
    }
}

/* Zobrazovaný tvar telefónu, napr. "0948 757 959". */
add_shortcode('rs_contact_phone', function () {
    return esc_html(rl_contact_option('agent_phone'));
});

/* Hodnota do href, napr. "tel:+421948757959". */
add_shortcode('rs_contact_phone_url', function () {
    $href = rl_contact_tel_href();

    return $href === '' ? '' : esc_url('tel:' . $href, array('tel'));
});

/* Všeobecný e-mail webu. */
add_shortcode('rs_contact_email', function () {
    $email = rl_contact_option('site_email');
    if ($email === '' || !is_email($email)) {
        return '';
    }

    return esc_html(antispambot($email));
});

/* Hodnota do href, napr. "mailto:info@...". */
add_shortcode('rs_contact_email_url', function () {
    $email = rl_contact_option('site_email');
    if ($email === '' || !is_email($email)) {
        return '';
    }

    return esc_url('mailto:' . antispambot($email), array('mailto'));
});

/* Meno makléra. */
add_shortcode('rs_agent_name', function () {
    return esc_html(rl_contact_option('agent_name'));
});
