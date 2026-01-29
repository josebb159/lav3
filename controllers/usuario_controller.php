<?php
require_once '../modelo/db.php';
require_once '../modelo/helpers.php';
require_once '../modelo/notifications.php';
require_once '../modelo/audit_logger.php'; // Sistema de auditoría
$conn = conect();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'cambiar_status') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $result = $conn->query("SELECT * FROM usuarios WHERE id = $id");
    $datos_anteriores = $result->fetch_assoc();
    
    $stmt = $conn->prepare("UPDATE usuarios SET status = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $id);
    $stmt->execute();
    
    log_actualizar($conn, 'usuarios', $id, $datos_anteriores, ['status' => $status], "Estado de usuario cambiado a: $status");
    echo 'ok';
}

if ($action == 'eliminar_usuario') {
    $id = $_POST['id'];
    
    // Obtener datos completos antes de eliminar
    $result = $conn->query("SELECT * FROM usuarios WHERE id = $id");
    $datos_eliminados = $result->fetch_assoc();
    
    if ($datos_eliminados) {
        $correo_actual = $datos_eliminados['correo'];
        $nuevo_correo = $correo_actual . '_deleted_' . time();
        
        // Liberar lavadoras asignadas al usuario
        $stmt_lavadoras = $conn->prepare("UPDATE lavadoras SET id_domiciliario = 0 WHERE id_domiciliario = ?");
        $stmt_lavadoras->bind_param("i", $id);
        $stmt_lavadoras->execute();
        
        // Marcar usuario como eliminado
        $stmt = $conn->prepare("UPDATE usuarios SET status = 99, correo = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_correo, $id);
        
        if ($stmt->execute()) {
            log_eliminar($conn, 'usuarios', $id, $datos_eliminados, "Usuario eliminado: {$datos_eliminados['nombre']}");
            echo 'ok';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
}

if ($action == 'tomar_recaudo') {
    $id = $_POST['id'];             // ID del conductor
    $monedero = $_POST['monedero']; // Valor a transferir
    $negocio = $_POST['negocio'];   // ID del negocio

    // Seguridad básica
    $id = intval($id);
    $monedero = intval($monedero);
    $negocio = intval($negocio);

    // 1. Registrar la transacción
    $stmt = $conn->prepare("INSERT INTO transacciones_cobro (origen_id, destino_id, monto, descripcion) VALUES (?, ?, ?, ?)");
    $descripcion = 'Recaudo entregado por conductor al negocio';
    $stmt->bind_param("iiis", $id, $negocio, $monedero, $descripcion);
    $stmt->execute();

    // 2. Limpiar monedero del conductor
    $stmt = $conn->prepare("UPDATE usuarios SET monedero = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // 3. Sumar al monedero del negocio
    $stmt = $conn->prepare("UPDATE usuarios SET monedero = monedero + ? WHERE id = ?");
    $stmt->bind_param("ii", $monedero, $negocio);
    $stmt->execute();

    echo 'ok';
}

if ($action == 'obtener_usuario') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT id, nombre, correo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
}

if ($action == 'editar_usuario') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    
    if (email_exists($conn, $correo, $id)) {
        echo 'error_correo_duplicado';
        exit;
    }
    
    $result = $conn->query("SELECT * FROM usuarios WHERE id = $id");
    $datos_anteriores = $result->fetch_assoc();
    
    $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nombre, $correo, $id);
    $stmt->execute();
    
    log_actualizar($conn, 'usuarios', $id, $datos_anteriores, ['nombre' => $nombre, 'correo' => $correo], "Usuario actualizado");
    echo 'ok';
}
if ($action == 'crear_usuario_app') {
    $nombre_usuario = $_POST['usuario_nombre'];
    $apellido_usuario = $_POST['usuario_apellido'];
    $telefono_usuario = $_POST['usuario_telefono'];
    $correo_usuario = $_POST['usuario_correo'];
    $usuario_usuario = $_POST['usuario_usuario'];
    $negocio = $_POST['id'];
 
    $plainPassword = generarContrasenaAleatoria();
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    $rol_id = 3;

    if (email_exists($conn, $correo_usuario)) {
        echo 'error_correo_duplicado';
        exit;
    }

    $stmt_user = $conn->prepare("INSERT INTO usuarios (nombre, apellido, telefono,  correo, usuario, contrasena, rol_id,conductor_negocio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_user->bind_param("ssssssii", $nombre_usuario, $apellido_usuario, $telefono_usuario,  $correo_usuario, $usuario_usuario, $hashedPassword, $rol_id, $negocio);
    if ($stmt_user->execute()) {
        $nuevo_id = $conn->insert_id;
        log_crear($conn, 'usuarios', $nuevo_id, [
            'nombre' => $nombre_usuario,
            'apellido' => $apellido_usuario,
            'correo' => $correo_usuario,
            'rol_id' => $rol_id,
            'conductor_negocio' => $negocio
        ], "Nuevo usuario conductor creado: $nombre_usuario");
        echo 'ok';
    } else {
        echo 'error_crear_usuario';
        exit;
    }

    require '../controllers/mailNewUser.php';
    enviarCorreoUsuarioNuevo($correo_usuario, $plainPassword);
}

if ($action == 'reset_ban_counter') {
    $id = $_POST['id'];
    $id = intval($id);
    
    $stmt = $conn->prepare("UPDATE ban_user SET cantidad = 0 WHERE id_user = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        log_accion($conn, 'RESET_STRIKES', "Strikes de usuario reiniciados", 'ban_user', $id);
        if ($stmt->affected_rows > 0) {
            echo 'ok';
        } else {
            echo 'ok';
        }
    } else {
        echo 'error';
    }
    $stmt->close();
}

if ($action == 'reset_all_strikes') {
    // Resetear strikes de TODOS los usuarios
    $query = "UPDATE ban_user SET cantidad = 0";
    
    if ($conn->query($query)) {
        $affected = $conn->affected_rows;
        log_accion($conn, 'RESET_ALL_STRIKES', "Strikes de todos los usuarios reiniciados (Total: $affected)", 'ban_user', 0);
        echo json_encode(['status' => 'ok', 'affected' => $affected]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al resetear strikes']);
    }
}


if ($action == 'crear_usuario_sistema') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $rol_id = $_POST['rol_id'];
    
    // Validar que el correo no exista
    if (email_exists($conn, $correo)) {
        echo 'error_correo_duplicado';
        exit;
    }
    
    // Hashear la contraseña
    $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);
    
    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol_id, status) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("sssi", $nombre, $correo, $hashedPassword, $rol_id);
    
    if ($stmt->execute()) {
        $nuevo_id = $conn->insert_id;
        log_crear($conn, 'usuarios', $nuevo_id, [
            'nombre' => $nombre,
            'correo' => $correo,
            'rol_id' => $rol_id
        ], "Nuevo usuario del sistema creado: $nombre");
        echo 'ok';
    } else {
        echo 'error';
    }
}

if ($action == 'cambiar_contrasena') {
    $id = $_POST['id'];
    $nueva_contrasena = $_POST['nueva_contrasena'];
    
    // Obtener datos anteriores
    $result = $conn->query("SELECT * FROM usuarios WHERE id = $id");
    $datos_anteriores = $result->fetch_assoc();
    
    // Hashear la nueva contraseña
    $hashedPassword = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
    
    // Actualizar contraseña
    $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $id);
    
    if ($stmt->execute()) {
        log_actualizar($conn, 'usuarios', $id, $datos_anteriores, ['contrasena' => '[CONTRASEÑA ACTUALIZADA]'], "Contraseña actualizada para usuario: {$datos_anteriores['nombre']}");
        echo 'ok';
    } else {
        echo 'error';
    }
}

function generarContrasenaAleatoria($longitud = 6) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $contrasena = '';
    for ($i = 0; $i < $longitud; $i++) {
        $contrasena .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $contrasena;
}

?>
