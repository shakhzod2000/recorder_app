<?php
require 'db.php';

$resetPopup = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        echo 'invalid_or_expired';
        exit;
    }

    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $stmt->execute([$hash, $user['id']]);
    $resetPopup = true;
} else {
    $token = $_GET['token'] ?? '';
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Reset Passwort</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="assets/bootstrap-5.3.5/css/bootstrap.css" />
    <link href="assets/font_awesome/css/font_awesome_all.min.css" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/site.webmanifest">

    <style>
        .popup-message {
            position: fixed;
            top: -100px; /*start hidden above screen*/
            left: 50%;
            transform: translateX(-50%);
            width: auto;
            padding: 15px;
            border: 1px solid #000;
            border-radius: 4px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            opacity: 0;
            transition: top 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }
        .popup-message.updated {
            background-color: #f0fff0;
            border-left: 4px solid #4CAF50;
            color: #2e7d32;
        }
    </style>
</head>
<body class="bg-light">

  <div class="container d-flex flex-column align-items-center justify-content-center min-vh-100">
    <?php if ($resetPopup): ?>
        <div class="popup-message updated">
            ✅ Passwort aktualisiert!
        </div>
        <!-- Redirect message -->
        <div id="redirectMsg" class="alert alert-warning text-center mb-3 d-none" style="max-width: 500px">
            ⏳ Sie werden zur Login Seite weitergeleitet...
        </div>
    <?php endif; ?>
    <!-- Form Container -->
    <div class="bg-white shadow-sm rounded p-4 w-100" style="max-width: 400px;">
      <h4 class="mb-4 text-center">Passwort zurücksetzen</h4>
      <form method="POST">
        <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
        <div class="mb-3">
            <label class="form-label">Neues Passwort</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Zurücksetzen</button>
      </form>
    </div>

  </div>

  <script>
      document.addEventListener("DOMContentLoaded", function () {
          let popupMsg = document.querySelector(".popup-message");
          const redirectMsg = document.getElementById('redirectMsg');

          if (popupMsg) {
              // Slide down into view
              setTimeout(() => {
                  popupMsg.style.top = "5px";
                  popupMsg.style.opacity = "1";
              }, 10);
              // Hide popup after 3 seconds
              setTimeout(function () {
                  popupMsg.style.top = "-100px"; //move back up
                  popupMsg.style.opacity = "0";
              }, 3000);
          }

          if (redirectMsg) {
              setTimeout(() => {
                  redirectMsg.classList.remove('d-none');
              }, 3000);
              // Redirect after 4 sec
              setTimeout(() => {
                  window.location.href = 'index_recorder.php';
              }, 7000);
          }
      });
  </script>

</body>
</html>
