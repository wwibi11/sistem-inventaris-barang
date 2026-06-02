<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE id=?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
  die("Data tidak ditemukan");
}

if (isset($_POST['update'])) {

  $stmt = $pdo->prepare("
    UPDATE kegiatan
    SET tanggal=?, lokasi=?, keterangan=?, pertemuan_ke=?, status=?
    WHERE id=?
  ");

  $stmt->execute([
    $_POST['tanggal'],
    $_POST['lokasi'],
    $_POST['keterangan'],
    $_POST['pertemuan_ke'],
    $_POST['status'],
    $id
  ]);

  header("Location: index.php?url=kegiatan");
  exit;
}
?>

<div class="container-fluid">
  <h4>Edit Kegiatan</h4>

  <form method="POST">

    <input type="date" name="tanggal"
           value="<?= $data['tanggal'] ?>"
           class="form-control mb-2">

    <input type="text" name="lokasi"
           value="<?= $data['lokasi'] ?>"
           class="form-control mb-2">

    <input type="number" name="pertemuan_ke"
           value="<?= $data['pertemuan_ke'] ?>"
           class="form-control mb-2">

    <textarea name="keterangan" class="form-control mb-2"><?= $data['keterangan'] ?></textarea>

    <select name="status" class="form-control mb-3">
      <option value="scheduled" <?= $data['status']=='scheduled'?'selected':'' ?>>Scheduled</option>
      <option value="selesai" <?= $data['status']=='selesai'?'selected':'' ?>>Selesai</option>
    </select>

    <button name="update" class="btn btn-primary">Update</button>

  </form>
</div>