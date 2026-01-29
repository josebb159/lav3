<?php
require_once '../modelo/db.php';
require_once '../modelo/helpers.php';
require_once '../modelo/notifications.php';
require_once '../modelo/audit_logger.php'; // Sistema de auditor¨ªa
$conn = conect();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'obtener_transacciones' && isset($_GET['lavadora_id'])) {
    $lavadora_id = intval($_GET['lavadora_id']);
    
    // Usar prepared statement para evitar SQL injection
    $stmt = $conn->prepare("SELECT * FROM transacciones WHERE lavadora_id = ? ORDER BY fecha DESC");
    $stmt->bind_param("i", $lavadora_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered">';
        echo '<thead><tr><th>ID</th><th>Tipo</th><th>Fecha</th><th>Observaciones</th></tr></thead><tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['tipo'] . '</td>';
            echo '<td>' . $row['fecha'] . '</td>';
            echo '<td>' . htmlspecialchars($row['observaciones']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No hay transacciones registradas.</p>';
    }

    exit;
}

if ($action == 'cambiar_status') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $result = $conn->query("SELECT * FROM lavadoras WHERE id = $id");
    $datos_anteriores = $result->fetch_assoc();
    
    $stmt = $conn->prepare("UPDATE lavadoras SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    
    log_actualizar($conn, 'lavadoras', $id, $datos_anteriores, ['status' => $status], "Estado de lavadora cambiado a: $status");
    echo 'ok';
}

if ($action == 'asginar') {
    // Validar que id_user existe
    if (!isset($_POST['id_user']) || empty($_POST['id_user'])) {
        echo 'error_id_user';
        exit;
    }
    
    $id = (int) $_POST['id_lavadora_asignar'];
    $id_user = (int) $_POST['id_user'];
    $estado = 'delivery';
    $estado2 = 'entrega';

    $stmt = $conn->prepare("UPDATE lavadoras SET en = ?, id_domiciliario = ? WHERE id = ?");
    $stmt->bind_param("sii", $estado, $id_user, $id);
    $stmt->execute();

   $stmt_negocio = $conn->prepare("INSERT INTO transacciones (delivery_id, lavadora_id, tipo) VALUES (?, ?, ?)");
   $stmt_negocio->bind_param("iis", $id_user, $id, $estado2);
   if ($stmt_negocio->execute()) {
       $token = getUserFCM($conn, $id_user);
  
       enviarNotificacionFCM($token, "asignaci¨®n", "Se asigno una lavadora", "", "asignacion");
       
       echo 'ok';
   } else {
       echo 'error_asignado';
   }
}

if ($action == 'devolver') {
    $id = (int) $_POST['id_lavadora_devolver'];
    $id_user = 0;
    $estado = 'bodega';
    $estado2 = 'recepcion';
    $observaciones = $_POST['observacion'];
    $id_user_snd = "";
        $stmt = $conn->prepare("SELECT id_domiciliario FROM lavadoras WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $id_user_snd = $row['id_domiciliario'];
    
    } else {
        echo "No se encontr¨® lavadora con ese id";
    }

    $stmt = $conn->prepare("UPDATE lavadoras SET en = ?, id_domiciliario = ? WHERE id = ?");
    $stmt->bind_param("sii", $estado, $id_user, $id);
    $stmt->execute();

   $stmt_negocio = $conn->prepare("INSERT INTO transacciones (delivery_id, lavadora_id, tipo, observaciones) VALUES (?, ?,  ? , ?)");
   $stmt_negocio->bind_param("iiss", $id_user, $id, $estado2, $observaciones);
   



    
   
   if ($stmt_negocio->execute()) {
     $token = getUserFCM($conn, $id_user_snd);
     var_dump($token);
       enviarNotificacionFCM($token, "Devuelta a bodega", "La lavadora fue devuelta a bodega", "", "devuelta_bodega");
       echo 'ok';
   } else {
       echo 'error_asignado';
   }
    echo 'ok';
}


if ($action == 'obtener_lavadora') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM lavadoras WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
}

if ($action == 'editar_lavadora') {
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $type = $_POST['type'];
    
    $result = $conn->query("SELECT * FROM lavadoras WHERE id = $id");
    $datos_anteriores = $result->fetch_assoc();

    $stmt = $conn->prepare("UPDATE lavadoras SET codigo = ?, type = ? WHERE id = ?");
    $stmt->bind_param("ssi", $codigo,$type, $id );
    $stmt->execute();
    
    log_actualizar($conn, 'lavadoras', $id, $datos_anteriores, ['codigo' => $codigo, 'type' => $type], "Lavadora actualizada");
    echo 'ok';
}

if ($action == 'crear_lavadora') {
    $codigo = $_POST['codigo'];
    $type = $_POST['type'];
    if (isset($_POST['id'])) {
        $negocio = $_POST['id'];
    }else{
        $negocio = $_POST['negocio'];
    }

    $stmt_negocio = $conn->prepare("INSERT INTO lavadoras (codigo, negocio_id, type) VALUES (?, ?, ?)");
    $stmt_negocio->bind_param("sis", $codigo, $negocio, $type);
    if ($stmt_negocio->execute()) {
        $nuevo_id = $conn->insert_id;
        log_crear($conn, 'lavadoras', $nuevo_id, [
            'codigo' => $codigo,
            'negocio_id' => $negocio,
            'type' => $type
        ], "Nueva lavadora creada: $codigo");
        echo 'ok';
    } else {
        echo 'error_negocio';
    }
}

if ($action == 'eliminar_lavadora') {
    $id = $_POST['id'];
    
    $result = $conn->query("SELECT * FROM lavadoras WHERE id = $id");
    $datos_eliminados = $result->fetch_assoc();
    
    $stmt = $conn->prepare("UPDATE lavadoras SET status = 'eliminado' WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        log_eliminar($conn, 'lavadoras', $id, $datos_eliminados, "Lavadora eliminada: {$datos_eliminados['codigo']}");
        echo 'ok';
    } else {
        echo 'error';
    }
}

?>
