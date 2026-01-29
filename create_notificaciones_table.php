<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "lavadora";

$mysqli = new mysqli($host, $user, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS notificaciones_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tipo_usuario ENUM('cliente', 'domiciliario') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo_notificacion VARCHAR(50),
    id_relacionado INT DEFAULT NULL,
    leida TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_leida (leida)
)";

if ($mysqli->query($sql)) {
    echo "Tabla notificaciones_usuarios creada exitosamente.\n";
} else {
    echo "Error al crear tabla: " . $mysqli->error . "\n";
}

$mysqli->close();
?>
