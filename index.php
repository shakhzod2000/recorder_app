<?php
// this is for debugging
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();
// Redirect to main page if already logged in
if (!empty($_SESSION['user_id'])) {
    header('Location: index_recorder.php');
    exit();
}

require_once 'config.php';
require_once 'db.php';

// Auto-login if session expired but cookie exists
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $hashed_token = hash('sha256', $token);

    $stmt = $pdo->prepare('
        SELECT user_id FROM auth_tokens
        WHERE token_hash = ? AND expires_at > NOW()
    ');
    $stmt->execute([$hashed_token]);

    if ($user_id = $stmt->fetchColumn()) {
        $_SESSION['user_id'] = $user_id;
        header('Location: index_recorder.php');
        exit();
    } else {
        // Invalid token - clear cookie
        setcookie('remember_token', '', time() - 3600, '/', '.ioflow.net', true, true); // 3600 = 1 hour
    }
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <script src="assets/jQuery/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="assets/bootstrap-5.3.5/css/bootstrap.css" />

    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/site.webmanifest">
</head>
<script>
    // Force reload when page is restored from cache
    if (performance.navigation.type === 2) {
        window.location.reload(true);
    }
    // Disable back-forward cache
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };
</script>
<body class="bg-light">
  <div class="container mt-custom">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header text-center">Login</div>
          <div class="card-body">
            <form id="loginForm">
              <div class="mb-3">
                <label class="form-label">Benutzername</label>
                <input type="text" name="username" class="form-control" required>
              </div>
              <div class="mb-1">
                <label class="form-label">Passwort</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <p class="mb-3"><a href="reset_form.php">Passwort vergessen?</a></p>
              <div class="mb-3">
                <label class="form-check d-flex align-items-center gap-2" style="cursor: pointer;">
                  <input type="checkbox" name="remember_me" class="form-check-input" style="cursor: pointer;">
                  <span class="form-check-label">Eingeloggt bleiben</span>
                </label>
              </div>
              <button type="submit" class="btn btn-success w-100">Einloggen</button>
              <div id="error" class="text-danger mt-2"></div>
            </form>
            <hr>
            <p>Noch kein Konto? <a href="register_form.php">Anmelden</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        $.post('login.php', $(this).serialize(), function(res) {
            if (res === 'success') {
                location.href = 'index_recorder.php';
            } else {
                const popup = $('<div class="popup-message err">❌ Login fehlgeschlagen</div>');
                $('body').prepend(popup);
                // Trigger popup after small delay
                setTimeout(() => {
                    popup.css({
                        'top': '5px',
                        'opacity': '1'
                    });
                }, 10);
                // Hide after 3 seconds
                setTimeout(() => {
                    popup.css({
                        'top': '-100px',
                        'opacity': '0'
                    });
                    // Remove from DOM after animation completes
                    popup.on('transitionend', () => popup.remove());
                }, 3000);
            }
        });
    });
  </script>

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
          z-index: 3;
      }
      .popup-message.err {
          background-color: #fff0f0;
          border-left: 4px solid #f44336;
          color: #c62828;
      }

      .mt-custom {
          margin-top: 4rem;
      }
  </style>

</body>
</html>
