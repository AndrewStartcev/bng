<?php
defined('ABSPATH') || exit;
get_header();
$lang = bng_language();
$slug = bng_section_slug();
$copy = bng_copy($slug, $lang);
$post_id = get_queried_object_id();
$gallery = bng_field('bng_gallery', [], $post_id);
$documents = bng_field('bng_documents', [], $post_id);
$section_num = array_search($slug, bng_sections(), true);
?>
<main id="content" class="bng-main">
    <div class="container">
        <div class="running-head"><span><?php echo esc_html(bng_copy('', $lang)['head'][$slug] ?? get_the_title()); ?></span><span><?php echo esc_html(str_pad((string) ((int) $section_num + 1), 2, '0', STR_PAD_LEFT)); ?> / 08</span></div>
        <?php
        $template = locate_template('template-parts/sections/' . $slug . '.php');
        if ($template) {
            include $template;
        } else {
            echo '<section class="section"><h1>' . esc_html(get_the_title()) . '</h1></section>';
        }
        ?>
        <?php $body = bng_field('bng_body', '', $post_id); ?>
        <?php if ($body) : ?><section class="section body-copy"><?php echo wp_kses_post($body); ?></section><?php endif; ?>
        <?php if (is_array($gallery) && $gallery) : ?>
            <section class="section bng-gallery" aria-label="<?php echo esc_attr($lang === 'en' ? 'Photo gallery' : 'Фотогалерея'); ?>">
                <?php foreach ($gallery as $photo) :
                    $image_id = is_array($photo) ? (int) ($photo['ID'] ?? 0) : (int) $photo;
                    if ($image_id) {
                        echo wp_get_attachment_image($image_id, 'large', false, ['loading' => 'lazy']);
                    }
                endforeach; ?>
            </section>
        <?php endif; ?>
        <?php if (is_array($documents) && $documents) : ?>
            <section class="section"><h2><?php echo esc_html($lang === 'en' ? 'Documents' : 'Документы'); ?></h2>
                <div class="button-group">
                    <?php foreach ($documents as $row) :
                        $file = $row['file'] ?? 0;
                        $id = is_array($file) ? (int) ($file['ID'] ?? 0) : (int) $file;
                        $url = $id ? wp_get_attachment_url($id) : false;
                        if (!$url) {
                            continue;
                        }
                    ?>
                        <a class="button" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($row['title'] ?? ($lang === 'en' ? 'Download' : 'Скачать')); ?> ↗</a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
