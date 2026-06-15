<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = $_GET['id_kegiatan'] ?? '';

/*
|--------------------------------------------------------------------------
| DAFTAR KEGIATAN
|--------------------------------------------------------------------------
*/
$kegiatan = $pdo->query("
    SELECT *
    FROM kegiatan
    ORDER BY tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| DATA PEMERIKSAAN
|--------------------------------------------------------------------------
*/
$data = [];

if ($id_kegiatan) {

    $stmt = $pdo->prepare("
        SELECT
            p.*,
            a.nama,
            a.tanggal_lahir,
            u.nama AS petugas

        FROM pemeriksaan p

        JOIN anak a
            ON a.id = p.id_anak

        LEFT JOIN users u
            ON u.id = p.diukur_oleh

        WHERE p.id_kegiatan = ?

        ORDER BY a.nama ASC
    ");

    $stmt->execute([$id_kegiatan]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*
|--------------------------------------------------------------------------
| RINGKASAN
|--------------------------------------------------------------------------
*/
$totalPeriksa = count($data);

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Pemeriksaan Anak
            </h3>

            <small class="text-muted">
                Monitoring hasil pemeriksaan Posyandu
            </small>

        </div>

    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET">

                <input type="hidden"
                       name="url"
                       value="pemeriksaan">

                <div class="row">

                    <div class="col-md-9">

                        <select name="id_kegiatan"
                                class="form-control">

                            <option value="">
                                Pilih Kegiatan Posyandu
                            </option>

                            <?php foreach($kegiatan as $k): ?>

                                <option value="<?= $k['id'] ?>"
                                    <?= $id_kegiatan == $k['id'] ? 'selected' : '' ?>>

                                    Pertemuan <?= $k['pertemuan_ke'] ?>
                                    -
                                    <?= date('d M Y', strtotime($k['tanggal'])) ?>
                                    -
                                    <?= htmlspecialchars($k['lokasi']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <button class="btn btn-primary btn-block">
                            Tampilkan
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <?php if($id_kegiatan): ?>

    <div class="row mb-4">

        <div class="col-md-12">

            <div class="card shadow-sm border-left-primary">

                <div class="card-body">

                    <h3><?= $totalPeriksa ?></h3>

                    <small class="text-muted">
                        Total Anak Sudah Diperiksa
                    </small>

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow-sm">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Data Pemeriksaan
                </h5>

                <a href="index.php?url=pemeriksaan-input&id_kegiatan=<?= $id_kegiatan ?>"
                   class="btn btn-success btn-sm">

                    + Input Pemeriksaan

                </a>

            </div>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0">

                    <thead class="thead-light">

                        <tr>
                            <th>Nama Anak</th>
                            <th>BB (kg)</th>
                            <th>TB (cm)</th>
                            <th>LK (cm)</th>
                            <th>Status Gizi</th>
                            <th>Catatan</th>
                            <th>Petugas</th>
                            <th width="120">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if(count($data)): ?>

                        <?php foreach($data as $d): ?>

                        <?php

                        $badge = 'secondary';

                        if ($d['status_gizi'] == 'Baik') {
                            $badge = 'success';
                        } elseif ($d['status_gizi'] == 'Kurang') {
                            $badge = 'warning';
                        } elseif ($d['status_gizi'] == 'Buruk') {
                            $badge = 'danger';
                        }

                        ?>

                        <tr>

                            <td>

                                <strong>
                                    <?= htmlspecialchars($d['nama']) ?>
                                </strong>

                            </td>

                            <td>
                                <?= $d['berat_badan'] ?>
                            </td>

                            <td>
                                <?= $d['tinggi_badan'] ?>
                            </td>

                            <td>
                                <?= $d['lingkar_kepala'] ?>
                            </td>

                            <td>

                                <span class="badge badge-<?= $badge ?>">
                                    <?= htmlspecialchars($d['status_gizi']) ?>
                                </span>

                            </td>

                            <td>
                                <?= htmlspecialchars($d['catatan'] ?? '-') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($d['petugas'] ?? '-') ?>
                            </td>

                            <td>

                                <a href="index.php?url=pemeriksaan-edit&id=<?= $d['id'] ?>"
                                class="btn btn-warning btn-sm">

                                    Edit
                                </a>

                                <a href="index.php?url=pemeriksaan-delete&id=<?= $d['id'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus data?')">
                                    Hapus
                                </a>

                            </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="7"
                                class="text-center text-muted py-4">

                                Belum ada data pemeriksaan untuk kegiatan ini.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <?php endif; ?>

</div>