<?php
require_once __DIR__ . '/../../config/database.php';

$data = $pdo->query("
SELECT
    p.*,
    a.nama AS nama_anak,
    k.tanggal
FROM pemeriksaan p
JOIN anak a
    ON a.id = p.id_anak
JOIN kegiatan k
    ON k.id = p.id_kegiatan
ORDER BY k.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total = count($data);
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">Laporan Pemeriksaan</h3>
            <small class="text-muted">
                Riwayat seluruh pemeriksaan balita
            </small>
        </div>

        <button onclick="window.print()"
                class="btn btn-primary">

            <i class="fas fa-print"></i>
            Cetak

        </button>

    </div>

    <div class="card shadow">

        <div class="card-header">

            Total Pemeriksaan :
            <strong><?= $total ?></strong>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered mb-0">

                    <thead>

                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Anak</th>
                            <th>BB</th>
                            <th>TB</th>
                            <th>LK</th>
                            <th>Status Gizi</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach($data as $d): ?>

                        <tr>

                            <td>
                                <?= date('d M Y', strtotime($d['tanggal'])) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($d['nama_anak']) ?>
                            </td>

                            <td>
                                <?= $d['berat_badan'] ?> Kg
                            </td>

                            <td>
                                <?= $d['tinggi_badan'] ?> Cm
                            </td>

                            <td>
                                <?= $d['lingkar_kepala'] ?> Cm
                            </td>

                            <td>
                                <?= $d['status_gizi'] ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>