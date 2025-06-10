<?php
// this is for debugging
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
session_start();

require 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$email    = $_POST['email'] ?? '';

$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
$user = $stmt->fetch();

if ($user) {
    echo 'exists';
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
$stmt->execute([$username, $hash, $email]);

// Automatically log in
$user_id = $pdo->lastInsertId(); // Returns ID of last inserted row
// Create user's default button
$defaultBtn = [
    'btn_num' => 1,
    'name' => $username,
    'email' => $email,
    'color' => '#dc3545', // default red
    'icon' => 'fa-solid fa-star'
];

// Save to DB
$stmt = $pdo->prepare('
    INSERT INTO target_settings 
    (user_id, btn_num, name, email, color, icon_class)
    VALUES (?, ?, ?, ?, ?, ?)
');
$stmt->execute([
    $user_id,
    $defaultBtn['btn_num'],
    $defaultBtn['name'],
    $defaultBtn['email'],
    $defaultBtn['color'],
    $defaultBtn['icon'],
]);

// Session creation
session_regenerate_id(true);
$_SESSION['user_id'] = $user_id;
$_SESSION['username'] = $username;
$_SESSION['logged_in'] = true;

// Add "Remember Me" automatically for new registrations
$isMobile = preg_match('/iPhone|Android/i', $_SERVER['HTTP_USER_AGENT']);
$duration = $isMobile ? 86400 * 90 : 86400 * 30;

$token = bin2hex(random_bytes(32));
$hashed_token = hash('sha256', $token);
$expiry = date('Y-m-d H:i:s', time() + $duration);

$stmt = $pdo->prepare('INSERT INTO auth_tokens (token_hash, user_id, expires_at) VALUES (?, ?, ?)');
$stmt->execute([$hashed_token, $user_id, $expiry]);

setcookie('remember_token', $token, [
    'expires' => time() + $duration,
    'path' => '/',
    'domain' => 'localhost',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

echo 'success';
