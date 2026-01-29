<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "lavadora";

$mysqli = new mysqli($host, $user, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== DIAGNÓSTICO DE SERVICIOS CERCANOS ===\n\n";

$user_id = 194;
$latitud = 7.9022404;
$longitud = -72.5245473;
$radio_km = 10;

// 1. Verificar el usuario
echo "1. Información del usuario ID $user_id:\n";
$result = $mysqli->query("SELECT id, nombre, apellido, conductor_negocio, rol_id FROM usuarios WHERE id = $user_id");
if ($result && $user = $result->fetch_assoc()) {
    echo "   - Nombre: {$user['nombre']} {$user['apellido']}\n";
    echo "   - Negocio: {$user['conductor_negocio']}\n";
    echo "   - Rol: {$user['rol_id']}\n\n";
    
    $conductor_negocio = $user['conductor_negocio'];
} else {
    echo "   ✗ Usuario no encontrado\n\n";
    exit;
}

// 2. Verificar servicios pendientes del negocio
echo "2. Servicios pendientes del negocio $conductor_negocio:\n";
$query = "
    SELECT 
        a.id,
        a.user_id,
        a.tipo_lavadora,
        a.latitud,
        a.longitud,
        a.status_servicio,
        a.conductor_id,
        a.negocio_id,
        a.status,
        CONCAT(u.nombre, ' ', u.apellido) as nombre_cliente
    FROM alquileres a
    JOIN usuarios u ON a.user_id = u.id
    WHERE a.negocio_id = $conductor_negocio
    AND a.status_servicio = 1
    AND a.status = 'activo'
";

$result = $mysqli->query($query);
if ($result && $result->num_rows > 0) {
    while ($servicio = $result->fetch_assoc()) {
        echo "\n   Servicio ID {$servicio['id']}:\n";
        echo "   - Cliente: {$servicio['nombre_cliente']}\n";
        echo "   - Tipo: {$servicio['tipo_lavadora']}\n";
        echo "   - Conductor ID: {$servicio['conductor_id']}\n";
        echo "   - Status Servicio: {$servicio['status_servicio']}\n";
        echo "   - Negocio ID: {$servicio['negocio_id']}\n";
        echo "   - Status: {$servicio['status']}\n";
        echo "   - Ubicación: {$servicio['latitud']}, {$servicio['longitud']}\n";
        
        // Calcular distancia
        function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
            $radioTierra = 6371;
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                 sin($dLon / 2) * sin($dLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            return $radioTierra * $c;
        }
        
        $distancia = calcularDistancia($latitud, $longitud, $servicio['latitud'], $servicio['longitud']);
        echo "   - Distancia: " . round($distancia, 2) . " km\n";
        
        if ($servicio['conductor_id'] == 0 || $servicio['conductor_id'] === null) {
            echo "   ✓ Sin conductor asignado\n";
        } else {
            echo "   ✗ Ya tiene conductor: {$servicio['conductor_id']}\n";
        }
        
        if ($distancia <= $radio_km) {
            echo "   ✓ Dentro del radio ($radio_km km)\n";
        } else {
            echo "   ✗ Fuera del radio ($radio_km km)\n";
        }
    }
} else {
    echo "   ✗ No hay servicios pendientes\n";
}

echo "\n3. Verificando filtro completo:\n";
$query_completo = "
    SELECT COUNT(*) as total
    FROM alquileres a
    WHERE a.negocio_id = $conductor_negocio
    AND a.status_servicio = 1
    AND (a.conductor_id = 0 OR a.conductor_id IS NULL)
    AND a.status = 'activo'
";
$result = $mysqli->query($query_completo);
$row = $result->fetch_assoc();
echo "   Total servicios que cumplen filtros: {$row['total']}\n";

$mysqli->close();
?>
