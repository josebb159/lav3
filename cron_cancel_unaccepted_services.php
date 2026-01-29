<?php
// Cron job para cancelar servicios no aceptados después de 5 minutos
require_once __DIR__ . '/modelo/db.php';

$conn = conect();
$conn->set_charset("utf8mb4");

// Archivo de log
$log_file = __DIR__ . '/logs/cron_cancel_unaccepted.txt';
$log_content = "=== CRON CANCELACIÓN AUTOMÁTICA ===\n";
$log_content .= "Fecha y hora: " . date('Y-m-d H:i:s') . "\n";
$servicios_cancelados = 0;

// Consultar servicios pendientes por más de 5 minutos sin aceptar
$sql = "SELECT a.id, a.user_id, a.fecha_inicio, 
               TIMESTAMPDIFF(MINUTE, a.fecha_inicio, NOW()) as minutos_transcurridos
        FROM alquileres a
        WHERE a.status_servicio = 1 
        AND (a.conductor_id = 0 OR a.conductor_id IS NULL)
        AND TIMESTAMPDIFF(MINUTE, a.fecha_inicio, NOW()) >= 5";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rental_id = $row['id'];
        $user_id = $row['user_id'];
        $minutos_transcurridos = $row['minutos_transcurridos'];
        
        // Motivo de cancelación
        $motivo = "Cancelado automáticamente: No hubo domiciliarios disponibles";
        
        // Actualizar estado a cancelado (status_servicio = 7)
        $update = "UPDATE alquileres 
                   SET status_servicio = 7, 
                       status = 'cancelado',
                       fecha_fin = NOW(), 
                       motivo = '$motivo' 
                   WHERE id = $rental_id";
        
        if ($conn->query($update)) {
            // Obtener token FCM del cliente
            $token_cliente = null;
            
            $sql_cliente = "SELECT fcm FROM usuarios WHERE id = $user_id LIMIT 1";
            $res_cliente = $conn->query($sql_cliente);
            if ($res_cliente && $row_cliente = $res_cliente->fetch_assoc()) {
                $token_cliente = $row_cliente['fcm'];
            }
            
            // Enviar notificación al cliente
            if ($token_cliente) {
                $titulo = "Servicio Cancelado";
                $mensaje = "Lo sentimos, no hay domiciliarios disponibles en este momento. Por favor, intenta nuevamente.";
                enviarNotificacionFCM($token_cliente, $titulo, $mensaje, $rental_id, 'service_cancelled_no_availability');
            }
            
            $servicios_cancelados++;
            $log_content .= "- Servicio #$rental_id cancelado (Usuario: $user_id, Tiempo transcurrido: $minutos_transcurridos minutos)\n";
        }
    }
}

function enviarNotificacionFCM($token, $titulo, $mensaje, $id_servico, $type)
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
        'type' => $type
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
$log_content .= "\nTotal de servicios cancelados: $servicios_cancelados\n";
$log_content .= "===================\n\n";

// Guardar en archivo (agregar al contenido existente)
file_put_contents($log_file, $log_content, FILE_APPEND);

echo "Cron ejecutado: " . date('Y-m-d H:i:s') . " - Servicios cancelados: $servicios_cancelados\n";
?>
