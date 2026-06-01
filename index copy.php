<?php
session_start();

// =========================
// PROTEKSI LOGIN
// =========================
if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}

// =========================
// URL
// =========================
$url = $_GET['url'] ?? 'dashboard';
$url = trim($url, '/');

// =========================
// ROUTES
// =========================
$routes = [

    // dashboard
    'dashboard' => 'modules/dashboard/index.php',

    // keluarga
    'keluarga'        => 'modules/keluarga/index.php',
    'keluarga-create' => 'modules/keluarga/create.php',
    'keluarga-edit'   => 'modules/keluarga/edit.php',
    'keluarga-delete' => 'modules/keluarga/delete.php',
    'keluarga-view'   => 'modules/keluarga/view.php',

    // anak
    'anak'            => 'modules/anak/index.php',
    'anak-create'     => 'modules/anak/create.php',
    'anak-edit'       => 'modules/anak/edit.php',
    'anak-delete'     => 'modules/anak/delete.php',
    'anak-view'       => 'modules/anak/view.php',

    // kegiatan
    'kegiatan'        => 'modules/kegiatan/index.php',
    'kegiatan-create' => 'modules/kegiatan/create.php',
    'kegiatan-edit'   => 'modules/kegiatan/edit.php',
    'kegiatan-delete' => 'modules/kegiatan/delete.php',

    // kehadiran
    'kehadiran'        => 'modules/kehadiran/index.php',
    'kehadiran-create' => 'modules/kehadiran/create.php',
    'kehadiran-edit'   => 'modules/kehadiran/edit.php',
    'kehadiran-delete' => 'modules/kehadiran/delete.php',

    // pemeriksaan
    'pemeriksaan'        => 'modules/pemeriksaan/index.php',
    'pemeriksaan-create' => 'modules/pemeriksaan/create.php',
    'pemeriksaan-edit'   => 'modules/pemeriksaan/edit.php',
    'pemeriksaan-delete' => 'modules/pemeriksaan/delete.php',

    // imunisasi
    'imunisasi'        => 'modules/imunisasi/index.php',
    'imunisasi-create' => 'modules/imunisasi/create.php',
    'imunisasi-edit'   => 'modules/imunisasi/edit.php',
    'imunisasi-delete' => 'modules/imunisasi/delete.php',

    // users
    'users'         => 'modules/users/index.php',
    'users-create'  => 'modules/users/create.php',
    'users-edit'    => 'modules/users/edit.php',
    'users-delete'  => 'modules/users/delete.php',
];

// =========================
// ROLE ACCESS
// =========================
$role_access = [

    // ADMIN
    'admin' => array_keys($routes),

    // KADER
    'kader' => [
        'dashboard',

        'keluarga',
        'keluarga-create',
        'keluarga-edit',
        'keluarga-delete',
        'keluarga-view',

        'anak',
        'anak-create',
        'anak-edit',
        'anak-delete',
        'anak-view',

        'kegiatan',
        'kegiatan-create',
        'kegiatan-edit',
        'kegiatan-delete',

        'kehadiran',
        'kehadiran-create',
        'kehadiran-edit',
        'kehadiran-delete',

        'pemeriksaan',
        'pemeriksaan-create',
        'pemeriksaan-edit',
        'pemeriksaan-delete',

        'imunisasi',
        'imunisasi-create',
        'imunisasi-edit',
        'imunisasi-delete',
    ],

    // BIDAN
    'bidan' => [
        'dashboard',

        'keluarga',
        'keluarga-create',
        'keluarga-edit',
        'keluarga-delete',
        'keluarga-view',

        'anak',
        'anak-create',
        'anak-edit',
        'anak-delete',
        'anak-view',

        'kegiatan',
        'kegiatan-create',
        'kegiatan-edit',
        'kegiatan-delete',

        'kehadiran',
        'kehadiran-create',
        'kehadiran-edit',
        'kehadiran-delete',

        'pemeriksaan',
        'pemeriksaan-create',
        'pemeriksaan-edit',
        'pemeriksaan-delete',

        'imunisasi',
        'imunisasi-create',
        'imunisasi-edit',
        'imunisasi-delete',
    ],
];

// =========================
// VALIDASI ROUTE
// =========================
if (!array_key_exists($url, $routes)) {
    echo "404 - Halaman tidak ditemukan";
    exit;
}

// =========================
// VALIDASI ROLE
// =========================
$user_role = $_SESSION['user']['role'];

if (!in_array($url, $role_access[$user_role])) {
    echo "403 - Akses ditolak";
    exit;
}

// =========================
// LAYOUT
// =========================
include 'views/header.php';
include 'views/sidebar.php';
include 'views/topbar.php';

// =========================
// HALAMAN
// =========================
include $routes[$url];

// =========================
// FOOTER
// =========================
include 'views/footer.php';
?>