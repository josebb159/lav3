<?php

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreoRecuperacion($correoDestino, $tokenRecuperacion) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'mail.alquilav.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'alquilavapp@alquilav.com';
        $mail->Password   = 'ic,l7LG@*wNb^26?'; // Tu contraseña real
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Remitente y destinatario
        $mail->setFrom('alquilavapp@alquilav.com', 'Alquilav - Recuperación');
        $mail->addAddress($correoDestino);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de contraseña';

        $enlace = "https://alquilav.com/reset_password.php?token=$tokenRecuperacion";

        // Cuerpo HTML embellecido
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 30px; border-radius: 8px; max-width: 600px; margin: auto;'>
            <div style='text-align: center;'>
                <img src='https://alquilav.com/logo.png' alt='Alquilav' style='max-height: 100px; margin-bottom: 20px;' />
            </div>
            <h2 style='color: #333;'>Recuperación de contraseña</h2>
            <p style='font-size: 16px; color: #555;'>
                Recibimos una solicitud para restablecer tu contraseña en <strong>Alquilav</strong>.
            </p>
            <p style='font-size: 16px; color: #555;'>
                Haz clic en el siguiente botón para crear una nueva contraseña:
            </p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='$enlace' style='background-color: #4A148C; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-size: 16px;'>Restablecer contraseña</a>
            </div>
            <p style='font-size: 14px; color: #777;'>
                Si tú no solicitaste este cambio, puedes ignorar este mensaje. Tu contraseña permanecerá segura.
            </p>
            <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;' />
            <p style='font-size: 12px; color: #aaa; text-align: center;'>
                © " . date("Y") . " Alquilav. Todos los derechos reservados.
            </p>
        </div>
        ";

        // Alternativo (sin HTML)
        $mail->AltBody = "Recuperación de contraseña en Alquilav. Enlace para restablecer: $enlace";

        $mail->send();
        return ['status' => 'ok', 'message' => 'Correo enviado con éxito'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => "No se pudo enviar el correo: {$mail->ErrorInfo}"];
    }
}
