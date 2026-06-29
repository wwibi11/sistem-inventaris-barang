<?php
// ============================================
// HELPER FUNCTIONS
// ============================================

require_once __DIR__ . '/../config/database.php';

// ============================================
// CODE GENERATORS
// ============================================

/**
 * Generate item code
 */
function generateItemCode() {
    $year = date('Y');
    $month = date('m');
    $prefix = "BRG-{$year}{$month}-";
    
    $count = fetchColumn(
        "SELECT COUNT(*) FROM items WHERE code LIKE ?",
        [$prefix . '%']
    );
    
    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

/**
 * Generate loan code
 */
function generateLoanCode() {
    $year = date('Y');
    $month = date('m');
    $prefix = "L-{$year}{$month}-";
    
    $count = fetchColumn(
        "SELECT COUNT(*) FROM loans WHERE code LIKE ?",
        [$prefix . '%']
    );
    
    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

/**
 * Generate return code
 */
function generateReturnCode() {
    $year = date('Y');
    $month = date('m');
    $prefix = "RET-{$year}{$month}-";
    
    $count = fetchColumn(
        "SELECT COUNT(*) FROM returns WHERE code LIKE ?",
        [$prefix . '%']
    );
    
    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

/**
 * Generate borrower code
 */
function generateBorrowerCode() {
    $prefix = "BOR-";
    
    $count = fetchColumn(
        "SELECT COUNT(*) FROM borrowers WHERE code LIKE ?",
        [$prefix . '%']
    );
    
    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

// ============================================
// DATE FUNCTIONS
// ============================================

/**
 * Format date
 */
function formatDate($date, $format = 'd/m/Y') {
    if (!$date || $date === '0000-00-00') {
        return '-';
    }
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime($date, $format = 'd/m/Y H:i') {
    if (!$date || $date === '0000-00-00 00:00:00') {
        return '-';
    }
    return date($format, strtotime($date));
}

/**
 * Get days difference
 */
function daysDiff($date1, $date2) {
    $d1 = new DateTime($date1);
    $d2 = new DateTime($date2);
    $diff = $d1->diff($d2);
    return $diff->days;
}

/**
 * Check if date is overdue
 */
function isOverdue($returnDate) {
    return strtotime($returnDate) < strtotime(date('Y-m-d'));
}

// ============================================
// HTML HELPERS
// ============================================

/**
 * Get status badge
 */
function getStatusBadge($status) {
    $badges = [
        'baik' => '<span class="badge badge-success">Baik</span>',
        'rusak' => '<span class="badge badge-danger">Rusak</span>',
        'perbaikan' => '<span class="badge badge-warning">Perbaikan</span>',
        'tersedia' => '<span class="badge badge-success">Tersedia</span>',
        'dipinjam' => '<span class="badge badge-warning">Dipinjam</span>',
        'hilang' => '<span class="badge badge-danger">Hilang</span>',
        'pending' => '<span class="badge badge-secondary">Pending</span>',
        'dikembalikan' => '<span class="badge badge-info">Dikembalikan</span>',
        'terlambat' => '<span class="badge badge-danger">Terlambat</span>',
    ];
    
    return $badges[$status] ?? '<span class="badge badge-secondary">' . $status . '</span>';
}

/**
 * Get role badge
 */
function getRoleBadge($role) {
    $badges = [
        'super_admin' => '<span class="badge badge-danger">Super Admin</span>',
        'admin' => '<span class="badge badge-primary">Admin</span>',
        'staff' => '<span class="badge badge-info">Staff</span>'
    ];
    return $badges[$role] ?? '<span class="badge badge-secondary">' . $role . '</span>';
}

/**
 * Get stock status badge
 */
function getStockStatusBadge($quantity, $minQuantity) {
    if ($quantity <= 0) {
        return '<span class="badge badge-danger">Habis</span>';
    } elseif ($quantity <= $minQuantity) {
        return '<span class="badge badge-warning">Stok Menipis</span>';
    } else {
        return '<span class="badge badge-success">Cukup</span>';
    }
}

/**
 * Get condition badge
 */
function getConditionBadge($condition) {
    $badges = [
        'baik' => '<span class="badge badge-success">Baik</span>',
        'rusak' => '<span class="badge badge-danger">Rusak</span>',
        'perbaikan' => '<span class="badge badge-warning">Perbaikan</span>'
    ];
    return $badges[$condition] ?? '<span class="badge badge-secondary">' . $condition . '</span>';
}

// ============================================
// USER FUNCTIONS
// ============================================

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user']);
}

/**
 * Check if user is super admin
 */
function isSuperAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'super_admin';
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['admin', 'super_admin']);
}

/**
 * Check if user is staff
 */
function isStaff() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'staff';
}

/**
 * Get current user
 */
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user']['id'] ?? 0;
}

