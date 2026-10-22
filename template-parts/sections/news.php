<?php defined('ABSPATH') || exit; ?>
<section class="section">
    <?php bng_render_lead($copy, (int) $post_id); ?>
    <div class="columns-2 news-columns">
        <section><h2><?php echo esc_html($copy['logTitle'] ?? ''); ?></h2><p class="text-muted"><?php echo esc_html($copy['logNote'] ?? ''); ?></p>
            <?php foreach (($copy['items'] ?? []) as $item) : ?><article class="news-item"><div class="news-item__date"><?php echo esc_html($item[0] ?? ''); ?><span><?php echo esc_html($item[1] ?? ''); ?></span></div><h3><?php echo esc_html($item[2] ?? ''); ?></h3><p><?php echo esc_html($item[3] ?? ''); ?></p></article><?php endforeach; ?>
        </section>
        <section><h2><?php echo esc_html($copy['aheadTitle'] ?? ''); ?></h2><p class="text-muted"><?php echo esc_html($copy['aheadNote'] ?? ''); ?></p><div class="notice-panel">
            <?php foreach (($copy['ahead'] ?? []) as $item) : ?><div class="news-ahead"><strong><?php echo esc_html($item[0] ?? ''); ?></strong><span><?php echo esc_html($item[1] ?? ''); ?></span></div><?php endforeach; ?>
        </div>
        <div class="section-inner"><h2><?php echo esc_html($copy['mediaTitle'] ?? ''); ?></h2><p><?php echo esc_html($copy['mediaBody'] ?? ''); ?></p><a class="button" href="<?php echo esc_url(home_url('/')); ?>" data-bng-target="company"><?php echo esc_html($copy['mediaCta'] ?? ''); ?> ↗</a></div>
        </section>
    </div>
</section>
