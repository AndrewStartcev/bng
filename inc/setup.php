<?php
defined('ABSPATH') || exit;
add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus(['primary' => 'Основное меню']);
});
// Keep approved client CSS intact; apply only scoped homepage responsive/video fixes.
add_action('wp_enqueue_scripts', static function (): void {
    if (!is_front_page()) return;
    wp_enqueue_script('bng-home-video', get_template_directory_uri().'/assets/js/home-video.js', [], (string) filemtime(get_template_directory().'/assets/js/home-video.js'), true);
    $relative = '/assets/css/bng-home-responsive.css';
    wp_enqueue_style('bng-home-responsive', get_template_directory_uri() . $relative, [], (string) filemtime(get_template_directory() . $relative));
});
add_filter('use_block_editor_for_post_type', static function ($enabled, $type) {
    return $type === 'bng_section' ? false : $enabled;
}, 10, 2);
add_action('admin_notices', static function (): void {
    if (!current_user_can('activate_plugins') || function_exists('acf_add_local_field_group')) return;
    echo '<div class="notice notice-warning"><p>' . esc_html__('BNG: установи и активируй ACF Pro.', 'bng') . '</p></div>';
});
