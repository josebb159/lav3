<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Bogota');

$host = "localhost";
$user = "alquilav_ndb";
$password = "&^L1s,)Z_W56";
$dbname = "alquilav_ndb";

$mysqli = new mysqli($host, $user, $password, $dbname);

if ($mysqli->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Error de conexión']));
}

$result = $mysqli->query("SELECT * FROM config_general LIMIT 1");

if ($config_general = $result->fetch_assoc()) {
    $km = $config_general['km'];
    $valor_minimo = $config_general['valor_minimo'];
    $porcentaje = $config_general['porcentaje']; 
    $global_tarifa = $config_general['global_tarifa'];
}

$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        register($mysqli, $data);
        break;
    case 'login':
        login($mysqli, $data);
        break;
    case 'login_domiciliario':
        login_domiciliario($mysqli, $data);
        break;
    case 'login_google':
        login_google($mysqli, $data);
        break;
    case 'rent_machine':
        rent_machine($mysqli, $data);
        break;
    case 'finish_rental':
        finish_rental($mysqli, $data);
        break;
    case 'get_user':
        get_user($mysqli, $data);
        break;
    case 'get_rental':
        get_rental($mysqli, $data);
        break;
    case 'get_rental_all':
        get_rental_all($mysqli, $data);
        break;
    case 'sum_rent_machine':
        sum_rent_machine($mysqli, $data);
        break;
    case 'available_machines':
        $resultado = available_machines($mysqli, $data);
        log_api($mysqli, 'available_machines', $data, $resultado);
        break;
    case 'edit_user':
        edit_user($mysqli, $data);
        break;
    case 'change_status':
        change_status($mysqli, $data);
        break;
    case 'get_status_driver':
        get_status_driver($mysqli, $data);
        break;
    case 'accept_service':
        accept_service($mysqli, $data);
        break;
    case 'get_pending_deliveries':
        get_pending_deliveries($mysqli, $data);
        break;
    case 'get_delivery_service':
        get_delivery_service($mysqli, $data);
        break;
    case 'delivered':
        mark_delivered_or_collected($mysqli, $data, 'delivered');
        break;
    case 'collected':
        mark_delivered_or_collected($mysqli, $data, 'collected');
        break;
    case 'simulate_delivery':
        simulate_delivery($mysqli, $data);
        break;
    case  'simulate_collection':
        simulate_collection($mysqli, $data);
    case  'lavadoras_asignadas':
        lavadoras_asignadas($mysqli, $data);
        break;
    case  'update_ubicacion_domiciliario':
        update_ubicacion_domiciliario($mysqli, $data);
        break;
    case  'get_servicio_solicitud_domicialiario':
        get_servicio_solicitud_domicialiario($mysqli, $data);
        break;
     case  'get_detail_service':
        get_detail_service($mysqli, $data);
        break;
     case  'aceptar_servicio':
        aceptar_servicio($mysqli, $data);
        break;
    case  'entregar_servicio':
        entregar_servicio($mysqli, $data);
      break;
    case  'get_rental_all_delivery':
        get_rental_all_delivery($mysqli, $data);
        break;

    case  'get_motivos':
        get_motivos($mysqli, $data);
                break;
    case  'cancelar_servicio':
        cancelar_servicio($mysqli, $data);
            break;
    case  'servicio_pendiente':
        servicio_pendiente($mysqli, $data);
        break;
    case 'forgot_password':
        forgot_password($mysqli, $data);
        break;
    case 'get_ubication_domicialiario':
        get_ubication_domicialiario($mysqli, $data);
        break;
   case 'get_ubication_domicialiario_from_deviery':
        get_ubication_domicialiario_from_deviery($mysqli, $data);
        break;
    case 'terminos_cliente':
        terminos_cliente($mysqli, $data);
        break;
    case 'terminos_delivery':
        terminos_delivery($mysqli, $data);
        break;
    case 'update_password_config':
        update_password($mysqli, $data);
    break;
    case 'pendiente_recoger':
        pendiente_recoger($mysqli, $data);
    break;
    case 'get_detail_service_finish':
        get_detail_service_finish($mysqli, $data);
    break;
    case 'recoger':
        recoger($mysqli, $data);
    break;
    case 'recaudado':
        recaudado($mysqli, $data);
    break;
    case 'check_cancelacion_permitida':
        check_cancelacion_permitida($mysqli, $data);
    break;
    case 'get_config_general':
        get_config_general($mysqli, $data);
    break;
    case 'get_banner':
        get_banner($mysqli, $data);
    break;
    case 'send_chat_message':
        send_chat_message($mysqli, $data);
    break;
    case 'get_chat_messages':
        get_chat_messages($mysqli, $data);
    break;
    case 'mark_chat_read':
        mark_chat_read($mysqli, $data);
    break;
    case 'save_fcm':
        save_fcm($mysqli, $data);
    break;
     case 'get_pagos_realizados':
        get_pagos_realizados($mysqli, $data);
    break;
     case 'get_pagos_payu':
        get_pagos_payu($mysqli, $data);
    break;
    
    case 'get_pagos_payu':
        get_pagos_payu($mysqli, $data);
    break;
    
    case 'asignar_lavadora':
        asignar_lavadora($mysqli, $data);
    break;
    
    case 'lavadoras_de_negocio':
        lavadoras_de_negocio($mysqli, $data);
    break;
    case 'get_notificaciones':
        get_notificaciones($mysqli, $data);
    break;
    case 'registrar_notificacion':
        registrar_notificacion($mysqli, $data);
    break;
    case 'get_notificaciones_usuario':
        get_notificaciones_usuario($mysqli, $data);
    break;
    case 'marcar_notificacion_leida':
        marcar_notificacion_leida($mysqli, $data);
    break;
    case 'get_servicios_cercanos':
        get_servicios_cercanos($mysqli, $data);
    break;
    case 'confirmar_entrega_lavadora':
        confirmar_entrega_lavadora($mysqli, $data);
    break;
    case 'get_valor_minimo':
        get_valor_minimo($mysqli, $data);
    break;
    case 'get_user_strikes':
        get_user_strikes($mysqli, $data);
    break;
    case 'recaudado':
        recaudado($mysqli, $data);
    break;
    case 'rate_service':
        rate_service($mysqli, $data);
    break;
    case 'get_contacto_soporte':
        get_contacto_soporte($mysqli, $data);
    break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}

function asignar_lavadora($mysqli, $data) {
    $id_lavadora = intval($data['id_lavadora'] ?? 0);
    $id_domiciliario = intval($data['id_domiciliario'] ?? 0);
    $nuevo_estado_en = $data['en'] ?? ''; // Puede ser 'delivery' o 'bodega'
    // Si el nuevo estado es bodega, desasignamos (id_domiciliario = 0)
    if ($nuevo_estado_en === 'bodega') {
        $id_domiciliario = 0;
    }

    $mysqli->set_charset("utf8mb4");
    // Validaciones básicas
    // Si es para delivery, debe tener domiciliario. Si es bodega, no importa (será 0).
    if ($id_lavadora <= 0 || ($nuevo_estado_en === 'delivery' && $id_domiciliario <= 0) || !in_array($nuevo_estado_en, ['delivery', 'bodega'])) {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
        return;
    }

    // Verificar si la lavadora existe
    $stmt_check = $mysqli->prepare("SELECT id, en FROM lavadoras WHERE id = ?");
    $stmt_check->bind_param("i", $id_lavadora);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Lavadora no encontrada']);
        return;
    }

    $lavadora = $result->fetch_assoc();
    $stmt_check->close();

    // Actualizar asignación y ubicación
    $stmt = $mysqli->prepare("
        UPDATE lavadoras 
        SET id_domiciliario = ?, en = ? 
        WHERE id = ?
    ");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta']);
        return;
    }

    $stmt->bind_param("isi", $id_domiciliario, $nuevo_estado_en, $id_lavadora);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'ok',
            'message' => 'Lavadora asignada correctamente',
            'data' => [
                'id_lavadora' => $id_lavadora,
                'id_domiciliario' => $id_domiciliario,
                'nuevo_estado' => $nuevo_estado_en
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar la lavadora']);
    }

    $stmt->close();
}


function lavadoras_de_negocio($mysqli, $data) {
    // Validar y obtener id del negocio
    $id_negocio = intval($data['id_negocio'] ?? 0);
$mysqli->set_charset("utf8mb4");
    if ($id_negocio <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de negocio inválido', 'debug' => 'id_negocio recibido: ' . ($data['id_negocio'] ?? 'no enviado')]);
        return;
    }

    // Preparar la consulta para evitar inyección SQL
    $stmt = $mysqli->prepare("SELECT * FROM lavadoras WHERE negocio_id = ? AND status != 'eliminado'");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la preparación de la consulta', 'error' => $mysqli->error]);
        return;
    }

    $stmt->bind_param("i", $id_negocio);
    
    // Verificar la ejecución
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Error al ejecutar la consulta', 'error' => $stmt->error]);
        $stmt->close();
        return;
    }
    
    $result = $stmt->get_result();
    
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Error al obtener resultados', 'error' => $stmt->error]);
        $stmt->close();
        return;
    }

    $lavadoras = [];
    while ($row = $result->fetch_assoc()) {
        $lavadoras[] = $row;
    }

    if (!empty($lavadoras)) {
        echo json_encode(
    ['status' => 'ok', 'disponibles' => $lavadoras, 'total' => count($lavadoras)],
    JSON_INVALID_UTF8_SUBSTITUTE
);

    } else {
        // Verificar si existen lavadoras en la base de datos para este negocio
        $check_stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM lavadoras WHERE negocio_id = ?");
        $check_stmt->bind_param("i", $id_negocio);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $check_row = $check_result->fetch_assoc();
        $check_stmt->close();
        
        echo json_encode([
            'status' => 'error', 
            'message' => 'No hay lavadoras asignadas', 
            'debug' => [
                'id_negocio_buscado' => $id_negocio,
                'total_en_db' => $check_row['total']
            ]
        ]);
    }

    $stmt->close();
}







function get_pagos_payu($mysqli, $data) {
    $id_usuario = intval($data['id_usuario'] ?? 0);
$mysqli->set_charset("utf8mb4");
    if (!$id_usuario) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de usuario requerido'
        ]);
        return;
    }

    // El user_id está en el reference_code como PayU-{user_id}-xxxx
    $sql = "SELECT id, reference_code, amount, currency, estado, transaction_id, metodo_pago, fecha_pago, fecha_actualizacion
            FROM pagos_pay
            WHERE SUBSTRING_INDEX(SUBSTRING_INDEX(reference_code, '-', 2), '-', -1) = '$id_usuario'
            ORDER BY fecha_pago DESC";

    $result = $mysqli->query($sql);
    $pagos = [];
    while ($row = $result->fetch_assoc()) {
        $pagos[] = $row;
    }

    echo json_encode([
        'status' => 'ok',
        'pagos' => $pagos
    ]);
}


