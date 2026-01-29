<?php
/**
 * Funciones centralizadas para notificaciones FCM
 * Elimina duplicación de código en lavadora_controller, pago_controller, usuario_controller
 */

require_once __DIR__ . '/helpers.php';

/**
 * Obtener token FCM de un usuario
 */
function getUserFCM($mysqli, $id_usuario) {
    $id_usuario = intval($id_usuario);
    if ($id_usuario <= 0) {
        return null;
    }
    
    $stmt = $mysqli->prepare("SELECT fcm FROM usuarios WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $row = $result->fetch_assoc()) {
        return $row['fcm'];
    }
    
    return null;
}

/**
 * Enviar notificación FCM
 */
function enviarNotificacionFCM($token, $titulo, $mensaje, $id_servicio = "", $type = "") {
    if (empty($token)) {
        log_error("Token FCM vacío", ['titulo' => $titulo]);
        return false;
    }
    
    $url = 'https://alquilav.com/firebase/enviar.php';
    
    $data = [
        'token' => $token,
        'titulo' => $titulo,
        'mensaje' => $mensaje,
        'id_servicio' => $id_servicio,
        'type' => $type
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        log_error("Error en envío FCM", ['error' => $error, 'titulo' => $titulo]);
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    return true;
}
?>
