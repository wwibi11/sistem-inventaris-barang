<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'];

// kegiatan
$kegiatan = $pdo->prepare("SELECT * FROM kegiatan WHERE id=?");
$kegiatan->execute([$id]);
$kegiatan = $kegiatan->fetch();

// anak
$anak = $pdo->query("SELECT * FROM anak")->fetchAll();

// kehadiran
$kehadiran = $pdo->prepare("
  SELECT * FROM kehadiran
  WHERE id_kegiatan=?
");
$kehadiran->execute([$id]);
$hadir = $kehadiran->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
?>