<?php
/**
 * config/database.php
 * Konfigurasi koneksi database MySQL
 */

// Database Configuration
define('DB_HOST', 'mysql_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');  // Sesuaikan dengan password MySQL Anda
define('DB_NAME', 'stok_hp');
define('DB_PORT', 3306);

// Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conn->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Koneksi database gagal: ' . $conn->connect_error]));
    }
    
    // Set charset
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die(json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]));
}

// Fungsi helper untuk query
function query($sql, $params = []) {
    global $conn;
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return ['status' => 'error', 'message' => 'Query error: ' . $conn->error];
    }
    
    if (!empty($params)) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) $types .= 'i';
            elseif (is_float($param)) $types .= 'd';
            else $types .= 's';
        }
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt;
}
?>
