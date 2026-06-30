<?php
// config/app.php

// ============================================
// APPLICATION CONFIGURATION
// ============================================

// Application Info
define('APP_NAME', 'Sistem Inventaris Barang');
define('APP_VERSION', '2.0.0');
define('APP_URL', 'http://inventaris-barang.test/');

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
define('DEFAULT_LOAN_DURATION', 7);
define('MAX_LOAN_PER_BORROWER', 5);

// Security
define('SESSION_TIMEOUT', 3600);

// ============================================
// SESSION CONFIGURATION
// ============================================

function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['created_at'])) {
        $_SESSION['created_at'] = time();
    } elseif (time() - $_SESSION['created_at'] > SESSION_TIMEOUT) {
        session_regenerate_id(true);
        $_SESSION['created_at'] = time();
    }
}