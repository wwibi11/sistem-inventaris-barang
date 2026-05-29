<?php
require_once __DIR__ . '/../../config/database.php';

$anak = $pdo->query("SELECT * FROM anak")->fetchAll();
$kegiatan = $pdo->query("SELECT * FROM kegiatan")->fetchAll();

if (isset($_POST['simpan'])) {
  $stmt = $pdo->prepare("
    INSERT INTO pemeriksaan 
    (id_anak, id_kegiatan, berat_badan, tinggi_badan, lingkar_kepala, diukur_oleh)
    VALUES (?, ?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $_POST['anak'],
    $_POST['kegiatan'],
    $_POST['bb'],
    $_POST['tb'],
    $_POST['lk'],
    $_SESSION['user']['id']
  ]);

  header("Location: index.php?url=pemeriksaan");
}
?>

<div class="container-fluid">
  <h3>Pemeriksaan</h3>

  <form method="POST">
    <select name="anak" class="form-control mb-2">
      <?php foreach ($anak as $a): ?>
      <option value="<?= $a['id'] ?>"><?= $a['nama'] ?></option>
      <?php endforeach; ?>
    </select>

    <select name="kegiatan" class="form-control mb-2">
      <?php foreach ($kegiatan as $k): ?>
      <option value="<?= $k['id'] ?>"><?= $k['tanggal'] ?></option>
      <?php endforeach; ?>
    </select>

    <input type="number" step="0.01" name="bb" placeholder="Berat" class="form-control mb-2">
    <input type="number" step="0.01" name="tb" placeholder="Tinggi" class="form-control mb-2">
    <input type="number" step="0.01" name="lk" placeholder="Lingkar Kepala" class="form-control mb-2">

    <button name="simpan" class="btn btn-success">Simpan</button>
  </form>
</div>