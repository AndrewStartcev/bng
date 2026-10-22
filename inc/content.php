<?php
defined('ABSPATH') || exit;

/**
 * Original client copy extracted from the RU/EN standalone files.
 * This is a read-only fallback; ACF fields override it where editable.
 */
function bng_source(): array {
    static $data = null;
    if ($data !== null) {
        return $data;
    }
    $path = get_template_directory() . '/data/source-content.json';
    $json = is_readable($path) ? file_get_contents($path) : false;
    $decoded = is_string($json) ? json_decode($json, true) : null;
    $data = is_array($decoded) ? $decoded : [];
    return $data;
}

function bng_copy(string $section = '', ?string $lang = null): array {
    $locale = $lang ?: bng_language();
    $copy = bng_source()['copy'][$locale === 'en' ? 'en' : 'ru'] ?? [];
    if ($section === '') {
        return is_array($copy) ? $copy : [];
    }
    $key = $section === 'flowsheet' ? 'flow' : $section;
    $part = $copy[$key] ?? [];
    return is_array($part) ? $part : [];
}

function bng_labels(string $lang): array {
    $fallback = $lang === 'en'
        ? ['home' => 'Home', 'products' => 'Products', 'rock' => 'Rock', 'flowsheet' => 'Flowsheet', 'evidence' => 'Evidence', 'milestones' => 'Milestones', 'news' => 'News', 'company' => 'Company']
        : ['home' => 'Главная', 'products' => 'Продукты', 'rock' => 'Порода', 'flowsheet' => 'Схема', 'evidence' => 'Доказательства', 'milestones' => 'Этапы', 'news' => 'Новости', 'company' => 'О нас'];
    return array_replace($fallback, bng_copy('', $lang)['nav'] ?? []);
}

function bng_field(string $name, $default = '', $object = null) {
    if (!function_exists('get_field')) {
        return $default;
    }
    $value = get_field($name, $object);
    return $value === false || $value === null || $value === '' ? $default : $value;
}

function bng_section_id(string $section, string $lang = 'ru'): int {
    foreach ([$section . '-ru', $section] as $path) {
        $post = get_page_by_path($path, OBJECT, 'bng_section');
        if ($post instanceof WP_Post) return (int) $post->ID;
    }
    return 0;
}

function bng_section_slug(): string {
    if (!is_singular('bng_section')) {
        return 'home';
    }
    return (string) preg_replace('/-(ru|en)$/', '', (string) get_post_field('post_name', get_queried_object_id()));
}

function bng_switch_url(string $lang): string {
    return bng_url(bng_section_slug(), $lang);
}

function bng_section_text(string $key, string $fallback = '', int $post_id = 0): string {
    return (string) bng_field($key, $fallback, $post_id ?: get_queried_object_id());
}

function bng_spec_files(int $post_id): array {
    $result = [];
    $rows = bng_field('bng_specifications', [], $post_id);
    if (!is_array($rows)) {
        return $result;
    }
    foreach ($rows as $row) {
        $code = trim((string) ($row['code'] ?? ''));
        $file = $row['file'] ?? 0;
        $id = is_array($file) ? (int) ($file['ID'] ?? 0) : (int) $file;
        $url = $id ? wp_get_attachment_url($id) : false;
        if ($code !== '' && $url) {
            $result[$code] = $url;
        }
    }
    return $result;
}
