<?php
require_once __DIR__ . '/../../config/database.php';

$totalAnak = $pdo->query("
SELECT COUNT(*) FROM anak
WHERE status='aktif'
")->fetchColumn();

$totalKegiatan = $pdo->query("
SELECT COUNT(*) FROM kegiatan
")->fetchColumn();

$totalSelesai = $pdo->query("
SELECT COUNT(*) FROM kegiatan
WHERE status='selesai'
")->fetchColumn();

$totalScheduled = $pdo->query("
SELECT COUNT(*) FROM kegiatan
WHERE status='scheduled'
")->fetchColumn();

$data = $pdo->query("
SELECT
    k.*,
    u.nama AS pembuat,

    COUNT(DISTINCT CASE
        WHEN h.status_hadir='hadir'
        THEN h.id_anak
    END) AS total_hadir,

    COUNT(DISTINCT CASE
        WHEN h.status_hadir='tidak'
        THEN h.id_anak
    END) AS total_tidak_hadir,

    COUNT(DISTINCT p.id_anak) AS total_pemeriksaan,

    COUNT(DISTINCT i.id_anak) AS total_imunisasi

FROM kegiatan k

LEFT JOIN users u
    ON u.id = k.created_by

LEFT JOIN kehadiran h
    ON h.id_kegiatan = k.id

LEFT JOIN pemeriksaan p
    ON p.id_kegiatan = k.id

LEFT JOIN imunisasi i
    ON i.id_kegiatan = k.id

GROUP BY k.id

ORDER BY k.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>

.stat-card{
    border:none;
    border-radius:18px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

.stat-number{
    font-size:28px;
    font-weight:700;
}

.kegiatan-card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
    transition:.2s;
}

.kegiatan-card:hover{
    transform:translateY(-2px);
}

.progress{
    height:10px;
    border-radius:20px;
}

</style>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">
            Jadwal Posyandu
        </h3>

        <small class="text-muted">
            Monitoring seluruh kegiatan posyandu
        </small>

    </div>

    <a href="index.php?url=kegiatan-create"
       class="btn btn-primary">

        + Tambah Kegiatan

    </a>

</div>

  <!-- RINGKASAN -->
<div class="row mb-4">

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <small class="text-muted">Total Anak</small>
                <div class="stat-number text-primary">
                    <?= $totalAnak ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <small class="text-muted">Total Kegiatan</small>
                <div class="stat-number text-success">
                    <?= $totalKegiatan ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <small class="text-muted">Selesai</small>
                <div class="stat-number text-info">
                    <?= $totalSelesai ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <small class="text-muted">Scheduled</small>
                <div class="stat-number text-warning">
                    <?= $totalScheduled ?>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">

<?php foreach($data as $d): ?>

<?php

$progress = 0;

if($totalAnak > 0){
    $progress =
    round(
        ($d['total_hadir'] / $totalAnak) * 100
    );
}

?>

<div class="col-md-6 mb-4">

    <div class="card kegiatan-card">

        <div class="card-body">

            <div class="d-flex justify-content-between">

                <div>

                    <h5 class="mb-1">
                        Pertemuan Ke <?= $d['pertemuan_ke'] ?>
                    </h5>

                    <small class="text-muted">
                        <?= date('d M Y', strtotime($d['tanggal'])) ?>
                    </small>

                </div>

                <div>

                    <?php if($d['status']=='selesai'): ?>

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

            <hr>

            <p class="mb-2">

                <i class="fas fa-map-marker-alt text-danger"></i>

                <?= $d['lokasi'] ?>

            </p>

            <div class="row text-center">

                <div class="col-4">
                    <h5><?= $d['total_hadir'] ?></h5>
                    <small>Hadir</small>
                </div>

                <div class="col-4">
                    <h5><?= $d['total_pemeriksaan'] ?></h5>
                    <small>Periksa</small>
                </div>

                <div class="col-4">
                    <h5><?= $d['total_imunisasi'] ?></h5>
                    <small>Imunisasi</small>
                </div>

            </div>

            <div class="mt-3">

                <small>
                    Kehadiran
                    <?= $progress ?>%
                </small>

                <div class="progress">

                    <div
                        class="progress-bar bg-success"
                        style="width:<?= $progress ?>%">

                    </div>

                </div>

            </div>

            <div class="mt-3">

                <a
                    href="index.php?url=kegiatan-detail&id=<?= $d['id'] ?>"
                    class="btn btn-primary btn-block">

                    Kelola Kegiatan

                </a>

            </div>

            <div class="mt-2 text-right">

                <a
                    href="index.php?url=kegiatan-edit&id=<?= $d['id'] ?>"
                    class="btn btn-warning btn-sm">

                    Edit

                </a>

                <a
                    href="index.php?url=kegiatan-delete&id=<?= $d['id'] ?>"
                    onclick="return confirm('Hapus?')"
                    class="btn btn-danger btn-sm">

                    Hapus

                </a>

            </div>

        </div>

    </div>

</div>

<?php endforeach; ?>

</div>

</div>