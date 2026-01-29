<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "lavadora";

$mysqli = new mysqli($host, $user, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 0. Fix potential timestamp issues
$mysqli->query("ALTER TABLE alquileres MODIFY COLUMN fecha_aceptado TIMESTAMP NULL DEFAULT NULL");
$mysqli->query("UPDATE alquileres SET fecha_aceptado = NULL WHERE fecha_aceptado = '0000-00-00 00:00:00'");


// 1. Add tipo_lavadora column if not exists
$check = $mysqli->query("SHOW COLUMNS FROM alquileres LIKE 'tipo_lavadora'");
if ($check->num_rows == 0) {
    if ($mysqli->query("ALTER TABLE alquileres ADD COLUMN tipo_lavadora VARCHAR(100) NOT NULL DEFAULT '' AFTER lavadora_id")) {
        echo "Column tipo_lavadora added successfully.\n";
    } else {
        echo "Error adding column: " . $mysqli->error . "\n";
    }
} else {
    echo "Column tipo_lavadora already exists.\n";
}

// 2. Modify lavadora_id to allow 0 default
if ($mysqli->query("ALTER TABLE alquileres MODIFY lavadora_id INT(11) NOT NULL DEFAULT 0")) {
    echo "Column lavadora_id modified successfully.\n";
} else {
    echo "Error modifying column: " . $mysqli->error . "\n";
}

$mysqli->close();
?>