function get_pagos_realizados($mysqli, $data) {
    $id_usuario = intval($data['id_usuario'] ?? 0);
$mysqli->set_charset("utf8mb4");
    if (!$id_usuario) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de usuario requerido'
        ]);
        return;
    }

    $sql = "SELECT id, referencia, valor, metodo_pago, fecha, negocio_id, estado 
            FROM pagos 
            WHERE id_usuario = $id_usuario 
            ORDER BY fecha DESC";

    $result = $mysqli->query($sql);
    $pagos = [];
    while ($row = $result->fetch_assoc()) {
        $pagos[] = $row;
    }

    echo json_encode([
        'status' => 'ok',
        'pagos' => $pagos
    ]);
}

function getFMCByServicio($mysqli, $id_servicio, $tipo = 'usuario') {
    $id_servicio = intval($id_servicio);
    $tipo = strtolower($tipo);
$mysqli->set_charset("utf8mb4");
    if ($id_servicio <= 0) {
        return null;
    }

    // Consultamos el alquiler
    $sql = "SELECT user_id, conductor_id FROM alquileres WHERE id = $id_servicio LIMIT 1";
    $result = $mysqli->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        if ($tipo === 'usuario') {
            $id_usuario = intval($row['user_id']);
        } elseif ($tipo === 'domiciliario') {
            $id_usuario = intval($row['conductor_id']);
        } else {
            return null; // tipo inválido
        }

        if ($id_usuario > 0) {
            // Buscar el FMC del usuario encontrado
            $sqlUser = "SELECT fcm FROM usuarios WHERE id = $id_usuario LIMIT 1";
            $resultUser = $mysqli->query($sqlUser);

            if ($resultUser && $rowUser = $resultUser->fetch_assoc()) {
                return $rowUser['fcm'];
            }
        }
    }

    return null;
}


function getUserFMC($mysqli, $id_usuario) {
    $id_usuario = intval($id_usuario);
    if ($id_usuario <= 0) {
        return null;
    }
$mysqli->set_charset("utf8mb4");
    $sql = "SELECT fcm FROM usuarios WHERE id = $id_usuario LIMIT 1";
    $result = $mysqli->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['fcm'];
    }

    return null;
}

function save_fcm($mysqli, $data) {
    $userId = intval($data['user_id'] ?? 0);
    $fcm = $mysqli->real_escape_string($data['token'] ?? '');
$mysqli->set_charset("utf8mb4");
    if ($userId == 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario requerido']);
        return;
    }

    $query = "UPDATE usuarios SET fcm = '$fcm' WHERE id = $userId";

    if ($mysqli->query($query)) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al editar usuario']);
    }
}






function send_chat_message($mysqli, $data) {
   $mysqli->set_charset("utf8mb4");
    $id_alquiler = intval($data['id_alquiler'] ?? 0);
    $id_usuario = intval($data['id_usuario'] ?? 0);
    $id_domiciliario = intval($data['id_domiciliario'] ?? 0);
    $remitente = $mysqli->real_escape_string($data['tipo'] ?? '');
    $mensaje = $mysqli->real_escape_string($data['mensaje'] ?? '');

    if (!$id_alquiler || !$id_usuario || !$id_domiciliario || !$remitente || !$mensaje) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Faltan datos para enviar el mensaje'
        ]);
        return;
    }

    $sql = "INSERT INTO chat_mensajes (id_alquiler, id_usuario, id_domiciliario, remitente, mensaje) 
            VALUES ($id_alquiler, $id_usuario, $id_domiciliario, '$remitente', '$mensaje')";
    
    // Obtener nombre del remitente y token del destinatario
    if($remitente == "usuario"){
        // El usuario envió el mensaje, notificar al domiciliario
        $token = getUserFMC($mysqli, $id_domiciliario);
        
        // Obtener nombre del usuario
        $sqlNombre = "SELECT nombre FROM usuarios WHERE id = $id_usuario LIMIT 1";
        $resultNombre = $mysqli->query($sqlNombre);
        $nombreRemitente = "Cliente";
        if($resultNombre && $rowNombre = $resultNombre->fetch_assoc()){
            $nombreRemitente = $rowNombre['nombre'];
        }
    }else{
        // El domiciliario envió el mensaje, notificar al usuario
        $token = getUserFMC($mysqli, $id_usuario);
        
        // Obtener nombre del domiciliario
        $sqlNombre = "SELECT nombre FROM usuarios WHERE id = $id_domiciliario LIMIT 1";
        $resultNombre = $mysqli->query($sqlNombre);
        $nombreRemitente = "Domiciliario";
        if($resultNombre && $rowNombre = $resultNombre->fetch_assoc()){
            $nombreRemitente = $rowNombre['nombre'];
        }
    }
    
    // Enviar notificación con el mensaje real
    if ($token) {
        // Limitar el mensaje a 100 caracteres para la notificación
        $mensajeCorto = strlen($mensaje) > 100 ? substr($mensaje, 0, 100) . '...' : $mensaje;
        
        enviarNotificacionFCM(
            $token, 
            "💬 " . $nombreRemitente, 
            $mensajeCorto, 
            $id_alquiler, 
            "mensaje"
        );
    }
    
    if ($mysqli->query($sql)) {
        echo json_encode([
            'status' => 'ok',
            'message' => 'Mensaje enviado',
            'id_chat' => $mysqli->insert_id
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al enviar el mensaje: ' . $mysqli->error
        ]);
    }
}

function get_chat_messages($mysqli, $data) {
    $id_alquiler = intval($data['id_alquiler'] ?? 0);
    $mysqli->set_charset("utf8mb4");
    if (!$id_alquiler) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de alquiler requerido'
        ]);
        return;
    }

    $sql = "SELECT id_chat, id_alquiler, id_usuario, id_domiciliario, remitente, mensaje, fecha_envio, leido 
            FROM chat_mensajes 
            WHERE id_alquiler = $id_alquiler 
            ORDER BY fecha_envio ASC";

    $result = $mysqli->query($sql);
    $mensajes = [];
    while ($row = $result->fetch_assoc()) {
        $mensajes[] = $row;
    }

    echo json_encode([
        'status' => 'ok',
        'mensajes' => $mensajes
    ]);
}

function mark_chat_read($mysqli, $data) {
    $id_alquiler = intval($data['id_alquiler'] ?? 0);
    $remitente = $mysqli->real_escape_string($data['remitente'] ?? '');
$mysqli->set_charset("utf8mb4");
    if (!$id_alquiler || !$remitente) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Faltan datos para marcar como leído'
        ]);
        return;
    }

    $sql = "UPDATE chat_mensajes 
            SET leido = 1 
            WHERE id_alquiler = $id_alquiler AND remitente != '$remitente'";

    if ($mysqli->query($sql)) {
        echo json_encode([
            'status' => 'ok',
            'message' => 'Mensajes marcados como leídos'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al actualizar: ' . $mysqli->error
        ]);
    }
}




function get_banner($mysqli, $data) {
    $result = $mysqli->query("SELECT banner FROM config_general WHERE id = 1 LIMIT 1");
$mysqli->set_charset("utf8mb4");
    if ($result && $config = $result->fetch_assoc()) {
        echo json_encode([
            'status' => 'ok',
            'banner' => "https://alquilav.com/upload/".$config['banner'] 
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontró configuración'
        ]);
    }
}


