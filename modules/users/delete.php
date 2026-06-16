<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    DELETE FROM users
    WHERE id = ?
");

$stmt->execute([$id]);

echo "
<script>
    alert('User berhasil dihapus');
    location='index.php?url=users';
</script>
";