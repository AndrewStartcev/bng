<?php
defined('ABSPATH') || exit;
add_filter('acf/settings/save_json', static fn() => get_template_directory() . '/acf-json');
add_filter('acf/settings/load_json', static function (array $paths): array {
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
});
add_action('acf/init', static function (): void {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(['page_title'=>'BNG — настройки сайта','menu_title'=>'BNG — настройки','menu_slug'=>'bng-settings','redirect'=>false]);
    }
    if (!function_exists('acf_add_local_field_group')) return;
    acf_add_local_field_group([
        'key'=>'group_bng_sections','title'=>'Содержимое раздела',
        'fields'=>[
            ['key'=>'field_bng_intro','label'=>'Вступление','name'=>'bng_intro','type'=>'textarea','rows'=>4],
            ['key'=>'field_bng_body','label'=>'Основной контент','name'=>'bng_body','type'=>'wysiwyg','tabs'=>'visual','toolbar'=>'basic','media_upload'=>1],
            ['key'=>'field_bng_gallery','label'=>'Галерея','name'=>'bng_gallery','type'=>'gallery','return_format'=>'id','preview_size'=>'medium'],
            ['key'=>'field_bng_documents','label'=>'Документы / PDF','name'=>'bng_documents','type'=>'repeater','layout'=>'row','button_label'=>'Добавить документ','sub_fields'=>[
                ['key'=>'field_bng_doc_title','label'=>'Название','name'=>'title','type'=>'text'],
                ['key'=>'field_bng_doc_file','label'=>'Файл PDF','name'=>'file','type'=>'file','return_format'=>'id','mime_types'=>'pdf']
            ]]
        ],
        'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'bng_section']]],
        'position'=>'acf_after_title','style'=>'default'
    ]);
    acf_add_local_field_group([
        'key'=>'group_bng_home','title'=>'Главная BNG',
        'fields'=>[
            ['key'=>'field_bng_hero_heading_ru','label'=>'Заголовок RU','name'=>'hero_heading_ru','type'=>'text'],
            ['key'=>'field_bng_hero_heading_en','label'=>'Заголовок EN','name'=>'hero_heading_en','type'=>'text'],
            ['key'=>'field_bng_hero_text_ru','label'=>'Описание RU','name'=>'hero_text_ru','type'=>'textarea'],
            ['key'=>'field_bng_hero_text_en','label'=>'Описание EN','name'=>'hero_text_en','type'=>'textarea'],
            ['key'=>'field_bng_hero_video','label'=>'Видео на главной (обрезанное)','name'=>'hero_video','type'=>'file','return_format'=>'id','mime_types'=>'mp4,webm']
        ],
        'location'=>[[['param'=>'page_type','operator'=>'==','value'=>'front_page']]]
    ]);
    acf_add_local_field_group([
        'key'=>'group_bng_options','title'=>'Общие настройки',
        'fields'=>[
            ['key'=>'field_bng_logo','label'=>'Логотип','name'=>'bng_logo','type'=>'image','return_format'=>'id'],
            ['key'=>'field_bng_contact_email','label'=>'Контактный email','name'=>'bng_contact_email','type'=>'email']
        ],
        'location'=>[[['param'=>'options_page','operator'=>'==','value'=>'bng-settings']]]
    ]);
});