function get_config_general($mysqli, $data) {
    $mysqli->set_charset("utf8mb4");
    $result = $mysqli->query("SELECT *
        FROM config_general WHERE id = 1 LIMIT 1");

    if ($config = $result->fetch_assoc()) {
        echo json_encode([
            'status' => 'ok',
            'config' => $config
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontró configuración'
        ]);
    }
}

function get_contacto_soporte($mysqli, $data) {
    $mysqli->set_charset("utf8mb4");
    $result = $mysqli->query("SELECT whatsapp_contacto, correo_contacto 
        FROM config_general WHERE id = 1 LIMIT 1");

    if ($config = $result->fetch_assoc()) {
        echo json_encode([
            'status' => 'ok',
            'whatsapp_contacto' => $config['whatsapp_contacto'],
            'correo_contacto' => $config['correo_contacto']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontró información de contacto'
        ]);
    }
}






function check_cancelacion_permitida($mysqli, $data) {
    $user_id = $data['user_id'] ?? 0;
$mysqli->set_charset("utf8mb4");
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario no proporcionado']);
        return;
    }

    // Obtener cantidad actual de cancelaciones
    $stmt = $mysqli->prepare("SELECT cantidad FROM ban_user WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->fetch();
    $stmt->close();

    $cantidad = $cantidad ?? 0; // Si no existe, asumimos 0

    // Obtener límite desde config_general
    $result = $mysqli->query("SELECT max_intentos_cancelacion FROM config_general LIMIT 1");
    if (!$result || $result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró la configuración']);
        return;
    }
    $row = $result->fetch_assoc();
    $max_intentos = $row['max_intentos_cancelacion'];

    // Comparar
    if ($cantidad >= $max_intentos) {
        echo json_encode(['status' => 'denegado', 'message' => 'Límite de cancelaciones alcanzado']);
    } else {
        echo json_encode([
            'status' => 'ok',
            'message' => 'Cancelación permitida',
            'cancelaciones_realizadas' => $cantidad,
            'max_intentos' => $max_intentos
        ]);
    }
}



function recaudado($mysqli, $data) {
     $user_id = $data['user_id'] ?? 0;
    // status_sevicio 1 = pendiente, 2 = en curso, 3 = por retirar, 4 = finalizado
    $result = $mysqli->query("SELECT monedero FROM usuarios where id = $user_id LIMIT 1");
$mysqli->set_charset("utf8mb4");
    if ($usuario = $result->fetch_assoc()) {
        echo json_encode(['status' => 'ok', 'recaudado' => $usuario['monedero']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay alquiler activo']);
    }
}

function get_detail_service_finish($mysqli, $data) {
    $id_alquiler = $data['id_alquiler'] ?? 0;
    $user_id = $data['user_id'] ?? 0;
    $mysqli->set_charset("utf8mb4");

    if (!$id_alquiler) {
        echo json_encode(['status' => 'error', 'message' => 'ID de alquiler no proporcionado']);
        return;
    }

   
    $sql = "
        SELECT 
            alquileres.latitud AS lat_client,
            alquileres.longitud AS long_client,
            u_delivery.latitud AS lat_delivery,
            u_delivery.longitud AS long_delivery,
            alquileres.user_id AS user_id,
            u_cliente.nombre AS nombre,
            u_cliente.direccion AS direccion,
            u_cliente.telefono AS telefono,
            lavadoras.*,
            alquileres.conductor_id,
            precios_lavado.precio,
            alquileres.fecha_inicio,
            alquileres.fecha_fin
        FROM alquileres
        JOIN usuarios AS u_cliente ON alquileres.user_id = u_cliente.id
        LEFT JOIN usuarios AS u_delivery ON $user_id = u_delivery.id
        JOIN lavadoras ON alquileres.lavadora_id = lavadoras.id
        JOIN precios_lavado on precios_lavado.id_negocio = alquileres.negocio_id and lavadoras.type = precios_lavado.tipo_lavadora
        WHERE alquileres.id = $id_alquiler
          AND alquileres.status = 'finalizado'
        LIMIT 1
    ";

    $result = $mysqli->query($sql);

    if (!$result || $result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró servicio activo']);
        return;
    }

    $data = $result->fetch_assoc();

    echo json_encode([
        'status' => 'ok',
        'servicio' => $data
    ]);
}

function recoger($mysqli, $data) {
        global $porcentaje;
    $user_id = $data['user_id'] ?? 0;
    $id_alquiler = $data['id_alquiler'] ?? null;
    $total = $data['total'] ?? null;
$mysqli->set_charset("utf8mb4");

    if (!$user_id || $id_alquiler === null ) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        return;
    }
            
           $descuento =  calcularPorcentaje($total, $porcentaje);
         
     
          $stmt = $mysqli->prepare("UPDATE usuarios SET monedero = monedero - ?   WHERE id = ?");
            $stmt->bind_param("ii", $descuento,  $user_id);
            $stmt->execute();

    
         $token = getFMCByServicio($mysqli, $id_alquiler, $tipo = 'usuario');
    
        if ($token) {
            enviarNotificacionFCM($token, "Actualización de servicio", "La lavadora ha sido recogida", $id_alquiler, 'update_rental');
        }

    $stmt = $mysqli->prepare("UPDATE alquileres SET status_servicio = 4   WHERE id = ?");
    $stmt->bind_param("i",  $id_alquiler);
    $stmt->execute();

    $result = $mysqli->query("SELECT lavadora_id  FROM alquileres WHERE id = $id_alquiler");
   
    $row = $result->fetch_assoc();
    $lavadora_id = $row['lavadora_id'];

      $stmt = $mysqli->prepare("UPDATE lavadoras SET status = 'disponible'   WHERE id = ?");
    $stmt->bind_param("i",  $lavadora_id);
    $stmt->execute();



    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok', 'message' => 'Servicio aceptado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al aceptar']);
    }

    $stmt->close();
}


function pendiente_recoger($mysqli, $data) {
    $user_id = $data['user_id'] ?? 0;
$mysqli->set_charset("utf8mb4");
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario no proporcionado']);
        return;
    }

    // Obtener el ID del negocio asignado al conductor
    $result = $mysqli->query("SELECT conductor_negocio FROM usuarios WHERE id = $user_id");
    if (!$result || $result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
        return;
    }

    $row = $result->fetch_assoc();
    $conductor_negocio = $row['conductor_negocio'];

    // Buscar si tiene un servicio activo con status_servicio = 1
    $query = $mysqli->query("SELECT * FROM alquileres WHERE negocio_id = $conductor_negocio AND conductor_id = $user_id AND status_servicio = 3 AND status = 'finalizado' LIMIT 1");

    if ($query && $query->num_rows > 0) {
        $servicio = $query->fetch_assoc();

        echo json_encode([
            'status' => 'ok',
            'servicio' => $servicio
        ]);
    } else {
        echo json_encode(['status' => 'ok', 'servicio' => null]);
    }
}

function rate_service($mysqli, $data) {
    $alquiler_id = intval($data['alquiler_id'] ?? 0);
    $usuario_id = intval($data['usuario_id'] ?? 0);
    $puntuacion = intval($data['puntuacion'] ?? 0);
    $comentario = $mysqli->real_escape_string($data['comentario'] ?? '');
    
    $mysqli->set_charset("utf8mb4");

    if (!$alquiler_id || !$usuario_id || !$puntuacion) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos (alquiler_id, usuario_id, puntuacion)']);
        return;
    }

    if ($puntuacion < 1 || $puntuacion > 5) {
        echo json_encode(['status' => 'error', 'message' => 'La puntuación debe ser entre 1 y 5']);
        return;
    }

    // Verificar si ya existe una calificación para este alquiler
    $check = $mysqli->query("SELECT id FROM servicio_calificaciones WHERE alquiler_id = $alquiler_id AND usuario_id = $usuario_id");
    if ($check && $check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Ya has calificado este servicio']);
        return;
    }

    $stmt = $mysqli->prepare("INSERT INTO servicio_calificaciones (alquiler_id, usuario_id, puntuacion, comentario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $alquiler_id, $usuario_id, $puntuacion, $comentario);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok', 'message' => 'Calificación enviada correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar la calificación: ' . $mysqli->error]);
    }
    $stmt->close();
}

function update_password($mysqli, $data) {
      $contrasena = $data['password'] ?? '';
    $id = $data['id'] ?? 0;
$mysqli->set_charset("utf8mb4");
    if (empty($contrasena) || empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
        return;
    }

    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
    $stmt->bind_param("si", $hash, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok', 'message' => 'Contraseña actualizada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar la contraseña']);
    }

    $stmt->close();
}

function terminos_delivery($mysqli, $data) {
 $mysqli->set_charset("utf8mb4");
    // status_sevicio 1 = pendiente, 2 = en curso, 3 = por retirar, 4 = finalizado
    $result = $mysqli->query("SELECT terminos_delivery, terminos_uso_delivery FROM terminos_condiciones ");

    if ($terminos = $result->fetch_assoc()) {
        echo json_encode(['status' => 'ok', 'terminos' => $terminos]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay alquiler activo']);
    }
}

function terminos_cliente($mysqli, $data) {
 $mysqli->set_charset("utf8mb4");
    // status_sevicio 1 = pendiente, 2 = en curso, 3 = por retirar, 4 = finalizado
    $result = $mysqli->query("SELECT terminos, terminos_uso FROM terminos_condiciones ");

    if ($terminos = $result->fetch_assoc()) {
        echo json_encode(['status' => 'ok', 'terminos' => $terminos]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay alquiler activo']);
    }
}


function get_ubication_domicialiario_from_deviery($mysqli, $data) {
    $user_id = $data['user_id'] ?? 0;
$mysqli->set_charset("utf8mb4");
    $result = $mysqli->query("
        SELECT 
            alquileres.latitud AS latitud_servicio, 
            alquileres.longitud AS longitud_servicio, 
            usuarios.latitud AS latitud_delivery, 
            usuarios.longitud AS longitud_delivery 
        FROM usuarios, alquileres 
        WHERE alquileres.conductor_id = usuarios.id 
          AND alquileres.status_servicio = 1 
          AND alquileres.user_id = $user_id
    ");

    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode([
            'status' => 'ok',
            'ubication' => [
                'servicio' => [
                    'latitud' => $row['latitud_servicio'],
                    'longitud' => $row['longitud_servicio']
                ],
                'domiciliario' => [
                    'latitud' => $row['latitud_delivery'],
                    'longitud' => $row['longitud_delivery']
                ]
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontraron ubicaciones'
        ]);
    }
}

function forgot_password($mysqli, $data) {
    $email = $data['email'] ?? null;
$mysqli->set_charset("utf8mb4");
    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Correo electrónico requerido']);
        return;
    }

    // Verificar si el usuario existe
    $stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Generar un token aleatorio de 10 caracteres
        $token = substr(str_shuffle(str_repeat(
            $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ceil(10 / strlen($x))
        )), 1, 10);

        // Guardar el token en la base de datos
        $stmt1 = $mysqli->prepare("UPDATE usuarios SET tocken_recovery = ? WHERE id = ?");
        $stmt1->bind_param("si", $token, $user['id']);
        $stmt1->execute();
        $stmt1->close(); // <- Asegúrate de cerrar el statement

        // Enviar el correo
        require 'mailRecovery.php';
        $resultado = enviarCorreoRecuperacion($email, $token);

        echo json_encode(['status' => 'ok', 'message' => 'Correo enviado con éxito']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Correo electrónico no registrado']);
    }

    $stmt->close(); // <- Cerrar también este statement al final
}

function servicio_pendiente($mysqli, $data) {
    $user_id = $data['user_id'] ?? 0;
$mysqli->set_charset("utf8mb4");
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario no proporcionado']);
        return;
    }

    // Obtener el ID del negocio asignado al conductor
    $result = $mysqli->query("SELECT conductor_negocio FROM usuarios WHERE id = $user_id");
    if (!$result || $result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
        return;
    }

    $row = $result->fetch_assoc();
    $conductor_negocio = $row['conductor_negocio'];

    // Buscar si tiene un servicio activo con status_servicio = 1
    $query = $mysqli->query("SELECT * FROM alquileres WHERE negocio_id = $conductor_negocio AND conductor_id = $user_id AND status_servicio = 1 AND status = 'activo' LIMIT 1");

    if ($query && $query->num_rows > 0) {
        $servicio = $query->fetch_assoc();

        echo json_encode([
            'status' => 'ok',
            'servicio' => $servicio
        ]);
    } else {
        echo json_encode(['status' => 'ok', 'servicio' => null]);
    }
}


function cancelar_servicio($mysqli, $data) {
    $user_id = $data['user_id'] ?? 0;
    $id_alquiler = $data['id_alquiler'] ?? null;
    $id_motivo = $data['motivo'] ?? null;
$mysqli->set_charset("utf8mb4");
    if (!$user_id || $id_alquiler === null) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        return;
    }
    
     $token = getFMCByServicio($mysqli, $id_alquiler, $tipo = 'usuario');
    
    if ($token) {
        enviarNotificacionFCM($token, "Actualización de servicio", "El servicio ha sido cancelado", $id_alquiler, 'update_rental');
    }


     $token = getFMCByServicio($mysqli, $id_alquiler, $tipo = 'domiciliario');
        
        if ($token) {
            enviarNotificacionFCM($token, "Actualización de servicio", "El servicio ha sido cancelado", $id_alquiler, 'update_rental');
        }

    
    
    // Actualizar contador de cancelaciones del usuario
    $stmt = $mysqli->prepare("SELECT cantidad FROM ban_user WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Si existe, obtener cantidad actual y sumarle 1
        $row = $result->fetch_assoc();
        $cantidadActual = (int)$row['cantidad'];
        $nuevaCantidad = $cantidadActual + 1;
    
        $stmt_update = $mysqli->prepare("UPDATE ban_user SET cantidad = ? WHERE id_user = ?");
        $stmt_update->bind_param("ii", $nuevaCantidad, $user_id);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Si no existe, insertar con cantidad = 1
        $cantidadInicial = 1;
        $stmt_insert = $mysqli->prepare("INSERT INTO ban_user (id_user, cantidad) VALUES (?, ?)");
        
        $stmt_insert->bind_param("ii", $user_id, $cantidadInicial);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt->close();
    

    // Hora actual del servidor
    $fecha_actual = date('Y-m-d H:i:s');

    // Actualizar la tabla alquileres
    $stmt1 = $mysqli->prepare("UPDATE alquileres SET status='finalizado', motivo = ?, status_servicio = 5 WHERE id = ?");
    $stmt1->bind_param("ii", $id_motivo, $id_alquiler);
    
    if (!$stmt1->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar alquiler']);
        $stmt1->close();
        return;
    }
    $stmt1->close();

    // Obtener lavadora_id
    $result = $mysqli->query("SELECT lavadora_id FROM alquileres WHERE id = $id_alquiler");
    if (!$result || $result->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Lavadora no encontrada']);
        return;
    }
    $row = $result->fetch_assoc();
    $lavadora_id = $row['lavadora_id'];

    // Actualizar la lavadora como alquilada
    $stmt2 = $mysqli->prepare("UPDATE lavadoras SET status = 'disponible' WHERE id = ?");
    $stmt2->bind_param("i", $lavadora_id);
    
    if ($stmt2->execute()) {
        echo json_encode(['status' => 'ok', 'message' => 'Servicio entregado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar lavadora']);
    }

    $stmt2->close();
}


function get_motivos($mysqli, $data) {
$mysqli->set_charset("utf8mb4");
    $result = $mysqli->query("SELECT * FROM motivo ");

    $motivos = [];
    while ($motivo = $result->fetch_assoc()) {
        $motivos[] = $motivo;  
    }

    if (count($motivos) > 0) {
        echo json_encode(['status' => 'ok', 'motivos' => $motivos]);  
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay alquileres finalizados']);
    }
}


function get_rental_all_delivery($mysqli, $data) {
    $userId = intval($data['user_id'] ?? 0);
    $mysqli->set_charset("utf8mb4");
    
    // Join with servicio_calificaciones to include rating information
    $sql = "SELECT 
                alquileres.*, 
                servicio_calificaciones.puntuacion AS calificacion,
                servicio_calificaciones.comentario AS comentario_calificacion
            FROM alquileres 
            LEFT JOIN servicio_calificaciones 
                ON alquileres.id = servicio_calificaciones.alquiler_id 
                AND servicio_calificaciones.usuario_id = alquileres.user_id
            WHERE alquileres.conductor_id = $userId";
    
    $result = $mysqli->query($sql);

    $rentals = [];
    while ($rental = $result->fetch_assoc()) {
        $rentals[] = $rental;  // Añade cada alquiler a la lista de rentals
    }

    if (count($rentals) > 0) {
        echo json_encode(['status' => 'ok', 'rentals' => $rentals]);  // Devuelve la lista de alquileres
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay alquileres finalizados']);
    }
}

function entregar_servicio($mysqli, $data) {
    $user_id = $data['user_id'] ?? 0;
    $id_alquiler = $data['id_alquiler'] ?? null;
$mysqli->set_charset("utf8mb4");
    if (!$user_id || $id_alquiler === null) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        return;
    }
    
         $token = getFMCByServicio($mysqli, $id_alquiler, $tipo = 'usuario');
    
        if ($token) {
            enviarNotificacionFCM($token, "Actualización de servicio", "La lavadora ha sido entregada", $id_alquiler, 'update_rental');
        }


    // Hora actual del servidor
    $fecha_actual = date('Y-m-d H:i:s');

    // Actualizar la tabla alquileres
    $stmt1 = $mysqli->prepare("UPDATE alquileres SET start_time = ?, status_servicio = 2 WHERE id = ?");
    $stmt1->bind_param("si", $fecha_actual, $id_alquiler);
    
    if (!$stmt1->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar alquiler']);
        $stmt1->close();
        return;
    }

    // Obtener lavadora_id
    $result = $mysqli->query("SELECT lavadora_id FROM alquileres WHERE id = $id_alquiler");
    if (!$result || $result->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Lavadora no encontrada']);
        return;
    }
    $row = $result->fetch_assoc();
    $lavadora_id = $row['lavadora_id'];

    // Actualizar la lavadora como alquilada
    $stmt2 = $mysqli->prepare("UPDATE lavadoras SET status = 'alquilada' WHERE id = ?");
    $stmt2->bind_param("i", $lavadora_id);
    
    if ($stmt2->execute()) {
        echo json_encode(['status' => 'ok', 'message' => 'Servicio entregado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar lavadora']);
    }


}




