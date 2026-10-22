<?php
defined('ABSPATH') || exit;

/** Shared section heading; editorial copy is overridable in ACF. */
function bng_render_lead(array $copy, int $post_id): void {
    $title = bng_section_text('bng_title', (string) ($copy['title'] ?? get_the_title()), $post_id);
    $intro = bng_section_text('bng_intro', (string) ($copy['sub'] ?? ''), $post_id);
    echo '<div class="section-heading"><h1>' . esc_html($title) . '</h1>';
    if ($intro !== '') {
        echo '<p>' . esc_html($intro) . '</p>';
    }
    echo '</div>';
}
