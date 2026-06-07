<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = $_GET['id'] ?? 0;

/* =========================
   DATA KEGIATAN
========================= */
$stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE id = ?");
$stmt->execute([$id_kegiatan]);
$kegiatan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kegiatan) {
  echo "<script>alert('Kegiatan tidak ditemukan'); window.location='index.php?url=kegiatan';</script>";
  exit;
}

/* =========================
   DATA ANAK
========================= */
$anak = $pdo->query("SELECT * FROM anak ORDER BY nama ASC")->fetchAll();

/* =========================
   KEHADIRAN EXISTING
========================= */
$kehadiran = $pdo->prepare("SELECT * FROM kehadiran WHERE id_kegiatan = ?");
$kehadiran->execute([$id_kegiatan]);
$kehadiranData = $kehadiran->fetchAll(PDO::FETCH_KEY_PAIR|PDO::FETCH_GROUP);

/* =========================
   SIMPAN KEHADIRAN
========================= */
if (isset($_POST['simpan_kehadiran'])) {

  foreach ($_POST['hadir'] as $id_anak => $status) {

    $stmt = $pdo->prepare("
      INSERT INTO kehadiran (id_anak, id_kegiatan, status_hadir, dicatat_oleh)
      VALUES (?, ?, ?, ?)
      ON DUPLICATE KEY UPDATE status_hadir = VALUES(status_hadir)
    ");

    $stmt->execute([
      $id_anak,
      $id_kegiatan,
      $status,
      $_SESSION['user']['id']
    ]);
  }

  header("Location: kegiatan-detail.php?id=$id_kegiatan");
  exit;
}
?>

<div class="container-fluid">

  <h4 class="mb-3">
    Kegiatan: <?= htmlspecialchars($kegiatan['lokasi']) ?> (<?= $kegiatan['tanggal'] ?>)
  </h4>

  <!-- NAV TAB -->
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#kehadiran">Kehadiran</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#pemeriksaan">Pemeriksaan</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#imunisasi">Imunisasi</a>
    </li>
  </ul>

  <div class="tab-content">

    <!-- =========================
         KEHADIRAN
    ========================== -->
    <div class="tab-pane fade show active" id="kehadiran">

      <form method="POST">

        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>Nama Anak</th>
              <th>Status</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($anak as $a): ?>
            <tr>
              <td><?= $a['nama'] ?></td>
              <td>
                <select name="hadir[<?= $a['id'] ?>]" class="form-control form-control-sm">
                  <option value="hadir">Hadir</option>
                  <option value="tidak">Tidak</option>
                </select>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <button name="simpan_kehadiran" class="btn btn-primary btn-sm">
          Simpan Kehadiran
        </button>

      </form>

    </div>

    <!-- =========================
         PEMERIKSAAN
    ========================== -->
    <div class="tab-pane fade" id="pemeriksaan">

      <p class="text-muted">
        Input pemeriksaan hanya untuk anak yang hadir (logika nanti kita kunci di backend).
      </p>

      <table class="table table-sm table-bordered">
        <thead>
          <tr>
            <th>Anak</th>
            <th>BB</th>
            <th>TB</th>
            <th>LK</th>
            <th>Status Gizi</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($anak as $a): ?>
          <tr>
            <td><?= $a['nama'] ?></td>
            <td><input class="form-control form-control-sm"></td>
            <td><input class="form-control form-control-sm"></td>
            <td><input class="form-control form-control-sm"></td>
            <td><input class="form-control form-control-sm"></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    </div>

    <!-- =========================
         IMUNISASI
    ========================== -->
    <div class="tab-pane fade" id="imunisasi">

      <table class="table table-sm table-bordered">
        <thead>
          <tr>
            <th>Anak</th>
            <th>Jenis Imunisasi</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($anak as $a): ?>
          <tr>
            <td><?= $a['nama'] ?></td>
            <td>
              <input class="form-control form-control-sm" placeholder="Contoh: BCG / Polio">
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    </div>

  </div>

</div>