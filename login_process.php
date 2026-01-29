<?php
session_start();
include('modelo/db.php');  // Incluir la conexión a la base de datos
$conn = conect();

if (isset($_POST['login'])) {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Validar si el correo existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);  // "s" significa string
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario) {
        // Verificar la contraseña
        if (password_verify($contrasena, $usuario['contrasena'])) {
            // Si la contraseña es correcta, iniciar sesión
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_rol'] = $usuario['rol_id'];

            // Si es rol_id 2 (negocio), obtener el negocio asociado
            if ($usuario['rol_id'] == 2) {
                $stmt = $conn->prepare("SELECT * FROM negocios WHERE usuario_id = ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $negocio = $result->fetch_assoc();
                
                // Validar que el negocio existe antes de acceder a sus datos
                if ($negocio && isset($negocio['id'])) {
                    $_SESSION['negocio'] = $negocio['id'];
                } else {
                    // Usuario sin negocio asignado
                    require_once 'modelo/helpers.php';
                    log_error("Usuario rol 2 sin negocio", ['user_id' => $_SESSION['user_id'], 'correo' => $correo]);
                    echo "Error: Usuario no tiene negocio asignado. Por favor contacte al administrador.";
                    exit();
                }
            }

            // Redirigir al dashboard
            header('Location: vista/home.php?m=i');
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Correo no encontrado.";
    }
}
?>
