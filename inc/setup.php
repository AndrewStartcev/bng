<?php
defined('ABSPATH') || exit;

add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus(['primary' => 'Основное меню']);
});

add_action('wp_enqueue_scripts', static function (): void {
    $css_path = get_template_directory() . '/assets/css/main.css';
    $js_path = get_template_directory() . '/assets/js/main.js';
    // Source design system first, then the existing BNG components and exact source layout overrides.
    $source_css_path = get_template_directory() . '/assets/css/client-base.css';
    wp_enqueue_style(
        'bng-client-base',
        get_template_directory_uri() . '/assets/css/client-base.css',
        [],
        file_exists($source_css_path) ? (string) filemtime($source_css_path) : wp_get_theme()->get('Version')
    );
    wp_enqueue_style(
        'bng-main',
        get_template_directory_uri() . '/assets/css/main.css',
        ['bng-client-base'],
        file_exists($css_path) ? (string) filemtime($css_path) : wp_get_theme()->get('Version')
    );
    $layout_path = get_template_directory() . '/assets/css/client-layout.css';
    wp_enqueue_style(
        'bng-client-layout',
        get_template_directory_uri() . '/assets/css/client-layout.css',
        ['bng-main'],
        file_exists($layout_path) ? (string) filemtime($layout_path) : wp_get_theme()->get('Version')
    );
    wp_enqueue_script(
        'bng-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        file_exists($js_path) ? (string) filemtime($js_path) : wp_get_theme()->get('Version'),
        true
    );
});

add_filter('use_block_editor_for_post_type', static function ($enabled, $type) {
    return $type === 'bng_section' ? false : $enabled;
}, 10, 2);

// ACF Pro is required for editable repeater/gallery/options; Rank Math is optional.
add_action('admin_notices', static function (): void {
    if (!current_user_can('activate_plugins') || function_exists('acf_add_local_field_group')) {
        return;
    }
    echo '<div class="notice notice-warning"><p>' .
        esc_html__('BNG: установи и активируй ACF Pro. Без него сайт использует исходные тексты, но управление полями недоступно.', 'bng') .
        '</p></div>';
});
