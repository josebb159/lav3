<?php
/**
 * Funciones auxiliares para validación, sanitización y utilidades
 */

/**
 * Sanitizar entrada según tipo
 */
function sanitize_input($data, $type = 'string') {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    
    switch($type) {
        case 'int':
            return intval($data);
        case 'float':
            return floatval($data);
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        default:
            return $conn->real_escape_string($data);
    }
}

/**
 * Validar formato de email
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validar campos requeridos
 */
function validate_required($fields, $data) {
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $missing[] = $field;
        }
    }
    return $missing;
}

/**
 * Respuesta JSON estandarizada
 */
function json_response($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Logging centralizado
 */
function log_error($message, $context = []) {
    $log_dir = __DIR__ . '/../logs';
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    
    $log_file = $log_dir . '/app_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $context_str = !empty($context) ? json_encode($context) : '';
    
    $log_message = "[$timestamp] $message $context_str\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

/**
 * Verificar si email ya existe en BD
 */
function email_exists($conn, $email, $exclude_id = null) {
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?" . ($exclude_id ? " AND id != ?" : ""));
    if ($exclude_id) {
        $stmt->bind_param("si", $email, $exclude_id);
    } else {
        $stmt->bind_param("s", $email);
    }
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}
?>
