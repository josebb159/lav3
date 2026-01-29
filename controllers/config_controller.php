<?php
require_once '../modelo/db.php'; // Aseg迆rate que conecta correctamente
$conn = conect();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($action == 'guardar_config') {
    if(isset($_POST['id'])){
        $negocio_id = $_POST['id'];
        $tarifa = $_POST['tarifa'];
        $telefono = $_POST['telefono'];

        // Verificar si ya existe una configuraci車n para este negocio
        $stmt_check = $conn->prepare("SELECT id FROM config WHERE id_negocio = ?");
        $stmt_check->bind_param("i", $negocio_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Ya existe: actualizar
            $stmt_update = $conn->prepare("UPDATE config SET tarifa = ?, telefono = ? WHERE id_negocio = ?");
            $stmt_update->bind_param("dis", $tarifa, $telefono, $negocio_id);
            $stmt_update->execute();
            echo 'actualizado';
        } else {
            // No existe: insertar nuevo
            $stmt_insert = $conn->prepare("INSERT INTO config (id_negocio, tarifa, telefono) VALUES (?,?, ?)");
            $stmt_insert->bind_param("ids", $negocio_id, $tarifa, $telefono);
            $stmt_insert->execute();
            echo 'insertado';
        }
    }else{
     $id = 1;

        // Recibir los datos del formulario
        $km = $_POST['km'];
        $precio = $_POST['precio'];
        $porcentaje = $_POST['porcentaje'];
        $min_servicio = $_POST['min_servicio'];

        $max_intentos_cancelacion = $_POST['max_intentos_cancelacion'];
        $multa_cliente = $_POST['multa_cliente'];
        $multa_domiciliario = $_POST['multa_domiciliario'];

        $payu_habilitado = isset($_POST['payu_habilitado']) ? 1 : 0;
        $payu_cuenta = $_POST['payu_cuenta'] ?? '';
        $payu_checkout_url = $_POST['payu_checkout_url'] ?? '';
        $payu_merchant_id = $_POST['payu_merchant_id'] ?? '';
        $payu_account_id = $_POST['payu_account_id'] ?? '';
        $payu_response_url = $_POST['payu_response_url'] ?? '';
        $payu_confirmation_url = $_POST['payu_confirmation_url'] ?? '';
        $email_pay = $_POST['email_pay'] ?? '';
        
        // DEBUG: Log para verificar qué se está recibiendo
        error_log("DEBUG CONFIG - email_pay recibido: '" . $email_pay . "'");
        error_log("DEBUG CONFIG - payu_habilitado: " . $payu_habilitado);

        $bancolombia_habilitado = isset($_POST['bancolombia_habilitado']) ? 1 : 0;
        $bancolombia_cuenta = $_POST['bancolombia_cuenta'];

        $nequi_habilitado = isset($_POST['nequi_habilitado']) ? 1 : 0;
        $nequi_cuenta = $_POST['nequi_cuenta'];

        $daviplata_habilitado = isset($_POST['daviplata_habilitado']) ? 1 : 0;
        $daviplata_cuenta = $_POST['daviplata_cuenta'];

        $whatsapp_contacto = $_POST['whatsapp_contacto'];
        $correo_contacto = $_POST['correo_contacto'];

        // Verificar si ya existe una configuraci車n
        $stmt_check = $conn->prepare("SELECT id, banner FROM config_general WHERE id = ?");
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $existing_config = $result_check->fetch_assoc();
        
        // Inicializar $newName con el banner existente o null
        $newName = $existing_config['banner'] ?? null;
        
        if (isset($_FILES['logo_negocio']) && $_FILES['logo_negocio']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['logo_negocio']['tmp_name'];
        $fileName = $_FILES['logo_negocio']['name'];
        $fileSize = $_FILES['logo_negocio']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Extensiones permitidas
        $allowedExt = ['png', 'jpg', 'jpeg', 'gif'];

            if (in_array($fileExt, $allowedExt)) {
                $newName = uniqid("logo_", true) . "." . $fileExt;
                $uploadPath = __DIR__ . "/../upload/" . $newName;
    
                if (!file_exists(__DIR__ . "/../upload/")) {
                    mkdir(__DIR__ . "/../upload/", 0777, true);
                }
    
                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    echo "Imagen subida correctamente: " . $newName;
                    // Aquí guardas en tu BD el nombre del archivo si quieres
                } else {
                    echo "Error al mover el archivo.";
                }
            } else {
                echo "Formato no permitido. Solo PNG, JPG, JPEG, GIF.";
            }
        } else {
            echo "No se subió ninguna imagen.";
        }

        if ($existing_config) {
            // Actualizar - Usando query directa para debug
            
            // Escapar todos los valores string
            $payu_cuenta_esc = $conn->real_escape_string($payu_cuenta);
            $payu_checkout_url_esc = $conn->real_escape_string($payu_checkout_url);
            $payu_merchant_id_esc = $conn->real_escape_string($payu_merchant_id);
            $payu_account_id_esc = $conn->real_escape_string($payu_account_id);
            $payu_response_url_esc = $conn->real_escape_string($payu_response_url);
            $payu_confirmation_url_esc = $conn->real_escape_string($payu_confirmation_url);
            $email_pay_esc = $conn->real_escape_string($email_pay);
            $bancolombia_cuenta_esc = $conn->real_escape_string($bancolombia_cuenta);
            $nequi_cuenta_esc = $conn->real_escape_string($nequi_cuenta);
            $daviplata_cuenta_esc = $conn->real_escape_string($daviplata_cuenta);
            $whatsapp_contacto_esc = $conn->real_escape_string($whatsapp_contacto);
            $correo_contacto_esc = $conn->real_escape_string($correo_contacto);
            $newName_esc = $conn->real_escape_string($newName ?? '');
            
            // DEBUG: Log del valor escapado
            error_log("DEBUG CONFIG - email_pay escapado: '" . $email_pay_esc . "'");
            
            $sql = "UPDATE config_general SET 
                km = $km, 
                global_tarifa = $precio, 
                porcentaje = $porcentaje, 
                valor_minimo = $min_servicio, 
                max_intentos_cancelacion = $max_intentos_cancelacion, 
                multa_cliente = $multa_cliente, 
                multa_domiciliario = $multa_domiciliario, 
                payu_habilitado = $payu_habilitado, 
                payu_cuenta = '$payu_cuenta_esc', 
                payu_checkout_url = '$payu_checkout_url_esc', 
                payu_merchant_id = '$payu_merchant_id_esc', 
                payu_account_id = '$payu_account_id_esc', 
                payu_response_url = '$payu_response_url_esc', 
                payu_confirmation_url = '$payu_confirmation_url_esc', 
                email_pay = '$email_pay_esc', 
                bancolombia_habilitado = $bancolombia_habilitado, 
                bancolombia_cuenta = '$bancolombia_cuenta_esc', 
                nequi_habilitado = $nequi_habilitado, 
                nequi_cuenta = '$nequi_cuenta_esc', 
                daviplata_habilitado = $daviplata_habilitado, 
                daviplata_cuenta = '$daviplata_cuenta_esc', 
                whatsapp_contacto = '$whatsapp_contacto_esc', 
                correo_contacto = '$correo_contacto_esc', 
                banner = '$newName_esc' 
                WHERE id = $id";
            
            // DEBUG: Log de la query completa
            error_log("DEBUG CONFIG - SQL Query: " . $sql);
            
            if ($conn->query($sql)) {
                error_log("DEBUG CONFIG - UPDATE exitoso. Filas afectadas: " . $conn->affected_rows);
                echo 'actualizado';
            } else {
                error_log("DEBUG CONFIG - ERROR en UPDATE: " . $conn->error);
                echo 'error: ' . $conn->error;
            }
        } else {
            // Insertar nuevo
            $stmt_insert = $conn->prepare("INSERT INTO config_general (
                id, km, global_tarifa, porcentaje, valor_minimo, 
                max_intentos_cancelacion, multa_cliente, multa_domiciliario, 
                payu_habilitado, payu_cuenta, payu_checkout_url, payu_merchant_id, payu_account_id, 
                payu_response_url, payu_confirmation_url, email_pay, 
                bancolombia_habilitado, bancolombia_cuenta, 
                nequi_habilitado, nequi_cuenta, 
                daviplata_habilitado, daviplata_cuenta, 
                whatsapp_contacto, correo_contacto, banner) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt_insert->bind_param("idddddddissssssississsss", 
                $id, $km, $precio, $porcentaje, $min_servicio, 
                $max_intentos_cancelacion, $multa_cliente, $multa_domiciliario, 
                $payu_habilitado, $payu_cuenta, $payu_checkout_url, $payu_merchant_id, $payu_account_id, 
                $payu_response_url, $payu_confirmation_url, $email_pay, 
                $bancolombia_habilitado, $bancolombia_cuenta, 
                $nequi_habilitado, $nequi_cuenta, 
                $daviplata_habilitado, $daviplata_cuenta, 
                $whatsapp_contacto, $correo_contacto, $newName
            );

            $stmt_insert->execute();
            echo 'insertado';
        }

    }
}

?>
