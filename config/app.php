<?php
// ============================================
// APPLICATION CONFIGURATION
// ============================================

// Application Info
define('APP_NAME', 'Sistem Inventaris Barang');
define('APP_VERSION', '2.0.0');
define('APP_URL', 'http://localhost/inventaris-app/');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Paths
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('UPLOAD_ITEMS_PATH', UPLOAD_PATH . 'items/');

// Upload Settings
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('PER_PAGE', 15);

// Loan Settings
define('DEFAULT_LOAN_DURATION', 7); // days
define('MAX_LOAN_PER_BORROWER', 5);

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour

// ============================================
// SESSION CONFIGURATION
// ============================================

function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Regenerate session ID periodically
    if (!isset($_SESSION['created_at'])) {
        $_SESSION['created_at'] = time();
    } elseif (time() - $_SESSION['created_at'] > SESSION_TIMEOUT) {
        session_regenerate_id(true);
        $_SESSION['created_at'] = time();
    }
}

// ============================================
// ERROR REPORTING
// ============================================

// Set error reporting based on environment
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}