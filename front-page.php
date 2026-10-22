<?php
/** Approved BNG client HTML/CSS/JS, extracted into reusable source sections. */
defined('ABSPATH') || exit;
$is_en = isset($_GET['lang']) && sanitize_key(wp_unslash($_GET['lang'])) === 'en';
$original_dir = get_template_directory() . '/assets/original' . ($is_en ? '/en' : '');
$source = $original_dir . '/page.html';
if (!is_readable($source)) {
    status_header(500);
    wp_die(esc_html__('BNG original client template is missing. Build source assets first.', 'bng'));
}
$document = file_get_contents($source);
foreach (['home','products','rock','flowsheet','evidence','milestones','news','company'] as $part) {
    $path = $is_en ? $original_dir . '/sections/' . $part . '.html' : get_template_directory() . '/template-parts/original/' . $part . '.html';
    if (is_readable($path)) {
        $document = str_replace('<!--BNG_SECTION_' . $part . '-->', file_get_contents($path), $document);
    }
}
$copy = ['ru' => [], 'en' => []];
foreach (['ru','en'] as $locale) {
    foreach (bng_sections() as $section) {
        $key = $section === 'flowsheet' ? 'flow' : $section;
        $copy[$locale][$key] = bng_copy($section, $locale);
    }
}
$json = wp_json_encode($copy, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$script = '<script>window.BNG_WP_COPY = ' . ($json ?: '{}') . ';';
$resource_path = $original_dir . '/resources.json';
$resources = is_readable($resource_path) ? json_decode(file_get_contents($resource_path), true) : [];
$script .= 'window.__resources = ' . wp_json_encode((object) $resources, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';</script>';
ob_start();
wp_head();
$head = ob_get_clean();
ob_start();
wp_footer();
$foot = ob_get_clean();
$document = str_replace(
    ['__BNG_THEME_URI__','__BNG_WP_HEAD__','__BNG_WP_BRIDGE__','__BNG_WP_FOOT__'],
    [esc_url(get_template_directory_uri()),$head,$script,$foot],
    $document
);
echo $document; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- approved client HTML and encoded WP values.
