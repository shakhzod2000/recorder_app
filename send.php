<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'config.php';

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';

if (isset($_REQUEST['message'])) {
    // Text Mode
    require 'phpmailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        // SMTP-Configuration
        $mail->isSMTP();
        $mail->Host       = $mail_config['host']; // e.g. smtp.gmail.com
        $mail->Port       = $mail_config['port']; // oder 465
        $mail->SMTPAuth   = $mail_config['SMTPAuth']; // activate SMTP Authentication
        $mail->Username   = $mail_config['username'];
        $mail->Password   = $mail_config['password'];
        $mail->SMTPSecure = $mail_config['SMTPSecure']; // or 'ssl'

        // Sender & Receiver
        $mail->setFrom($mail_config['from1'], $mail_config['from2']);

        $mail->addAddress($_REQUEST['receiver']); // Receiver


        // Nachricht
        $mail->isHTML(true);
        $mail->Subject = 'New message: '.substr($_REQUEST['message'],0,20).'...';
        $mail->Body    = $_REQUEST['message'];
        $mail->send();
        http_response_code(200);
        echo 'Sent!';
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Error with sending: ' . $mail->ErrorInfo;
    }


} else {
    // Audio Mode
    require 'phpmailer/src/SMTP.php';

    if ($_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $webmTmp = $_FILES['audio']['tmp_name'];
        $webmName = pathinfo($_FILES['audio']['name'], PATHINFO_FILENAME);

        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir);

        $mp3Path = $uploadDir . 'recording_' . time() . '.mp3';

        // WebM ? MP3 conversion with ffmpeg
        $ffmpegCmd = "ffmpeg -i " . escapeshellarg($webmTmp) . " -vn -ar 44100 -ac 2 -b:a 192k " . escapeshellarg($mp3Path);
        exec($ffmpegCmd, $output, $returnCode);

        if ($returnCode !== 0) {
            http_response_code(500);
            echo 'Error with converting.';
            exit;
        }

        // Send MP3 via email
        $tmpName = $_FILES['audio']['tmp_name'];
        $filename = $_FILES['audio']['name'];

        $mail = new PHPMailer(true);

        try {
            // SMTP-Configuration
            $mail->isSMTP();
            $mail->Host       = $mail_config['host']; // e.g. smtp.gmail.com
            $mail->SMTPAuth   = $mail_config['SMTPAuth']; // SMTP Authentifizierung aktivieren
            $mail->Username   = $mail_config['username'];
            $mail->Password   = $mail_config['password'];
            $mail->SMTPSecure = $mail_config['SMTPSecure']; // oder 'ssl'
            $mail->Port       = $mail_config['port']; // oder 465

            // Absender & Empfänger
            $mail->setFrom($mail_config['from1'], $mail_config['from2']);

            $mail->addAddress($_REQUEST['receiver']); // Empfaenger

            // Anhang (die Sprachaufnahme)

            $mail->addAttachment($mp3Path, basename($mp3Path));

            // Nachricht
            $mail->isHTML(true);
            $mail->Subject = 'New Recording';
            $mail->Body    = 'A new voice recording is attached.';

            $mail->send();
            http_response_code(200);
            echo 'Sent!';
        } catch (Exception $e) {
            http_response_code(500);
            echo 'Error with sending: ' . $mail->ErrorInfo;
        }
        // Optional: delete MP3 after sending
        unlink($mp3Path);
    } else {
        http_response_code(400);
        echo 'File upload failed.';
    }
}
