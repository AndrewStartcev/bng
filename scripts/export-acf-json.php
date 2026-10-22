<?php
/**
 * Export the existing BNG PHP ACF definitions to portable ACF Local JSON.
 * Usage: php scripts/export-acf-json.php
 * This generator intentionally runs without WordPress and retains all field keys.
 */
declare(strict_types=1);
define('ABSPATH', __DIR__ . '/../');
function get_template_directory(): string { return dirname(__DIR__); }
function add_filter(...$args): void {}
function add_action(...$args): void {}
function bng_source(): array {
    $data = json_decode((string) file_get_contents(dirname(__DIR__) . '/data/source-content.json'), true);
    if (!is_array($data)) throw new RuntimeException('Invalid BNG client content');
    return $data;
}
require dirname(__DIR__) . '/inc/acf.php';

$target = dirname(__DIR__) . '/acf-json';
if (!is_dir($target) && !mkdir($target, 0775, true) && !is_dir($target)) {
    throw new RuntimeException('Cannot create acf-json directory');
}
$labels = ['ru' => '🇷🇺 Контент — Русский', 'en' => '🇬🇧 Content — English'];
$modified = 1792700000;
$export = static function (array $group) use ($target, $modified): void {
    $group += [
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ];
    $group['modified'] = $modified;
    $filename = $target . '/' . $group['key'] . '.json';
    $json = json_encode($group, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    file_put_contents($filename, $json . "\n");
    echo basename($filename) . "\n";
};
foreach (array_keys(bng_editor_layout()) as $section) {
    foreach ($labels as $lang => $title) {
        $location = $section === 'home'
            ? [[['param'=>'page_type','operator'=>'==','value'=>'front_page']]]
            : [[['param'=>'bng_section','operator'=>'==','value'=>$section]]];
        $export([
            'key' => 'group_bng_' . $section . '_' . $lang,
            'title' => $title,
            'fields' => bng_editor_fields($section, $lang),
            'location' => $location,
            'menu_order' => $lang === 'ru' ? 0 : 10,
        ]);
    }
}
$export([
    'key' => 'group_bng_options',
    'title' => 'BNG — общие настройки',
    'fields' => [
        ['key'=>'field_bng_logo','label'=>'Логотип','name'=>'bng_logo','type'=>'image',
         'return_format'=>'id','wrapper'=>['width'=>'50']],
        ['key'=>'field_bng_contact_email','label'=>'Контактный email',
         'name'=>'bng_contact_email','type'=>'email','wrapper'=>['width'=>'50']],
    ],
    'location' => [[['param'=>'options_page','operator'=>'==','value'=>'bng-settings']]],
]);