function aceptar_servicio($mysqli, $data) {
    global $porcentaje;
    $user_id = intval($data['user_id'] ?? 0); // Este es el conductor
    $id_alquiler = intval($data['id_alquiler'] ?? 0);
$mysqli->set_charset("utf8mb4");
    if (!$user_id || !$id_alquiler) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        return;
    }

    // 1. Obtener información del domiciliario (incluyendo negocio)
    $sqlDomiciliario = "SELECT conductor_negocio, nombre, apellido FROM usuarios WHERE id = $user_id LIMIT 1";
    $resDomiciliario = $mysqli->query($sqlDomiciliario);
    
    if(!$resDomiciliario || $resDomiciliario->num_rows === 0){
        echo json_encode(['status' => 'error', 'message' => 'Domiciliario no encontrado']);
        return;
    }
    
    $domiciliario = $resDomiciliario->fetch_assoc();
    $negocio_domiciliario = $domiciliario['conductor_negocio'];
    $nombre_domiciliario = $domiciliario['nombre'] . ' ' . $domiciliario['apellido'];

    // 2. Verificar el alquiler
    $sqlAlquiler = "SELECT * FROM alquileres WHERE id = $id_alquiler AND status_servicio = 1 LIMIT 1";
    $resAlquiler = $mysqli->query($sqlAlquiler);
    
    if($resAlquiler && $alquiler = $resAlquiler->fetch_assoc()){
        // Verificar tipo
        $tipoNecesario = $alquiler['tipo_lavadora']; 
        
        // 3. Verificar que el conductor tenga una lavadora disponible de ese tipo
        $sqlLav = "SELECT id, codigo FROM lavadoras 
                   WHERE id_domiciliario = $user_id 
                   AND type = '$tipoNecesario' 
                   AND status = 'disponible' 
                   LIMIT 1";
                   
        $resLav = $mysqli->query($sqlLav);
        
        if($resLav && $lav = $resLav->fetch_assoc()){
            $idLavadoraAsignar = $lav['id'];
            $codigoLavadora = $lav['codigo'];
            
            // 4. Proceder a asignar
            $fecha_actual = date('Y-m-d H:i:s');
            
            // Update Alquiler
            
            // Update Alquiler

            $stmt = $mysqli->prepare("UPDATE alquileres SET conductor_id = ?, lavadora_id = ?, fecha_aceptado = ?, status_servicio = 6, negocio_id = ? WHERE id = ?");
            $stmt->bind_param("iisii", $user_id, $idLavadoraAsignar, $fecha_actual, $negocio_domiciliario, $id_alquiler);
            
            
            if ($stmt->execute()) {
                // Update Lavadora status
                $mysqli->query("UPDATE lavadoras SET status = 'alquilada' WHERE id = $idLavadoraAsignar");
                
                // Obtener información del negocio
                $sqlNegocio = "SELECT nombre, telefono, direccion FROM negocios WHERE id = $negocio_domiciliario LIMIT 1";
                $resNegocio = $mysqli->query($sqlNegocio);
                $negocio = $resNegocio ? $resNegocio->fetch_assoc() : null;
                
                // Notificar al cliente
                $token = getFMCByServicio($mysqli, $id_alquiler, 'usuario');
                if ($token) {
                    enviarNotificacionFCM($token, "Servicio Aceptado", "$nombre_domiciliario va en camino", $id_alquiler, 'update_rental');
                }
                
                echo json_encode([
                    'status' => 'ok', 
                    'message' => 'Servicio aceptado correctamente',
                    'data' => [
                        'id_alquiler' => $id_alquiler,
                        'lavadora_id' => $idLavadoraAsignar,
                        'codigo_lavadora' => $codigoLavadora,
                        'fecha_aceptado' => $fecha_actual,
                        'negocio' => $negocio
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar servicio']);
            }
            $stmt->close();
            
        } else {
             echo json_encode(['status' => 'error', 'message' => 'No tienes lavadoras disponibles de tipo: ' . $tipoNecesario]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Servicio no encontrado o ya tomado']);
    }
}

function calcularPorcentaje($valor, $porcentaje) {
    return ($valor * $porcentaje) / 100;
}

function get_servicio_solicitud_domicialiario($mysqli, $data) {
    $user_id = $data['user_id'] ?? 0;
$mysqli->set_charset("utf8mb4");
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario no proporcionado']);
        return;
    }

    // Obtener el ID del negocio asignado al conductor
    $result = $mysqli->query("SELECT conductor_negocio FROM usuarios WHERE id = $user_id");
    if (!$result || $result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
        return;
    }

    $row = $result->fetch_assoc();
    $conductor_negocio = $row['conductor_negocio'];

    // MARKETPLACE: Buscar TODOS los servicios pendientes (status_servicio=1) de mi negocio
    // Y que NO tengan conductor asignado (conductor_id=0 o NULL)
    // Opcional: Filtrar cercanos si se desea, por ahora todos los del negocio.
    
    $query = $mysqli->query("SELECT alquileres.*, usuarios.nombre AS nombre_cliente, usuarios.direccion AS direccion_cliente, usuarios.telefono AS telefono_cliente
                             FROM alquileres 
                             JOIN usuarios ON alquileres.user_id = usuarios.id
                             WHERE alquileres.negocio_id = $conductor_negocio 
                             AND alquileres.status_servicio = 1 
                             AND (alquileres.conductor_id = 0 OR alquileres.conductor_id IS NULL)
                             ORDER BY alquileres.fecha_inicio DESC");

    $servicios = [];
    while($item = $query->fetch_assoc()){
        $servicios[] = $item;
    }

    if (count($servicios) > 0) {
        echo json_encode([
            'status' => 'ok',
            'servicios' => $servicios // Devuelve ARRAY de servicios, no solo uno
        ]);
    } else {
        echo json_encode(['status' => 'ok', 'servicios' => []]);
    }
}

function get_detail_service($mysqli, $data) {
    $id_alquiler = $data['id_alquiler'] ?? 0;
    $user_id = $data['user_id'] ?? 0;
    $mysqli->set_charset("utf8mb4");

    if (!$id_alquiler) {
        echo json_encode(['status' => 'error', 'message' => 'ID de alquiler no proporcionado']);
        return;
    }

    // Consulta para obtener el detalle del servicio asignado al conductor
    $sql = "
        SELECT 
            alquileres.latitud AS lat_client,
            alquileres.longitud AS long_client,
            alquileres.user_id AS user_id,
            
            u_delivery.latitud AS lat_delivery,
            u_delivery.longitud AS long_delivery,
            u_cliente.nombre AS nombre,
            u_cliente.direccion AS direccion,
            u_cliente.telefono AS telefono,
            alquileres.status,
            alquileres.fecha_inicio,
            alquileres.fecha_fin,
            alquileres.fecha_aceptado,
            alquileres.status_servicio,
            lavadoras.*,
            alquileres.conductor_id
        FROM alquileres
        JOIN usuarios AS u_cliente ON alquileres.user_id = u_cliente.id
        LEFT JOIN usuarios AS u_delivery ON $user_id = u_delivery.id
        JOIN lavadoras ON alquileres.lavadora_id = lavadoras.id
        WHERE alquileres.id = $id_alquiler

        LIMIT 1
    ";

    $result = $mysqli->query($sql);

    if (!$result || $result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró servicio activo']);
        return;
    }

    $data = $result->fetch_assoc();

    echo json_encode([
        'status' => 'ok',
        'servicio' => $data
    ]);
}

function update_ubicacion_domiciliario($mysqli, $data) {
    $user_id = $data['id_usuario'] ?? 0;
    $latitud = $data['latitud'] ?? null;
    $longitud = $data['longitud'] ?? null;
$mysqli->set_charset("utf8mb4");
    if (!$user_id || $latitud === null || $longitud === null) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        return;
    }

    // Hora actual del servidor
    $fecha_actual = date('Y-m-d H:i:s');

    $stmt = $mysqli->prepare("UPDATE usuarios SET latitud = ?, longitud = ?, ultima_actualizacion_ubicacion = ? WHERE id = ?");
    $stmt->bind_param("sssi", $latitud, $longitud, $fecha_actual, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok', 'message' => 'Ubicación actualizada correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar ubicación']);
    }

    $stmt->close();
}
function register($mysqli, $data) {
    $nombre = $mysqli->real_escape_string($data['nombre'] ?? '');
    $apellido = $mysqli->real_escape_string($data['apellido'] ?? '');
    $telefono = $mysqli->real_escape_string($data['telefono'] ?? '');
    $direccion = $mysqli->real_escape_string($data['direccion'] ?? '');
    $correo = $mysqli->real_escape_string($data['correo'] ?? '');
    $usuario = $mysqli->real_escape_string($data['usuario'] ?? '');
    $contrasena = $mysqli->real_escape_string($data['password'] ?? '');
    $google_token = $mysqli->real_escape_string($data['google_token'] ?? '');

    $mysqli->set_charset("utf8mb4");

    if ($correo == '') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Correo requerido']);
        return;
    }

    // Verificar si el correo ya existe
    $check_query = "SELECT id FROM usuarios WHERE correo = '$correo' LIMIT 1";
    $check_result = $mysqli->query($check_query);
    if ($check_result && $check_result->num_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'El correo ya está registrado']);
        return;
    }

    if ($google_token == '') {
        if ($usuario == '' || $contrasena == '') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Usuario y contraseña requeridos']);
            return;
        }
        $contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
    }

    $query = "INSERT INTO usuarios (nombre, apellido, telefono, direccion, correo, usuario, contrasena, google_token)
              VALUES ('$nombre', '$apellido', '$telefono', '$direccion', '$correo', '$usuario', '$contrasena', '$google_token')";

    try {
        if ($mysqli->query($query)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'ok', 'user_id' => $mysqli->insert_id]);
        } else {
            // Este bloque podría no alcanzarse si mysqli lanza excepciones, pero se mantiene por si acaso/configuración
            throw new Exception($mysqli->error);
        }
    } catch (mysqli_sql_exception $e) {
        header('Content-Type: application/json');
        // Revisar si es error de duplicado (código 1062)
        if ($e->getCode() == 1062) {
             echo json_encode(['status' => 'error', 'message' => 'El correo ya está registrado']);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Error al registrar usuario: ' . $e->getMessage()]);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar usuario']);
    }
}

