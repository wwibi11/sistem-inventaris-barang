<?php
require_once __DIR__ . '/../../config/database.php';

$data = $pdo->query("
SELECT
    i.*,
    a.nama AS nama_anak,
    k.pertemuan_ke,
    u.nama AS petugas
FROM imunisasi i

JOIN anak a
    ON a.id = i.id_anak

LEFT JOIN kegiatan k
    ON k.id = i.id_kegiatan

LEFT JOIN users u
    ON u.id = i.diberikan_oleh

ORDER BY i.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total = count($data);
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Laporan Imunisasi
            </h3>

            <small class="text-muted">
                Rekap seluruh imunisasi balita
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

            Total Imunisasi :
            <strong><?= $total ?></strong>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered mb-0">

                    <thead>

                        <tr>

                            <th>Tanggal</th>
                            <th>Nama Anak</th>
                            <th>Jenis Imunisasi</th>
                            <th>Pertemuan</th>
                            <th>Petugas</th>

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
                                <?= $d['jenis_imunisasi'] ?>
                            </td>

                            <td>
                                <?= $d['pertemuan_ke'] ?? '-' ?>
                            </td>

                            <td>
                                <?= $d['petugas'] ?? '-' ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>