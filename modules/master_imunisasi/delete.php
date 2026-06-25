<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

// Cek apakah data ada
$stmt = $pdo->prepare("SELECT * FROM master_imunisasi WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "
    <script>
        alert('Data tidak ditemukan');
        window.location='index.php?url=master_imunisasi';
    </script>
    ";
    exit;
}

// Hapus data
$stmt = $pdo->prepare("DELETE FROM master_imunisasi WHERE id = ?");
$result = $stmt->execute([$id]);

if ($result) {
    echo "
    <script>
        alert('Data imunisasi berhasil dihapus');
        window.location='index.php?url=master_imunisasi';
    </script>
    ";
} else {
    echo "
    <script>
        alert('Gagal menghapus data. Silahkan coba lagi.');
        window.location='index.php?url=master_imunisasi';
    </script>
    ";
}
?>