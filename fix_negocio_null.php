<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "lavadora";

$mysqli = new mysqli($host, $user, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Modificando columna negocio_id en alquileres...\n\n";

// 1. Eliminar la foreign key de negocio_id si existe
$sql1 = "ALTER TABLE alquileres DROP FOREIGN KEY fk_negocio_id";
if ($mysqli->query($sql1)) {
    echo "✓ Foreign key fk_negocio_id eliminada\n";
} else {
    echo "⚠ Error al eliminar FK (puede que no exista): " . $mysqli->error . "\n";
}

// 2. Modificar la columna negocio_id para permitir NULL
$sql2 = "ALTER TABLE alquileres MODIFY COLUMN negocio_id INT(11) NULL DEFAULT NULL";
if ($mysqli->query($sql2)) {
    echo "✓ Columna negocio_id modificada para permitir NULL\n";
} else {
    echo "✗ Error al modificar columna: " . $mysqli->error . "\n";
}

// 3. Recrear la foreign key pero permitiendo NULL (opcional)
$sql3 = "ALTER TABLE alquileres 
         ADD CONSTRAINT fk_negocio_id 
         FOREIGN KEY (negocio_id) 
         REFERENCES negocios(id) 
         ON DELETE SET NULL 
         ON UPDATE CASCADE";
if ($mysqli->query($sql3)) {
    echo "✓ Foreign key recreada con soporte para NULL\n";
} else {
    echo "✗ Error al crear FK: " . $mysqli->error . "\n";
}

echo "\n✅ Proceso completado!\n";

$mysqli->close();
?>
