<?php

require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: ../../index.php?url=keluarga');
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    DELETE FROM keluarga
    WHERE id = ?
");

$stmt->execute([$id]);

echo "
<script>
    alert('Data keluarga berhasil dihapus');
    window.location='../../index.php?url=keluarga';
</script>
";