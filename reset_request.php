<?php
require_once 'db.php';
require_once 'send_reset_mail.php';

$email = $_POST['email'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo 'not_found';
    exit;
}

$token = bin2hex(random_bytes(32));
$expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

$stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
$stmt->execute([$token, $expires, $user['id']]);

$reset_link = HOST."reset_password.php?token=$token";
sendPasswordResetMail($email, $reset_link, $mail_config); // ($to, $link)
echo 'sent';
