<?php
defined('ABSPATH') || exit;
$lang = bng_language();
$labels = bng_labels($lang);
$logo_id = (int) bng_field('bng_logo', 0, 'option');
$current = bng_section_slug();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class('bng-site'); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content"><?php echo esc_html($lang === 'en' ? 'Skip to content' : 'К содержимому'); ?></a>
<header class="site-header">
    <div class="container site-header__inner">
        <a class="site-header__brand" href="<?php echo esc_url(bng_url('home', $lang)); ?>" aria-label="BNG Elements">
            <?php if ($logo_id) : ?>
                <?php echo wp_get_attachment_image($logo_id, 'full', false, ['class' => 'site-header__logo', 'alt' => 'BNG Elements']); ?>
            <?php else : ?>
                <span class="site-header__mark" aria-hidden="true">◉</span>
                <span class="site-header__brand-name"><?php echo esc_html(bng_copy('', $lang)['brand'] ?? 'БНГ'); ?><small><?php echo esc_html(bng_copy('', $lang)['descriptor'] ?? 'ЭЛЕМЕНТС'); ?></small></span>
            <?php endif; ?>
        </a>
        <button class="site-header__toggle" type="button" aria-expanded="false" aria-controls="bng-nav">
            <?php echo esc_html($lang === 'en' ? 'Menu' : 'Меню'); ?> <span aria-hidden="true">☰</span>
        </button>
        <nav class="site-header__nav" id="bng-nav" aria-label="<?php echo esc_attr($lang === 'en' ? 'Main navigation' : 'Основная навигация'); ?>">
            <?php foreach ($labels as $slug => $label) : ?>
                <a href="<?php echo esc_url(bng_url($slug, $lang)); ?>"<?php if ($current === $slug) : ?> class="is-active" aria-current="page"<?php endif; ?>><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="site-header__language" aria-label="<?php echo esc_attr($lang === 'en' ? 'Language' : 'Язык'); ?>">
            <a lang="ru" href="<?php echo esc_url(bng_switch_url('ru')); ?>"<?php if ($lang === 'ru') : ?> aria-current="page"<?php endif; ?>>RU</a>
            <span aria-hidden="true">/</span>
            <a lang="en" href="<?php echo esc_url(bng_switch_url('en')); ?>"<?php if ($lang === 'en') : ?> aria-current="page"<?php endif; ?>>EN</a>
        </div>
    </div>
</header>
