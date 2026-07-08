<?php
// modules/borrowers/delete.php

ob_start();

require_once __DIR__ . '/../../config/functions.php';

// Redirect jika bukan admin
if (!isAdmin()) {
    $_SESSION['error'] = 'Akses ditolak! Anda tidak memiliki izin.';
    redirect('index.php?url=dashboard');
}

// Ambil ID dari GET
$id = $_GET['id'] ?? 0;

if ($id <= 0) {
    $_SESSION['error'] = 'ID peminjam tidak valid!';
    redirect('index.php?url=borrowers');
}

// Ambil data peminjam
$borrower = fetchOne("SELECT * FROM borrowers WHERE id = ?", [$id]);

if (!$borrower) {
    $_SESSION['error'] = 'Data peminjam tidak ditemukan!';
    redirect('index.php?url=borrowers');
}

// Cek apakah peminjam memiliki peminjaman aktif
$activeLoans = fetchColumn(
    "SELECT COUNT(*) FROM loans WHERE borrower_id = ? AND status IN ('dipinjam', 'terlambat')",
    [$id]
);

if ($activeLoans > 0) {
    $_SESSION['error'] = 'Peminjam "' . $borrower['name'] . '" memiliki ' . $activeLoans . ' peminjaman aktif. Tidak dapat dihapus!';
    redirect('index.php?url=borrowers');
}

try {
    $deleted = delete('borrowers', 'id = ?', [$id]);
    
    if ($deleted) {
        $_SESSION['success'] = 'Peminjam "' . $borrower['name'] . '" berhasil dihapus!';
    } else {
        $_SESSION['error'] = 'Gagal menghapus data. Silakan coba lagi.';
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

redirect('index.php?url=borrowers');

ob_end_flush();
?>