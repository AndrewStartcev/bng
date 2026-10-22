<?php
defined('ABSPATH') || exit;

add_filter('acf/settings/save_json', static function () {
    return get_template_directory() . '/acf-json';
});
add_filter('acf/settings/load_json', static function (array $paths): array {
    $paths[] = get_template_directory() . '/acf-json';
    return array_unique($paths);
});

add_action('acf/init', static function (): void {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => 'BNG — настройки сайта',
            'menu_title' => 'BNG — настройки',
            'menu_slug' => 'bng-settings',
            'redirect' => false,
        ]);
    }
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    acf_add_local_field_group([
        'key' => 'group_bng_sections',
        'title' => 'Раздел — содержимое',
        'position' => 'acf_after_title',
        'style' => 'default',
        'fields' => [
            ['key' => 'field_bng_tab_base', 'label' => 'Тексты', 'type' => 'tab', 'placement' => 'left'],
            ['key' => 'field_bng_title', 'label' => 'Заголовок H1 (необязательно)', 'name' => 'bng_title', 'type' => 'text', 'instructions' => 'Если пусто — используется исходный заголовок выбранного языка.'],
            ['key' => 'field_bng_intro', 'label' => 'Описание первого экрана', 'name' => 'bng_intro', 'type' => 'textarea', 'rows' => 4],
            ['key' => 'field_bng_body', 'label' => 'Дополнительный контент под блоками', 'name' => 'bng_body', 'type' => 'wysiwyg', 'tabs' => 'visual', 'toolbar' => 'basic', 'media_upload' => 1],
            ['key' => 'field_bng_tab_gallery', 'label' => 'Фотогалерея', 'type' => 'tab', 'placement' => 'left'],
            ['key' => 'field_bng_gallery', 'label' => 'Фотографии в заданном порядке', 'name' => 'bng_gallery', 'type' => 'gallery', 'return_format' => 'id', 'preview_size' => 'medium'],
            ['key' => 'field_bng_tab_docs', 'label' => 'Документы', 'type' => 'tab', 'placement' => 'left'],
            ['key' => 'field_bng_documents', 'label' => 'Дополнительные PDF', 'name' => 'bng_documents', 'type' => 'repeater', 'layout' => 'row', 'button_label' => 'Добавить PDF',
                'sub_fields' => [
                    ['key' => 'field_bng_doc_title', 'label' => 'Название', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'field_bng_doc_file', 'label' => 'PDF', 'name' => 'file', 'type' => 'file', 'return_format' => 'id', 'mime_types' => 'pdf'],
                ],
            ],
            ['key' => 'field_bng_tab_specs', 'label' => 'Спецификации', 'type' => 'tab', 'placement' => 'left'],
            ['key' => 'field_bng_specifications', 'label' => 'PDF по коду марки', 'name' => 'bng_specifications', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Прикрепить PDF', 'instructions' => 'Раздел «Продукты». Код должен точно совпадать с кодом марки в таблице, например PS-K01.A.',
                'sub_fields' => [
                    ['key' => 'field_bng_spec_code', 'label' => 'Код марки', 'name' => 'code', 'type' => 'text', 'required' => 1],
                    ['key' => 'field_bng_spec_file', 'label' => 'PDF', 'name' => 'file', 'type' => 'file', 'return_format' => 'id', 'mime_types' => 'pdf', 'required' => 1],
                ],
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'bng_section']]],
    ]);
    acf_add_local_field_group([
        'key' => 'group_bng_home',
        'title' => 'Главная — BNG Elements',
        'fields' => [
            ['key' => 'field_bng_tab_home_ru', 'label' => 'Русский', 'type' => 'tab', 'placement' => 'left'],
            ['key' => 'field_bng_hero_heading_ru', 'label' => 'H1 на русском', 'name' => 'hero_heading_ru', 'type' => 'text'],
            ['key' => 'field_bng_hero_text_ru', 'label' => 'Описание RU', 'name' => 'hero_text_ru', 'type' => 'textarea', 'rows' => 4],
            ['key' => 'field_bng_tab_home_en', 'label' => 'English', 'type' => 'tab', 'placement' => 'left'],
            ['key' => 'field_bng_hero_heading_en', 'label' => 'H1 in English', 'name' => 'hero_heading_en', 'type' => 'text'],
            ['key' => 'field_bng_hero_text_en', 'label' => 'Description EN', 'name' => 'hero_text_en', 'type' => 'textarea', 'rows' => 4],
            ['key' => 'field_bng_tab_home_media', 'label' => 'Видео', 'type' => 'tab', 'placement' => 'left'],
            ['key' => 'field_bng_hero_video', 'label' => 'Видео на главной (обрезанный файл)', 'name' => 'hero_video', 'type' => 'file', 'return_format' => 'id', 'mime_types' => 'mp4,webm'],
            ['key' => 'field_bng_hero_poster', 'label' => 'Постер видео', 'name' => 'hero_poster', 'type' => 'image', 'return_format' => 'id'],
        ],
        'location' => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]],
    ]);
    acf_add_local_field_group([
        'key' => 'group_bng_options',
        'title' => 'Общие настройки',
        'fields' => [
            ['key' => 'field_bng_logo', 'label' => 'Логотип', 'name' => 'bng_logo', 'type' => 'image', 'return_format' => 'id'],
            ['key' => 'field_bng_contact_email', 'label' => 'Контактный email', 'name' => 'bng_contact_email', 'type' => 'email'],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'bng-settings']]],
    ]);
});
