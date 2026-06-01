<?php

require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    header("Location: index.php?url=anak");
    exit;
}

$stmt = $pdo->prepare("
    DELETE FROM anak
    WHERE id = ?
");

$stmt->execute([$id]);

echo "
<script>
    alert('Data anak berhasil dihapus');
    window.location='index.php?url=anak';
</script>
";