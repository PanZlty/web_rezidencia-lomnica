<?php
/**
 * Hide the legacy standalone storage post type.
 *
 * Storage belongs to an apartment and is not a standalone sellable unit.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter(
    'register_post_type_args',
    static function ($args, $post_type) {
        if ('sklad' !== $post_type) {
            return $args;
        }

        $args['public'] = false;
        $args['publicly_queryable'] = false;
        $args['exclude_from_search'] = true;
        $args['show_ui'] = false;
        $args['show_in_nav_menus'] = false;
        $args['show_in_rest'] = false;
        $args['has_archive'] = false;
        $args['rewrite'] = false;

        return $args;
    },
    1000,
    2
);

add_action(
    'init',
    static function () {
        if (post_type_exists('sklad')) {
            unregister_post_type('sklad');
        }
    },
    1000
);