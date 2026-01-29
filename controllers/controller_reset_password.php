<?php
require_once '../modelo/db.php';
require_once 'mailNewUser.php';

$conn = conect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'reset_password') {
    $id = intval($_POST['id']);
    $correo = $_POST['correo'];

    // 1. Generar nueva contraseña temporal
    $temporalPassword = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'), 0, 8);

    // 2. Hashear la contraseña
    $hashPassword = password_hash($temporalPassword, PASSWORD_DEFAULT);

    // 3. Actualizar en la base de datos
    $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
    $stmt->bind_param("si", $hashPassword, $id);

    if ($stmt->execute()) {
        // 4. Enviar correo
        $resultadoCorreo = enviarCorreoUsuarioNuevo($correo, $temporalPassword);

        if ($resultadoCorreo['status'] === 'ok') {
            echo json_encode(['status' => 'ok', 'message' => 'Contraseña reseteada y correo enviado.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Contraseña actualizada pero error al enviar correo: ' . $resultadoCorreo['message']]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar contraseña.']);
    }
}
?>
