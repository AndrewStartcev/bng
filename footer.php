<?php
defined('ABSPATH') || exit;
$lang = bng_language();
$footer = bng_copy('', $lang)['footer'] ?? [];
$email = sanitize_email((string) bng_field('bng_contact_email', '', 'option'));
?>
<footer class="site-footer">
    <div class="container site-footer__inner">
        <p><?php echo esc_html($footer['legal'] ?? ('© ' . wp_date('Y') . ' BNG Elements')); ?></p>
        <p><?php echo esc_html($footer['licence'] ?? ''); ?></p>
        <p><?php echo esc_html($footer['note'] ?? ''); ?></p>
        <?php if ($email) : ?><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a><?php endif; ?>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
