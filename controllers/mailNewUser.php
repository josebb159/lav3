<?php

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreoUsuarioNuevo($correoDestino, $temporalPassword) {
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
        $mail->setFrom('alquilavapp@alquilav.com', 'Alquilav - Cuenta de Delivery');
        $mail->addAddress($correoDestino);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Cuenta de Delivery creada';

$mail->Body = "
<div style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 30px; border-radius: 8px; max-width: 600px; margin: auto;'>
    <div style='text-align: center;'>
        <img src='https://alquilav.com/logo.png' alt='Alquilav' style='max-height: 100px; margin-bottom: 20px;' />
    </div>
    <h2 style='color: #333;'>¡Bienvenido a Alquilav!</h2>
    <p style='font-size: 16px; color: #555;'>
        Se ha creado una cuenta de tipo <strong>delivery</strong> para ti en <strong>Alquilav</strong>.
    </p>
    <p style='font-size: 16px; color: #555;'>
        Esta es tu <strong>contraseña temporal</strong>:
    </p>
    <div style='background-color: #eee; padding: 12px; border-radius: 6px; font-size: 18px; font-weight: bold; text-align: center; margin: 20px 0;'>
        $temporalPassword
    </div>
    <p style='font-size: 14px; color: #777;'>
        Por razones de seguridad, te recomendamos cambiar esta contraseña desde tu perfil después de iniciar sesión.
    </p>
    <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;' />
    <p style='font-size: 12px; color: #aaa; text-align: center;'>
        © " . date("Y") . " Alquilav. Todos los derechos reservados.
    </p>
</div>
";

$mail->AltBody = "Se ha creado una cuenta delivery en Alquilav. Tu contraseña temporal es: $temporalPassword. Te recomendamos cambiarla después de iniciar sesión.";

        $mail->send();
        return ['status' => 'ok', 'message' => 'Correo enviado con éxito'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => "No se pudo enviar el correo: {$mail->ErrorInfo}"];
    }
}
