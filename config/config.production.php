<?php
/**
 * Production Configuration File
 * Copy this to config/config.php for production deployment
 */

// Application Settings
define('APP_NAME', 'Kyna English - Student Evaluation System');
define('APP_VERSION', '1.0.0');

// Base URL - Update this for your production domain
define('BASE_URL', 'https://yourdomain.com/');
define('BASE_PATH', '/');

// Contact Information
define('CONTACT_PHONE', '1900 6364 09');
define('CONTACT_EMAIL', 'hotro@kynaforkids.vn');
define('CONTACT_WEBSITE', 'https://kynaforkids.vn');

// System Settings
define('TIMEZONE', 'Asia/Ho_Chi_Minh');
date_default_timezone_set(TIMEZONE);

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 1); // Set to 1 for HTTPS
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

// Error Reporting - PRODUCTION MODE
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/../logs/error.log');

// PDF Settings
define('PDF_AUTHOR', 'Kyna English');
define('PDF_CREATOR', 'Kyna English Evaluation System');
define('PDF_OUTPUT_DIR', dirname(__FILE__) . '/../pdf_output/');

// Upload Settings
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
