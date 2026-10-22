<?php
/**
 * BNG WordPress cleanup — follows ArcticRC's remove-functions.php.
 * Only non-used features are disabled; plugins, media, ACF and SEO remain.
 */
defined('ABSPATH') || exit;

foreach ([
    ['wlwmanifest_link', 10], ['rsd_link', 10], ['wp_generator', 10],
    ['wp_shortlink_wp_head', 10], ['adjacent_posts_rel_link_wp_head', 10],
    ['feed_links_extra', 3], ['wp_oembed_add_discovery_links', 10],
    ['rest_output_link_wp_head', 10],
] as $action) {
    remove_action('wp_head', $action[0], $action[1]);
}
remove_action('template_redirect', 'wp_shortlink_header', 11);
remove_action('template_redirect', 'rest_output_link_header', 11);
add_filter('the_generator', '__return_empty_string');

foreach ([
    ['wp_head', 'print_emoji_detection_script', 7],
    ['admin_print_scripts', 'print_emoji_detection_script', 10],
    ['wp_enqueue_scripts', 'wp_enqueue_emoji_styles', 10],
    ['admin_print_styles', 'wp_enqueue_emoji_styles', 10],
    ['wp_print_styles', 'print_emoji_styles', 10],
    ['admin_print_styles', 'print_emoji_styles', 10],
] as $action) {
    remove_action($action[0], $action[1], $action[2]);
}
add_action('init', static function (): void {
    foreach (get_post_types() as $type) {
        remove_post_type_support($type, 'comments');
        remove_post_type_support($type, 'trackbacks');
    }
}, 100);
add_filter('comments_open', '__return_false', 100);
add_filter('pings_open', '__return_false', 100);
add_filter('xmlrpc_enabled', '__return_false');

add_action('admin_menu', static function (): void {
    remove_menu_page('edit.php');
    remove_menu_page('edit-comments.php');
    foreach ([
        ['themes.php', 'site-editor.php'],
        ['themes.php', 'widgets.php'],
        ['themes.php', 'customize.php'],
        ['themes.php', 'theme-editor.php'],
        ['plugins.php', 'plugin-editor.php'],
        ['options-general.php', 'options-discussion.php'],
    ] as $item) {
        remove_submenu_page($item[0], $item[1]);
    }
}, 999);

add_action('admin_bar_menu', static function ($bar): void {
    foreach (['wp-logo', 'customize', 'comments', 'new-post'] as $node) $bar->remove_node($node);
}, 999);
add_action('wp_dashboard_setup', static function (): void {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
});
remove_action('welcome_panel', 'wp_welcome_panel');

add_filter('use_block_editor_for_post', static function ($use, $post) {
    return $post instanceof WP_Post && (int) get_option('page_on_front') === (int) $post->ID
        ? false : $use;
}, 10, 2);
add_action('admin_init', static function (): void {
    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
    if ($post_id && $post_id === (int) get_option('page_on_front')) {
        remove_post_type_support('page', 'editor');
    }
}, 50);
add_action('wp_enqueue_scripts', static function (): void {
    foreach (['wp-block-library','wp-block-library-theme','global-styles','classic-theme-styles'] as $handle) {
        wp_dequeue_style($handle);
    }
}, 100);
