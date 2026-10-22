<?php
defined('ABSPATH') || exit;

function bng_is_site_page(): bool {
    return is_front_page() || is_singular('bng_section') || (get_query_var('bng_lang') === 'en' && !is_admin());
}

// Rank Math owns canonicals and metadata when installed.
// This fallback makes both languages indexable without an SEO plugin.
add_action('wp_head', static function (): void {
    if (!bng_is_site_page()) {
        return;
    }
    $lang = bng_language();
    $section = bng_section_slug();
    if (!defined('RANK_MATH_VERSION')) {
        echo '<link rel="canonical" href="' . esc_url(bng_url($section, $lang)) . '">' . "\n";
        $text = bng_copy($section, $lang);
        $desc = (string) ($text['sub'] ?? '');
        if ($desc !== '') {
            echo '<meta name="description" content="' . esc_attr(wp_html_excerpt(wp_strip_all_tags($desc), 155, '')) . '">' . "\n";
        }
    }
    foreach (['ru', 'en'] as $locale) {
        echo '<link rel="alternate" hreflang="' . esc_attr($locale) . '" href="' . esc_url(bng_url($section, $locale)) . '">' . "\n";
    }
}, 2);

add_action('wp', static function (): void {
    if (bng_is_site_page()) {
        remove_action('wp_head', 'rel_canonical');
    }
});
add_filter('rank_math/frontend/canonical', static function ($canonical) {
    return bng_is_site_page() ? bng_url(bng_section_slug(), bng_language()) : $canonical;
});

add_filter('language_attributes', static function (string $output): string {
    return 'lang="' . (bng_language() === 'en' ? 'en-US' : 'ru-RU') . '"';
});

add_filter('document_title_parts', static function (array $parts): array {
    if (bng_is_site_page()) {
        $page = bng_copy(bng_section_slug());
        $parts['title'] = (string) bng_section_text(
            'bng_title',
            $page['title'] ?? (bng_copy('home')['title'] ?? 'BNG Elements'),
            is_singular('bng_section') ? get_queried_object_id() : (int) get_option('page_on_front')
        );
    }
    return $parts;
});
