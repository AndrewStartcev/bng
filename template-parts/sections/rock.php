<?php defined('ABSPATH') || exit; $ore = bng_source()['ore'] ?? []; ?>
<section class="section">
    <?php bng_render_lead($copy, (int) $post_id); ?>
    <div class="stats-grid">
        <?php foreach (($copy['facts'] ?? []) as $fact) : ?>
            <article class="stat-card"><strong><?php echo esc_html($fact[0] ?? ''); ?></strong><p><?php echo esc_html($fact[1] ?? ''); ?></p></article>
        <?php endforeach; ?>
    </div>
    <section class="section-inner">
        <h2><?php echo esc_html($copy['compTitle'] ?? ''); ?></h2>
        <p><?php echo esc_html($copy['compNote'] ?? ''); ?></p>
        <div class="ore-chart" role="list" aria-label="<?php echo esc_attr($copy['compTitle'] ?? 'Composition'); ?>">
            <?php foreach ($ore as $mineral) : ?><div role="listitem"><div class="ore-chart__top"><span><?php echo esc_html($mineral['formula'] ?? ''); ?> · <?php echo esc_html($mineral[$lang] ?? ''); ?></span><strong><?php echo esc_html($mineral['pct'] ?? ''); ?></strong></div><div class="ore-chart__track"><span style="width:<?php echo esc_attr($mineral['w'] ?? '0%'); ?>"></span></div></div><?php endforeach; ?>
        </div>
    </section>
    <section class="section-inner">
        <div class="section-split"><h2><?php echo esc_html($copy['surfaceTitle'] ?? ''); ?></h2>
            <div class="facts-grid">
                <?php foreach (($copy['points'] ?? []) as $point) : ?><div class="fact"><span class="fact__label"><?php echo esc_html($point[0] ?? ''); ?></span><p><?php echo esc_html($point[1] ?? ''); ?></p></div><?php endforeach; ?>
            </div>
        </div>
    </section>
    <section class="section-inner"><h2><?php echo esc_html($copy['mapTitle'] ?? ''); ?></h2><p class="lead"><?php echo esc_html($copy['mapSub'] ?? ''); ?></p></section>
</section>
