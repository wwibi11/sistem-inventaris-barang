<?php
require_once __DIR__ . '/../../config/database.php';

// =======================
// DATA UTAMA
// =======================
$total_anak       = $pdo->query("SELECT COUNT(*) FROM anak")->fetchColumn();
$total_keluarga   = $pdo->query("SELECT COUNT(*) FROM keluarga")->fetchColumn();
$total_kegiatan   = $pdo->query("SELECT COUNT(*) FROM kegiatan")->fetchColumn();

$today = date('Y-m-d');

// =======================
// KEHADIRAN HARI INI
// =======================
$stmt = $pdo->prepare("
  SELECT COUNT(*) 
  FROM kehadiran k
  JOIN kegiatan g ON k.id_kegiatan = g.id
  WHERE g.tanggal = ? AND k.status_hadir = 'hadir'
");
$stmt->execute([$today]);
$hadir_hari_ini = $stmt->fetchColumn();

// =======================
// PEMERIKSAAN HARI INI
// =======================
$stmt = $pdo->prepare("
  SELECT COUNT(*) 
  FROM pemeriksaan p
  JOIN kegiatan g ON p.id_kegiatan = g.id
  WHERE g.tanggal = ?
");
$stmt->execute([$today]);
$pemeriksaan_hari_ini = $stmt->fetchColumn();

// =======================
// BELUM VALIDASI
// =======================
$pending = $pdo->query("
  SELECT COUNT(*) FROM pemeriksaan WHERE status_validasi = 'pending'
")->fetchColumn();

// =======================
// KEGIATAN TERBARU
// =======================
$kegiatan = $pdo->query("
  SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// =======================
// ANAK TERBARU
// =======================
$anak = $pdo->query("
  SELECT a.*, k.nama_kepala_keluarga 
  FROM anak a
  JOIN keluarga k ON a.id_keluarga = k.id
  ORDER BY a.created_at DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">

  <h1 class="h3 mb-4 text-gray-800">
    Dashboard Posyandu
    <small class="text-muted">| <?= date('d M Y') ?></small>
  </h1>

  <!-- =======================
       STAT CARD
  ======================== -->
  <div class="row">

    <?php
    function card($title, $value, $icon, $color) {
      return "
      <div class='col-md-3 mb-4'>
        <div class='card border-left-$color shadow h-100 py-2'>
          <div class='card-body'>
            <div class='row no-gutters align-items-center'>
              <div class='col mr-2'>
                <div class='text-xs font-weight-bold text-$color text-uppercase mb-1'>
                  $title
                </div>
                <div class='h5 mb-0 font-weight-bold text-gray-800'>$value</div>
              </div>
              <div class='col-auto'>
                <i class='fas $icon fa-2x text-gray-300'></i>
              </div>
            </div>
          </div>
        </div>
      </div>";
    }

    echo card("Total Anak", $total_anak, "fa-child", "primary");
    echo card("Total Keluarga", $total_keluarga, "fa-home", "success");
    echo card("Total Kegiatan", $total_kegiatan, "fa-calendar", "info");
    echo card("Hadir Hari Ini", $hadir_hari_ini, "fa-check", "warning");
    ?>

  </div>

  <!-- =======================
       INFORMASI CEPAT
  ======================== -->
  <div class="row">

    <div class="col-md-6 mb-4">
      <div class="card shadow">
        <div class="card-body">
          <h6 class="font-weight-bold text-primary mb-3">
            Pemeriksaan Hari Ini
          </h6>
          <h2><?= $pemeriksaan_hari_ini ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-6 mb-4">
      <div class="card shadow">
        <div class="card-body">
          <h6 class="font-weight-bold text-danger mb-3">
            Belum Validasi
          </h6>
          <h2><?= $pending ?></h2>
        </div>
      </div>
    </div>

  </div>

  <!-- =======================
       TABEL DATA
  ======================== -->
  <div class="row">

    <!-- KEGIATAN -->
    <div class="col-md-6 mb-4">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          Kegiatan Terbaru
        </div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead class="thead-light">
              <tr>
                <th>Tanggal</th>
                <th>Lokasi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kegiatan as $k): ?>
              <tr>
                <td><?= date('d M Y', strtotime($k['tanggal'])) ?></td>
                <td><?= $k['lokasi'] ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$kegiatan): ?>
              <tr><td colspan="2" class="text-center">Tidak ada data</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ANAK -->
    <div class="col-md-6 mb-4">
      <div class="card shadow">
        <div class="card-header bg-success text-white">
          Anak Terdaftar Terbaru
        </div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead class="thead-light">
              <tr>
                <th>Nama</th>
                <th>Keluarga</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($anak as $a): ?>
              <tr>
                <td><?= $a['nama'] ?></td>
                <td><?= $a['nama_kepala_keluarga'] ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$anak): ?>
              <tr><td colspan="2" class="text-center">Tidak ada data</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

</div>