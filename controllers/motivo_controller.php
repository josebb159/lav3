<?php
require_once '../modelo/db.php'; // Asegúrate que conecta correctamente
$conn = conect();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Cambiar estado del motivo
if ($action == 'cambiar_status_motivo') {
    $id = $_POST['id'];
    $estado = $_POST['status'];
    $stmt = $conn->prepare("UPDATE motivo SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado, $id);
    $stmt->execute();
    echo 'ok';
}

// Obtener motivo por ID
if ($action == 'obtener_motivo') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM motivo WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
}

// Editar motivo
if ($action == 'editar_motivo') {
    $id = $_POST['id'];
    $descripcion = $_POST['descripcion'];


    $stmt = $conn->prepare("UPDATE motivo SET descripcion = ? WHERE id = ?");
    $stmt->bind_param("si", $descripcion, $id);
    $stmt->execute();
    echo 'ok';
}

// Crear motivo
if ($action == 'crear_motivo') {
    $descripcion = $_POST['descripcion'];

    $stmt = $conn->prepare("INSERT INTO motivo (descripcion) VALUES (?)");
    $stmt->bind_param("s", $descripcion);

    if ($stmt->execute()) {
        echo 'ok';
    } else {
        echo 'error_motivo';
    }
}
?>
