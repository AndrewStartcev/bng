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
add_action('after_switch_theme',static function ():void {bng_seed_sections();flush_rewrite_rules();});
function bng_seed_sections():void {
    foreach(array_slice(bng_sections(),1) as $slug) {
        if(bng_section_id($slug,'ru'))continue;
        wp_insert_post(['post_type'=>'bng_section','post_status'=>'publish','post_name'=>$slug,
            'post_title'=>(string)(bng_labels('ru')[$slug]??$slug)],true);
    }
}
