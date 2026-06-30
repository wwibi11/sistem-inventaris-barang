<?php
// ============================================
// MAIN ROUTING - SISTEM INVENTARIS BARANG
// ============================================

session_start();

// Load semua fungsi dari folder config
require_once __DIR__ . '/config/functions.php';

// ============================================
// CHECK LOGIN
// ============================================
if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = 'Silakan login terlebih dahulu!';
    header("Location: auth/login.php");
    exit;
}

// ============================================
// CEK STATUS AKTIF USER
// ============================================
$user = fetchOne(
    "SELECT is_active FROM users WHERE id = ?",
    [$_SESSION['user']['id']]
);

if (!$user || $user['is_active'] != 1) {
    session_destroy();
    $_SESSION['error'] = 'Akun Anda tidak aktif. Hubungi administrator!';
    header("Location: auth/login.php");
    exit;
}

// ============================================
// ROUTING
// ============================================

$url = $_GET['url'] ?? 'dashboard';
$url = trim($url, '/');
$parts = explode('/', $url);
$module = $parts[0] ?? 'dashboard';
$action = $parts[1] ?? 'index';
$id = $parts[2] ?? null;

// ============================================
// ACTIONS WITHOUT HEADER (AJAX, DELETE, DLL)
// ============================================

$no_header_actions = [
    'delete', 'hapus', 'proses', 'download', 'import', 'export',
    'add_to_cart', 'update_cart', 'remove_cart', 'clear_cart',
    'get_items', 'get_borrowers', 'check_stock', 'print', 'ajax'
];

// ============================================
// ROLE ACCESS DEFINITION
// ============================================

$role_access = [
    'super_admin' => ['*'], // Akses semua
    'admin' => [
        'dashboard',
        'categories',
        'items',
        'borrowers',
        'loans',
        'returns',
        'reports',
        'history'
    ],
    'staff' => [
        'dashboard',
        'items',      // hanya lihat & pinjam
        'borrowers',  // hanya lihat
        'loans',      // pinjam & kembali
        'returns',    // kembali
        'reports'     // lihat laporan
    ]
];

// ============================================
// MODUL YANG HANYA UNTUK ADMIN
// ============================================
$admin_only_modules = ['categories', 'users', 'settings', 'history'];

// ============================================
// ACTION YANG TIDAK BOLEH STAFF
// ============================================
$staff_forbidden_actions = ['create', 'edit', 'update', 'store', 'add', 'save', 'delete', 'hapus', 'remove'];

// ============================================
// CHECK FILE EXISTENCE
// ============================================

if ($module === 'dashboard') {
    $file = 'modules/dashboard/index.php';
} else {
    $file = "modules/{$module}/{$action}.php";
}

if (!file_exists($file)) {
    http_response_code(404);
    exit('404 - Halaman tidak ditemukan');
}

// ============================================
// VALIDATE ROLE ACCESS
// ============================================

if (!in_array($action, $no_header_actions)) {
    $user_role = $_SESSION['user']['role'] ?? 'staff';
    
    // 1. Cek apakah role terdaftar
    if (!isset($role_access[$user_role])) {
        http_response_code(403);
        exit('403 - Role tidak valid');
    }
    
    // 2. Cek akses module
    $allowed_modules = $role_access[$user_role];
    if (!in_array('*', $allowed_modules) && !in_array($module, $allowed_modules)) {
        http_response_code(403);
        exit('403 - Akses ditolak. Module "' . $module . '" tidak diizinkan.');
    }
    
    // 3. Admin-only modules
    if (in_array($module, $admin_only_modules) && !in_array($user_role, ['admin', 'super_admin'])) {
        http_response_code(403);
        exit('403 - Akses ditolak. Module "' . $module . '" hanya untuk Admin.');
    }
    
    // 4. Staff tidak boleh create/edit/delete di items & borrowers
    if ($user_role == 'staff') {
        if (in_array($module, ['items', 'borrowers']) && in_array($action, $staff_forbidden_actions)) {
            http_response_code(403);
            exit('403 - Staff hanya bisa melihat data, tidak bisa mengubah.');
        }
        
        // Staff tidak boleh hapus peminjaman
        if ($module == 'loans' && in_array($action, ['delete', 'hapus', 'remove'])) {
            http_response_code(403);
            exit('403 - Staff tidak bisa menghapus data peminjaman.');
        }
    }
    
    // 5. Users hanya untuk super_admin
    if ($module == 'users' && $user_role != 'super_admin') {
        http_response_code(403);
        exit('403 - Hanya Super Admin yang dapat mengelola user.');
    }
    
    // 6. Settings hanya untuk super_admin
    if ($module == 'settings' && $user_role != 'super_admin') {
        http_response_code(403);
        exit('403 - Hanya Super Admin yang dapat mengubah pengaturan.');
    }
}

// ============================================
// SET GLOBAL VARIABLES
// ============================================

$current_user = $_SESSION['user'];
$current_role = $current_user['role'];
$current_module = $module;
$current_action = $action;

// Set current user ID
if (function_exists('setCurrentUserId')) {
    setCurrentUserId($current_user['id']);
}

// ============================================
// INCLUDE FILE
// ============================================

if (in_array($action, $no_header_actions)) {
    include $file;
    exit;
}

include 'views/header.php';
include 'views/sidebar.php';
include 'views/topbar.php';
include $file;
include 'views/footer.php';