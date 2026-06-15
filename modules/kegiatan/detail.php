<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = $_GET['id'] ?? 0;

/* =========================
   DATA KEGIATAN
========================= */
$stmt = $pdo->prepare("
SELECT
    k.*,
    u.nama AS pembuat
FROM kegiatan k
LEFT JOIN users u
    ON u.id = k.created_by
WHERE k.id=?
");

$stmt->execute([$id_kegiatan]);
$kegiatan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kegiatan) {
    header("Location:index.php?url=kegiatan");
    exit;
}

/* =========================
   SIMPAN KEHADIRAN
========================= */
if (isset($_POST['simpan_kehadiran'])) {

    foreach ($_POST['hadir'] as $id_anak => $status) {

        $stmt = $pdo->prepare("
        INSERT INTO kehadiran
        (
            id_anak,
            id_kegiatan,
            status_hadir,
            dicatat_oleh
        )
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        status_hadir = VALUES(status_hadir)
        ");

        $stmt->execute([
            $id_anak,
            $id_kegiatan,
            $status,
            $_SESSION['user']['id']
        ]);
    }

    header("Location:index.php?url=kegiatan-detail&id=".$id_kegiatan);
    exit;
}

/* =========================
   DATA ANAK
========================= */
$anak = $pdo->query("
SELECT *
FROM anak
WHERE status='aktif'
ORDER BY nama
")->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   KEHADIRAN
========================= */
$q = $pdo->prepare("
SELECT *
FROM kehadiran
WHERE id_kegiatan=?
");

$q->execute([$id_kegiatan]);

$kehadiranData = [];

foreach($q->fetchAll(PDO::FETCH_ASSOC) as $row){
    $kehadiranData[$row['id_anak']] = $row;
}

/* =========================
   ANAK HADIR
========================= */
$q = $pdo->prepare("
SELECT
    a.*
FROM kehadiran h
JOIN anak a
    ON a.id = h.id_anak
WHERE h.id_kegiatan=?
AND h.status_hadir='hadir'
ORDER BY a.nama
");

$q->execute([$id_kegiatan]);
$anakHadir = $q->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   STATISTIK
========================= */

$totalAnak = count($anak);

$totalHadir = $pdo->prepare("
SELECT COUNT(*)
FROM kehadiran
WHERE id_kegiatan=?
AND status_hadir='hadir'
");

$totalHadir->execute([$id_kegiatan]);
$totalHadir = $totalHadir->fetchColumn();

$totalPemeriksaan = $pdo->prepare("
SELECT COUNT(*)
FROM pemeriksaan
WHERE id_kegiatan=?
");

$totalPemeriksaan->execute([$id_kegiatan]);
$totalPemeriksaan = $totalPemeriksaan->fetchColumn();

$totalImunisasi = $pdo->prepare("
SELECT COUNT(*)
FROM imunisasi
WHERE id_kegiatan=?
");

$totalImunisasi->execute([$id_kegiatan]);
$totalImunisasi = $totalImunisasi->fetchColumn();


$progress = 0;

if($totalHadir > 0){

    $progress =
    (
        ($totalPemeriksaan + $totalImunisasi)
        /
        ($totalHadir * 2)
    ) * 100;

    $progress = min(100, round($progress));
}
?>

<div class="container-fluid">

    <!-- HEADER -->

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between">

                <div>

                    <h3 class="mb-1">
                        <?= htmlspecialchars($kegiatan['lokasi']) ?>
                    </h3>

                    <div class="text-muted">
                        <?= date('d-m-Y', strtotime($kegiatan['tanggal'])) ?>
                    </div>

                    <div class="mt-2">

                        <span class="badge badge-info">
                            Pertemuan Ke <?= $kegiatan['pertemuan_ke'] ?>
                        </span>

                        <?php if($kegiatan['status']=='selesai'): ?>

                            <span class="badge badge-success">
                                Selesai
                            </span>

                        <?php else: ?>

                            <span class="badge badge-warning">
                                Scheduled
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="text-right">

                    <small class="text-muted">
                        Dibuat oleh
                    </small>

                    <div>
                        <?= htmlspecialchars($kegiatan['pembuat']) ?>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- STATISTIK -->

    <div class="row mb-4">

    <div class="col-md-4">
        <div class="card border-left-primary shadow-sm">
            <div class="card-body">
                <h4><?= $totalHadir ?></h4>
                <small>Anak Hadir</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-left-success shadow-sm">
            <div class="card-body">
                <h4><?= $totalPemeriksaan ?></h4>
                <small>Pemeriksaan</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-left-info shadow-sm">
            <div class="card-body">
                <h4><?= $totalImunisasi ?></h4>
                <small>Imunisasi</small>
            </div>
        </div>
    </div>
    </div>

    <!-- PROGRESS -->

    <div class="card mb-4 shadow-sm">

        <div class="card-body">

            <div class="d-flex justify-content-between mb-2">
                <strong>Progress Kegiatan</strong>
                <strong><?= $progress ?>%</strong>
            </div>

            <div class="progress">
                <div
                    class="progress-bar bg-success"
                    style="width:<?= $progress ?>%">
                </div>
            </div>

        </div>

    </div>

    <!-- TAB -->

    <ul class="nav nav-tabs">

        <li class="nav-item">
            <a class="nav-link active"
               data-toggle="tab"
               href="#kehadiran">
               Kehadiran
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"
               data-toggle="tab"
               href="#pemeriksaan">
               Pemeriksaan
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"
               data-toggle="tab"
               href="#imunisasi">
               Imunisasi
            </a>
        </li>

    </ul>

    <div class="tab-content border p-3 bg-white">

        <!-- KEHADIRAN -->

        <div class="tab-pane fade show active" id="kehadiran">

            <form method="POST">

                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>Nama Anak</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach($anak as $a): ?>

                        <?php
                        $status =
                        $kehadiranData[$a['id']]['status_hadir']
                        ?? 'hadir';
                        ?>

                        <tr>

                            <td><?= htmlspecialchars($a['nama']) ?></td>

                            <td>

                                <select
                                    name="hadir[<?= $a['id'] ?>]"
                                    class="form-control form-control-sm">

                                    <option value="hadir"
                                        <?= $status=='hadir'?'selected':'' ?>>
                                        Hadir
                                    </option>

                                    <option value="tidak"
                                        <?= $status=='tidak'?'selected':'' ?>>
                                        Tidak Hadir
                                    </option>

                                </select>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

                <button
                    class="btn btn-primary"
                    name="simpan_kehadiran">

                    Simpan Kehadiran

                </button>

            </form>

        </div>

        <!-- PEMERIKSAAN -->

        <div class="tab-pane fade" id="pemeriksaan">

    <div class="alert alert-info">
        Hanya anak yang hadir yang dapat diperiksa.
    </div>

    <div class="mb-3 text-right">

        <a href="index.php?url=pemeriksaan-input&id_kegiatan=<?= $id_kegiatan ?>"
           class="btn btn-success">

            Input Pemeriksaan

        </a>

    </div>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>Nama Anak</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach($anakHadir as $a): ?>

            <tr>
                <td><?= htmlspecialchars($a['nama']) ?></td>
            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

        <!-- IMUNISASI -->

        <div class="tab-pane fade" id="imunisasi">

    <div class="alert alert-info">
        Hanya anak yang hadir yang dapat diimunisasi.
    </div>

    <div class="mb-3 text-right">

        <a href="index.php?url=imunisasi-input&id_kegiatan=<?= $id_kegiatan ?>"
           class="btn btn-primary">

            Input Imunisasi

        </a>

    </div>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>Nama Anak</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach($anakHadir as $a): ?>

            <tr>
                <td><?= htmlspecialchars($a['nama']) ?></td>
            </tr>

        <?php endforeach; ?>
        </tbody>

    </table>

</div>

    </div>

</div>