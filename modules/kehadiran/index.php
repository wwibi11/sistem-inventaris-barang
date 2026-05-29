<?php
require_once __DIR__ . '/../../config/database.php';

$anak = $pdo->query("SELECT * FROM anak")->fetchAll();
$kegiatan = $pdo->query("SELECT * FROM kegiatan")->fetchAll();

if (isset($_POST['simpan'])) {
  foreach ($_POST['hadir'] as $id_anak => $status) {
    $stmt = $pdo->prepare("
      INSERT INTO kehadiran (id_anak, id_kegiatan, status_hadir, dicatat_oleh)
      VALUES (?, ?, ?, ?)
      ON DUPLICATE KEY UPDATE status_hadir=VALUES(status_hadir)
    ");

    $stmt->execute([
      $id_anak,
      $_POST['kegiatan'],
      $status,
      $_SESSION['user']['id']
    ]);
  }

  header("Location: index.php?url=kehadiran");
}
?>

<div class="container-fluid">
  <h3>Kehadiran</h3>

  <form method="POST">
    <select name="kegiatan" class="form-control mb-3">
      <?php foreach ($kegiatan as $k): ?>
      <option value="<?= $k['id'] ?>">
        <?= $k['tanggal'] ?> - <?= $k['lokasi'] ?>
      </option>
      <?php endforeach; ?>
    </select>

    <table class="table">
      <?php foreach ($anak as $a): ?>
      <tr>
        <td><?= $a['nama'] ?></td>
        <td>
          <select name="hadir[<?= $a['id'] ?>]" class="form-control">
            <option value="hadir">Hadir</option>
            <option value="tidak">Tidak</option>
          </select>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>

    <button name="simpan" class="btn btn-primary">Simpan</button>
  </form>
</div>