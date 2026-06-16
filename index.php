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


if ($module === 'dashboard') {
    $file = 'modules/dashboard/index.php';
} else {
    $file = "modules/{$module}/{$action}.php";
}

if (!file_exists($file)) {
    http_response_code(404);
    exit('404 - Halaman tidak ditemukan');
}

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
        'imunisasi'
    ],

    'bidan' => [
        'dashboard',
        'keluarga',
        'anak',
        'kegiatan',
        'kehadiran',
        'pemeriksaan',
        'laporan',
        'imunisasi'
    ],
];

// VALIDASI ROLE
$user_role = $_SESSION['user']['role'] ?? '';

if (!isset($role_access[$user_role])) {
    http_response_code(403);
    exit('403 - Role tidak valid');
}

if (
    !in_array('*', $role_access[$user_role]) &&
    !in_array($module, $role_access[$user_role])
) {
    http_response_code(403);
    exit('403 - Akses ditolak');
}


include 'views/header.php';
include 'views/sidebar.php';
include 'views/topbar.php';
include $file;
include 'views/footer.php';