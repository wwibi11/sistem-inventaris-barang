<?php
require_once __DIR__ . '/../../config/database.php';

$anak = $pdo->query("SELECT * FROM anak")->fetchAll();
$kegiatan = $pdo->query("SELECT * FROM kegiatan")->fetchAll();

if (isset($_POST['simpan'])) {
  $stmt = $pdo->prepare("
    INSERT INTO imunisasi 
    (id_anak, id_kegiatan, jenis_imunisasi, tanggal, diberikan_oleh)
    VALUES (?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $_POST['anak'],
    $_POST['kegiatan'],
    $_POST['jenis'],
    $_POST['tanggal'],
    $_SESSION['user']['id']
  ]);

  header("Location: index.php?url=imunisasi");
}
?>

<div class="container-fluid">
  <h3>Imunisasi</h3>

  <form method="POST">
    <select name="anak" class="form-control mb-2">
      <?php foreach ($anak as $a): ?>
      <option value="<?= $a['id'] ?>"><?= $a['nama'] ?></option>
      <?php endforeach; ?>
    </select>

    <input type="text" name="jenis" placeholder="Jenis Imunisasi" class="form-control mb-2">
    <input type="date" name="tanggal" class="form-control mb-2">

    <select name="kegiatan" class="form-control mb-2">
      <?php foreach ($kegiatan as $k): ?>
      <option value="<?= $k['id'] ?>"><?= $k['tanggal'] ?></option>
      <?php endforeach; ?>
    </select>

    <button name="simpan" class="btn btn-primary">Simpan</button>
  </form>
</div>