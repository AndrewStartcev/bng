<?php defined('ABSPATH') || exit; ?>
<section class="section">
    <?php bng_render_lead($copy, (int) $post_id); ?>
    <div class="columns-2">
        <section class="evidence-panel"><h2><?php echo esc_html($copy['nowTitle'] ?? ''); ?></h2><p class="text-muted"><?php echo esc_html($copy['nowNote'] ?? ''); ?></p><ol class="numbered-list"><?php foreach (($copy['provable'] ?? []) as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?></ol></section>
        <section class="evidence-panel"><h2><?php echo esc_html($copy['earnTitle'] ?? ''); ?></h2><p class="text-muted"><?php echo esc_html($copy['earnNote'] ?? ''); ?></p><ul class="evidence-todo"><?php foreach (($copy['earned'] ?? []) as $item) : ?><li><p><?php echo esc_html($item[0] ?? ''); ?></p><span><?php echo esc_html($item[1] ?? ''); ?></span></li><?php endforeach; ?></ul></section>
    </div>
    <div class="notice-panel"><h2><?php echo esc_html($copy['dossierTitle'] ?? ''); ?></h2><p><?php echo esc_html($copy['dossierBody'] ?? ''); ?></p><a href="<?php echo esc_url(bng_url('company', $lang)); ?>" class="button"><?php echo esc_html($copy['dossierCta'] ?? ''); ?> ↗</a></div>
</section>
