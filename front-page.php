<?php
defined('ABSPATH') || exit;
get_header();
?>
<main id="content" class="bng-main"><div class="container">
<?php foreach (['ru','en'] as $lang): ?>
    <?php foreach (bng_sections() as $index=>$slug):
        $copy=bng_copy($slug,$lang);
        $post_id=$slug==='home' ? (int)get_option('page_on_front') : bng_section_id($slug,'ru');
        $template=locate_template('template-parts/sections/'.$slug.'.php');
    ?>
        <div class="bng-screen" data-bng-screen="<?php echo esc_attr($slug); ?>" data-bng-lang="<?php echo esc_attr($lang); ?>" lang="<?php echo esc_attr($lang); ?>"<?php if($slug!=='home'||$lang!=='ru'): ?> hidden<?php endif; ?>>
            <?php if($slug!=='home'): ?>
                <div class="running-head"><span><?php echo esc_html(bng_copy('',$lang)['head'][$slug]??$slug); ?></span><span><?php echo esc_html(str_pad((string)($index+1),2,'0',STR_PAD_LEFT)); ?> / 08</span></div>
            <?php endif; ?>
            <?php if($template)include $template; ?>
            <?php if($slug!=='home' && $post_id): ?>
                <?php $body=bng_field('bng_body','',$post_id); ?>
                <?php if($body && $lang==='ru'): ?><section class="section body-copy"><?php echo wp_kses_post($body); ?></section><?php endif; ?>
                <?php $gallery=bng_field('bng_gallery',[],$post_id); ?>
                <?php if(is_array($gallery) && $gallery): ?><section class="section bng-gallery">
                    <?php foreach($gallery as $photo):
                        $id=is_array($photo)?(int)($photo['ID']??0):(int)$photo;
                        if($id)echo wp_get_attachment_image($id,'large',false,['loading'=>'lazy']);
                    endforeach; ?>
                </section><?php endif; ?>
                <?php $documents=bng_field('bng_documents',[],$post_id); ?>
                <?php if(is_array($documents) && $documents): ?><section class="section"><div class="button-group">
                    <?php foreach($documents as $doc):
                        $file=$doc['file']??0;$id=is_array($file)?(int)($file['ID']??0):(int)$file;
                        $url=$id?wp_get_attachment_url($id):false;
                        if($url): ?><a class="button" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($doc['title']??'PDF'); ?> ↗</a><?php endif;
                    endforeach; ?>
                </div></section><?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>
</div></main>
<?php get_footer(); ?>
