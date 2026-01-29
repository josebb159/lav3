<?php
require_once '../modelo/db.php'; // Asegúrate de que este archivo tenga la función conect()
$conn = conect();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'guardar_config') {
    // Recibir datos del formulario
    $terminos = $_POST['terminos'] ?? '';
    $terminos_uso = $_POST['terminos_uso'] ?? '';
    $terminos_delivery = $_POST['terminos_delivery'] ?? '';
    $terminos_uso_delivery = $_POST['terminos_uso_delivery'] ?? '';

    // Verificar si ya existe un registro
    $stmt_check = $conn->query("SELECT id FROM terminos_condiciones LIMIT 1");

    if ($stmt_check && $stmt_check->num_rows > 0) {
        // Ya existe: Actualizar
        $stmt_update = $conn->prepare("UPDATE terminos_condiciones SET terminos = ?, terminos_uso = ?, terminos_delivery = ?, terminos_uso_delivery = ? WHERE id = 1");
        $stmt_update->bind_param("ssss", $terminos, $terminos_uso, $terminos_delivery, $terminos_uso_delivery);
        $stmt_update->execute();
        echo 'actualizado';
    } else {
        // No existe: Insertar
        $stmt_insert = $conn->prepare("INSERT INTO terminos_condiciones (terminos, terminos_uso, terminos_delivery, terminos_uso_delivery) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $terminos, $terminos_uso, $terminos_delivery, $terminos_uso_delivery);
        $stmt_insert->execute();
        echo 'insertado';
    }
}
?>
