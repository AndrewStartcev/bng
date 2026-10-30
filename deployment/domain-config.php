<?php
/** Include from wp-config.php BEFORE wp-settings.php, only on client hosting.
 * Remove any existing WP_HOME/WP_SITEURL definitions before including this file.
 * Both domains must share document root, database and a valid HTTPS certificate.
 */
$bng_host = strtolower(explode(':', (string) ($_SERVER['HTTP_HOST'] ?? ''))[0]);
$bng_hosts = [
    'bng-elements.ru' => 'https://bng-elements.ru',
    'www.bng-elements.ru' => 'https://bng-elements.ru',
    'bng-elements.com' => 'https://bng-elements.com',
    'www.bng-elements.com' => 'https://bng-elements.com',
];
if (isset($bng_hosts[$bng_host])) {
    define('WP_HOME', $bng_hosts[$bng_host]);
    define('WP_SITEURL', $bng_hosts[$bng_host]);
}
unset($bng_host, $bng_hosts);
