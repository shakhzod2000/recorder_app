<?php

$env = parse_ini_file('.env');
if (!$env) die('Missing .env file');

define('DOMAIN', $env['DOMAIN']);

define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS']);

define('SMTP_ADDRESS', $env['SMTP_ADDRESS']);
define('SMTP_HOST', $env['SMTP_HOST']);
define('SMTP_PORT', $env['SMTP_PORT']);
define('SMTP_USER', $env['SMTP_USER']);
define('SMTP_PASS', $env['SMTP_PASS']);
define('SMTP_FROM_1', $env['SMTP_FROM_1']);
define('SMTP_FROM_2', $env['SMTP_FROM_2']);


// DB Configuration
$dbname=DB_NAME;
$dbuser=DB_USER;
$dbpass=DB_PASS;
$dbserver=DB_HOST;

$isMobile = preg_match('/iPhone|Android/i', $_SERVER['HTTP_USER_AGENT']);
$duration = $isMobile ? 86400 * 90 : 86400 * 30;

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', $duration);
    ini_set('session.gc_maxlifetime', $duration);
    // session.gc_maxlifetime specifies # of secs after which data will be seen as 'garbage' and potentially cleaned up
    session_set_cookie_params([
        'lifetime' => $duration,
        'path' => '/',
        'domain' => DOMAIN, //.your-domain.com
        'secure' => true,
        'httponly' => true,
        'samesite' => 'None'
    ]);

    session_start();
}

define('HOST','https://' . DOMAIN . '/');

// SMTP-Konfiguration
if(!isset($mail_config)) {
    $mail_config = array(
        'address'    => SMTP_ADDRESS, //your-email-address
        'host'       => SMTP_HOST,
        'port'       => SMTP_PORT,
        'SMTPAuth'   => true,
        'SMTPSecure' => 'tls',
        'username'   => SMTP_USER, // email-address
        'password'   => SMTP_PASS, // 16-digit application password from google account (xxxx xxxx xxxx xxxx)
        'from1'      => SMTP_FROM_1, // <email-address>
        'from2'      => SMTP_FROM_2, //Name of sender
    );
}
