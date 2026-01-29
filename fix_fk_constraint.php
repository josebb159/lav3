<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "lavadora";

$mysqli = new mysqli($host, $user, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Modificando restricciones de la tabla alquileres...\n\n";

// 1. Eliminar la foreign key existente
$sql1 = "ALTER TABLE alquileres DROP FOREIGN KEY alquileres_ibfk_2";
if ($mysqli->query($sql1)) {
    echo "✓ Foreign key alquileres_ibfk_2 eliminada\n";
} else {
    echo "⚠ Error al eliminar FK (puede que no exista): " . $mysqli->error . "\n";
}

// 2. Modificar la columna lavadora_id para permitir NULL
$sql2 = "ALTER TABLE alquileres MODIFY COLUMN lavadora_id INT(11) NULL DEFAULT NULL";
if ($mysqli->query($sql2)) {
    echo "✓ Columna lavadora_id modificada para permitir NULL\n";
} else {
    echo "✗ Error al modificar columna: " . $mysqli->error . "\n";
}

// 3. Recrear la foreign key pero permitiendo NULL
$sql3 = "ALTER TABLE alquileres 
         ADD CONSTRAINT alquileres_ibfk_2 
         FOREIGN KEY (lavadora_id) 
         REFERENCES lavadoras(id) 
         ON DELETE SET NULL 
         ON UPDATE CASCADE";
if ($mysqli->query($sql3)) {
    echo "✓ Foreign key recreada con soporte para NULL\n";
} else {
    echo "✗ Error al crear FK: " . $mysqli->error . "\n";
}

// 4. Actualizar registros existentes con lavadora_id = 0 a NULL
$sql4 = "UPDATE alquileres SET lavadora_id = NULL WHERE lavadora_id = 0";
if ($mysqli->query($sql4)) {
    $affected = $mysqli->affected_rows;
    echo "✓ Actualizados $affected registros con lavadora_id = 0 a NULL\n";
} else {
    echo "✗ Error al actualizar registros: " . $mysqli->error . "\n";
}

echo "\n✅ Proceso completado!\n";

$mysqli->close();
?>
