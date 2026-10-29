<?php
// Exercise routing without bootstrapping WordPress or touching a database.
define('ABSPATH', __DIR__);
function add_action(...$args) {}
function sanitize_key($v) { return preg_replace('/[^a-z0-9_-]/', '', strtolower($v)); }
function wp_unslash($v) { return $v; }
function home_url($v) { return 'https://bng.web-projects-it.ru'.$v; }
function add_query_arg($key, $value, $url) { return $url.'?'.$key.'='.$value; }
require __DIR__.'/../inc/routing.php';
function check($actual, $expected): void {
    if ($actual !== $expected) throw new RuntimeException('Routing regression: '.var_export($actual, true));
}
$_GET['lang']='en';
foreach (['bng-elements.ru','www.bng-elements.ru','BNG-ELEMENTS.RU:443'] as $host) {
    $_SERVER['HTTP_HOST']=$host;
    check(bng_language(), 'ru');
    check(bng_url('products','en'), 'https://bng-elements.com/#products');
}
foreach (['bng-elements.com','www.bng-elements.com'] as $host) {
    $_SERVER['HTTP_HOST']=$host;
    $_GET['lang']='ru';
    check(bng_language(), 'en');
    check(bng_url('home','ru'), 'https://bng-elements.ru/');
}
foreach (['bng.web-projects-it.ru','bng-elements.com.attacker.test','attacker.test'] as $host) {
    $_SERVER['HTTP_HOST']=$host;
    check(bng_domain_urls(), []);
    check(bng_language(), 'ru');
    $_GET['lang']='en';
    check(bng_language(), 'en');
    $_GET['lang']='ru';
}
echo "Domain whitelist, language precedence, dev fallback and section URLs OK\n";
