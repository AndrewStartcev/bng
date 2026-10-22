<?php
defined('ABSPATH') || exit;
foreach (['setup', 'routing', 'content', 'acf', 'seo'] as $module) {
    require_once get_template_directory() . '/inc/' . $module . '.php';
}
