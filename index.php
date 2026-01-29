<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: vista/home.php?m=i');  // Si ya está logueado, redirigir al dashboard
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .card {
            border-radius: 12px;
        }
        .card-header {
            background-color: #0056b3;
            border-radius: 12px 12px 0 0;
        }
        .card-footer {
            background-color: #f8f9fa;
            border-radius: 0 0 12px 12px;
        }
        .logo {
            max-width: 150px;
            margin: 0 auto;
            display: block;
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="max-width: 400px; width: 100%;">
            <!-- Logo de la empresa -->
            <div class="card-header text-center">
                <img src="logo.png" alt="Logo de la Empresa" class="logo">
                <h4 class="text-white mt-3">Iniciar sesión</h4>
            </div>
            <div class="card-body">
                <form action="login_process.php" method="POST">
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo:</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Iniciar sesión</button>
                </form>
            </div>
            <div class="card-footer text-center">
      
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 SP production. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS (opcional) -->

</body>
</html>
