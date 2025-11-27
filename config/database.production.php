<?php
/**
 * Production Database Configuration Template
 * Copy this to config/database.php and update credentials
 */

// Database credentials - PRODUCTION
define('DB_HOST', 'tungdt.io.vn');
define('DB_NAME', 'kyna_english');
define('DB_USER', 'root');
define('DB_PASS', 'TungDT@2025');
define('DB_CHARSET', 'utf8mb4');

// Create connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::ATTR_PERSISTENT         => false // Set to true for connection pooling
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log error instead of displaying it
    error_log("Database connection failed: " . $e->getMessage());
    
    // Show generic error to user
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng liên hệ quản trị viên.");
}

// Helper function to execute queries
function query($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query error: " . $e->getMessage());
        return false;
    }
}

// Helper function to get single row
function getOne($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

// Helper function to get all rows
function getAll($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt ? $stmt->fetchAll() : false;
}

// Helper function to get last insert ID
function getLastInsertId() {
    global $pdo;
    return $pdo->lastInsertId();
}

// Helper function to begin transaction
function beginTransaction() {
    global $pdo;
    return $pdo->beginTransaction();
}

// Helper function to commit transaction
function commit() {
    global $pdo;
    return $pdo->commit();
}

// Helper function to rollback transaction
function rollback() {
    global $pdo;
    return $pdo->rollBack();
}
