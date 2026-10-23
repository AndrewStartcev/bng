<?php
/** Approved BNG client HTML/CSS/JS, extracted into reusable source sections. */
defined('ABSPATH') || exit;
$is_en = isset($_GET['lang']) && sanitize_key(wp_unslash($_GET['lang'])) === 'en';
// The same client component contains RU and EN copy; JavaScript switches languages in-place.
$original_dir = get_template_directory() . '/assets/original';
$source = $original_dir . '/page.html';
if (!is_readable($source)) {
    status_header(500);
    wp_die(esc_html__('BNG original client template is missing. Build source assets first.', 'bng'));
}
$document = file_get_contents($source);
foreach (['home','products','rock','flowsheet','evidence','milestones','news','company'] as $part) {
    $path = get_template_directory() . '/template-parts/original/' . $part . '.html';
    if (is_readable($path)) {
        $fragment = (string) file_get_contents($path);

        $document = str_replace('<!--BNG_SECTION_' . $part . '-->', $fragment, $document);
    }
}
$copy = ['ru' => [], 'en' => []];
foreach (['ru','en'] as $locale) {
    foreach (bng_sections() as $section) {
        $key = $section === 'flowsheet' ? 'flow' : $section;
        $copy[$locale][$key] = bng_copy($section, $locale);
    }
}
$media = [];
$front_id = (int) get_option('page_on_front');
foreach (['ru','en'] as $locale) {
    $video_id = (int) bng_field('bng_home_video_' . $locale, 0, $front_id);
    if ($locale === 'en' && !$video_id) $video_id = (int) bng_field('bng_home_video_ru', 0, $front_id);
    $mime = $video_id ? (string) get_post_mime_type($video_id) : '';
    $video_url = $video_id && in_array($mime, ['video/mp4','video/webm'], true)
        ? wp_get_attachment_url($video_id) : false;
    $poster_id = (int) bng_field('bng_home_poster_' . $locale, 0, $front_id);
    if ($locale === 'en' && !$poster_id) $poster_id = (int) bng_field('bng_home_poster_ru', 0, $front_id);
    $media[$locale] = [
        'video' => $video_url ? esc_url_raw($video_url) : '',
        'poster' => $poster_id ? (string) wp_get_attachment_image_url($poster_id, 'full') : '',
    ];
}
// Three ordered rock photos per language; empty entries preserve original placeholders.
$rock_media = [];
$rock_post_id = bng_section_id('rock');
foreach (['ru', 'en'] as $locale) {
    $rock_media[$locale] = [];
    foreach (['core', 'hand', 'plant'] as $slot) {
        $id = $rock_post_id ? (int) bng_field('bng_' . $locale . '_rock_photo_' . $slot, 0, $rock_post_id) : 0;
        $rock_media[$locale][$slot] = $id ? (string) wp_get_attachment_image_url($id, 'large') : '';
    }
}
$json = wp_json_encode($copy, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$script = '<script>window.BNG_WP_COPY = ' . ($json ?: '{}') . ';';
$script .= 'window.BNG_WP_MEDIA = ' . wp_json_encode($media, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
$script .= 'window.BNG_ROCK_MEDIA = ' . wp_json_encode($rock_media, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
$script .= 'window.BNG_INITIAL_LANG = ' . wp_json_encode($is_en ? 'en' : 'ru') . ';';
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
