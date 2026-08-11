<?php

if (!defined('ABSPATH')) {
    exit;
}

function rs_disable_comments_everywhere() {
    return false;
}

add_filter('comments_open', 'rs_disable_comments_everywhere', 20, 2);
add_filter('pings_open', 'rs_disable_comments_everywhere', 20, 2);
add_filter('comments_array', '__return_empty_array', 20, 2);

add_action('admin_init', function () {
    $post_types = get_post_types(array('public' => true), 'names');

    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
        }

        if (post_type_supports($post_type, 'trackbacks')) {
            remove_post_type_support($post_type, 'trackbacks');
        }
    }

    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});

add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

add_action('init', function () {
    global $pagenow;

    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }
});

add_filter('feed_links_show_comments_feed', '__return_false');
add_filter('xmlrpc_methods', function ($methods) {
    unset($methods['pingback.ping']);
    unset($methods['pingback.extensions.getPingbacks']);

    return $methods;
});

add_filter('wp_headers', function ($headers) {
    unset($headers['X-Pingback']);

    return $headers;
});

add_filter('rest_endpoints', function ($endpoints) {
    if (isset($endpoints['/wp/v2/comments'])) {
        unset($endpoints['/wp/v2/comments']);
    }

    if (isset($endpoints['/wp/v2/comments/(?P<id>[\\d]+)'])) {
        unset($endpoints['/wp/v2/comments/(?P<id>[\\d]+)']);
    }

    return $endpoints;
});

add_action('pre_comment_on_post', function () {
    wp_die(
        esc_html__('Komentare su na tejto stranke vypnute.', 'rezidencia-lomnica'),
        esc_html__('Komentare vypnute', 'rezidencia-lomnica'),
        array('response' => 403)
    );
});

add_filter('preprocess_comment', function ($commentdata) {
    if (!empty($commentdata['comment_type']) && $commentdata['comment_type'] !== 'comment') {
        wp_die(
            esc_html__('Pingbacky a trackbacky su zablokovane.', 'rezidencia-lomnica'),
            esc_html__('Odoslanie zablokovane', 'rezidencia-lomnica'),
            array('response' => 403)
        );
    }

    return $commentdata;
});
