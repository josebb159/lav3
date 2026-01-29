<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
require_once '../modelo/db.php';
$conn = conect();


$sql = "SELECT id, mensaje, fecha, vista FROM notificaciones  ORDER BY fecha DESC LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$notificaciones = $result->fetch_all(MYSQLI_ASSOC);

// Contar no vistas
$no_vistas = count(array_filter($notificaciones, fn($n) => $n['vista'] == 0));


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom Validation -->
    <script src="js/validation.js"></script>

    <style>
        html, body {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

.content {
    flex: 1;
}
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #343a40;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            font-size: 16px;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .sidebar a.active {
            background-color: #004494;
            font-weight: bold;
            border-left: 4px solid #00cba9;
        }
        .content {
            margin-left: 260px;
            padding: 30px;
        }
        .card {
            border-radius: 12px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Menú lateral -->
    <?php
        if ($_SESSION['user_rol'] == 1) {
            require_once "menu.php";
        }else if ($_SESSION['user_rol'] == 2) {
            require_once "menu_negocio.php";
        }
       
    ?>
    <!-- Contenido principal -->
    <div class="content">
        <div class="container-fluid">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <?php
            require_once "controllador_vista.php";
        ?>
        </div>
    </div>

<!-- Botón flotante mejorado -->
<div id="notificaciones-btn" class="position-fixed bottom-0 end-0 m-4" style="z-index: 1055;">
  <button class="btn btn-light border rounded-circle shadow-lg d-flex align-items-center justify-content-center position-relative" 
          onclick="toggleNotificaciones()" 
          style="width: 60px; height: 60px; transition: 0.3s ease;">
    <i class="fas fa-bell fa-lg text-primary"></i>
    
    <?php if ($no_vistas > 0): ?>
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger animate__animated animate__bounce">
        <?= $no_vistas ?>
      </span>
    <?php endif; ?>
  </button>
</div>

<!-- Panel de notificaciones -->
<div id="panelNotificaciones" class="card position-fixed end-0 shadow" style="bottom: 80px; right: 1rem; width: 300px; display: none; z-index: 1050;">
  <div class="card-header bg-primary text-white d-flex justify-content-between">
    Notificaciones
    <button class="btn-close btn-close-white btn-sm" onclick="toggleNotificaciones()"></button>
  </div>
  <ul class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
    <?php if (empty($notificaciones)): ?>
      <li class="list-group-item text-center">Sin notificaciones</li>
    <?php else: ?>
      <?php foreach ($notificaciones as $n): ?>
        <li class="list-group-item <?= $n['vista'] ? '' : 'bg-light fw-bold' ?>">
          <div><?= htmlspecialchars($n['mensaje']) ?></div>
          <small class="text-muted"><?= date("d/m/Y H:i", strtotime($n['fecha'])) ?></small>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>
      </div>

      

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2025 SP production. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function toggleNotificaciones() {
    const panel = document.getElementById('panelNotificaciones');
    panel.style.display = (panel.style.display === 'none') ? 'block' : 'none';
  }

  // Cierra el panel si haces clic fuera
  window.addEventListener('click', function(e) {
    const panel = document.getElementById('panelNotificaciones');
    const button = document.getElementById('notificaciones-btn');
    if (!panel.contains(e.target) && !button.contains(e.target)) {
      panel.style.display = 'none';
    }
  });
</script>
</body>
</html>
