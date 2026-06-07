<?php
require_once __DIR__ . '/../../config/database.php';

$data = $pdo->query("
SELECT
    k.*,

    COUNT(DISTINCT a.id) AS total_anak,

    COUNT(DISTINCT CASE
        WHEN h.status_hadir='hadir'
        THEN h.id_anak
    END) AS total_hadir,

    COUNT(DISTINCT CASE
        WHEN h.status_hadir='tidak'
        THEN h.id_anak
    END) AS total_tidak

FROM kegiatan k

LEFT JOIN anak a
    ON a.status='aktif'

LEFT JOIN kehadiran h
    ON h.id_kegiatan = k.id

GROUP BY k.id

ORDER BY k.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">

    <div class="mb-4">
        <h3>Kehadiran Posyandu</h3>
        <small class="text-muted">
            Monitoring dan pencatatan kehadiran anak pada setiap kegiatan.
        </small>
    </div>

    <div class="row">

        <?php foreach($data as $d): ?>

        <?php
        $persen = 0;

        if ($d['total_anak'] > 0) {
            $persen = round(
                ($d['total_hadir'] / $d['total_anak']) * 100
            );
        }
        ?>

        <div class="col-md-6 col-lg-4 mb-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <h5 class="font-weight-bold">
                        Pertemuan <?= $d['pertemuan_ke'] ?>
                    </h5>

                    <div class="text-muted mb-2">
                        <?= date('d M Y', strtotime($d['tanggal'])) ?>
                    </div>

                    <div class="mb-2">
                        📍 <?= htmlspecialchars($d['lokasi']) ?>
                    </div>

                    <div class="mb-2">

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

                    <hr>

                    <div>
                        Hadir :
                        <strong><?= $d['total_hadir'] ?></strong>
                    </div>

                    <div>
                        Tidak Hadir :
                        <strong><?= $d['total_tidak'] ?></strong>
                    </div>

                    <div>
                        Total Anak :
                        <strong><?= $d['total_anak'] ?></strong>
                    </div>

                    <div class="mt-3">

                        <small>
                            Progress Kehadiran <?= $persen ?>%
                        </small>

                        <div class="progress">
                            <div class="progress-bar"
                                 style="width: <?= $persen ?>%">
                            </div>
                        </div>

                    </div>

                </div>

                <div class="card-footer bg-white border-0">

                    <a href="index.php?url=kegiatan-detail&id=<?= $d['id'] ?>"
                       class="btn btn-primary btn-block">

                        Input Kehadiran

                    </a>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</div>