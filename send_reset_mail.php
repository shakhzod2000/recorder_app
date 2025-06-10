<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once 'phpmailer/src/Exception.php';

function sendPasswordResetMail($to, $link, $mail_config) {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    try {
        $mail->isSMTP();
        $mail->Host       = $mail_config['host'];
        $mail->Port       = $mail_config['port'];
        $mail->SMTPAuth   = $mail_config['SMTPAuth'];
        $mail->Username   = $mail_config['username'];
        $mail->Password   = $mail_config['password'];
        $mail->SMTPSecure = $mail_config['SMTPSecure'];

        $mail->setFrom($mail_config['from1'], $mail_config['from2']);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Passwort zurücksetzen';
        $mail->Body    = "Setze dein Passwort zurück: $link";

        $mail->send();
        http_response_code(200);

    } catch (Exception $e) {
        http_response_code(500);
        echo "Mailer Error: " . $mail->ErrorInfo;
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}
