<?php
defined('ABSPATH') || exit;
$home = $copy;
$front_id = (int) get_option('page_on_front');
$title = (string) bng_field('hero_heading_' . $lang, $home['title'] ?? 'BNG Elements', $front_id);
$subtitle = (string) bng_field('hero_text_' . $lang, $home['sub'] ?? '', $front_id);
$video_id = (int) bng_field('hero_video', 0, $front_id);
$video = $video_id ? wp_get_attachment_url($video_id) : '';
$poster_id = (int) bng_field('hero_poster', 0, $front_id);
$poster = $poster_id ? wp_get_attachment_url($poster_id) : '';
$source = bng_source();
?>
<div class="running-head"><span><?php echo esc_html(bng_copy('', $lang)['head']['home'] ?? 'BNG Elements'); ?></span><span>01 / 08</span></div>
        <section class="home-hero">
            <div>
                <span class="kicker"><?php echo esc_html($home['kicker'] ?? 'BNG Elements'); ?></span>
                <h1><?php echo esc_html($title); ?></h1>
            </div>
            <div class="home-hero__side">
                <p><?php echo esc_html($subtitle); ?></p>
                <div class="button-group">
                    <a class="button" href="<?php echo esc_url(home_url('/')); ?>" data-bng-target="products"><?php echo esc_html($home['cta1'] ?? 'Products'); ?></a>
                    <a class="button button--outline-light" href="<?php echo esc_url(home_url('/')); ?>" data-bng-target="evidence"><?php echo esc_html($home['cta2'] ?? 'Evidence'); ?></a>
                </div>
            </div>
        </section>
        <figure class="hero-video">
            <?php if ($video) : ?>
                <video muted autoplay loop playsinline preload="metadata"<?php if ($poster) : ?> poster="<?php echo esc_url($poster); ?>"<?php endif; ?>><source src="<?php echo esc_url($video); ?>" type="<?php echo esc_attr(get_post_mime_type($video_id) ?: 'video/mp4'); ?>"></video>
            <?php elseif ($poster) : ?>
                <?php echo wp_get_attachment_image($poster_id, 'full', false, ['class' => 'hero-video__poster']); ?>
            <?php else : ?>
                <div class="hero-video__empty"><span class="hero-video__play" aria-hidden="true">▶</span><span>BNG ELEMENTS</span></div>
            <?php endif; ?>
            <figcaption><?php echo esc_html($home['figcap'] ?? ''); ?></figcaption>
        </figure>
        <section class="stream-list" aria-label="<?php echo esc_attr($lang === 'en' ? 'Mineral streams' : 'Минеральные потоки'); ?>">
            <?php foreach (($source['buyers'] ?? []) as $symbol => $buyer) : ?>
                <a class="stream-list__item" href="<?php echo esc_url(home_url('/')); ?>" data-bng-target="flowsheet">
                    <span class="stream-list__symbol"><?php echo esc_html($symbol); ?><small><?php echo esc_html($source['atomic'][$symbol] ?? ''); ?></small></span>
                    <span class="stream-list__name"><?php echo esc_html(bng_copy('', $lang)['element'][$symbol] ?? $symbol); ?><small><?php echo esc_html($buyer[$lang] ?? ''); ?></small></span>
                    <span aria-hidden="true">↗</span>
                </a>
            <?php endforeach; ?>
        </section>
        <section class="home-position">
            <h2><?php echo esc_html($home['posTitle'] ?? ''); ?></h2>
            <div>
                <p class="lead"><?php echo esc_html($home['posBody'] ?? ''); ?></p>
                <div class="facts-grid">
                    <?php foreach (($home['cells'] ?? []) as $cell) : ?>
                        <div class="fact"><span class="fact__label"><?php echo esc_html($cell[0] ?? ''); ?></span><p><?php echo esc_html($cell[1] ?? ''); ?></p></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
