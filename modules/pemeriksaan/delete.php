<?php
// modules/pemeriksaan/delete.php

require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;
$id_kegiatan = $_GET['id_kegiatan'] ?? 0;

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM pemeriksaan WHERE id = ?");
    $stmt->execute([$id]);
}

echo "<script>
    alert('Data pemeriksaan berhasil dihapus');
    window.location='index.php?url=pemeriksaan&id_kegiatan=" . $id_kegiatan . "';
</script>";
exit;
?>