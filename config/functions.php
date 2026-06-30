<?php
// ============================================
// FUNCTIONS - SEMUA FUNGSI GLOBAL
// ============================================

require_once __DIR__ . '/database.php';

// ============================================
// SESSION & AUTH FUNCTIONS
// ============================================

function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function setCurrentUserId($userId) {
    $_SESSION['current_user_id'] = $userId;
    try {
        $pdo = getDbConnection();
        $pdo->exec("SET @current_user_id = " . (int)$userId);
    } catch (Exception $e) {
        // Silent fail
    }
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['admin', 'super_admin']);
}

function isSuperAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'super_admin';
}

function isStaff() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'staff';
}

function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

function getCurrentUserId() {
    return $_SESSION['user']['id'] ?? 0;
}

// ============================================
// CODE GENERATORS
// ============================================

function generateItemCode() {
    $year = date('Y');
    $month = date('m');
    $prefix = "BRG-{$year}{$month}-";
    $count = fetchColumn("SELECT COUNT(*) FROM items WHERE code LIKE ?", [$prefix . '%']);
    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

function generateLoanCode() {
    $year = date('Y');
    $month = date('m');
    $prefix = "L-{$year}{$month}-";
    $count = fetchColumn("SELECT COUNT(*) FROM loans WHERE code LIKE ?", [$prefix . '%']);
    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

function generateReturnCode() {
    $year = date('Y');
    $month = date('m');
    $prefix = "RET-{$year}{$month}-";
    $count = fetchColumn("SELECT COUNT(*) FROM returns WHERE code LIKE ?", [$prefix . '%']);
    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

function generateBorrowerCode() {
    $prefix = "BOR-";
    $count = fetchColumn("SELECT COUNT(*) FROM borrowers WHERE code LIKE ?", [$prefix . '%']);
    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

// ============================================
// DATE FUNCTIONS
// ============================================

function formatDate($date, $format = 'd/m/Y') {
    if (!$date || $date === '0000-00-00') return '-';
    return date($format, strtotime($date));
}

function formatDateTime($date, $format = 'd/m/Y H:i') {
    if (!$date || $date === '0000-00-00 00:00:00') return '-';
    return date($format, strtotime($date));
}

function daysDiff($date1, $date2) {
    $d1 = new DateTime($date1);
    $d2 = new DateTime($date2);
    return $d1->diff($d2)->days;
}

function isOverdue($returnDate) {
    return strtotime($returnDate) < strtotime(date('Y-m-d'));
}

// ============================================
// HTML HELPERS (BADGES)
// ============================================

function getStatusBadge($status) {
    $badges = [
        'baik' => '<span class="badge bg-success">Baik</span>',
        'rusak' => '<span class="badge bg-danger">Rusak</span>',
        'perbaikan' => '<span class="badge bg-warning text-dark">Perbaikan</span>',
        'tersedia' => '<span class="badge bg-success">Tersedia</span>',
        'dipinjam' => '<span class="badge bg-warning text-dark">Dipinjam</span>',
        'hilang' => '<span class="badge bg-danger">Hilang</span>',
        'pending' => '<span class="badge bg-secondary">Pending</span>',
        'dikembalikan' => '<span class="badge bg-info">Dikembalikan</span>',
        'terlambat' => '<span class="badge bg-danger">Terlambat</span>',
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}

function getRoleBadge($role) {
    $badges = [
        'super_admin' => '<span class="badge bg-danger">Super Admin</span>',
        'admin' => '<span class="badge bg-primary">Admin</span>',
        'staff' => '<span class="badge bg-info">Staff</span>'
    ];
    return $badges[$role] ?? '<span class="badge bg-secondary">' . $role . '</span>';
}

function getStockStatusBadge($quantity, $minQuantity) {
    if ($quantity <= 0) return '<span class="badge bg-danger">Habis</span>';
    if ($quantity <= $minQuantity) return '<span class="badge bg-warning text-dark">Stok Menipis</span>';
    return '<span class="badge bg-success">Cukup</span>';
}

function getConditionBadge($condition) {
    $badges = [
        'baik' => '<span class="badge bg-success">Baik</span>',
        'rusak' => '<span class="badge bg-danger">Rusak</span>',
        'perbaikan' => '<span class="badge bg-warning text-dark">Perbaikan</span>'
    ];
    return $badges[$condition] ?? '<span class="badge bg-secondary">' . $condition . '</span>';
}

// ============================================
// FLASH MESSAGES
// ============================================

function flashMessage($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function getFlashMessage($key) {
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

function showFlashMessages() {
    $html = '';
    $types = ['success', 'error', 'warning', 'info'];
    foreach ($types as $type) {
        $msg = getFlashMessage($type);
        if ($msg) {
            $class = $type === 'error' ? 'danger' : $type;
            $html .= "<div class='alert alert-{$class} alert-dismissible fade show' role='alert'>
                {$msg}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
        }
    }
    return $html;
}

// ============================================
// FILE UPLOAD
// ============================================

function uploadFile($file, $targetDir = 'uploads/items/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($ext, $allowed)) return false;
    if ($file['size'] > 2 * 1024 * 1024) return false;
    
    $filename = uniqid() . '.' . $ext;
    $targetPath = $targetDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/items/' . $filename;
    }
    return false;
}

function deleteFile($filePath) {
    if ($filePath && file_exists($filePath)) return unlink($filePath);
    return false;
}

// ============================================
// REDIRECT
// ============================================

function redirect($url) {
    header("Location: $url");
    exit;
}

function redirectBack() {
    $url = $_SERVER['HTTP_REFERER'] ?? 'index.php?url=dashboard';
    header("Location: $url");
    exit;
}

// ============================================
// INPUT SANITIZATION
// ============================================

function sanitize($input) {
    if (is_array($input)) return array_map('sanitize', $input);
    return htmlspecialchars(strip_tags(trim($input)));
}

function sanitizeLike($input) {
    return '%' . addcslashes($input, '%_') . '%';
}

// ============================================
// VALIDATION
// ============================================

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidPhone($phone) {
    return preg_match('/^(08|62|0)[0-9]{8,13}$/', $phone);
}

function isValidDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// ============================================
// FORMAT HELPERS
// ============================================

function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . $suffix;
}

function randomString($length = 10) {
    return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}