<?php
defined('ABSPATH') || exit;
function bng_sections(): array {
    return ['home','rock','evidence','flowsheet','products','milestones','news','company'];
}
function bng_language(): string {
    $lang = get_query_var('bng_lang', '');
    if ($lang !== '') return $lang === 'en' ? 'en' : 'ru';
    if (is_singular('bng_section')) return str_ends_with((string) get_post_field('post_name', get_queried_object_id()), '-en') ? 'en' : 'ru';
    return 'ru';
}
function bng_url(string $section = 'home', ?string $lang = null): string {
    $lang = $lang ?: bng_language();
    if (!in_array($section, bng_sections(), true)) $section = 'home';
    if ($section === 'home') return home_url($lang === 'en' ? '/en/' : '/');
    return home_url('/' . $lang . '/' . $section . '/');
}
add_action('init', static function (): void {
    register_post_type('bng_section', [
        'labels' => ['name'=>'Разделы BNG','singular_name'=>'Раздел BNG'],
        'public'=>true,'publicly_queryable'=>true,'has_archive'=>false,'show_in_rest'=>false,
        'menu_icon'=>'dashicons-layout','supports'=>['title'],'rewrite'=>false,
        'capability_type'=>'post'
    ]);
    add_rewrite_tag('%bng_lang%', '(ru|en)');
    add_rewrite_rule('^en/?$', 'index.php?bng_lang=en', 'top');
    foreach (['ru','en'] as $lang) {
        foreach (array_slice(bng_sections(), 1) as $section) {
            add_rewrite_rule('^' . $lang . '/' . $section . '/?$', 'index.php?post_type=bng_section&name=' . $section . '-' . $lang . '&bng_lang=' . $lang, 'top');
        }
    }
}, 10);
add_filter('query_vars', static function (array $vars): array {
    $vars[] = 'bng_lang';
    return $vars;
});
add_filter('template_include', static function (string $template): string {
    if (get_query_var('bng_lang') === 'en' && !is_singular('bng_section')) {
        status_header(200);
        return get_template_directory() . '/front-page.php';
    }
    return $template;
});
add_filter('post_type_link', static function (string $url, WP_Post $post): string {
    if ($post->post_type !== 'bng_section') return $url;
    if (preg_match('/^(home|rock|evidence|flowsheet|products|milestones|news|company)-(ru|en)$/', $post->post_name, $matches)) {
        return bng_url($matches[1], $matches[2]);
    }
    return $url;
}, 10, 2);
add_action('after_switch_theme', static function (): void {
    bng_seed_sections();
    flush_rewrite_rules();
});
function bng_seed_sections(): void {
    $titles = [
        'ru'=>['rock'=>'Порода','evidence'=>'Доказательства','flowsheet'=>'Схема','products'=>'Продукты','milestones'=>'Этапы','news'=>'Новости','company'=>'О нас'],
        'en'=>['rock'=>'Rock','evidence'=>'Evidence','flowsheet'=>'Flowsheet','products'=>'Products','milestones'=>'Milestones','news'=>'News','company'=>'Company']
    ];
    foreach ($titles as $lang => $sections) foreach ($sections as $slug => $title) {
        $name = $slug . '-' . $lang;
        if (get_page_by_path($name, OBJECT, 'bng_section')) continue;
        wp_insert_post(['post_type'=>'bng_section','post_status'=>'publish','post_title'=>$title,'post_name'=>$name], true);
    }
}
