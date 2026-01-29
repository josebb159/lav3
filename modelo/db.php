<?php
/**
 * Conexión mejorada a base de datos con manejo de errores
 */

// Habilitar reporte de errores mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function conect(){
    


    $servername = "localhost";
    $username = "alquilav_ndb";
    $password = "&^L1s,)Z_W56";
    $dbname = "alquilav_ndb";
    
    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Establecer charset UTF-8 para evitar problemas con caracteres especiales
        $conn->set_charset("utf8mb4");
        
        return $conn;
    } catch (mysqli_sql_exception $e) {
        // Log error en producción (requiere helpers.php)
        if (file_exists(__DIR__ . '/helpers.php')) {
            require_once __DIR__ . '/helpers.php';
            log_error("Error de conexión a BD", ['error' => $e->getMessage()]);
        }
        
        die("Error de conexión a la base de datos. Por favor contacte al administrador.");
    }
}

/**
 * Ejecutar query con prepared statement de forma segura
 */
function db_query($conn, $sql, $params = [], $types = "") {
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}
?>
