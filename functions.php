<?php
defined('ABSPATH') || exit;

foreach (['setup', 'routing', 'content', 'render', 'acf', 'seo', 'cleanup'] as $module) {
    require_once get_template_directory() . '/inc/' . $module . '.php';
}
