<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'];

// kegiatan
$stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE id=?");
$stmt->execute([$id]);
$kegiatan = $stmt->fetch();

// kehadiran
$hadir = $pdo->prepare("
  SELECT k.*, a.nama
  FROM kehadiran k
  JOIN anak a ON a.id = k.id_anak
  WHERE k.id_kegiatan=?
");
$hadir->execute([$id]);
$kehadiran = $hadir->fetchAll();

// pemeriksaan
$periksa = $pdo->prepare("
  SELECT p.*, a.nama
  FROM pemeriksaan p
  JOIN anak a ON a.id = p.id_anak
  WHERE p.id_kegiatan=?
");
$periksa->execute([$id]);
$pemeriksaan = $periksa->fetchAll();

// imunisasi
$imun = $pdo->prepare("
  SELECT i.*, a.nama
  FROM imunisasi i
  JOIN anak a ON a.id = i.id_anak
  WHERE i.id_kegiatan=?
");
$imun->execute([$id]);
$imunisasi = $imun->fetchAll();
?>

<div class="container-fluid">

  <h4>Detail Kegiatan</h4>

  <p>
    <b><?= $kegiatan['tanggal'] ?></b> |
    <?= $kegiatan['lokasi'] ?> |
    <?= $kegiatan['status'] ?>
  </p>

  <hr>

  <h5>Kehadiran</h5>
  <ul>
    <?php foreach ($kehadiran as $k): ?>
      <li><?= $k['nama'] ?> - <?= $k['status_hadir'] ?></li>
    <?php endforeach; ?>
  </ul>

  <h5>Pemeriksaan</h5>
  <ul>
    <?php foreach ($pemeriksaan as $p): ?>
      <li><?= $p['nama'] ?> - BB: <?= $p['berat_badan'] ?></li>
    <?php endforeach; ?>
  </ul>

  <h5>Imunisasi</h5>
  <ul>
    <?php foreach ($imunisasi as $i): ?>
      <li><?= $i['nama'] ?> - <?= $i['jenis_imunisasi'] ?></li>
    <?php endforeach; ?>
  </ul>

</div>