<?php
defined('ABSPATH') || exit;
$ru=bng_labels('ru');$en=bng_labels('en');$logo=(int)bng_field('bng_logo',0,'option');
?>
<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class('bng-site'); ?>><?php wp_body_open(); ?>
<a class="skip-link" href="#content" data-bng-i18n-ru="К содержимому" data-bng-i18n-en="Skip to content">К содержимому</a>
<header class="site-header"><div class="container site-header__inner">
<a class="site-header__brand" href="<?php echo esc_url(home_url('/')); ?>" data-bng-target="home" aria-label="BNG Elements">
<?php if($logo): echo wp_get_attachment_image($logo,'full',false,['class'=>'site-header__logo','alt'=>'BNG Elements']); else: ?>
<img class="site-header__mark-image" src="<?php echo esc_url(get_template_directory_uri().'/assets/images/bng-logo.svg'); ?>" width="36" height="36" alt="">
<span class="site-header__brand-name"><span data-bng-i18n-ru="<?php echo esc_attr(bng_copy('','ru')['brand']??'БНГ'); ?>" data-bng-i18n-en="<?php echo esc_attr(bng_copy('','en')['brand']??'BNG'); ?>"><?php echo esc_html(bng_copy('','ru')['brand']??'БНГ'); ?></span><small data-bng-i18n-ru="<?php echo esc_attr(bng_copy('','ru')['descriptor']??'ЭЛЕМЕНТС'); ?>" data-bng-i18n-en="<?php echo esc_attr(bng_copy('','en')['descriptor']??'ELEMENTS'); ?>"><?php echo esc_html(bng_copy('','ru')['descriptor']??'ЭЛЕМЕНТС'); ?></small></span>
<?php endif; ?></a>
<button class="site-header__toggle" type="button" aria-expanded="false" aria-controls="bng-nav"><span data-bng-i18n-ru="Меню" data-bng-i18n-en="Menu">Меню</span><span aria-hidden="true">☰</span></button>
<nav class="site-header__nav" id="bng-nav" aria-label="Main navigation">
<?php foreach(['home','rock','evidence','flowsheet','products','milestones','news','company'] as $slug): $label=$ru[$slug]??$slug; ?>
<a href="<?php echo esc_url(home_url('/')); ?>" data-bng-target="<?php echo esc_attr($slug); ?>" data-bng-i18n-ru="<?php echo esc_attr($label); ?>" data-bng-i18n-en="<?php echo esc_attr($en[$slug]??$label); ?>"<?php if($slug==='home'): ?> class="is-active" aria-current="page"<?php endif; ?>><?php echo esc_html($label); ?></a>
<?php endforeach; ?></nav>
<div class="site-header__language"><button type="button" data-bng-lang-button="ru" aria-pressed="true">RU</button><span>/</span><button type="button" data-bng-lang-button="en" aria-pressed="false">EN</button></div>
</div></header>
