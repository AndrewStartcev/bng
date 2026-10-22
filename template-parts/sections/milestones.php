<?php defined('ABSPATH') || exit; ?>
<section class="section">
    <?php bng_render_lead($copy, (int) $post_id); ?>
    <div class="columns-2 milestones">
        <section><h2><?php echo esc_html($copy['doneTitle'] ?? ''); ?></h2><ol class="timeline timeline--done">
            <?php foreach (($copy['done'] ?? []) as $step) : ?><li><time><?php echo esc_html($step[0] ?? ''); ?></time><div><h3><?php echo esc_html($step[1] ?? ''); ?></h3><p><?php echo esc_html($step[2] ?? ''); ?></p></div></li><?php endforeach; ?>
        </ol></section>
        <section><h2><?php echo esc_html($copy['aheadTitle'] ?? ''); ?></h2><ol class="timeline timeline--ahead">
            <?php foreach (($copy['ahead'] ?? []) as $step) : ?><li><time><?php echo esc_html($step[0] ?? ''); ?></time><div><h3><?php echo esc_html($step[1] ?? ''); ?></h3><p><?php echo esc_html($step[2] ?? ''); ?></p></div></li><?php endforeach; ?>
        </ol></section>
    </div>
</section>
