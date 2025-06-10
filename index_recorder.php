<?php
// this is for debugging
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'db.php';

$showPopup = false;
if ($_SESSION['logged_in'] === true) {
    $showPopup = true;
    // unset session to show popup only once
    unset($_SESSION['logged_in']);
}

try {
    $stmt = $pdo->prepare('
        SELECT btn_num, name, email, color, icon_class
        FROM target_settings
        WHERE user_id = ?
        ORDER BY btn_num
    ');
    $stmt->execute([$_SESSION['user_id']]);
    // Create [btn_num => [settings]] map
    $buttons = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);
// PDO::FETCH_GROUP - groups results by 1st column(btn_num) where keys are btn_num
// [
//    1 => ['name' => 'Shakhzod', 'email' => 'example@gmail.com', ...],
//    2 => ['name' => 'Alice', 'email' => 'example@gmail.com', ...]
// ]
// PDO::FETCH_UNIQUE - ensures each group contains only one row,
// without this, we'd get nested arrays even for single rows

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $buttons = []; // Fallback to defaults
}


$output = shell_exec('ffmpeg -version 2>&1');
if (strpos($output, 'ffmpeg version') === false) {
    echo "FFmpeg is NOT installed.\n";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recording Page </title>

    <link rel="stylesheet" href="assets/bootstrap-5.3.5/css/bootstrap.css" />
    <link rel="manifest" href="manifest.json" />
    <script src="assets/jQuery/jquery-3.7.1.min.js"></script>
    <script src="script.js"></script>
    <link href="assets/font_awesome/css/font_awesome_all.min.css" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/site.webmanifest">
</head>
<body class="text-center" style="background-color: #4b4b50;">

    <?php if ($showPopup): ?>
        <div class="popup-message success">
            ✅ Login successful!
        </div>
    <?php endif; ?>

    <nav class="navbar bg-white shadow-sm px-3 py-2">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Recorder App</h4>

            <div id="menuToggle" class="menu-btn text-dark fs-3">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>

        <div class="dropdown-menu dropdown-menu-end p-2" id="dropdownMenu" style="min-width: 150px;">
            <a href="options.php" class="dropdown-item d-flex align-items-center gap-2 px-2 py-1">
                <i class="fa-solid fa-gear"></i> Options
            </a>
            <a href="logout.php" class="dropdown-item d-flex align-items-center gap-2 px-2 py-1 text-danger">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container py-4" style="max-width: 800px;">
        <!-- Status message -->
        <p id="status" class="text-white mt-3"></p>
        <input type="hidden" name="hidden_receiver" id="hidden-receiver">

        <!-- Action Buttons -->
        <div class="d-flex flex-column align-items-center gap-3">
            <div class="d-flex justify-content-center gap-3 my-2">
                <button id="startRecord" class="btn btn-primary  align-items-center justify-content-center"
                      style="width: 100px; height: 100px; font-size: 2.5rem;">
                  <i class="fas fa-microphone"></i>
                </button>

                <button id="toggleInputBtn" class="btn btn-secondary align-items-center justify-content-center"
                    style="width: 100px; height: 100px; font-size: 2.5rem;">
                    <i class="fa-solid fa-pen"></i>
                </button>
            </div>

            <textarea id="inputText" class="form-control d-none"
                      style="width: 100%; max-width: 300px; margin: auto; overflow: hidden" rows="3"></textarea>
        </div>

        <!-- Email Buttons Container -->
        <div class="container mt-4">
            <div id="emailButtonsContainer" class="custom-row justify-content-center">
                <?php foreach($buttons as $btn_num => $button): ?>
                    <?php if ($button['email']): ?>
                        <div class="custom-col mb-3">
                            <button disabled class="btn flex-column align-items-center justify-content-center stopRecord" style="background-color: <?=$button['color']?>; width: 150px; height: 100px; font-size: 1.5rem;" data-receiver="<?=htmlspecialchars($button['email'])?>" >
                                <i class="<?=$button['icon_class']?>" style="color: <?=
                                    match($button['icon_class']) {
                                        'fa-solid fa-star' => '#FFD700',
                                        'fa-solid fa-heart' => '#FF0000',
                                        'fa-solid fa-square-check' => '#00FF00',
                                        'fa-solid fa-flag' => '#FF0000',
                                        default => '#FFFFFF' // Default white for others
                                    }
                                ?>;"></i>
                                <span style="font-size: 0.9rem; margin-top: 4px;"><?=$button['name']?></span>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

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
        .popup-message.success {
            background-color: #f0fff0;
            border-left: 4px solid #4CAF50;
            color: #2e7d32;
        }

        .custom-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.75rem; /* consistent spacing between buttons */
        }
        /* 1 button per row below 380px */
        @media (max-width: 379px) {
            .custom-col {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        /* 2 buttons per row between 380px and 540px */
        @media (min-width: 380px) and (max-width: 540px) {
            .custom-col {
                flex: 0 0 48%;
                max-width: 48%;
            }
            .dropdown-menu a {
                padding: 12px 15px;
            }
        }
        /* 3 buttons per row for ≥541px */
        @media (min-width: 541px) {
            .custom-col {
                flex: 0 0 31%;
                max-width: 31%;
            }
            .container {
                max-width: 560px;
            }
        }

        /* Navbar styles */
        .menu-btn {
            cursor: pointer;
        }
        #dropdownMenu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            opacity: 0;
            transition: all 0.3s ease;
            transform: translateY(-10px);
        }
        #dropdownMenu.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let popupMessage = document.querySelector(".popup-message");

            if (popupMessage) {
                // Slide down into view
                setTimeout(() => {
                    popupMessage.style.top = "5px";
                    popupMessage.style.opacity = "1";
                }, 10);
                // Hide popup after 3 seconds
                setTimeout(function () {
                    popupMessage.style.top = "-100px"; //move back up
                    popupMessage.style.opacity = "0";
                }, 3000);
            }

            // Menu toggle functionality
            const menuToggle = document.getElementById('menuToggle');
            const dropdownMenu = document.getElementById('dropdownMenu');

            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent immediate document click
                dropdownMenu.classList.toggle('show');
            });

            // Close when clicking outside
            document.addEventListener('click', function() {
                dropdownMenu.classList.remove('show');
            });

        });

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('service-worker.js');
        }
    </script>
</body>
</html>
