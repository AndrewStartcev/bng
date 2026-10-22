<?php
defined('ABSPATH') || exit;
function bng_sections(): array {return ['home','products','rock','flowsheet','evidence','milestones','news','company'];}
function bng_language(): string {return 'ru';}
function bng_url(string $section='home',?string $lang=null): string {return home_url('/');}
add_action('init',static function ():void {
    register_post_type('bng_section',[
        'labels'=>['name'=>'Разделы BNG','singular_name'=>'Раздел BNG','edit_item'=>'Редактировать раздел'],
        'public'=>false,'publicly_queryable'=>false,'exclude_from_search'=>true,'has_archive'=>false,
        'rewrite'=>false,'query_var'=>false,'show_ui'=>true,'show_in_rest'=>false,
        'menu_icon'=>'dashicons-layout','supports'=>['title']
    ]);
});

/**
 * Create only missing managed screens when an administrator opens wp-admin.
 * Existing legacy records, ACF content, and client edits are left untouched.
 * No CLI seeding command or public rewrite rules are needed.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('edit_pages')) return;

    $front_id = (int) get_option('page_on_front');
    if (get_option('show_on_front') !== 'page' || !$front_id) {
        $home = get_page_by_path('home', OBJECT, 'page');
        if (!$home) $home = get_page_by_path('glavnaya', OBJECT, 'page');
        if (!$home) $home = get_page_by_path('главная', OBJECT, 'page');
        $front_id = $home instanceof WP_Post ? (int) $home->ID : wp_insert_post([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'Главная',
            'post_name' => 'home',
        ], true);
        if (!is_wp_error($front_id) && $front_id) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', (int) $front_id);
        }
    }

    $labels = bng_labels('ru');
    foreach (array_slice(bng_sections(), 1) as $slug) {
        if (bng_section_id($slug)) continue;
        wp_insert_post([
            'post_type' => 'bng_section',
            'post_status' => 'publish',
            'post_name' => $slug,
            'post_title' => (string) ($labels[$slug] ?? $slug),
        ], true);
    }
}, 20);

/** Keep imported legacy -en posts for rollback, but present one row per section. */
add_action('pre_get_posts', static function (WP_Query $query): void {
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'bng_section') return;
    $hidden=[];
    foreach (array_slice(bng_sections(),1) as $slug) {
        $post=get_page_by_path($slug.'-en',OBJECT,'bng_section');
        if($post instanceof WP_Post)$hidden[]=(int)$post->ID;
    }
    if($hidden)$query->set('post__not_in',array_merge((array)$query->get('post__not_in'),$hidden));
});
