<?php
defined('ABSPATH') || exit;
/** Section heading from editable language copy (fallback: original client text). */
function bng_render_lead(array $copy, int $post_id = 0): void {
    $title = (string)($copy['title'] ?? 'BNG Elements');
    $intro = (string)($copy['sub'] ?? '');
    echo '<div class="section-heading"><h1>'.esc_html($title).'</h1>';
    if ($intro !== '') echo '<p>'.esc_html($intro).'</p>';
    echo '</div>';
}
