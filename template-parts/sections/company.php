<?php defined('ABSPATH') || exit; ?>
<section class="section">
    <?php bng_render_lead($copy, (int) $post_id); ?>
    <div class="cards-grid">
        <?php foreach (($copy['pillars'] ?? []) as $pillar) : ?><article class="info-card"><span class="kicker"><?php echo esc_html($pillar[0] ?? ''); ?></span><h3><?php echo esc_html($pillar[1] ?? ''); ?></h3><p><?php echo esc_html($pillar[2] ?? ''); ?></p><strong><?php echo esc_html($pillar[3] ?? ''); ?></strong></article><?php endforeach; ?>
    </div>
    <section class="section-inner"><h2><?php echo esc_html($copy['archTitle'] ?? ''); ?></h2><div class="segment-grid">
        <?php foreach (($copy['arch'] ?? []) as $card) : ?><article class="segment-card"><h3><?php echo esc_html($card[0] ?? ''); ?></h3><span class="kicker"><?php echo esc_html($card[1] ?? ''); ?></span><p><?php echo esc_html($card[2] ?? ''); ?></p><strong><?php echo esc_html($card[3] ?? ''); ?></strong></article><?php endforeach; ?>
    </div></section>
    <section class="section-inner section-split"><h2><?php echo esc_html($copy['aboutTitle'] ?? ''); ?></h2><div class="body-copy"><?php foreach (($copy['about'] ?? []) as $p) : ?><p><?php echo esc_html($p); ?></p><?php endforeach; ?></div></section>
    <section class="section-inner"><h2><?php echo esc_html($copy['contactTitle'] ?? ''); ?></h2><div class="contact-grid">
        <?php foreach (($copy['contacts'] ?? []) as $contact) : ?>
            <?php $value = (string) ($contact[1] ?? ''); ?>
            <div class="contact-card"><span class="kicker"><?php echo esc_html($contact[0] ?? ''); ?></span>
            <?php if (is_email($value)) : ?><a href="mailto:<?php echo esc_attr($value); ?>"><?php echo esc_html($value); ?></a><?php else : ?><strong><?php echo esc_html($value); ?></strong><?php endif; ?>
            <p><?php echo esc_html($contact[2] ?? ''); ?></p></div>
        <?php endforeach; ?>
    </div></section>
</section>
