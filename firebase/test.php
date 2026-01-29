<?php
require 'vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;

function enviarNotificacionFCM($token, $titulo, $mensaje, $id_servicio, $type)
{
    $pathToCredentials = __DIR__ . '/firebase-credentials.json';
    $projectId = 'alquilav-133d2'; // Reemplaza con tu ID real de proyecto

    $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

    $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
    $credentials = new ServiceAccountCredentials($scopes, $pathToCredentials);
    $accessToken = $credentials->fetchAuthToken()['access_token'];

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
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "Error cURL: $error\n";
    } else {
        echo "Respuesta de Firebase: $result\n";
    }
}

// Prueba directa
$token =  'd_smf1UlRLi0B1F92Hs0dA:APA91bGlhGZ0cr7Y3Gnwo-30dm81z-HYc0xJynDhQZR-MgNjr4BWGfbDpyhGanxq-jYh93OBp3TUJUfKk2pCj7SLyk3hZjc2IWASGXJ2bVeexPIlfKbKDh4';
$titulo =  'Recarga';
$mensaje =  'Se realizo una recarga de $20000';
$id_servicio =   '';
$type =  'open_mis_servicios';

if (empty($token) || empty($titulo) || empty($mensaje)) {
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

enviarNotificacionFCM($token, $titulo, $mensaje, $id_servicio, $type);
