<?php
require_once '../modelo/db.php';
require_once '../modelo/audit_logger.php'; // NUEVO: Sistema de auditoría
$conn = conect();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Cambiar estado del proveedor
if ($action == 'cambiar_status_proveedor') {
    $id = $_POST['id'];
    $estado = $_POST['status'];
    
    // Obtener datos anteriores para el log
    $result = $conn->query("SELECT * FROM proveedores WHERE id = $id");
    $datos_anteriores = $result->fetch_assoc();
    
    $stmt = $conn->prepare("UPDATE proveedores SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado, $id);
    $stmt->execute();
    
    // LOG: Registrar cambio de estado
    log_actualizar(
        $conn, 
        'proveedores', 
        $id, 
        $datos_anteriores,
        ['estado' => $estado],
        "Estado de proveedor cambiado a: $estado"
    );
    
    echo 'ok';
}

// Obtener proveedor por ID
if ($action == 'obtener_proveedor') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM proveedores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
}

// Editar proveedor
if ($action == 'editar_proveedor') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    
    // Obtener datos anteriores para el log
    $result = $conn->query("SELECT * FROM proveedores WHERE id = $id");
    $datos_anteriores = $result->fetch_assoc();

    $stmt = $conn->prepare("UPDATE proveedores SET nombre = ?, telefono = ?, correo = ?, direccion = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nombre, $telefono, $correo, $direccion, $id);
    $stmt->execute();
    
    // LOG: Registrar actualización
    $datos_nuevos = [
        'nombre' => $nombre,
        'telefono' => $telefono,
        'correo' => $correo,
        'direccion' => $direccion
    ];
    log_actualizar($conn, 'proveedores', $id, $datos_anteriores, $datos_nuevos, "Proveedor actualizado");
    
    echo 'ok';
}

// Crear proveedor
if ($action == 'crear_proveedor') {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $estado = $_POST['estado'] ?? 'activo';
    $negocio = $_POST['negocio'];

    $stmt = $conn->prepare("INSERT INTO proveedores (nombre, telefono, correo, direccion, estado, negocio_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $nombre, $telefono, $correo, $direccion, $estado, $negocio);

    if ($stmt->execute()) {
        $nuevo_id = $conn->insert_id;
        
        // LOG: Registrar creación
        $datos_nuevos = [
            'id' => $nuevo_id,
            'nombre' => $nombre,
            'telefono' => $telefono,
            'correo' => $correo,
            'direccion' => $direccion,
            'estado' => $estado,
            'negocio_id' => $negocio
        ];
        log_crear($conn, 'proveedores', $nuevo_id, $datos_nuevos, "Nuevo proveedor creado: $nombre");
        
        echo 'ok';
    } else {
        echo 'error_proveedor';
    }
}

if ($action == 'eliminar_proveedor') {
    $id = $_POST['id'];
    
    // Obtener datos antes de eliminar para el log
    $result = $conn->query("SELECT * FROM proveedores WHERE id = $id");
    $datos_eliminados = $result->fetch_assoc();
    
    $stmt = $conn->prepare("UPDATE proveedores SET estado = 'eliminado' WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // LOG: Registrar eliminación
        log_eliminar($conn, 'proveedores', $id, $datos_eliminados, "Proveedor eliminado: {$datos_eliminados['nombre']}");
        
        echo 'ok';
    } else {
        echo 'error';
    }
}
?>
