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
// The SVG remains the untouched original; only the six bottom information
// cards and their source note are editable through ACF.
$map_data_path = get_template_directory() . '/data/rock-map-content.json';
$map_defaults = is_readable($map_data_path)
    ? json_decode((string) file_get_contents($map_data_path), true) : [];
$map_content = [];
foreach (['ru','en'] as $locale) {
    $data = is_array($map_defaults[$locale] ?? null) ? $map_defaults[$locale] : [];
    $cards = (array) ($data['cards'] ?? []);
    if ($rock_post_id && function_exists('get_field')) {
        $edited = get_field('bng_' . $locale . '_rock_map_cards', $rock_post_id);
        if (is_array($edited) && count($edited) === 6) {
            $cards = array_map(static function (array $row): array {
                return [
                    (string) ($row['label'] ?? ''),
                    (string) ($row['title'] ?? ''),
                    (string) ($row['distance'] ?? ''),
                    (string) ($row['note'] ?? ''),
                ];
            }, $edited);
        }
    }
    $footnote = (string) ($data['footnote'] ?? '');
    if ($rock_post_id && function_exists('get_field')) {
        $saved = get_field('bng_' . $locale . '_rock_map_footnote', $rock_post_id);
        if (is_string($saved) && $saved !== '') $footnote = $saved;
    }
    $map_content[$locale] = ['cards' => $cards, 'footnote' => $footnote];
}
// The Rock chemistry is editorial content, not fixed code in the client JS.
$ore_seed_path = get_template_directory() . '/data/rock-ore-content.json';
$ore_seed = is_readable($ore_seed_path) ? json_decode((string) file_get_contents($ore_seed_path), true) : [];
$ore_content = [];
foreach (['ru', 'en'] as $locale) {
    $rows = is_array($ore_seed[$locale] ?? null) ? $ore_seed[$locale] : [];
    if ($rock_post_id && function_exists('get_field')) {
        $saved_rows = get_field('bng_' . $locale . '_rock_ore', $rock_post_id);
        if (is_array($saved_rows) && $saved_rows) $rows = $saved_rows;
    }
    $ore_content[$locale] = array_values(array_map(static function ($item): array {
        $pct = is_array($item) ? (float) ($item['percentage'] ?? 0) : 0;
        $pct = max(0, min(100, $pct));
        return [
            'formula' => sanitize_text_field((string) ($item['formula'] ?? '')),
            'name' => sanitize_text_field((string) ($item['name'] ?? '')),
            'percentage' => $pct,
            'display' => sanitize_text_field((string) ($item['display'] ?? '')),
        ];
    }, $rows));
}
// Process stream data is editable per language and uses the exact original client values as fallback.
$streams_path = get_template_directory() . '/data/flowsheet-streams.json';
$streams_default = is_readable($streams_path) ? json_decode((string) file_get_contents($streams_path), true) : [];
$flowsheet_streams = [];
$flowsheet_id = bng_section_id('flowsheet');
foreach (['ru','en'] as $locale) {
    $rows = is_array($streams_default[$locale] ?? null) ? $streams_default[$locale] : [];
    if ($flowsheet_id && function_exists('get_field')) {
        $stored = get_field('bng_' . $locale . '_flowsheet_streams', $flowsheet_id);
        if (is_array($stored) && $stored) {
            $rows = array_map(static function ($item): array {
                $products = is_array($item['products'] ?? null) ? $item['products'] : [];
                return [
                    'symbol' => (string) ($item['symbol'] ?? ''),
                    'atomic' => (string) ($item['atomic'] ?? ''),
                    'name' => (string) ($item['name'] ?? ''),
                    'count' => (string) ($item['count'] ?? ''),
                    'buyers' => (string) ($item['buyers'] ?? ''),
                    'products' => array_map(static function ($product): array {
                        return ['formula' => (string) ($product['formula'] ?? ''),
                                'name' => (string) ($product['name'] ?? '')];
                    }, $products),
                ];
            }, $stored);
        }
    }
    $flowsheet_streams[$locale] = array_values($rows);
}
$json = wp_json_encode($copy, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$script = '<script>window.BNG_WP_COPY = ' . ($json ?: '{}') . ';';
$script .= 'window.BNG_WP_MEDIA = ' . wp_json_encode($media, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
$script .= 'window.BNG_ROCK_MEDIA = ' . wp_json_encode($rock_media, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
$script .= 'window.BNG_ROCK_ORE = ' . wp_json_encode($ore_content, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
$script .= 'window.BNG_FLOWSHEET_STREAMS = ' . wp_json_encode($flowsheet_streams, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
$script .= 'window.BNG_INITIAL_LANG = ' . wp_json_encode($is_en ? 'en' : 'ru') . ';';
// Client map is an original self-contained HTML/SVG embed, not a WordPress page.
$resources = [];
$map_urls = [];
foreach (['ru', 'en'] as $locale) {
    $locale_path = $original_dir . ($locale === 'en' ? '/en' : '') . '/resources.json';
    $manifest = is_readable($locale_path) ? json_decode((string) file_get_contents($locale_path), true) : [];
    if (!is_array($manifest)) $manifest = [];
    foreach ($manifest as $key => $url) {
        if (!is_string($url)) continue;
        $manifest[$key] = str_replace('__BNG_THEME_URI__', get_template_directory_uri(), $url);
    }
    $resources[$locale] = $manifest;
    $map_urls[$locale] = $manifest['depositMap'] ?? '';
}
$script .= 'window.BNG_MAP_URLS = ' . wp_json_encode($map_urls, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
$script .= 'window.BNG_MAP_CONTENT = ' . wp_json_encode($map_content, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
$script .= 'window.__resources = ' . wp_json_encode((object) $resources['ru'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';</script>';
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
