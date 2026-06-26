<?php
// modules/imunisasi_ibu_hamil/delete.php
// FILE INI HANYA UNTUK PROSES HAPUS

require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM imunisasi_ibu_hamil WHERE id = ?");
    $stmt->execute([$id]);
}

echo "<script>
    alert('Data imunisasi berhasil dihapus');
    window.location='index.php?url=imunisasi_ibu';
</script>";
exit;
?>