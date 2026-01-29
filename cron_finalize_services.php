<?php
// Cron job para finalizar servicios automáticamente
require_once __DIR__ . '/modelo/db.php';

die();
$conn = conect();
$conn->set_charset("utf8mb4");

// Archivo de log
$log_file = __DIR__ . '/cron_log.txt';
$log_content = "=== CRON EJECUTADO ===\n";
$log_content .= "Fecha y hora: " . date('Y-m-d H:i:s') . "\n";
$servicios_finalizados = 0;

// Obtener servicios activos que ya cumplieron su tiempo
$sql = "SELECT a.id, a.user_id, a.conductor_id, a.tiempo_alquiler, a.fecha_inicio, a.adicional_time
        FROM alquileres a
        WHERE a.status_servicio = 2 
        AND TIMESTAMPDIFF(HOUR, a.fecha_inicio, NOW()) >= (a.tiempo_alquiler + IFNULL(a.adicional_time, 0))";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rental_id = $row['id'];
        $user_id = $row['user_id'];
        $conductor_id = $row['conductor_id'];
        
        // Actualizar estado a finalizado (status_servicio = 3)
        $update = "UPDATE alquileres SET status_servicio = 3, fecha_fin = NOW() WHERE id = $rental_id";
        
        if ($conn->query($update)) {
            // Obtener tokens FCM
            $token_cliente = null;
            $token_conductor = null;
            
            $sql_cliente = "SELECT fcm FROM usuarios WHERE id = $user_id LIMIT 1";
            $res_cliente = $conn->query($sql_cliente);
            if ($res_cliente && $row_cliente = $res_cliente->fetch_assoc()) {
                $token_cliente = $row_cliente['fcm'];
            }
            
            if ($conductor_id > 0) {
                $sql_conductor = "SELECT fcm FROM usuarios WHERE id = $conductor_id LIMIT 1";
                $res_conductor = $conn->query($sql_conductor);
                if ($res_conductor && $row_conductor = $res_conductor->fetch_assoc()) {
                    $token_conductor = $row_conductor['fcm'];
                }
            }
            
            // Enviar notificaciones
            $mensaje = "El servicio #$rental_id ha finalizado automáticamente";
            
            if ($token_cliente) {
                enviarNotificacionFCM($token_cliente, "Servicio Finalizado", $mensaje, $rental_id, 'service_finished');
            }
            
            if ($token_conductor) {
                enviarNotificacionFCM($token_conductor, "Servicio Finalizado", $mensaje, $rental_id, 'service_finished');
            }
            
            $servicios_finalizados++;
            $log_content .= "- Servicio #$rental_id finalizado (Usuario: $user_id, Conductor: $conductor_id)\n";
        }
    }
}

function enviarNotificacionFCM($token, $titulo, $mensaje, $id_servico,$type)
{
    $fcm_token = $token;
    $titulo = $titulo;
    $mensaje = $mensaje;

    // Ruta hacia tu script de envío de notificación
    $url = 'https://alquilav.com/firebase/enviar.php';

    // Datos a enviar por POST
    $data = [
        'token' => $fcm_token,
        'titulo' => $titulo,
        'mensaje' => $mensaje,
        'id_servicio' => $id_servico,
        'type' =>$type
    ];

    // Inicializar cURL
    $ch = curl_init($url);

    // Configurar opciones
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Ejecutar la solicitud
    $response = curl_exec($ch);

    // Verificar errores
    if ($response === false) {
       // echo 'Error en cURL: ' . curl_error($ch);
    } else {
       // echo 'Respuesta de Firebase: ' . $response;
    }

    curl_close($ch);

}

// Resumen final
$log_content .= "\nTotal de servicios finalizados: $servicios_finalizados\n";
$log_content .= "===================\n\n";

// Guardar en archivo (sobrescribe el contenido anterior)
file_put_contents($log_file, $log_content);

echo "Cron ejecutado: " . date('Y-m-d H:i:s') . " - Servicios finalizados: $servicios_finalizados\n";
?>
