<?php
require 'vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;

function enviarNotificacionFCM($token, $titulo, $mensaje, $id_servicio, $type)
{
    $pathToCredentials = __DIR__ . '/firebase-credentials.json';
    $projectId = 'alquilav-133d2';

    // Verificar que el archivo de credenciales existe
    if (!file_exists($pathToCredentials)) {
        error_log("ERROR FCM: Archivo de credenciales no encontrado en: {$pathToCredentials}");
        return ['error' => 'Archivo de credenciales no encontrado'];
    }

    // Verificar que el archivo es legible
    if (!is_readable($pathToCredentials)) {
        error_log("ERROR FCM: Archivo de credenciales no es legible: {$pathToCredentials}");
        return ['error' => 'Archivo de credenciales no es legible'];
    }

    try {
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        
        // Intentar crear las credenciales
        $credentials = new ServiceAccountCredentials($scopes, $pathToCredentials);
        
        // Intentar obtener el token de acceso
        $authToken = $credentials->fetchAuthToken();
        
        if (!isset($authToken['access_token'])) {
            error_log("ERROR FCM: No se pudo obtener el access_token. Respuesta: " . json_encode($authToken));
            return ['error' => 'No se pudo obtener el token de acceso'];
        }
        
        $accessToken = $authToken['access_token'];

        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $titulo,
                    'body' => $mensaje,
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'type' => $type,
                    'id_servicio' => $id_servicio
                ]
            ]
        ];

        $headers = [
            "Authorization: Bearer $accessToken",
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("ERROR FCM cURL: {$error}");
            return ['error' => "Error cURL: {$error}"];
        }

        $response = json_decode($result, true);
        
        if ($httpCode >= 400) {
            error_log("ERROR FCM HTTP {$httpCode}: {$result}");
            return ['error' => "Error HTTP {$httpCode}", 'details' => $response];
        }

        error_log("FCM Notificación enviada exitosamente. Respuesta: {$result}");
        return ['success' => true, 'response' => $response];

    } catch (\Exception $e) {
        $errorMsg = "ERROR FCM Exception: " . $e->getMessage();
        error_log($errorMsg);
        error_log("Stack trace: " . $e->getTraceAsString());
        return ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
    }
}

// Prueba directa
$token = $_POST['token'] ?? '';
$titulo = $_POST['titulo'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';
$id_servicio = $_POST['id_servicio'] ?? '0';
$type = $_POST['type'] ?? '0';

if (empty($token) || empty($titulo) || empty($mensaje)) {
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

$resultado = enviarNotificacionFCM($token, $titulo, $mensaje, $id_servicio, $type);
echo json_encode($resultado);