function login($mysqli, $data) {
    $usuario = $mysqli->real_escape_string($data['correo'] ?? '');
    $contrasena = $mysqli->real_escape_string($data['contrasena'] ?? '');

    if ($usuario == '' || $contrasena == '') {
        echo json_encode(['status' => 'error', 'message' => 'Datos requeridos']);
        return;
    }

    $result = $mysqli->query("SELECT * FROM usuarios WHERE correo = '$usuario' LIMIT 1");

    if ($user = $result->fetch_assoc()) {
        if (password_verify($contrasena, $user['contrasena'])) {
            echo json_encode(['status' => 'ok', 'user' => $user]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    }
}

function mark_delivered_or_collected($mysqli, $data, $type) {
    $service_id = (int)($data['service_id'] ?? 0);
$mysqli->set_charset("utf8mb4");
    if ($service_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de servicio inválido']);
        return;
    }

    // Define el nuevo estado según el tipo de acción
    $new_status = $type === 'delivered' ? 2 : ($type === 'collected' ? 4 : null);

    if ($new_status === null) {
        echo json_encode(['status' => 'error', 'message' => 'Tipo de acción inválido']);
        return;
    }

    $stmt = $mysqli->prepare("UPDATE alquileres SET status_servicio = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $service_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar estado']);
    }

    $stmt->close();
}


function get_pending_deliveries($mysqli, $data) {
 
    // status_sevicio 1 = pendiente, 2 = en curso, 3 = por retirar, 4 = finalizado
    $result = $mysqli->query("SELECT * FROM alquileres WHERE status_servicio in (1,3) AND conductor_id = 0 LIMIT 1");

    if ($rental = $result->fetch_assoc()) {
        echo json_encode(['status' => 'ok', 'rental' => $rental]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay alquiler activo']);
    }
}

function get_delivery_service($mysqli, $data) {
    $delivery_id = $mysqli->real_escape_string($data['delivery_id'] ?? '');
    // status_sevicio 1 = pendiente, 2 = en curso, 3 = por retirar, 4 = finalizado
    $result = $mysqli->query("SELECT * FROM alquileres WHERE status_servicio in (1,3) AND conductor_id = $delivery_id LIMIT 1");
$mysqli->set_charset("utf8mb4");
    if ($rental = $result->fetch_assoc()) {
        echo json_encode(['status' => 'ok', 'rental' => $rental]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay alquiler activo']);
    }
}

function accept_service($mysqli, $data) {

    $delivery_id = $mysqli->real_escape_string($data['delivery_id'] ?? '');
    $service_id = $mysqli->real_escape_string($data['service_id'] ?? '');

 $mysqli->set_charset("utf8mb4");
    $query = "UPDATE alquileres SET conductor_id = $delivery_id WHERE id = $service_id";
    
    $token = getFMCByServicio($mysqli, $service_id, $tipo = 'usuario');
    
    if ($token) {
        enviarNotificacionFCM($token, "Actualización de servicio", "El servicio ha sido aceptado", $service_id, 'update_rental');
    }

    if ($mysqli->query($query)) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al aceptar servicio']);
    }
}

function login_domiciliario($mysqli, $data) {
    $usuario = $mysqli->real_escape_string($data['correo'] ?? '');
    $contrasena = $mysqli->real_escape_string($data['contrasena'] ?? '');
$mysqli->set_charset("utf8mb4");
    if ($usuario == '' || $contrasena == '') {
        echo json_encode(['status' => 'error', 'message' => 'Datos requeridos']);
        return;
    }

    $result = $mysqli->query("SELECT * FROM usuarios WHERE correo = '$usuario' and id_rol=3 LIMIT 1");

    if ($user = $result->fetch_assoc()) {
        if (password_verify($contrasena, $user['contrasena'])) {
            echo json_encode(['status' => 'ok', 'user' => $user]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    }
}

function login_google($mysqli, $data) {
    $correo = $mysqli->real_escape_string($data['correo'] ?? '');
$mysqli->set_charset("utf8mb4");
    if ($correo == '') {
        echo json_encode(['status' => 'error', 'message' => 'Correo requerido']);
        return;
    }

    $result = $mysqli->query("SELECT * FROM usuarios WHERE correo = '$correo' and status=1 LIMIT 1");

    if ($user = $result->fetch_assoc()) {
        echo json_encode(['status' => 'ok', 'user' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no registrado']);
    }
}

function rent_machine($mysqli, $data) {
    global $km, $global_tarifa, $porcentaje;
    $userId = intval($data['user_id'] ?? 0);
    $tiempo = intval($data['tiempo'] ?? 0);
$mysqli->set_charset("utf8mb4");

    if ($userId == 0 || $tiempo == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        return;
    }
    
    
    $latitud = floatval($data['latitud'] ?? 0);
    $longitud = floatval($data['longitud'] ?? 0);
    
        // Evitar duplicados: Verificar si ya existe una solicitud pendiente creada hace menos de 2 minutos
    $check_dup = $mysqli->query("SELECT id FROM alquileres WHERE user_id = $userId AND status_servicio = 1 AND fecha_inicio > DATE_SUB(NOW(), INTERVAL 2 MINUTE) LIMIT 1");
    if ($check_dup && $check_dup->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Ya tienes una solicitud en proceso.']);
        return;
    }


    // debe existir id_lavadora
    if(!isset($data['id_lavadora'])) {
        $negocios = [];
        $result = $mysqli->query("SELECT id, latitud, longitud FROM negocios");
        while ($row = $result->fetch_assoc()) {
            $distancia = calcularDistancia($latitud, $longitud, $row['latitud'], $row['longitud']);
            if ($distancia <= $km) {
                $row['distancia_km'] = $distancia;
                $negocios[] = $row;
            }
        }
        
        if (!empty($negocios)) {
            $ids_negocios = array_column($negocios, 'id');
            $ids_sql = implode(',', array_map('intval', $ids_negocios)); // sanitizar IDs
        
            $result = $mysqli->query("SELECT * FROM lavadoras WHERE status = 'disponible' AND negocio_id IN ($ids_sql) LIMIT 1");
           
            if ($lavadora = $result->fetch_assoc()) {
             
            } else {
                echo json_encode(["mensaje" => "No hay lavadoras disponibles en negocios cercanos"]);
                die();
            }
        } else {
            echo json_encode(["mensaje" => "No hay negocios dentro de $km km"]);
            die();
        }
    }else{
        $result = $mysqli->query("SELECT * FROM lavadoras WHERE status = 'disponible' AND id = {$data['id_lavadora']} LIMIT 1");
        
    }
 
    if ($lavadora = $result->fetch_assoc() || isset($data['id_lavadora'])) {
        
        // MARKETPLACE LOGIC
        // 1. Determinar ID Negocio (Si no viene id_lavadora, usamos el del primer resultado cercano o el seleccionado)
        if(isset($data['id_lavadora'])){
            $idLav = intval($data['id_lavadora']);
            // Obtener datos de la lavadora específica
            $resultLav = $mysqli->query("SELECT * FROM lavadoras WHERE id = $idLav LIMIT 1");
            if($resultLav && $lavadoraEspecifica = $resultLav->fetch_assoc()){
                $lavadora = $lavadoraEspecifica;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lavadora no encontrada']);
                return;
            }
        }
        
        // Usamos los datos de $lavadora para obtener el tipo
        $tipo_lavadora_req = $lavadora['type']; // Tipo detectado

        $metodo = $data['payment_method'];
        $total_amount = $data['total_amount'];
        $tariff_type = $mysqli->real_escape_string($data['tariff_type'] ?? 'normal');

        // Obtener tarifa desde el cliente (service_value reemplaza global_tarifa)
        $tarifa = floatval($data['service_value'] ?? $global_tarifa);

        // NO asignamos lavadora_id aun (NULL). NO asignamos conductor (0).
        // NO asignamos negocio (NULL) - el negocio no es relevante para el marketplace
        
        $mensaje = "Nuevo servicio disponible: " . $tipo_lavadora_req;
        
        // Insert Alquiler con lavadora_id = NULL y negocio_id = NULL
        $fecha_inicio = date('Y-m-d H:i:s');
        $query = "INSERT INTO alquileres (user_id, lavadora_id, tipo_lavadora, tiempo_alquiler, status, fecha_inicio, latitud, longitud, valor_servicio, negocio_id, metodo_pago, total, status_servicio, conductor_id, porcentaje, tariff_type)
                  VALUES ($userId, NULL, '$tipo_lavadora_req', $tiempo, 'activo', '$fecha_inicio', '$latitud', '$longitud', $tarifa, NULL, '$metodo', $total_amount, 1, 0, $porcentaje, '$tariff_type')";

        if ($mysqli->query($query)) {
            $newRentalId = $mysqli->insert_id;
            
            // Notificar a TODOS los conductores activos (sin filtrar por negocio)
            $sqlDrivers = "SELECT fcm FROM usuarios WHERE rol_id = 3 AND fcm IS NOT NULL AND fcm != '' AND activo = 1";
            $resDrivers = $mysqli->query($sqlDrivers);
            while($driver = $resDrivers->fetch_assoc()){
                if(!empty($driver['fcm'])){
                    enviarNotificacionFCM($driver['fcm'], "Nuevo Servicio", "Hay un nuevo servicio disponible cerca.", $newRentalId, 'new_service_available');
                }
            }

            echo json_encode(['status' => 'ok', 'lavadora_id' => 0, 'rental_id' => $newRentalId, 'message' => 'Solicitud creada, esperando conductor']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al realizar alquiler: ' . $mysqli->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay cobertura o lavadoras disponibles']);
    }
}

function sum_rent_machine($mysqli, $data) {
    $userId = intval($data['user_id'] ?? 0);
$mysqli->set_charset("utf8mb4");
    if ($userId <= 0) {
        return [
            'status' => 'error',
            'message' => 'ID de usuario inválido'
        ];
    }

    // Verificar el tiempo adicional actual
    $check = $mysqli->query("SELECT id, conductor_id, adicional_time FROM alquileres WHERE user_id = $userId AND status = 'activo' LIMIT 1");

    if ($check && $row = $check->fetch_assoc()) {
        $adicional = intval($row['adicional_time']);
        $conductor_id = intval($row['conductor_id']);
        $rentalId = intval($row['id']);

        if ($adicional >= 2) {
            echo json_encode( [
                'status' => 'error',
                'message' => 'No se puede aumentar más el tiempo adicional (máximo 2)'
            ]);
            return; // Important to stop execution here
        }

        // Si es menor que 2, actualizamos
        $sql = "UPDATE alquileres 
                SET tiempo_alquiler = tiempo_alquiler + 1, adicional_time = adicional_time + 1 
                WHERE user_id = $userId AND status = 'activo' LIMIT 1";

        if ($mysqli->query($sql)) {
            
            // Enviar notificación al domiciliario con el nombre del cliente
            if ($conductor_id > 0) {
                // Obtener nombre del cliente
                $sqlCliente = "SELECT nombre FROM usuarios WHERE id = $userId LIMIT 1";
                $resultCliente = $mysqli->query($sqlCliente);
                $nombreCliente = "El cliente";
                
                if ($resultCliente && $rowCliente = $resultCliente->fetch_assoc()) {
                    $nombreCliente = $rowCliente['nombre'];
                }
                
                $token = getUserFMC($mysqli, $conductor_id); 
                if ($token) { 
                    enviarNotificacionFCM(
                        $token, 
                        '⏰ Tiempo Adicional Solicitado', 
                        $nombreCliente . ' ha solicitado 1 hora adicional de servicio. Servicio #' . $rentalId, 
                        $rentalId, 
                        'add_time'
                    ); 
                } 
            }
            
            echo json_encode( [
                'status' => 'ok',
                'message' => 'Tiempo adicional agregado'
            ]);
        } else {
            echo json_encode( [
                'status' => 'error',
                'message' => 'Error al actualizar: ' . $mysqli->error
            ]);
        }
    }


}


function finish_rental($mysqli, $data) {
    global $config_general;
    $rentalId = intval($data['rental_id'] ?? 0);
$mysqli->set_charset("utf8mb4");
    if ($rentalId == 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de alquiler requerido']);
        return;
    }

    $result = $mysqli->query("SELECT lavadora_id, conductor_id, total, valor_servicio, tiempo_alquiler FROM alquileres WHERE id = $rentalId AND status = 'activo' LIMIT 1");

    $token = getFMCByServicio($mysqli, $rentalId, $tipo = 'domiciliario');
    
    if ($token) {
        enviarNotificacionFCM($token, "Actualización de servicio", "Servicio finalizado, pendiente de recogida", $rentalId, 'update_rental');
    }

    if ($rental = $result->fetch_assoc()) {
        $lavadoraId = $rental['lavadora_id'];
        $conductorId = $rental['conductor_id'];
        
        // Recalcular el total del servicio usando valor_servicio * tiempo_alquiler
        $valorServicio = floatval($rental['valor_servicio']);
        $tiempoAlquiler = floatval($rental['tiempo_alquiler']);
        $totalServicio = $valorServicio * $tiempoAlquiler;

        // Calcular comisión
        $porcentaje = floatval($config_general['porcentaje'] ?? 0);
        $comision = ($totalServicio * $porcentaje) / 100;

        // Descontar del monedero del domiciliario
        if ($conductorId > 0 && $comision > 0) {
            $updateMonedero = $mysqli->query("UPDATE usuarios SET monedero = monedero - $comision WHERE id = $conductorId");
            
            if (!$updateMonedero) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Error al descontar comisión del monedero'
                ]);
                return;
            }
        }

        // Actualizar lavadora y alquiler
        $fecha_fin = date('Y-m-d H:i:s');
        $mysqli->query("UPDATE lavadoras SET status = 'disponible' WHERE id = $lavadoraId");
        $mysqli->query("UPDATE alquileres SET status = 'finalizado', status_servicio = 3, fecha_fin = '$fecha_fin' WHERE id = $rentalId");

        echo json_encode([
            'status' => 'ok',
            'message' => 'Servicio finalizado',
            'comision_descontada' => $comision,
            'porcentaje_aplicado' => $porcentaje
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Alquiler no encontrado o ya finalizado']);
    }
}

function get_user($mysqli, $data) {
    $userId = intval($data['id'] ?? 0);
$mysqli->set_charset("utf8mb4");
    $result = $mysqli->query("SELECT * FROM usuarios WHERE id = $userId LIMIT 1");

    if ($user = $result->fetch_assoc()) {
        echo json_encode(['status' => 'ok', 'user' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    }
}

function get_rental($mysqli, $data) {
    $userId = intval($data['user_id'] ?? 0);
$mysqli->set_charset("utf8mb4");

    $result = $mysqli->query("SELECT * FROM alquileres WHERE user_id = $userId AND status_servicio in (1,2,3,6) ORDER by id DESC limit 1;");

    if ($rental = $result->fetch_assoc()) {
        echo json_encode(['status' => 'ok', 'rental' => $rental]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay alquiler activo']);
    }
}

function get_rental_all($mysqli, $data) {
    $userId = intval($data['user_id'] ?? 0);
    
    $mysqli->set_charset("utf8mb4");
    
    // Join with servicio_calificaciones to include rating information
    $sql = "SELECT 
                alquileres.*, 
                servicio_calificaciones.puntuacion AS calificacion,
                servicio_calificaciones.comentario AS comentario_calificacion
            FROM alquileres 
            LEFT JOIN servicio_calificaciones 
                ON alquileres.id = servicio_calificaciones.alquiler_id 
                AND servicio_calificaciones.usuario_id = $userId
            WHERE alquileres.user_id = $userId";
    
    $result = $mysqli->query($sql);

    $rentals = [];
    while ($rental = $result->fetch_assoc()) {
        $rentals[] = $rental;  // Añade cada alquiler a la lista de rentals
    }

    if (count($rentals) > 0) {
        echo json_encode(['status' => 'ok', 'rentals' => $rentals]);  // Devuelve la lista de alquileres
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay alquileres finalizados']);
    }
}


function lavadoras_asignadas($mysqli, $data) {
    $userId = intval($data['user_id'] ?? 0);

    if ($userId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario requerido']);
        return;
    }
    $mysqli->set_charset("utf8mb4");
    
    // Obtener TODAS las lavadoras asignadas al domiciliario
    $stmt = $mysqli->prepare("
        SELECT 
            lavadoras.id,
            lavadoras.codigo,
            lavadoras.status,
            lavadoras.negocio_id,
            lavadoras.fecha,
            lavadoras.type,
            lavadoras.en,
            lavadoras.id_domiciliario,
            CASE 
                WHEN lavadoras.status = 'alquilada' THEN 
                    (SELECT CONCAT(u.nombre, ' ', u.apellido) 
                     FROM alquileres a 
                     JOIN usuarios u ON a.user_id = u.id 
                     WHERE a.lavadora_id = lavadoras.id 
                     AND a.status_servicio IN (1,2,3,6) 
                     LIMIT 1)
                ELSE NULL
            END as cliente_actual
        FROM lavadoras 
        WHERE lavadoras.id_domiciliario = ? AND lavadoras.status != 'eliminado'
        ORDER BY lavadoras.status ASC, lavadoras.id ASC
    ");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
        return;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $asings = [];
    while ($lavadora = $result->fetch_assoc()) {
        // Obtener precios globales para este tipo de lavadora
        $tipo_lavadora = $mysqli->real_escape_string($lavadora['type']);
        
        // CAMBIO: Usar precios globales (id_negocio = 0)
        $precios_query = $mysqli->query("
            SELECT tipo_servicio, precio 
            FROM precios_lavado 
            WHERE tipo_lavadora = '$tipo_lavadora' 
            AND id_negocio = 0
        ");
        
        $precios = [
            'normal' => 0,
            '24horas' => 0,
            'nocturno' => 0
        ];
        
        if ($precios_query) {
            while ($precio_row = $precios_query->fetch_assoc()) {
                $precios[$precio_row['tipo_servicio']] = (float)$precio_row['precio'];
            }
        }
        
        // Agregar precios al objeto lavadora
        $lavadora['precios'] = $precios;
        $lavadora['precio_normal'] = $precios['normal'];
        $lavadora['precio_24horas'] = $precios['24horas'];
        $lavadora['precio_nocturno'] = $precios['nocturno'];
        
        $asings[] = $lavadora;
    }

    if (count($asings) > 0) {
        echo json_encode(['status' => 'ok', 'asignadas' => $asings], JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        echo json_encode(['status' => 'ok', 'asignadas' => []]);
    }

    $stmt->close();
}


function edit_user($mysqli, $data) {
    $userId = intval($data['id'] ?? 0);
    $nombre = $mysqli->real_escape_string($data['nombre'] ?? '');
    $apellido = $mysqli->real_escape_string($data['apellido'] ?? '');
    $telefono = $mysqli->real_escape_string($data['telefono'] ?? '');
    $direccion = $mysqli->real_escape_string($data['direccion'] ?? '');
$mysqli->set_charset("utf8mb4");
    if ($userId == 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario requerido']);
        return;
    }

    $query = "UPDATE usuarios SET nombre = '$nombre', apellido = '$apellido', telefono = '$telefono', direccion = '$direccion' WHERE id = $userId";

    if ($mysqli->query($query)) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al editar usuario']);
    }
}

function change_status($mysqli, $data) {
    $userId = intval($data['user_id'] ?? 0);
    $activo = isset($data['activo']) ? intval($data['activo']) : -1;
    $mysqli->set_charset("utf8mb4");

    if ($userId <= 0 || !in_array($activo, [0, 1])) {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
        return;
    }

    $stmt = $mysqli->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
    $stmt->bind_param("ii", $activo, $userId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok', 'message' => 'Estado actualizado', 'nuevo_estado' => $activo]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar estado']);
    }
    $stmt->close();
}

function get_status_driver($mysqli, $data) {
    $userId = intval($data['user_id'] ?? 0);
    $mysqli->set_charset("utf8mb4");

    if ($userId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario requerido']);
        return;
    }

    $stmt = $mysqli->prepare("SELECT activo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(['status' => 'ok', 'activo' => intval($row['activo'])]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    }
    $stmt->close();
}


function available_machines($mysqli, $data) {
    global $km, $global_tarifa, $valor_minimo;
    $latitud = floatval($data['latitud'] ?? 0);
    $longitud = floatval($data['longitud'] ?? 0);

    if ($latitud == 0 || $longitud == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Ubicación requerida']);
        return;
    }

    $tipos_lavadora = [
        'Manual doble tina sin bomba',
        'Manual doble tina con bomba',
        'Automática de 18 libras',
        'Automática de 24 libras'
    ];

    $response = ['status' => 'ok', 'data' => []];

    foreach ($tipos_lavadora as $tipo) {
        $countDisponibles = 0;
        $ejemploLavadora = null;
        $tarifasEncontradas = [
            'normal' => 0,
            '24horas' => 0,
            'nocturno' => 0
        ];
        



        // Buscar TODAS las lavadoras disponibles de ese tipo
        $query = "SELECT lavadoras.*, usuarios.latitud, usuarios.longitud, usuarios.monedero 
                  FROM lavadoras
                  JOIN usuarios ON lavadoras.id_domiciliario = usuarios.id
                  WHERE lavadoras.status = 'disponible' AND lavadoras.type = '$tipo' AND usuarios.activo = 1";
                  
     $mysqli->set_charset("utf8mb4");

          
        $result = $mysqli->query($query);
 
        
        // Iterar sobre todas las lavadoras candidatas
        while ($lav = $result->fetch_assoc()) {
    
            // Verificar rango y monedero
            $is_in_range = estaDentroDelRango($latitud, $longitud, $lav['latitud'], $lav['longitud'], $km);
          
            if ($is_in_range && $lav['monedero'] >= $valor_minimo) {
                
            
                // Es válida
                $countDisponibles++;
                    
                // Guardamos la primera válida como ejemplo para devolver ID y Precios
                if ($ejemploLavadora === null) {
                    $ejemploLavadora = $lav; 
                    
                    // CAMBIO: Cargar tarifas globales (id_negocio = 0) en lugar de por negocio
                    $tarifas_query = "SELECT tipo_servicio, precio FROM precios_lavado 
                                      WHERE tipo_lavadora = '$tipo' AND id_negocio = 0";
                                      
    
                    $tarifas_result = $mysqli->query($tarifas_query);
                    
                    // Tarifa base global por defecto
                    $tarifasEncontradas['normal'] = $global_tarifa;
                    
                    while ($t = $tarifas_result->fetch_assoc()) {
                        $tarifasEncontradas[$t['tipo_servicio']] = (float)$t['precio'];
                    }
                }
            }
        }

        // Armar respuesta para este tipo
        if ($countDisponibles > 0 && $ejemploLavadora) {
            $response['data'][] = [
                'type' => $tipo,
                'disponibles' => $countDisponibles,
                'id_lavadora' => (int)$ejemploLavadora['id'], // ID de una de las disponibles
                'tarifas' => $tarifasEncontradas
            ];
        } else {
            $response['data'][] = [
                'type' => $tipo,
                'disponibles' => 0,
                'id_lavadora' => 0,
                'tarifas' => [
                    'normal' => 0,
                    '24horas' => 0,
                    'nocturno' => 0
                ]
            ];
        }
    }

    echo json_encode($response);
}

function estaDentroDelRango($lat1, $lon1, $lat2, $lon2, $km_maximo) {

    $radioTierra = 6371; // km

    // Convertir a float
    $lat1 = floatval($lat1);
    $lon1 = floatval($lon1);
    $lat2 = floatval($lat2);
    $lon2 = floatval($lon2);
    $km_maximo = floatval($km_maximo);

    //  echo "<strong>📌 Coordenadas:</strong><br>";
    // echo "Origen → Lat: {$lat1}, Lon: {$lon1}<br>";
    // echo "Destino → Lat: {$lat2}, Lon: {$lon2}<br><br>";

    // Convertir grados a radianes
    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);

    // Haversine
    $difLat = $lat2Rad - $lat1Rad;
    $difLon = $lon2Rad - $lon1Rad;

    $a = sin($difLat / 2) * sin($difLat / 2) +
         cos($lat1Rad) * cos($lat2Rad) *
         sin($difLon / 2) * sin($difLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distancia = $radioTierra * $c;

    // echo "<strong>📏 Cálculo:</strong><br>";
    // echo "Distancia: " . round($distancia, 3) . " km<br>";
    // echo "Rango máximo: {$km_maximo} km<br><br>";

    if ($distancia <= $km_maximo) {
        //  echo "✅ <strong>RESULTADO:</strong> DENTRO del rango<br>";
        return true;
    } else {
        //     echo "❌ <strong>RESULTADO:</strong> FUERA del rango<br>";
        return false;
    }
}

function get_ubication_domicialiario($mysqli, $data) {
    $user_id = $data['user_id'] ?? 0;
    $mysqli->set_charset("utf8mb4");
    $result = $mysqli->query("SELECT latitud, longitud FROM usuarios WHERE id = $user_id");
    $row = $result->fetch_assoc();
    echo json_encode(['status' => 'ok', "ubication" => [ 'latitud' => $row['latitud'], 'longitud' => $row['longitud']]]);
}

function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
    $radioTierra = 6371; // Radio de la tierra en km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $radioTierra * $c;
}


function simulate_collection($mysqli, $data) {
    $userId = intval($data['user_id'] ?? 0);
$mysqli->set_charset("utf8mb4");
    $temporalUserDelivery = 25;

    if ($userId == 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario requerido']);
        return;
    }
    // actualizo el servicio y la fecha de inicio del servicio a la fecha actual
    $query = "UPDATE alquileres SET  status_servicio = 4 WHERE user_id = $userId ";


    if ($mysqli->query($query)) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al editar usuario']);
    }
}



function simulate_delivery($mysqli, $data) {
    $userId = intval($data['user_id'] ?? 0);

    $temporalUserDelivery = 25;
$mysqli->set_charset("utf8mb4");
    if ($userId == 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario requerido']);
        return;
    }
    // actualizo el servicio y la fecha de inicio del servicio a la fecha actual
    $start_time = date('Y-m-d H:i:s');
    $query = "UPDATE alquileres SET start_time = '$start_time', conductor_id = $temporalUserDelivery, status_servicio = 2 WHERE user_id = $userId ";


    if ($mysqli->query($query)) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al editar usuario']);
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

function get_notificaciones($mysqli, $data) {
    $user_id = intval($data['user_id'] ?? 0);
    $tipo_usuario = $data['tipo'] ?? 'cliente'; // 'cliente' o 'domiciliario'

    if ($user_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario requerido']);
        return;
    }
$mysqli->set_charset("utf8mb4");
    // Obtener notificaciones basadas en servicios del usuario
    if ($tipo_usuario === 'cliente') {
        // Notificaciones para clientes basadas en sus alquileres
        $query = "
            SELECT 
                a.id as id_alquiler,
                a.status_servicio,
                a.fecha_inicio,
                a.fecha_aceptado,
                a.start_time,
                a.fecha_fin,
                l.type as tipo_lavadora,
                l.codigo as codigo_lavadora,
                CONCAT(u.nombre, ' ', u.apellido) as nombre_domiciliario,
                u.telefono as telefono_domiciliario,
                CASE 
                    WHEN a.status_servicio = 1 THEN 'Buscando domiciliario...'
                    WHEN a.status_servicio = 2 AND a.start_time IS NULL THEN 'Domiciliario en camino'
                    WHEN a.status_servicio = 2 AND a.start_time IS NOT NULL THEN 'Servicio en curso'
                    WHEN a.status_servicio = 3 THEN 'Servicio finalizado, esperando recogida'
                    WHEN a.status_servicio = 4 THEN 'Servicio completado'
                    WHEN a.status_servicio = 5 THEN 'Servicio cancelado'
                    ELSE 'Estado desconocido'
                END as mensaje,
                CASE 
                    WHEN a.status_servicio = 1 THEN 'pendiente'
                    WHEN a.status_servicio = 2 THEN 'en_curso'
                    WHEN a.status_servicio = 3 THEN 'por_recoger'
                    WHEN a.status_servicio = 4 THEN 'completado'
                    WHEN a.status_servicio = 5 THEN 'cancelado'
                    ELSE 'desconocido'
                END as tipo_notificacion
            FROM alquileres a
            LEFT JOIN lavadoras l ON a.lavadora_id = l.id
            LEFT JOIN usuarios u ON a.conductor_id = u.id
            WHERE a.user_id = ?
            AND a.status_servicio IN (1,2,3)
            ORDER BY a.fecha_inicio DESC
            LIMIT 20
        ";
    } else {
        // Notificaciones para domiciliarios basadas en servicios asignados
        $query = "
            SELECT 
                a.id as id_alquiler,
                a.status_servicio,
                a.fecha_inicio,
                a.fecha_aceptado,
                a.start_time,
                a.fecha_fin,
                a.latitud,
                a.longitud,
                l.type as tipo_lavadora,
                l.codigo as codigo_lavadora,
                CONCAT(u.nombre, ' ', u.apellido) as nombre_cliente,
                u.telefono as telefono_cliente,
                u.direccion as direccion_cliente,
                CASE 
                    WHEN a.status_servicio = 2 AND a.start_time IS NULL THEN 'Recoger lavadora y entregar al cliente'
                    WHEN a.status_servicio = 2 AND a.start_time IS NOT NULL THEN 'Servicio en curso'
                    WHEN a.status_servicio = 3 THEN 'Recoger lavadora del cliente'
                    ELSE 'Servicio activo'
                END as mensaje,
                CASE 
                    WHEN a.status_servicio = 2 AND a.start_time IS NULL THEN 'por_entregar'
                    WHEN a.status_servicio = 2 AND a.start_time IS NOT NULL THEN 'en_curso'
                    WHEN a.status_servicio = 3 THEN 'por_recoger'
                    ELSE 'activo'
                END as tipo_notificacion
            FROM alquileres a
            LEFT JOIN lavadoras l ON a.lavadora_id = l.id
            LEFT JOIN usuarios u ON a.user_id = u.id
            WHERE a.conductor_id = ?
            AND a.status_servicio IN (2,3)
            ORDER BY a.fecha_inicio DESC
            LIMIT 20
        ";
    }

    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
        return;
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $notificaciones = [];
    while ($row = $result->fetch_assoc()) {
        $notificaciones[] = $row;
    }

    echo json_encode([
        'status' => 'ok',
        'notificaciones' => $notificaciones,
        'total' => count($notificaciones)
    ]);

    $stmt->close();
}

function registrar_notificacion($mysqli, $data) {
    $user_id = intval($data['user_id'] ?? 0);
    $tipo_usuario = $data['tipo_usuario'] ?? 'cliente';
    $titulo = $mysqli->real_escape_string($data['titulo'] ?? '');
    $mensaje = $mysqli->real_escape_string($data['mensaje'] ?? '');
    $tipo_notificacion = $mysqli->real_escape_string($data['tipo_notificacion'] ?? 'general');
    $id_relacionado = intval($data['id_relacionado'] ?? 0);
$mysqli->set_charset("utf8mb4");
    if ($user_id <= 0 || empty($titulo) || empty($mensaje)) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        return;
    }

    $stmt = $mysqli->prepare("
        INSERT INTO notificaciones_usuarios 
        (user_id, tipo_usuario, titulo, mensaje, tipo_notificacion, id_relacionado) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
        return;
    }

    $stmt->bind_param("issssi", $user_id, $tipo_usuario, $titulo, $mensaje, $tipo_notificacion, $id_relacionado);

    if ($stmt->execute()) {
        $notificacion_id = $mysqli->insert_id;
        
        // Enviar notificación FCM si el usuario tiene token
        $token = getUserFMC($mysqli, $user_id);
        if ($token) {
            enviarNotificacionFCM($token, $titulo, $mensaje, $id_relacionado, $tipo_notificacion);
        }

        echo json_encode([
            'status' => 'ok',
            'message' => 'Notificación registrada',
            'id_notificacion' => $notificacion_id
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar notificación']);
    }

    $stmt->close();
}

function get_notificaciones_usuario($mysqli, $data) {
    $user_id = intval($data['user_id'] ?? 0);
    $solo_no_leidas = isset($data['solo_no_leidas']) ? (bool)$data['solo_no_leidas'] : false;
    $limit = intval($data['limit'] ?? 50);
$mysqli->set_charset("utf8mb4");
    if ($user_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario requerido']);
        return;
    }

    $where_leida = $solo_no_leidas ? "AND leida = 0" : "";

    $stmt = $mysqli->prepare("
        SELECT 
            id,
            user_id,
            tipo_usuario,
            titulo,
            mensaje,
            tipo_notificacion,
            id_relacionado,
            leida,
            fecha_creacion
        FROM notificaciones_usuarios
        WHERE user_id = ?
        $where_leida
        ORDER BY fecha_creacion DESC
        LIMIT ?
    ");

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
        return;
    }

    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $notificaciones = [];
    $no_leidas = 0;

    while ($row = $result->fetch_assoc()) {
        $notificaciones[] = $row;
        if ($row['leida'] == 0) {
            $no_leidas++;
        }
    }

    echo json_encode([
        'status' => 'ok',
        'notificaciones' => $notificaciones,
        'total' => count($notificaciones),
        'no_leidas' => $no_leidas
    ]);

    $stmt->close();
}

function marcar_notificacion_leida($mysqli, $data) {
    $id_notificacion = intval($data['id_notificacion'] ?? 0);
    $user_id = intval($data['user_id'] ?? 0);

    if ($id_notificacion <= 0 || $user_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        return;
    }
$mysqli->set_charset("utf8mb4");
    $stmt = $mysqli->prepare("
        UPDATE notificaciones_usuarios 
        SET leida = 1 
        WHERE id = ? AND user_id = ?
    ");

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
        return;
    }

    $stmt->bind_param("ii", $id_notificacion, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok', 'message' => 'Notificación marcada como leída']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar notificación']);
    }

    $stmt->close();
}

function get_servicios_cercanos($mysqli, $data) {
    global $km;
    $mysqli->set_charset("utf8mb4");
    $user_id = intval($data['user_id'] ?? 0);
    $latitud = floatval($data['latitud'] ?? 0);
    $longitud = floatval($data['longitud'] ?? 0);
    $radio_km = floatval($data['radio_km'] ?? $km); // Usar radio personalizado o el global

    if ($user_id <= 0 || $latitud == 0 || $longitud == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        return;
    }

    // Obtener el negocio del domiciliario
    $result = $mysqli->query("SELECT conductor_negocio FROM usuarios WHERE id = $user_id");
    if (!$result || $result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
        return;
    }

    $row = $result->fetch_assoc();
    $conductor_negocio = $row['conductor_negocio'];

    // Obtener servicios pendientes del negocio (sin conductor asignado)
    $query = "
        SELECT 
            a.id as id_alquiler,
            a.user_id,
            a.tipo_lavadora,
            a.tiempo_alquiler,
            a.latitud,
            a.longitud,
            a.total,
            a.metodo_pago,
            a.fecha_inicio,
            CONCAT(u.nombre, ' ', u.apellido) as nombre_cliente,
            u.telefono as telefono_cliente,
            u.direccion as direccion_cliente
        FROM alquileres a
        JOIN usuarios u ON a.user_id = u.id
        WHERE  a.status_servicio = 1
        AND (a.conductor_id = 0 OR a.conductor_id IS NULL)
        AND a.status = 'activo'
        ORDER BY a.fecha_inicio DESC
    ";

    $result = $mysqli->query($query);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
        return;
    }

    $servicios_cercanos = [];
    
    while ($servicio = $result->fetch_assoc()) {
        // Calcular distancia
        $distancia = calcularDistancia(
            $latitud, 
            $longitud, 
            $servicio['latitud'], 
            $servicio['longitud']
        );
        
        // Solo incluir si está dentro del radio
        if ($distancia <= $radio_km) {
            $servicio['distancia_km'] = round($distancia, 2);
            $servicios_cercanos[] = $servicio;
        }
    }

    // Ordenar por distancia (más cercano primero)
    usort($servicios_cercanos, function($a, $b) {
        return $a['distancia_km'] <=> $b['distancia_km'];
    });

    echo json_encode([
        'status' => 'ok',
        'servicios' => $servicios_cercanos,
        'total' => count($servicios_cercanos),
        'radio_km' => $radio_km
    ]);
}


function confirmar_entrega_lavadora($mysqli, $data) {
    $user_id = intval($data['user_id'] ?? 0);
    $id_alquiler = intval($data['id_alquiler'] ?? 0);

    if (!$user_id || !$id_alquiler) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        return;
    }

    // Verificar que el servicio existe y está en status_servicio = 6 (en proceso de entrega)
    $stmt_check = $mysqli->prepare("SELECT id, conductor_id, user_id, status_servicio FROM alquileres WHERE id = ?");
    $stmt_check->bind_param("i", $id_alquiler);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Servicio no encontrado']);
        return;
    }

    $servicio = $result->fetch_assoc();
    $stmt_check->close();

    // Verificar que el usuario es el domiciliario asignado
    if ($servicio['conductor_id'] != $user_id) {
        echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para confirmar esta entrega']);
        return;
    }

    // Verificar que el servicio está en status 6 (en proceso de entrega)
    if ($servicio['status_servicio'] != 6) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'El servicio no está en proceso de entrega',
            'current_status' => $servicio['status_servicio']
        ]);
        return;
    }

    // Actualizar status_servicio de 6 a 2 (entregada/en curso)
    $fecha_entrega = date('Y-m-d H:i:s');
    $stmt = $mysqli->prepare("UPDATE alquileres SET status_servicio = 2, fecha_inicio = ? WHERE id = ?");
    $stmt->bind_param("si", $fecha_entrega, $id_alquiler);

    if ($stmt->execute()) {
        // Enviar notificación al cliente
        $token = getFMCByServicio($mysqli, $id_alquiler, 'usuario');
        
        if ($token) {
            enviarNotificacionFCM(
                $token, 
                "Lavadora Entregada", 
                "La lavadora ha sido entregada en tu ubicación. ¡Disfruta del servicio!", 
                $id_alquiler, 
                'lavadora_entregada'
            );
        }

        echo json_encode([
            'status' => 'ok', 
            'message' => 'Entrega confirmada exitosamente',
            'id_alquiler' => $id_alquiler,
            'nuevo_status' => 2,
            'fecha_entrega' => $fecha_entrega
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al confirmar entrega: ' . $mysqli->error]);
    }

    $stmt->close();
}


function log_api($mysqli, $accion, $entrada, $salida) {
    $mysqli->set_charset("utf8mb4");
    $stmt = $mysqli->prepare("INSERT INTO api_logs (accion, entrada, salida) VALUES (?, ?, ?)");
    $entrada_json = json_encode($entrada);
    $salida_json = json_encode($salida);
    $stmt->bind_param("sss", $accion, $entrada_json, $salida_json);
    $stmt->execute();
    $stmt->close();
}



function get_user_strikes($mysqli, $data) {
    $user_id = intval($data['user_id'] ?? 0);
    $mysqli->set_charset("utf8mb4");
    
    if ($user_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario requerido']);
        return;
    }

    $stmt = $mysqli->prepare("SELECT cantidad FROM ban_user WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $strikes = intval($row['cantidad']);
    } else {
        $strikes = 0;
    }
    
    $stmt->close();

    // Obtener configuration general para max_intentos_cancelacion
    $result_config = $mysqli->query("SELECT max_intentos_cancelacion FROM config_general LIMIT 1");
    $max_intentos = 0;
    if ($row_config = $result_config->fetch_assoc()) {
        $max_intentos = intval($row_config['max_intentos_cancelacion']);
    }

    echo json_encode([
        'status' => 'ok',
        'strikes' => $strikes,
        'max_intentos' => $max_intentos
    ]);
}

function get_valor_minimo($mysqli, $data) {
    $mysqli->set_charset("utf8mb4");
    
    // Obtener valor_minimo de config_general
    $result = $mysqli->query("SELECT valor_minimo FROM config_general LIMIT 1");
    
    if ($row = $result->fetch_assoc()) {
        $valor_minimo = floatval($row['valor_minimo']);
        
        echo json_encode([
            'status' => 'ok',
            'valor_minimo' => $valor_minimo
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se pudo obtener la configuración'
        ]);
    }
}

?>
