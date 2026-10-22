<?php
defined('ABSPATH') || exit;
function bng_labels(string $lang): array {
    return $lang === 'en'
        ? ['home'=>'Home','rock'=>'Rock','evidence'=>'Evidence','flowsheet'=>'Flowsheet','products'=>'Products','milestones'=>'Milestones','news'=>'News','company'=>'Company']
        : ['home'=>'Главная','rock'=>'Порода','evidence'=>'Доказательства','flowsheet'=>'Схема','products'=>'Продукты','milestones'=>'Этапы','news'=>'Новости','company'=>'О нас'];
}
function bng_field(string $name, $default = '', $object = null) {
    if (!function_exists('get_field')) return $default;
    $value = get_field($name, $object);
    return ($value === false || $value === null || $value === '') ? $default : $value;
}
function bng_section_id(string $section, string $lang): int {
    $post = get_page_by_path($section . '-' . $lang, OBJECT, 'bng_section');
    return $post instanceof WP_Post ? (int) $post->ID : 0;
}

function bng_switch_url(string $lang): string {
    $section = 'home';
    if (is_singular('bng_section')) {
        $name = (string) get_post_field('post_name', get_queried_object_id());
        $section = preg_replace('/-(ru|en)$/', '', $name);
    }
    return bng_url($section, $lang);
}
