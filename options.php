<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';

$savedPopup = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $savedPopup = true;

    try {
        for ($i = 1; $i <= 6; $i++) {
            $stmt = $pdo->prepare("
            INSERT INTO target_settings
            (user_id, btn_num, name, email, color, icon_class)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                email = VALUES(email),
                color = VALUES(color),
                icon_class = VALUES(icon_class)
            ");

            $stmt->execute([
                $_SESSION['user_id'],
                $i,
                $_POST["name_$i"],
                $_POST["email_$i"],
                $_POST["color_$i"],
                $_POST["icon_$i"]
            ]);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to save settings. Please try again.";
    }
}


$stmt = $pdo->prepare("
    SELECT btn_num, name, email, color, icon_class 
    FROM target_settings
    WHERE user_id = ?
    ORDER BY btn_num
");
$stmt->execute([$_SESSION['user_id']]);
$savedValues = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);
// PDO::FETCH_GROUP - groups results by 1st column(btn_num) where keys are btn_num
// [
//    1 => ['name' => 'Scholz', 'email' => 'scholz@akeon.de', ...],
//    2 => ['name' => 'Kontakt', 'email' => 'kontakt@akeon.de', ...]
// ]
// PDO::FETCH_UNIQUE - ensures each group contains only one row,
// without this, we'd get nested arrays even for single rows



// Image Upload Code
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    if (isset($_FILES['icon_img']) && $_FILES['icon_img']['error'] === UPLOAD_ERR_OK) {
//        $uploadDir = 'img_uploads/';
//        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
//        $tmpName = $_FILES['icon_img']['tmp_name']; //  temporary file path created by PHP
//        $filename = basename($_FILES['icon_img']['name']); // original name of uploaded file
//        // basename() ensures only filename is used
//        $targetPath = $uploadDir . uniqid() . '_' . $filename;
//        // uniqid() generates unique str (e.g. 65a3c1e6a8a1f) to avoid filename conflicts
//
//        if (move_uploaded_file($tmpName, $targetPath)) {
//            $_SESSION['custom_img'] = $targetPath;
//            chmod($targetPath, 0644); // ensure img file is readable
//        }
//    }
//
//    header('Location: options.php'); // redirect to avoid re-posting
//    exit();
//}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Options</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="assets/bootstrap-5.3.5/css/bootstrap.css" />
    <script src="assets/jQuery/jquery-3.7.1.min.js"></script>
    <link href="assets/font_awesome/css/font_awesome_all.min.css" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/site.webmanifest">
</head>
<script>
    // Set all unset color inputs to #dc3545(red) on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="color"]').forEach(input => {
            if (!input.value || input.value === '#000000') {
                input.value = '#dc3545';
            }
        });
    });
</script>
<!--<body class="p-4" style="background-color: #E9EEF3;">-->
<body style="background-color: #E9EEF3;">

    <?php if ($savedPopup): ?>
        <div class="popup-message saved">
            ✅ Saved!
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
            <a href="index_recorder.php" class="dropdown-item d-flex align-items-center gap-2 px-2 py-1">
                <i class="fa-solid fa-house"></i> Homepage
            </a>
            <a href="logout.php" class="dropdown-item d-flex align-items-center gap-2 px-2 py-1 text-danger">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </nav>

  <form method="POST" action="" enctype="multipart/form-data" class="mt-3">
      <!-- enctype="multipart/form-data" allows image upload-->
      <div class="container">
      <div class="row">
      <?php for ($i=1; $i <= 6; $i++): ?>
      <div class="col-md-6 mb-4">
      <div class="card">
          <div class="card-header">Button <?=$i?></div>
          <div class="card-body row g-3">

              <div class="col-md-6">
                  <label class="form-label">Name:</label>
                  <input type="text" name="name_<?=$i?>" value="<?= htmlspecialchars($savedValues[$i]['name']) ?>" class="form-control">
              </div>

              <div class="col-md-6">
                  <label class="form-label">Email:</label>
                  <input type="email" name="email_<?=$i?>" value="<?= htmlspecialchars($savedValues[$i]['email']) ?>" class="form-control">
              </div>

              <div class="col-md-6">
                  <label class="form-label">Color:</label>
                  <input type="color" name="color_<?=$i?>" value="<?= htmlspecialchars($savedValues[$i]['color']) ?>" class="form-control form-control-color">
              </div>

              <?php
                $icons = [
                    'fa-solid fa-star' => '⭐ Star',
                    'fa-solid fa-heart' => '❤️ Heart',
                    'fa-solid fa-square-check' => '✔️ Check',
                    'fa-solid fa-flag' => '🚩 Flag',
                ];
              ?>
              <div class="col-md-6">
                  <label class="form-label">Icon:</label>
                  <select name="icon_<?=$i?>" class="form-select">
                      <option value="">Choose icon</option>
                      <?php foreach($icons as $value => $label): ?>
                        <option value="<?= $value ?>"
                            <?= $savedValues[$i]['icon_class'] === $value ? 'selected' : '' ?>
                        >
                            <?= $label ?>
                        </option>
                      <?php endforeach; ?>
                  </select>
              </div>

          </div>
      </div>
      </div>
      <?php endfor; ?>
      </div>
      </div>

      <div class="container text-end mb-3">
          <button type="submit" class="btn btn-success">Save</button>
      </div>
  </form>

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
        z-index: 1;
    }
    .popup-message.saved {
        background-color: #f0fff0;
        border-left: 4px solid #4CAF50;
        color: #2e7d32;
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
  </script>

</body>
</html>


<!-- Image Upload Frontend -->
<?php
//$imgPath = $_SESSION['custom_img'] ?? null;
?>
<!--      <div class="col-auto">-->
<!--          <label class="form-label">Bild:</label>-->
<!--          --><?php //if ($imgPath && file_exists($imgPath)): ?>
<!--            <img src="--><?//= htmlspecialchars($imgPath) ?><!--" alt="Benutzerbild" style="width:50px;height:50px;" >-->
<!--          --><?php //else: ?>
<!--            <i class="fas fa-paper-plane"></i>-->
<!--          --><?php //endif; ?>
<!--          <input type="file" name="icon_img" accept="image/*" class="form-control" >-->
<!--      </div>-->
