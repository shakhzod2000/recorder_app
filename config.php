<?php

// DB Configuration
$dbname='db-name';
$dbuser='db-username';
$dbpass='db-password';
$dbserver='localhost';

$isMobile = preg_match('/iPhone|Android/i', $_SERVER['HTTP_USER_AGENT']);
$duration = $isMobile ? 86400 * 90 : 86400 * 30;

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', $duration);
    ini_set('session.gc_maxlifetime', $duration);
    // session.gc_maxlifetime specifies # of secs after which data will be seen as 'garbage' and potentially cleaned up
    session_set_cookie_params([
        'lifetime' => $duration,
        'path' => '/',
        'domain' => '.your-domain.com',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'None'
    ]);

    session_start();
}

define('HOST','https://your-domain.com/');

// SMTP-Konfiguration
if(!isset($mail_config)) {
    $mail_config = array(
        'address'    => 'your-email-address',
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'SMTPAuth'   => true,
        'SMTPSecure' => 'tls',
        'username'   => 'username-in-email',
        'password'   => 'xxxx xxxx xxxx xxxx', // 16-digit application password from google account
        'from1'      => 'email-address',
        'from2'      => 'Name of email sender',
    );
}
