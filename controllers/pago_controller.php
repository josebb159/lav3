<?php
require_once '../modelo/db.php';
require_once '../modelo/helpers.php';
require_once '../modelo/notifications.php';
$conn = conect();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Cambiar estado del pago
if ($action == 'cambiar_status_pago') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE pagos SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    echo 'ok';
}

// Obtener pago por ID
if ($action == 'obtener_pago') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM pagos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
}

// Editar pago
if ($action == 'editar_pago') {
    $id = $_POST['id'];
    $valor = $_POST['valor'];
    $referencia = $_POST['referencia'];
    $metodo_pago = $_POST['metodo_pago'];

    $stmt = $conn->prepare("UPDATE pagos SET valor = ?, referencia = ?, metodo_pago = ? WHERE id = ?");
    $stmt->bind_param("dssi", $valor, $referencia, $metodo_pago, $id);
    
    if ($stmt->execute()) {
        echo 'ok';
    } else {
        echo 'error';
    }
}

// Crear nuevo pago
if ($action == 'crear_pago') {
    $referencia = $_POST['referencia'];
    $monto = $_POST['valor'];
    $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
    $estado = $_POST['estado'] ?? 1;
    $id = $_POST['usuario_id'];

    // Primero actualizar monedero
    $stmt2 = $conn->prepare("UPDATE usuarios SET monedero = monedero + ? WHERE id = ?");
    $stmt2->bind_param("ii", $monto,  $id);
    $stmt2->execute();
    
    // Luego insertar pago - CORREGIDO: tipos correctos sdsii
    $stmt = $conn->prepare("INSERT INTO pagos (referencia, valor, metodo_pago, estado, id_usuario) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsii", $referencia, $monto, $metodo_pago, $estado, $id);

    if ($stmt->execute()) {
        $token = getUserFCM($conn, $id);
        enviarNotificacionFCM($token, "Recarga", "Se ha realizado una recarga", "", "recarga");
        echo 'ok';
    } else {
        echo 'error_pago';
    }
}


?>
