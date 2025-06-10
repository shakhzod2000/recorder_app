<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'config.php';

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';

if (isset($_REQUEST['message'])) {
    // Text Modus
    require 'phpmailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        // SMTP-Konfiguration
        $mail->isSMTP();
        $mail->Host       = $mail_config['host']; // z.B. smtp.gmail.com
        $mail->Port       = $mail_config['port']; // oder 465
        $mail->SMTPAuth   = $mail_config['SMTPAuth']; // SMTP Authentifizierung aktivieren
        $mail->Username   = $mail_config['username'];
        $mail->Password   = $mail_config['password'];
        $mail->SMTPSecure = $mail_config['SMTPSecure']; // oder 'ssl'

        // Absender & Empfänger
        $mail->setFrom($mail_config['from1'], $mail_config['from2']);

        $mail->addAddress($_REQUEST['receiver']); // Empfaenger


        // Nachricht
        $mail->isHTML(true);
        $mail->Subject = 'Neue Notiz: '.substr($_REQUEST['message'],0,20).'...';
        $mail->Body    = $_REQUEST['message'];
        $mail->send();
        http_response_code(200);
        echo 'Gesendet!';
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Fehler beim Senden: ' . $mail->ErrorInfo;
    }


} else {
    // Audio Modus
    require 'phpmailer/src/SMTP.php';

    if ($_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $webmTmp = $_FILES['audio']['tmp_name'];
        $webmName = pathinfo($_FILES['audio']['name'], PATHINFO_FILENAME);

        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir);

        $mp3Path = $uploadDir . 'aufnahme_' . time() . '_' . rand(1000, 9999) . '.mp3';

        // WebM ? MP3 konvertieren mit ffmpeg
        $ffmpegCmd = "ffmpeg -i " . escapeshellarg($webmTmp) . " -vn -ar 44100 -ac 2 -b:a 192k " . escapeshellarg($mp3Path);
        exec($ffmpegCmd, $output, $returnCode);

        if ($returnCode !== 0) {
            http_response_code(500);
            echo 'Fehler bei der Konvertierung.';
            exit;
        }

        // MP3 per Mail versenden
        $tmpName = $_FILES['audio']['tmp_name'];
        $filename = $_FILES['audio']['name'];

        $mail = new PHPMailer(true);

        try {
            // SMTP-Konfiguration
            $mail->isSMTP();
            $mail->Host       = $mail_config['host']; // z.B. smtp.gmail.com
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
            $mail->Subject = 'Neue Sprachaufnahme';
            $mail->Body    = 'Im Anhang befindet sich eine neue Sprachaufnahme.';

            $mail->send();
            http_response_code(200);
            echo 'Gesendet!';
        } catch (Exception $e) {
            http_response_code(500);
            echo 'Fehler beim Senden: ' . $mail->ErrorInfo;
        }
        // Optional: MP3 nach Versand löschen
        unlink($mp3Path);
    } else {
        http_response_code(400);
        echo 'Dateiupload fehlgeschlagen.';
    }
}
