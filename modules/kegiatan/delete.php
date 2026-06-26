<?php
// modules/kegiatan/delete.php
// PAKAI JAVASCRIPT UNTUK REDIRECT, SEPERTI DI EDIT.PHP

require_once __DIR__ . '/../../config/database.php';

$pdo = new PDO("mysql:host=localhost;dbname=posyandu_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    // Hapus data terkait (foreign key)
    $pdo->prepare("DELETE FROM kehadiran WHERE id_kegiatan = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM pemeriksaan WHERE id_kegiatan = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM imunisasi WHERE id_kegiatan = ?")->execute([$id]);
    
    // Hapus kegiatan
    $stmt = $pdo->prepare("DELETE FROM kegiatan WHERE id = ?");
    $stmt->execute([$id]);
}

// PAKAI JAVASCRIPT, BUKAN HEADER()
echo "
<script>
    alert('Kegiatan berhasil dihapus');
    window.location='index.php?url=kegiatan';
</script>
";
exit;
?>