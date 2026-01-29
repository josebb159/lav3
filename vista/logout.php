<?php
session_start();

// Limpia la sesi車n
$_SESSION = array();
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cerrando sesi車n...</title>
  <script>
    // Redirige a index.php despu谷s de cerrar la sesi車n
    window.onload = function () {
      // Borra el cache del navegador (si aplica)
      if ('caches' in window) {
        caches.keys().then(function(names) {
          for (let name of names) caches.delete(name);
        });
      }

      // Espera un momento para asegurar el cierre de sesi車n
      setTimeout(function () {
        window.location.href = "../index.php";
      }, 500); // 0.5 segundos (puedes ajustar)
    };
  </script>
</head>
<body>
  <p>Cerrando sesi&oacute;n, por favor espera...</p>
</body>
</html>
