<?php
/**
 * Configuration File
 * 123 English Evaluation System
 * 
 * This file contains all system-wide configuration settings
 */

// Application Settings
define('APP_NAME', '123 English - Student Evaluation System');
define('APP_VERSION', '1.0.0');

// Base URL - Auto detect
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = str_replace('\\', '/', dirname($scriptName));
$basePath = $basePath === '/' ? '' : $basePath;

define('BASE_URL', $protocol . '://' . $host . $basePath . '/');
define('BASE_PATH', $basePath ? $basePath . '/' : '/');

// Contact Information
define('CONTACT_PHONE', '1900 6364 09');
define('CONTACT_EMAIL', 'support@123english.com');
define('CONTACT_WEBSITE', 'https://123english.com');

// System Settings
define('TIMEZONE', 'Asia/Ho_Chi_Minh');
date_default_timezone_set(TIMEZONE);

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    session_start();
}

// Error Reporting (Development)
// Comment these out in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// For production, use:
// error_reporting(0);
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/../logs/error.log');

// PDF Settings
define('PDF_AUTHOR', '123 English');
define('PDF_CREATOR', '123 English Evaluation System');
define('PDF_OUTPUT_DIR', dirname(__FILE__) . '/../pdf_output/');

// Upload Settings (if needed in future)
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// Pagination
define('ITEMS_PER_PAGE', 50);

// Security Settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LENGTH', 32);

// Generate CSRF Token
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Verify CSRF Token
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && 
           hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

