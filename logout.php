<?php
session_start();

require_once 'db.php';
// Clear session
$_SESSION = [];
session_destroy();

// Clear persistent token
try {
    if (isset($_COOKIE['remember_token'])) {
        $cookie = $_COOKIE['remember_token'];
        $hashed_token = hash('sha256', $_COOKIE['remember_token']);
        $pdo->prepare('DELETE FROM auth_tokens WHERE token_hash = ?')
            ->execute([$hashed_token]);

        // Delete cookie from all paths/domains
        setcookie('remember_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => DOMAIN, // Match your login cookie
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None'
        ]);
    }
} catch (PDOException $e) {
    error_log('Logout error: ' . $e->getMessage());
}

header("Location: index.php");
exit;
