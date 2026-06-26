<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}

$url = $_GET['url'] ?? 'dashboard';
$url = trim($url, '/');
$parts = explode('-', $url);
$module = $parts[0];
$action = $parts[1] ?? 'index';

// ============================================================
// ROUTING KHUSUS UNTUK DOWNLOAD TEMPLATE KELUARGA
// ============================================================
if ($module == 'keluarga' && $action == 'download') {
    $file = 'modules/keluarga/download.php';
    include $file;
    exit;
}

// ============================================================
// FILE TANPA HEADER (download, import, delete, dll)
// ============================================================
$no_header_modules = ['delete', 'hapus', 'proses', 'download', 'import'];

// Cek apakah file tanpa header
if ($module === 'dashboard') {
    $file = 'modules/dashboard/index.php';
} else {
    $file = "modules/{$module}/{$action}.php";
}

if (!file_exists($file)) {
    http_response_code(404);
    exit('404 - Halaman tidak ditemukan');
}

// ============================================================
// VALIDASI ROLE (kecuali untuk file tanpa header)
// ============================================================
if (!in_array($action, $no_header_modules)) {
    $role_access = [
        'admin' => ['*'],
        'kader' => [
            'dashboard',
            'keluarga',
            'anak',
            'kegiatan',
            'kehadiran',
            'pemeriksaan',
            'laporan',
            'imunisasi',
            'ibu_hamil',
            'master_imunisasi',
            'imunisasi_ibu',
            'kehadiran_ibu_hamil',
            'pemeriksaan_ibu',
            'statistik'
        ],
        'bidan' => [
            'dashboard',
            'keluarga',
            'anak',
            'kegiatan',
            'kehadiran',
            'pemeriksaan',
            'laporan',
            'imunisasi',
            'ibu_hamil',
            'master_imunisasi',
            'imunisasi_ibu',
            'kehadiran_ibu_hamil',
            'pemeriksaan_ibu',
            'statistik'
        ],
    ];

    $user_role = $_SESSION['user']['role'] ?? '';

    if (!isset($role_access[$user_role])) {
        http_response_code(403);
        exit('403 - Role tidak valid');
    }

    if (!in_array('*', $role_access[$user_role]) && !in_array($module, $role_access[$user_role])) {
        http_response_code(403);
        exit('403 - Akses ditolak');
    }
}

// ============================================================
// INCLUDE FILE
// ============================================================

// Jika file tanpa header, include langsung tanpa header
if (in_array($action, $no_header_modules)) {
    include $file;
    exit;
}

// Sisanya include dengan header
include 'views/header.php';
include 'views/sidebar.php';
include 'views/topbar.php';
include $file;
include 'views/footer.php';
?>