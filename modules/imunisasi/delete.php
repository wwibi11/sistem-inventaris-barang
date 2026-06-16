<?php

require_once __DIR__.'/../../config/database.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
DELETE FROM imunisasi
WHERE id = ?
");

$stmt->execute([$id]);

header("Location: index.php?url=imunisasi");
exit;