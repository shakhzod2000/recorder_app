<?php
// this is for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$isMobile = preg_match('/iPhone|Android/i', $_SERVER['HTTP_USER_AGENT']);

require 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$remember_me = isset($_POST['remember_me']); // Checkbox state


$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['logged_in'] = true;

    // Persistent Login, if "remember_me" checked
    if ($remember_me) {
        // Always generate new token
        $token = bin2hex(random_bytes(32));
        $hashed_token = hash('sha256', $token);
        $duration = $isMobile ? 86400 * 90 : 86400 * 30;
        $expiry = date('Y-m-d H:i:s', time() + $duration);

        // Store token in DB
        $stmt = $pdo->prepare('
            INSERT INTO auth_tokens (token_hash, user_id, expires_at) 
            VALUES (?, ?, ?)
        ');
        $stmt->execute([$hashed_token, $user['id'], $expiry]);

        // Set & refresh secure cookie
        setcookie('remember_token', $token, [
            'expires' => time() + $duration,
            'path' => '/',
            'domain' => DOMAIN,
            'secure' => true, // HTTPS only
            'httponly' => true, // Prevent JS access
            'samesite' => 'None' // Required for cross-site in Safari
        ]);
    }
    echo 'success';
} else {
    echo 'error';
}
