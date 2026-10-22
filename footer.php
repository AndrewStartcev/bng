<?php
defined('ABSPATH') || exit;
$footer_ru=bng_copy('','ru')['footer']??[];
$footer_en=bng_copy('','en')['footer']??[];
$email=sanitize_email((string)bng_field('bng_contact_email','','option'));
?>
<footer class="site-footer"><div class="container site-footer__inner">
<?php foreach(['legal','licence','note'] as $key): ?>
<p data-bng-i18n-ru="<?php echo esc_attr($footer_ru[$key]??''); ?>" data-bng-i18n-en="<?php echo esc_attr($footer_en[$key]??''); ?>"><?php echo esc_html($footer_ru[$key]??''); ?></p>
<?php endforeach; ?>
<?php if($email): ?><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a><?php endif; ?>
</div></footer><?php wp_footer(); ?></body></html>
