<?php
// modules/pemeriksaan_ibu/delete.php
// FILE INI HANYA UNTUK PROSES HAPUS

require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

// Ambil id_kegiatan untuk redirect
$id_kegiatan = 0;
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT id_kegiatan FROM pemeriksaan_ibu_hamil WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_kegiatan = $data['id_kegiatan'] ?? 0;
    
    $stmt = $pdo->prepare("DELETE FROM pemeriksaan_ibu_hamil WHERE id = ?");
    $stmt->execute([$id]);
}

echo "<script>
    alert('Data pemeriksaan berhasil dihapus');
    window.location='index.php?url=pemeriksaan_ibu&id_kegiatan=" . $id_kegiatan . "';
</script>";
exit;
?>