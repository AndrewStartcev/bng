<?php defined('ABSPATH') || exit; $lang = bng_language(); $labels = bng_labels($lang); ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class('bng-site'); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content"><?php echo esc_html($lang === 'en' ? 'Skip to content' : 'К содержимому'); ?></a>
<header class="site-header">
<div class="container site-header__inner">
<a class="site-header__brand" href="<?php echo esc_url(bng_url('home',$lang)); ?>" aria-label="BNG Elements">BNG <span>ELEMENTS</span></a>
<button class="site-header__toggle" aria-expanded="false" aria-controls="bng-nav" type="button"><?php echo esc_html($lang === 'en' ? 'Menu' : 'Меню'); ?><span aria-hidden="true">☰</span></button>
<nav class="site-header__nav" id="bng-nav" aria-label="<?php echo esc_attr($lang === 'en' ? 'Main navigation' : 'Основная навигация'); ?>">
<?php foreach($labels as $slug=>$title): ?><a class="<?php echo (is_singular('bng_section') && str_starts_with(get_post_field('post_name',get_queried_object_id()),$slug.'-')) || ($slug==='home' && !is_singular('bng_section')) ? 'is-active' : ''; ?>" href="<?php echo esc_url(bng_url($slug,$lang)); ?>"><?php echo esc_html($title); ?></a><?php endforeach; ?>
</nav>
<div class="site-header__language"><a lang="ru" href="<?php echo esc_url(bng_switch_url('ru')); ?>">RU</a><span>/</span><a lang="en" href="<?php echo esc_url(bng_switch_url('en')); ?>">EN</a></div>
</div>
</header>
<?php
