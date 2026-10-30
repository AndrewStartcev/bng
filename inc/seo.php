<?php
defined('ABSPATH') || exit;
add_action('wp_head',static function ():void {
    if(!is_front_page()||defined('RANK_MATH_VERSION'))return;
    echo '<link rel="canonical" href="'.esc_url(home_url('/')).'">'."\n";
    foreach (bng_domain_urls() as $locale => $url) {
        echo '<link rel="alternate" hreflang="'.esc_attr($locale).'" href="'.esc_url($url).'">'."\n";
    }
    $intro=(string)(bng_copy('home',bng_language())['sub']??'');
    if($intro!=='')echo '<meta name="description" content="'.esc_attr(wp_html_excerpt(wp_strip_all_tags($intro),155,'')).'">'."\n";
},2);
add_action('wp',static function ():void {if(is_front_page())remove_action('wp_head','rel_canonical');});
add_filter('rank_math/frontend/canonical',static fn($canonical)=>is_front_page()?home_url('/'):$canonical);
