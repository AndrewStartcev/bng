<?php
defined('ABSPATH') || exit;
add_action('wp_head', static function (): void {
    if (!is_front_page() && !is_singular('bng_section') && get_query_var('bng_lang') !== 'en') return;
    $lang = bng_language();
    $section = 'home';
    if (is_singular('bng_section')) {
        $name = (string) get_post_field('post_name', get_queried_object_id());
        $section = preg_replace('/-(ru|en)$/', '', $name);
    }
    echo '<link rel="canonical" href="' . esc_url(bng_url($section, $lang)) . '">' . "\n";
    foreach (['ru','en'] as $locale) echo '<link rel="alternate" hreflang="' . esc_attr($locale) . '" href="' . esc_url(bng_url($section, $locale)) . '">' . "\n";
}, 2);
add_filter('language_attributes', static function (string $output): string {
    return 'lang="' . esc_attr(bng_language() === 'en' ? 'en-US' : 'ru-RU') . '"';
});
