<?php
/**
 * Sistema de Auditoría y Logs
 * Funciones helper para registrar todas las acciones importantes
 */

/**
 * Registrar una acción en el log de auditoría
 * 
 * @param mysqli $conn Conexión a la base de datos
 * @param string $accion Tipo de acción: CREATE, UPDATE, DELETE, LOGIN, LOGOUT, etc
 * @param string $tabla_afectada Nombre de la tabla afectada
 * @param int|null $registro_id ID del registro afectado
 * @param string|null $descripcion Descripción detallada de la acción
 * @param array|null $datos_anteriores Datos antes del cambio (para UPDATE y DELETE)
 * @param array|null $datos_nuevos Datos después del cambio (para CREATE y UPDATE)
 * @param int|null $usuario_id ID del usuario que realiza la acción
 * @param string|null $usuario_nombre Nombre del usuario
 * @param string|null $usuario_rol Rol del usuario (admin, negocio, domiciliario, cliente)
 */
function registrar_log_auditoria(
    $conn, 
    $accion, 
    $tabla_afectada = null, 
    $registro_id = null, 
    $descripcion = null,
    $datos_anteriores = null,
    $datos_nuevos = null,
    $usuario_id = null,
    $usuario_nombre = null,
    $usuario_rol = null
) {
    try {
        // Obtener IP del usuario
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        // Obtener User Agent
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        // Convertir arrays a JSON
        $datos_anteriores_json = $datos_anteriores ? json_encode($datos_anteriores, JSON_UNESCAPED_UNICODE) : null;
        $datos_nuevos_json = $datos_nuevos ? json_encode($datos_nuevos, JSON_UNESCAPED_UNICODE) : null;
        
        // Preparar statement
        $stmt = $conn->prepare("
            INSERT INTO audit_logs 
            (usuario_id, usuario_nombre, usuario_rol, accion, tabla_afectada, registro_id, 
             descripcion, datos_anteriores, datos_nuevos, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt) {
            $stmt->bind_param(
                "issssisssss",
                $usuario_id,
                $usuario_nombre,
                $usuario_rol,
                $accion,
                $tabla_afectada,
                $registro_id,
                $descripcion,
                $datos_anteriores_json,
                $datos_nuevos_json,
                $ip_address,
                $user_agent
            );
            
            $stmt->execute();
            $stmt->close();
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        // No detener la ejecución si falla el log
        error_log("Error al registrar log de auditoría: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtener información del usuario desde la sesión
 * 
 * @return array Array con usuario_id, usuario_nombre, usuario_rol
 */
function obtener_info_usuario_sesion() {
    session_start();
    
    $usuario_id = $_SESSION['user_id'] ?? null;
    $usuario_nombre = $_SESSION['user_name'] ?? null;
    $usuario_rol = null;
    
    // Determinar rol
    if (isset($_SESSION['user_rol'])) {
        switch ($_SESSION['user_rol']) {
            case 1:
                $usuario_rol = 'admin';
                break;
            case 2:
                $usuario_rol = 'negocio';
                break;
            case 3:
                $usuario_rol = 'domiciliario';
                break;
            case 4:
                $usuario_rol = 'cliente';
                break;
            default:
                $usuario_rol = 'desconocido';
        }
    }
    
    return [
        'usuario_id' => $usuario_id,
        'usuario_nombre' => $usuario_nombre,
        'usuario_rol' => $usuario_rol
    ];
}

/**
 * Registrar creación de registro
 */
function log_crear($conn, $tabla, $registro_id, $datos_nuevos, $descripcion = null) {
    $info = obtener_info_usuario_sesion();
    registrar_log_auditoria(
        $conn,
        'CREATE',
        $tabla,
        $registro_id,
        $descripcion ?? "Nuevo registro creado en $tabla",
        null,
        $datos_nuevos,
        $info['usuario_id'],
        $info['usuario_nombre'],
        $info['usuario_rol']
    );
}

/**
 * Registrar actualización de registro
 */
function log_actualizar($conn, $tabla, $registro_id, $datos_anteriores, $datos_nuevos, $descripcion = null) {
    $info = obtener_info_usuario_sesion();
    registrar_log_auditoria(
        $conn,
        'UPDATE',
        $tabla,
        $registro_id,
        $descripcion ?? "Registro actualizado en $tabla",
        $datos_anteriores,
        $datos_nuevos,
        $info['usuario_id'],
        $info['usuario_nombre'],
        $info['usuario_rol']
    );
}

/**
 * Registrar eliminación de registro
 */
function log_eliminar($conn, $tabla, $registro_id, $datos_anteriores, $descripcion = null) {
    $info = obtener_info_usuario_sesion();
    registrar_log_auditoria(
        $conn,
        'DELETE',
        $tabla,
        $registro_id,
        $descripcion ?? "Registro eliminado de $tabla",
        $datos_anteriores,
        null,
        $info['usuario_id'],
        $info['usuario_nombre'],
        $info['usuario_rol']
    );
}

/**
 * Registrar login
 */
function log_login($conn, $usuario_id, $usuario_nombre, $usuario_rol, $exitoso = true) {
    registrar_log_auditoria(
        $conn,
        $exitoso ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED',
        'usuarios',
        $usuario_id,
        $exitoso ? "Inicio de sesión exitoso" : "Intento de inicio de sesión fallido",
        null,
        null,
        $usuario_id,
        $usuario_nombre,
        $usuario_rol
    );
}

/**
 * Registrar logout
 */
function log_logout($conn) {
    $info = obtener_info_usuario_sesion();
    registrar_log_auditoria(
        $conn,
        'LOGOUT',
        'usuarios',
        $info['usuario_id'],
        "Cierre de sesión",
        null,
        null,
        $info['usuario_id'],
        $info['usuario_nombre'],
        $info['usuario_rol']
    );
}

/**
 * Registrar acción personalizada
 */
function log_accion($conn, $accion, $descripcion, $tabla = null, $registro_id = null) {
    $info = obtener_info_usuario_sesion();
    registrar_log_auditoria(
        $conn,
        $accion,
        $tabla,
        $registro_id,
        $descripcion,
        null,
        null,
        $info['usuario_id'],
        $info['usuario_nombre'],
        $info['usuario_rol']
    );
}
?>
