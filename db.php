<?php

require_once ('config.php');

try {
    $pdo = new PDO(
        "mysql:host={$dbserver};dbname={$dbname}",
        "{$dbuser}",
        "{$dbpass}",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $pdo->exec('DELETE FROM auth_tokens WHERE expires_at < NOW()');
    //echo "✅ Connection successful!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
    exit;
}
