<?php
require_once __DIR__ . '/../../config/database.php';

$data = $pdo->query("SELECT * FROM kegiatan ORDER BY tanggal DESC")->fetchAll();

if (isset($_POST['simpan'])) {
  $stmt = $pdo->prepare("
    INSERT INTO kegiatan (tanggal, lokasi, keterangan, pertemuan_ke, created_by)
    VALUES (?, ?, ?, ?, ?)
  ");
  $stmt->execute([
    $_POST['tanggal'],
    $_POST['lokasi'],
    $_POST['ket'],
    $_POST['pertemuan'],
    $_SESSION['user']['id']
  ]);

  header("Location: index.php?url=kegiatan");
}
?>

<div class="container-fluid">
  <h3>Kegiatan</h3>

  <form method="POST" class="mb-4">
    <input type="date" name="tanggal" class="form-control mb-2" required>
    <input type="text" name="lokasi" class="form-control mb-2" placeholder="Lokasi">
    <input type="number" name="pertemuan" class="form-control mb-2" placeholder="Pertemuan ke">
    <textarea name="ket" class="form-control mb-2" placeholder="Keterangan"></textarea>
    <button name="simpan" class="btn btn-primary">Simpan</button>
  </form>

  <table class="table table-bordered">
    <tr><th>Tanggal</th><th>Lokasi</th><th>Pertemuan</th></tr>
    <?php foreach ($data as $d): ?>
    <tr>
      <td><?= $d['tanggal'] ?></td>
      <td><?= $d['lokasi'] ?></td>
      <td><?= $d['pertemuan_ke'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>