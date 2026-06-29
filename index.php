<?php
// ============================================
// MAIN ROUTING
// ============================================

session_start();

// Load configurations
require_once 'config/database.php';
require_once 'config/app.php';
require_once 'helpers/functions.php';

// Initialize session
initSession();

// Check login
if (!isset($_SESSION['user'])) {
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
// ACTIONS WITHOUT HEADER
// ============================================

$no_header_actions = [
    'delete', 'hapus', 'proses', 'download', 'import', 'export',
    'add_to_cart', 'update_cart', 'remove_cart', 'clear_cart',
    'get_items', 'get_borrowers', 'check_stock', 'print',
    'ajax' // AJAX requests
];

// ============================================
// ROLE ACCESS DEFINITION
// ============================================

$role_access = [
    'super_admin' => ['*'],
    'admin' => [
        'dashboard',
        'categories',
        'items',
        'borrowers',
        'loans',
        'returns',
        'reports',
        'history',
        'profile'
    ],
    'staff' => [
        'dashboard',
        'items',
        'borrowers',
        'loans',
        'returns',
        'reports',
        'profile'
    ]
];

$admin_only_modules = ['categories', 'users', 'settings', 'history'];
$staff_readonly_actions = ['view', 'show', 'index', 'read'];

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
    
    // Check if role exists
    if (!isset($role_access[$user_role])) {
        http_response_code(403);
        exit('403 - Role tidak valid');
    }
    
    // Check module access
    $allowed_modules = $role_access[$user_role];
    if (!in_array('*', $allowed_modules) && !in_array($module, $allowed_modules)) {
        http_response_code(403);
        exit('403 - Akses ditolak. Module "' . $module . '" tidak diizinkan untuk role ' . $user_role);
    }
    
    // Admin-only modules
    if (in_array($module, $admin_only_modules) && !in_array($user_role, ['admin', 'super_admin'])) {
        http_response_code(403);
        exit('403 - Akses ditolak. Module "' . $module . '" hanya untuk Admin.');
    }
    
    // Staff read-only restrictions
    if ($user_role == 'staff') {
        $write_actions = ['create', 'edit', 'update', 'store', 'add', 'save', 'delete', 'hapus', 'remove'];
        
        // Staff cannot write to items and borrowers
        if (in_array($module, ['items', 'borrowers']) && in_array($action, $write_actions)) {
            http_response_code(403);
            exit('403 - Akses ditolak. Staff hanya bisa melihat data.');
        }
        
        // Staff cannot delete loans
        if ($module == 'loans' && in_array($action, ['delete', 'hapus', 'remove'])) {
            http_response_code(403);
            exit('403 - Akses ditolak. Staff tidak bisa menghapus data peminjaman.');
        }
    }
    
    // Users management only for super_admin
    if ($module == 'users' && $user_role != 'super_admin') {
        http_response_code(403);
        exit('403 - Akses ditolak. Hanya Super Admin yang dapat mengelola user.');
    }
    
    // Settings only for super_admin
    if ($module == 'settings' && $user_role != 'super_admin') {
        http_response_code(403);
        exit('403 - Akses ditolak. Hanya Super Admin yang dapat mengubah pengaturan.');
    }
}

// ============================================
// SET GLOBAL VARIABLES
// ============================================

$current_user = $_SESSION['user'];
$current_role = $current_user['role'];
$current_module = $module;
$current_action = $action;

// Set current user ID for triggers
setCurrentUserId($current_user['id']);

// ============================================
// INCLUDE FILE
// ============================================

// If action without header, include directly
if (in_array($action, $no_header_actions)) {
    include $file;
    exit;
}

// Include with header and sidebar
include 'views/header.php';
include 'views/sidebar.php';
include 'views/topbar.php';
include $file;
include 'views/footer.php';
?>