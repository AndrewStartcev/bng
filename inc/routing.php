<?php
defined('ABSPATH') || exit;

function bng_sections(): array {
    return ['home', 'products', 'rock', 'flowsheet', 'evidence', 'milestones', 'news', 'company'];
}

function bng_language(): string {
    $lang = (string) get_query_var('bng_lang', '');
    if ($lang !== '') {
        return $lang === 'en' ? 'en' : 'ru';
    }
    if (is_singular('bng_section')) {
        $name = (string) get_post_field('post_name', get_queried_object_id());
        return str_ends_with($name, '-en') ? 'en' : 'ru';
    }
    return 'ru';
}

function bng_url(string $section = 'home', ?string $lang = null): string {
    $lang = $lang ?: bng_language();
    $lang = $lang === 'en' ? 'en' : 'ru';
    if (!in_array($section, bng_sections(), true)) {
        $section = 'home';
    }
    if ($section === 'home') {
        return home_url($lang === 'en' ? '/en/' : '/');
    }
    return home_url('/' . $lang . '/' . $section . '/');
}

add_action('init', static function (): void {
    register_post_type('bng_section', [
        'labels' => [
            'name' => 'Разделы BNG', 'singular_name' => 'Раздел BNG',
            'edit_item' => 'Редактировать раздел', 'all_items' => 'Все разделы',
        ],
        'public' => true,
        'publicly_queryable' => true,
        'has_archive' => false,
        'show_in_rest' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-layout',
        'supports' => ['title'],
        'rewrite' => false,
    ]);
    add_rewrite_tag('%bng_lang%', '(ru|en)');
    add_rewrite_rule('^en/?$', 'index.php?bng_lang=en', 'top');
    foreach (['ru', 'en'] as $lang) {
        foreach (array_slice(bng_sections(), 1) as $section) {
            add_rewrite_rule(
                '^' . $lang . '/' . $section . '/?$',
                'index.php?post_type=bng_section&name=' . $section . '-' . $lang . '&bng_lang=' . $lang,
                'top'
            );
        }
    }
}, 10);

add_filter('query_vars', static function (array $vars): array {
    $vars[] = 'bng_lang';
    return $vars;
});

// An English landing page must resolve to the actual WordPress front page,
// otherwise core serves the posts index, with incorrect title and status.
add_filter('request', static function (array $query): array {
    if (($query['bng_lang'] ?? '') !== 'en' || !empty($query['post_type']) || !empty($query['name'])) {
        return $query;
    }
    if (get_option('show_on_front') === 'page') {
        $front_id = (int) get_option('page_on_front');
        if ($front_id) {
            $query['page_id'] = $front_id;
        }
    }
    return $query;
});

add_filter('template_include', static function (string $template): string {
    if (get_query_var('bng_lang') === 'en' && !is_singular('bng_section')) {
        return get_template_directory() . '/front-page.php';
    }
    return $template;
});

add_filter('redirect_canonical', static function ($url) {
    if (get_query_var('bng_lang') === 'en' && !is_singular('bng_section')) {
        return false;
    }
    return $url;
});

add_filter('post_type_link', static function (string $url, WP_Post $post): string {
    if ($post->post_type !== 'bng_section') {
        return $url;
    }
    if (preg_match('/^(products|rock|flowsheet|evidence|milestones|news|company)-(ru|en)$/', $post->post_name, $matches)) {
        return bng_url($matches[1], $matches[2]);
    }
    return $url;
}, 10, 2);

add_action('after_switch_theme', static function (): void {
    bng_seed_sections();
    flush_rewrite_rules();
});

function bng_seed_sections(): void {
    foreach (['ru', 'en'] as $lang) {
        $labels = bng_labels($lang);
        foreach (array_slice(bng_sections(), 1) as $slug) {
            if (bng_section_id($slug, $lang)) {
                continue;
            }
            wp_insert_post([
                'post_type' => 'bng_section',
                'post_status' => 'publish',
                'post_title' => (string) ($labels[$slug] ?? $slug),
                'post_name' => $slug . '-' . $lang,
            ], true);
        }
    }
}
