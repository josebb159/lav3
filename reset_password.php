<?php
require 'modelo/db.php'; // Asegúrate de conectar correctamente
$conn = conect();

$token = $_GET['token'] ?? '';
$mensaje = '';
$exito = false;

// Procesar envío
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $pass1 = $_POST['password'] ?? '';
    $pass2 = $_POST['confirm_password'] ?? '';

    if (empty($pass1) || empty($pass2)) {
        $mensaje = 'Debes llenar ambos campos.';
    } elseif ($pass1 !== $pass2) {
        $mensaje = 'Las contraseñas no coinciden.';
    } else {
        // Buscar usuario por token
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE tocken_recovery = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $user = $res->fetch_assoc();
            $hash = password_hash($pass1, PASSWORD_DEFAULT);

            $stmt2 = $conn->prepare("UPDATE usuarios SET contrasena = ?, tocken_recovery = NULL WHERE id = ?");
            $stmt2->bind_param("si", $hash, $user['id']);
            $stmt2->execute();

            $mensaje = 'Contraseña actualizada correctamente.';
            $exito = true;
        } else {
            $mensaje = 'Token inválido o expirado.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña - Alquilav</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="text-center mb-4">
                <img src="https://alquilav.com/logo.png" alt="Alquilav" style="height: 80px;">
                <h3 class="mt-3">Restablecer contraseña</h3>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert <?= $exito ? 'alert-success' : 'alert-danger' ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>

            <?php if (!$exito): ?>
                <form method="POST">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="mb-3">
                        <label>Nueva contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Confirmar contraseña</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Actualizar contraseña</button>
                </form>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>
