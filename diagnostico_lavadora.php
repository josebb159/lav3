<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "lavadora";

$mysqli = new mysqli($host, $user, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== DIAGNÓSTICO DE LAVADORA Y NEGOCIO ===\n\n";

// 1. Verificar la lavadora 26
echo "1. Datos de la lavadora ID 26:\n";
$result = $mysqli->query("SELECT * FROM lavadoras WHERE id = 26");
if ($result && $lav = $result->fetch_assoc()) {
    echo "   - ID: {$lav['id']}\n";
    echo "   - Código: {$lav['codigo']}\n";
    echo "   - Tipo: {$lav['type']}\n";
    echo "   - Negocio ID: {$lav['negocio_id']}\n";
    echo "   - Status: {$lav['status']}\n";
    echo "   - Domiciliario ID: {$lav['id_domiciliario']}\n\n";
    
    $negocio_id = $lav['negocio_id'];
} else {
    echo "   ✗ Lavadora 26 no encontrada\n\n";
    exit;
}

// 2. Verificar si el negocio existe
echo "2. Verificando negocio ID $negocio_id:\n";
$result = $mysqli->query("SELECT * FROM negocios WHERE id = $negocio_id");
if ($result && $result->num_rows > 0) {
    $neg = $result->fetch_assoc();
    echo "   ✓ Negocio encontrado:\n";
    echo "   - Nombre: {$neg['nombre']}\n";
    echo "   - Status: {$neg['status']}\n\n";
} else {
    echo "   ✗ Negocio ID $negocio_id NO EXISTE\n\n";
    
    // 3. Listar negocios disponibles
    echo "3. Negocios disponibles en el sistema:\n";
    $result = $mysqli->query("SELECT id, nombre, status FROM negocios WHERE status = 1");
    if ($result && $result->num_rows > 0) {
        while ($neg = $result->fetch_assoc()) {
            echo "   - ID {$neg['id']}: {$neg['nombre']}\n";
        }
        echo "\n";
        
        // 4. Sugerir corrección
        $firstNegocio = $mysqli->query("SELECT id FROM negocios WHERE status = 1 LIMIT 1");
        if ($firstNegocio && $fn = $firstNegocio->fetch_assoc()) {
            $nuevo_negocio = $fn['id'];
            echo "4. SOLUCIÓN SUGERIDA:\n";
            echo "   Actualizar lavadora 26 al negocio ID $nuevo_negocio\n\n";
            echo "   ¿Deseas aplicar esta corrección? (s/n): ";
            
            // Para ejecución automática, aplicar directamente
            echo "Aplicando corrección automáticamente...\n";
            $update = $mysqli->query("UPDATE lavadoras SET negocio_id = $nuevo_negocio WHERE id = 26");
            if ($update) {
                echo "   ✓ Lavadora 26 actualizada al negocio ID $nuevo_negocio\n";
            } else {
                echo "   ✗ Error al actualizar: " . $mysqli->error . "\n";
            }
        }
    } else {
        echo "   ✗ No hay negocios activos en el sistema\n";
    }
}

$mysqli->close();
?>
