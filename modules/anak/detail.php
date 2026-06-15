<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

/*
|--------------------------------------------------------------------------
| DATA ANAK
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
SELECT
    a.*,

    TIMESTAMPDIFF(
        YEAR,
        a.tanggal_lahir,
        CURDATE()
    ) AS umur_tahun,

    TIMESTAMPDIFF(
        MONTH,
        a.tanggal_lahir,
        CURDATE()
    ) % 12 AS umur_bulan

FROM anak a
WHERE a.id = ?
");

$stmt->execute([$id]);

$anak = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anak) {
    die("Data anak tidak ditemukan");
}

/*
|--------------------------------------------------------------------------
| TOTAL KEHADIRAN
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
SELECT COUNT(*) total
FROM kehadiran
WHERE id_anak=?
AND status_hadir='hadir'
");
$stmt->execute([$id]);
$totalHadir = $stmt->fetchColumn();

/*
|--------------------------------------------------------------------------
| TOTAL PEMERIKSAAN
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
SELECT COUNT(*) total
FROM pemeriksaan
WHERE id_anak=?
");
$stmt->execute([$id]);
$totalPeriksa = $stmt->fetchColumn();

/*
|--------------------------------------------------------------------------
| TOTAL IMUNISASI
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
SELECT COUNT(*) total
FROM imunisasi
WHERE id_anak=?
");
$stmt->execute([$id]);
$totalImunisasi = $stmt->fetchColumn();

/*
|--------------------------------------------------------------------------
| RIWAYAT KEHADIRAN
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
SELECT
    k.tanggal,
    k.pertemuan_ke,
    k.lokasi,
    h.status_hadir

FROM kehadiran h

JOIN kegiatan k
    ON k.id = h.id_kegiatan

WHERE h.id_anak=?

ORDER BY k.tanggal DESC
");

$stmt->execute([$id]);

$riwayatHadir = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RIWAYAT PEMERIKSAAN
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
SELECT
    p.*,
    k.tanggal

FROM pemeriksaan p

JOIN kegiatan k
    ON k.id = p.id_kegiatan

WHERE p.id_anak=?

ORDER BY k.tanggal DESC
");

$stmt->execute([$id]);

$riwayatPeriksa = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RIWAYAT IMUNISASI
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
SELECT
    i.*,
    k.tanggal

FROM imunisasi i

JOIN kegiatan k
    ON k.id = i.id_kegiatan

WHERE i.id_anak=?

ORDER BY k.tanggal DESC
");

$stmt->execute([$id]);

$riwayatImunisasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between mb-4">

        <div>

            <h3 class="mb-1">
                Detail Anak
            </h3>

            <small class="text-muted">
                Informasi dan riwayat kesehatan anak
            </small>

        </div>

        <a href="index.php?url=anak"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

    <!-- BIODATA -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-8">

                    <h4>
                        <?= htmlspecialchars($anak['nama']) ?>
                    </h4>

                    <table class="table table-sm">

                        <tr>
                            <th width="180">NIK</th>
                            <td><?= htmlspecialchars($anak['nik']) ?></td>
                        </tr>

                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>
                                <?= $anak['jenis_kelamin']=='L'
                                    ? 'Laki-laki'
                                    : 'Perempuan' ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Tanggal Lahir</th>
                            <td>
                                <?= date(
                                    'd M Y',
                                    strtotime($anak['tanggal_lahir'])
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Umur</th>
                            <td>
                                <?= $anak['umur_tahun'] ?> Tahun
                                <?= $anak['umur_bulan'] ?> Bulan
                            </td>
                        </tr>

                        <tr>
                            <th>Nama Ayah</th>
                            <td><?= htmlspecialchars($anak['nama_ayah']) ?></td>
                        </tr>

                        <tr>
                            <th>Nama Ibu</th>
                            <td><?= htmlspecialchars($anak['nama_ibu']) ?></td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <!-- RINGKASAN -->

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card border-left-primary shadow-sm">

                <div class="card-body">

                    <h3><?= $totalHadir ?></h3>

                    <small>Kehadiran</small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-left-success shadow-sm">

                <div class="card-body">

                    <h3><?= $totalPeriksa ?></h3>

                    <small>Pemeriksaan</small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-left-info shadow-sm">

                <div class="card-body">

                    <h3><?= $totalImunisasi ?></h3>

                    <small>Imunisasi</small>

                </div>

            </div>

        </div>

    </div>

    <!-- TAB -->

    <ul class="nav nav-tabs mb-3">

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

    <div class="tab-content">

        <!-- KEHADIRAN -->

        <div class="tab-pane fade show active"
             id="kehadiran">

            <table class="table table-bordered">

                <thead>

                <tr>
                    <th>Tanggal</th>
                    <th>Pertemuan</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>

                </thead>

                <tbody>

                <?php foreach($riwayatHadir as $r): ?>

                <tr>

                    <td>
                        <?= date('d M Y', strtotime($r['tanggal'])) ?>
                    </td>

                    <td>
                        <?= $r['pertemuan_ke'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['lokasi']) ?>
                    </td>

                    <td>
                        <?= ucfirst($r['status_hadir']) ?>
                    </td>

                </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <!-- PEMERIKSAAN -->

        <div class="tab-pane fade"
             id="pemeriksaan">

            <table class="table table-bordered">

                <thead>

                <tr>
                    <th>Tanggal</th>
                    <th>BB</th>
                    <th>TB</th>
                    <th>LK</th>
                    <th>Gizi</th>
                </tr>

                </thead>

                <tbody>

                <?php foreach($riwayatPeriksa as $r): ?>

                <tr>

                    <td>
                        <?= date('d M Y', strtotime($r['tanggal'])) ?>
                    </td>

                    <td><?= $r['berat_badan'] ?></td>
                    <td><?= $r['tinggi_badan'] ?></td>
                    <td><?= $r['lingkar_kepala'] ?></td>
                    <td><?= $r['status_gizi'] ?></td>

                </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <!-- IMUNISASI -->

        <div class="tab-pane fade"
             id="imunisasi">

            <table class="table table-bordered">

                <thead>

                <tr>
                    <th>Tanggal</th>
                    <th>Jenis Imunisasi</th>
                </tr>

                </thead>

                <tbody>

                <?php foreach($riwayatImunisasi as $r): ?>

                <tr>

                    <td>
                        <?= date('d M Y', strtotime($r['tanggal'])) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['jenis_imunisasi']) ?>
                    </td>

                </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>