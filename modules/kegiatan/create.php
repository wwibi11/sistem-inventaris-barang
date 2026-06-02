<?php
require_once __DIR__ . '/../../config/database.php';

if (isset($_POST['simpan'])) {

  $stmt = $pdo->prepare("
    INSERT INTO kegiatan (tanggal, lokasi, keterangan, pertemuan_ke, status, created_by)
    VALUES (?, ?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $_POST['tanggal'],
    $_POST['lokasi'],
    $_POST['keterangan'],
    $_POST['pertemuan_ke'],
    $_POST['status'],
    $_SESSION['user']['id']
  ]);

  header("Location: index.php?url=kegiatan");
  exit;
}
?>

<div class="container-fluid">
  <h4>Tambah Kegiatan</h4>

  <form method="POST">

    <input type="date" name="tanggal" class="form-control mb-2" required>

    <input type="text" name="lokasi" class="form-control mb-2" placeholder="Lokasi">

    <input type="number" name="pertemuan_ke" class="form-control mb-2" placeholder="Pertemuan ke">

    <textarea name="keterangan" class="form-control mb-2" placeholder="Keterangan"></textarea>

    <select name="status" class="form-control mb-3">
      <option value="scheduled">Scheduled</option>
      <option value="selesai">Selesai</option>
    </select>

    <button class="btn btn-primary" name="simpan">Simpan</button>

  </form>
</div>