<?php
defined('ABSPATH') || exit;
get_header();
$lang=bng_language();
$post_id=get_queried_object_id();
$intro=bng_field('bng_intro','',$post_id);
$body=bng_field('bng_body','',$post_id);
$gallery=bng_field('bng_gallery',[],$post_id);
$documents=bng_field('bng_documents',[],$post_id);
?>
<main id="content">
<section class="page-hero"><div class="container"><a class="breadcrumb" href="<?php echo esc_url(bng_url('home',$lang)); ?>">← <?php echo esc_html(bng_labels($lang)['home']); ?></a><span class="eyebrow">BNG ELEMENTS</span><h1><?php the_title(); ?></h1><?php if($intro): ?><p><?php echo nl2br(esc_html($intro)); ?></p><?php endif; ?></div></section>
<?php if($body): ?><section class="section"><div class="container content-body"><?php echo wp_kses_post($body); ?></div></section><?php endif; ?>
<?php if(is_array($gallery) && $gallery): ?><section class="section"><div class="container gallery-grid"><?php foreach($gallery as $item){$id=is_array($item)?(int)($item['ID']??0):(int)$item;if($id) echo wp_get_attachment_image($id,'large',false,['loading'=>'lazy']);} ?></div></section><?php endif; ?>
<?php if(is_array($documents) && $documents): ?><section class="section"><div class="container"><h2><?php echo esc_html($lang==='en'?'Documents':'Документы'); ?></h2><div class="download-list"><?php foreach($documents as $doc){$file=$doc['file']??0;$id=is_array($file)?(int)($file['ID']??0):(int)$file;$url=$id?wp_get_attachment_url($id):'';if(!$url)continue; ?><a class="button" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($doc['title']??__('Download PDF','bng')); ?> ↓</a><?php } ?></div></div></section><?php endif; ?>
</main>
<?php get_footer(); ?>
