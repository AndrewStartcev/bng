<?php defined('ABSPATH') || exit; $source = bng_source(); ?>
<section class="section">
    <?php bng_render_lead($copy, (int) $post_id); ?>
    <div class="flow-visual">
        <div class="flow-visual__input"><span class="kicker"><?php echo esc_html($copy['feedKicker'] ?? ''); ?></span><strong><?php echo esc_html($copy['feed'] ?? ''); ?></strong><p><?php echo esc_html($copy['feedNote'] ?? ''); ?></p></div>
        <div class="flow-visual__arrow" aria-hidden="true">→</div>
        <div class="flow-visual__outputs">
            <?php foreach (($source['buyers'] ?? []) as $symbol => $buyer) : ?>
                <article class="flow-output" style="--stream-color:<?php echo esc_attr($source['stream_colors'][$symbol] ?? '#7C5CBF'); ?>">
                    <span><?php echo esc_html($symbol); ?></span><div><strong><?php echo esc_html(bng_copy('', $lang)['element'][$symbol] ?? $symbol); ?></strong><p><?php echo esc_html($buyer[$lang] ?? ''); ?></p></div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <section class="section-inner">
        <h2><?php echo esc_html($copy['routesTitle'] ?? ''); ?></h2>
        <div class="cards-grid">
            <?php foreach (($copy['routes'] ?? []) as $route) : ?><article class="info-card"><span class="kicker"><?php echo esc_html($route[0] ?? ''); ?></span><h3><?php echo esc_html($route[1] ?? ''); ?></h3><p><?php echo esc_html($route[2] ?? ''); ?></p><strong><?php echo esc_html($route[3] ?? ''); ?></strong></article><?php endforeach; ?>
        </div>
        <div class="notice-panel"><p><?php echo esc_html($copy['routesSelected'] ?? ''); ?></p><p><?php echo esc_html($copy['routesNote'] ?? ''); ?></p></div>
    </section>
    <section class="section-inner columns-2">
        <div><h2><?php echo esc_html($copy['givesTitle'] ?? ''); ?></h2><ul class="styled-list"><?php foreach (($copy['gives'] ?? []) as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?></ul></div>
        <div><h2><?php echo esc_html($copy['costsTitle'] ?? ''); ?></h2><ul class="styled-list"><?php foreach (($copy['costs'] ?? []) as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?></ul></div>
    </section>
</section>