// ============================================
// FLASH MESSAGES
// ============================================

/**
 * Set flash message
 */
function flashMessage($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

/**
 * Get flash message
 */
function getFlashMessage($key) {
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

/**
 * Display flash messages
 */
function showFlashMessages() {
    $html = '';
    $types = ['success', 'error', 'warning', 'info'];
    
    foreach ($types as $type) {
        $msg = getFlashMessage($type);
        if ($msg) {
            $class = $type === 'error' ? 'danger' : $type;
            $html .= "<div class='alert alert-{$class} alert-dismissible fade show' role='alert'>
                {$msg}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        }
    }
    
    return $html;
}

// ============================================
// FILE UPLOAD
// ============================================

/**
 * Upload file
 */
function uploadFile($file, $targetDir = null) {
    if (!$targetDir) {
        $targetDir = UPLOAD_ITEMS_PATH;
    }
    
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Create directory if not exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $filename = uniqid() . '.' . $ext;
    $targetPath = $targetDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/items/' . $filename;
    }
    
    return false;
}

/**
 * Delete file
 */
function deleteFile($filePath) {
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    return false;
}

// ============================================
// REDIRECT
// ============================================

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Redirect back
 */
function redirectBack() {
    $url = $_SERVER['HTTP_REFERER'] ?? 'index.php?url=dashboard';
    header("Location: $url");
    exit;
}

// ============================================
// INPUT SANITIZATION
// ============================================

/**
 * Sanitize input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)));
}

/**
 * Sanitize for SQL LIKE
 */
function sanitizeLike($input) {
    return '%' . addcslashes($input, '%_') . '%';
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================
// PAGINATION
// ============================================

/**
 * Get pagination data
 */
function getPagination($total, $page, $perPage = PER_PAGE) {
    $totalPages = ceil($total / $perPage);
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    
    return [
        'page' => $page,
        'perPage' => $perPage,
        'total' => $total,
        'totalPages' => $totalPages,
        'offset' => $offset,
        'start' => $offset + 1,
        'end' => min($offset + $perPage, $total)
    ];
}

/**
 * Generate pagination HTML
 */
function paginationLinks($url, $total, $page, $perPage = PER_PAGE) {
    $totalPages = ceil($total / $perPage);
    if ($totalPages <= 1) return '';
    
    $html = '<ul class="pagination">';
    
    // Previous
    if ($page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . ($page - 1) . '">«</a></li>';
    }
    
    // Pages
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
    
    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=1">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $page ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $url . '&page=' . $i . '">' . $i . '</a></li>';
    }
    
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Next
    if ($page < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . ($page + 1) . '">»</a></li>';
    }
    
    $html .= '</ul>';
    return $html;
}

// ============================================
// LOGGING
// ============================================

/**
 * Log activity
 */
function logActivity($action, $description = null) {
    $userId = getCurrentUserId() ?: 0;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $description = $description ?: $action;
    
    try {
        insert('activity_logs', [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ip,
            'user_agent' => $userAgent
        ]);
    } catch (Exception $e) {
        // Silent fail for logging
    }
}

// ============================================
// FORMAT HELPERS
// ============================================

/**
 * Format currency
 */
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Truncate text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Generate random string
 */
function randomString($length = 10) {
    return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

// ============================================
// VALIDATION
// ============================================

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Indonesia)
 */
function isValidPhone($phone) {
    return preg_match('/^(08|62|0)[0-9]{8,13}$/', $phone);
}

/**
 * Validate date
 */
function isValidDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}