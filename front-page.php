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
        $fragment = (string) file_get_contents($path);
        if ($part === 'home') {
            // Replace only the original video figure: preserve the client's approved layout.
            // Do not allow the original YouTube/Vimeo fallback to render.
            $front_id = (int) get_option('page_on_front');
            $lang = $is_en ? 'en' : 'ru';
            $video_id = (int) bng_field('bng_home_video_' . $lang, 0, $front_id);
            if (!$video_id && $lang === 'en') {
                $video_id = (int) bng_field('bng_home_video_ru', 0, $front_id);
            }
            $video_url = $video_id ? wp_get_attachment_url($video_id) : false;
            $mime = $video_id ? (string) get_post_mime_type($video_id) : '';
            if ($video_url && !in_array($mime, ['video/mp4', 'video/webm'], true)) {
                $video_url = false;
            }
            $poster_id = (int) bng_field('bng_home_poster_' . $lang, 0, $front_id);
            if (!$poster_id && $lang === 'en') {
                $poster_id = (int) bng_field('bng_home_poster_ru', 0, $front_id);
            }
            $poster_url = $poster_id ? wp_get_attachment_image_url($poster_id, 'full') : false;
            $caption = (string) (bng_copy('home', $lang)['figcap'] ?? '');
            $figure = '<figure class="blueprint" style="margin:56px 0 0;padding:0;height:420px;position:relative">';
            if ($video_url) {
                $figure .= '<video controls autoplay muted loop playsinline preload="metadata"'
                    . ($poster_url ? ' poster="' . esc_url($poster_url) . '"' : '')
                    . ' style="display:block;width:100%;height:100%;object-fit:cover;background:#12171D">'
                    . '<source src="' . esc_url($video_url) . '" type="' . esc_attr($mime) . '">'
                    . '</video>';
            } elseif ($poster_url) {
                $figure .= '<img src="' . esc_url($poster_url) . '" alt="BNG Elements" style="display:block;width:100%;height:100%;object-fit:cover;background:#12171D">';
            } else {
                $hint = $lang === 'ru' ? 'Видео загружается через поле ACF на главной странице' : 'Upload the video using the homepage ACF field';
                $figure .= '<div style="width:100%;height:100%;background:#12171D;color:#D8DBDE;display:flex;align-items:center;justify-content:center;text-align:center;padding:24px">'
                    . esc_html($hint) . '</div>';
            }
            if ($caption !== '') {
                $figure .= '<figcaption>' . esc_html($caption) . '</figcaption>';
            }
            $figure .= '</figure>';
            $fragment = preg_replace('~<figure class="blueprint"\\b[^>]*>.*?</figure>~s', $figure, $fragment, 1, $replacements);
            if ($replacements !== 1 || !is_string($fragment)) {
                status_header(500);
                wp_die(esc_html__('BNG: original homepage video block was not found.', 'bng'));
            }
        }
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
