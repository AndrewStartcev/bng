<?php
defined('ABSPATH') || exit;
get_header();
$lang = bng_language();
$title = bng_field('hero_heading_' . $lang, $lang === 'en' ? 'BNG Elements' : 'БНГ ЭЛЕМЕНТС', get_option('page_on_front'));
$intro = bng_field('hero_text_' . $lang, '', get_option('page_on_front'));
$video_id = (int) bng_field('hero_video', 0, get_option('page_on_front'));
$video_src = $video_id ? wp_get_attachment_url($video_id) : '';
?>
<main id="content">
<section class="hero"><div class="container hero__grid">
<div class="hero__copy"><span class="eyebrow">BNG ELEMENTS</span><h1><?php echo esc_html($title); ?></h1><?php if($intro): ?><p><?php echo esc_html($intro); ?></p><?php endif; ?><a class="button" href="<?php echo esc_url(bng_url('products',$lang)); ?>"><?php echo esc_html($lang === 'en' ? 'Explore products' : 'Смотреть продукты'); ?> ↗</a></div>
<div class="hero__media"><?php if($video_src): ?><video playsinline muted autoplay loop preload="metadata"><source src="<?php echo esc_url($video_src); ?>" type="video/mp4"></video><?php else: ?><div class="hero__placeholder"><span>BNG ELEMENTS</span><small><?php echo esc_html($lang==='en'?'Presentation video':'Презентационное видео'); ?></small></div><?php endif; ?></div>
</div></section>
<section class="section"><div class="container"><div class="section__heading"><span class="eyebrow">BNG ELEMENTS</span><h2><?php echo esc_html($lang === 'en' ? 'Explore our company' : 'Направления деятельности'); ?></h2></div><div class="section-grid">
<?php foreach(array_slice(bng_labels($lang),1,true) as $slug=>$label): ?><a class="section-card" href="<?php echo esc_url(bng_url($slug,$lang)); ?>"><span class="section-card__index">0<?php echo esc_html(array_search($slug,array_keys(bng_labels($lang)),true)); ?></span><h3><?php echo esc_html($label); ?></h3><span class="section-card__arrow" aria-hidden="true">↗</span></a><?php endforeach; ?>
</div></div></section>
</main>
<?php get_footer(); ?>
