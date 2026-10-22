<?php
defined('ABSPATH') || exit;
add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus(['primary' => 'Основное меню']);
});
add_action('wp_enqueue_scripts', static function (): void {
    $version = wp_get_theme()->get('Version');
    wp_enqueue_style('bng-main', get_template_directory_uri() . '/assets/css/main.css', [], $version);
    wp_enqueue_script('bng-main', get_template_directory_uri() . '/assets/js/main.js', [], $version, true);
});
add_filter('use_block_editor_for_post_type', static fn($enabled, $type) => $type === 'bng_section' ? false : $enabled, 10, 2);
