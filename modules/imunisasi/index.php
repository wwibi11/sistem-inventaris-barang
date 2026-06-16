<?php
require_once __DIR__ . '/../../config/database.php';

/*
|--------------------------------------------------------------------------
| DATA IMUNISASI
|--------------------------------------------------------------------------
*/
$data = $pdo->query("
    SELECT
        i.*,
        a.nama AS nama_anak,
        u.nama AS petugas,
        k.pertemuan_ke

    FROM imunisasi i

    JOIN anak a
        ON a.id = i.id_anak

    LEFT JOIN users u
        ON u.id = i.diberikan_oleh

    LEFT JOIN kegiatan k
        ON k.id = i.id_kegiatan

    ORDER BY i.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total_imunisasi = count($data);

$total_anak_imunisasi = $pdo->query("
SELECT COUNT(DISTINCT id_anak)
FROM imunisasi
")->fetchColumn();

$bulan_ini = $pdo->query("
SELECT COUNT(*)
FROM imunisasi
WHERE MONTH(tanggal)=MONTH(CURDATE())
AND YEAR(tanggal)=YEAR(CURDATE())
")->fetchColumn();

?>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">
            Imunisasi Anak
        </h3>

        <small class="text-muted">
            Monitoring dan pencatatan imunisasi balita
        </small>

    </div>

    <a href="index.php?url=imunisasi-input"
       class="btn btn-primary">

        <i class="fas fa-plus"></i>
        Tambah Imunisasi

    </a>

</div>

<div class="row mb-4">

    <div class="col-md-4">

        <div class="card border-left-primary shadow">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col">

                        <div class="text-xs font-weight-bold text-primary text-uppercase">

                            Total Imunisasi

                        </div>

                        <div class="h3 font-weight-bold">

                            <?= $total_imunisasi ?>

                        </div>

                    </div>

                    <div class="col-auto">

                        <i class="fas fa-syringe fa-2x text-gray-300"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-left-success shadow">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col">

                        <div class="text-xs font-weight-bold text-success text-uppercase">

                            Anak Diimunisasi

                        </div>

                        <div class="h3 font-weight-bold">

                            <?= $total_anak_imunisasi ?>

                        </div>

                    </div>

                    <div class="col-auto">

                        <i class="fas fa-child fa-2x text-gray-300"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-left-info shadow">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col">

                        <div class="text-xs font-weight-bold text-info text-uppercase">

                            Bulan Ini

                        </div>

                        <div class="h3 font-weight-bold">

                            <?= $bulan_ini ?>

                        </div>

                    </div>

                    <div class="col-auto">

                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="card shadow-sm mb-3">

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <input
                    type="text"
                    class="form-control"
                    placeholder="Cari nama anak...">

            </div>

            <div class="col-md-3">

                <select class="form-control">

                    <option>Semua Imunisasi</option>
                    <option>HB0</option>
                    <option>BCG</option>
                    <option>Polio</option>
                    <option>DPT-HB-Hib</option>
                    <option>Campak</option>
                    <option>MR</option>

                </select>

            </div>

            <div class="col-md-3">

                <button class="btn btn-primary btn-block">

                    <i class="fas fa-search"></i>
                    Cari

                </button>

            </div>

        </div>

    </div>

</div>

    <!-- DATA -->

<div class="card shadow-sm">

    <div class="card-header bg-white d-flex justify-content-between align-items-center">

        <h5 class="mb-0">

            Riwayat Imunisasi Anak

            <span class="badge badge-primary ml-2">

                <?= $total_imunisasi ?>

            </span>

        </h5>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0">

                <thead class="thead-light">

                    <tr>

                        <th>Nama Anak</th>
                        <th>Jenis Imunisasi</th>
                        <th>Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Petugas</th>
                        <th width="160">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php if(count($data)): ?>

                    <?php foreach($data as $d): ?>

                    <?php

                    $badge = 'primary';
                    $namaImunisasi = $d['jenis_imunisasi'];

                    switch($d['jenis_imunisasi']){

                        case 'HB0':
                            $badge = 'secondary';
                            $namaImunisasi = 'Hepatitis B (HB0)';
                            break;

                        case 'BCG':
                            $badge = 'success';
                            $namaImunisasi = 'BCG (Tuberkulosis)';
                            break;

                        case 'Polio':
                            $badge = 'info';
                            $namaImunisasi = 'Polio';
                            break;

                        case 'DPT-HB-Hib':
                            $badge = 'primary';
                            $namaImunisasi = 'DPT-HB-Hib';
                            break;

                        case 'Campak':
                            $badge = 'danger';
                            $namaImunisasi = 'Campak';
                            break;

                        case 'MR':
                            $badge = 'warning';
                            $namaImunisasi = 'Measles Rubella (MR)';
                            break;
                    }

                    ?>

                    <tr>

                        <td>

                            <a href="index.php?url=anak-detail&id=<?= $d['id_anak'] ?>">

                                <?= htmlspecialchars($d['nama_anak']) ?>

                            </a>

                        </td>

                        <td>

                            <span class="badge badge-<?= $badge ?>">

                                <?= $namaImunisasi ?>

                            </span>

                        </td>

                        <td>

                            <?= date('d M Y', strtotime($d['tanggal'])) ?>

                        </td>

                        <td>

                            Pertemuan <?= $d['pertemuan_ke'] ?? '-' ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($d['petugas'] ?? '-') ?>

                        </td>

                        <td>

                            <div class="btn-group">

                                <a
                                    href="index.php?url=anak-detail&id=<?= $d['id_anak'] ?>"
                                    class="btn btn-info btn-sm">

                                    <i class="fas fa-user"></i>

                                </a>

                                <a
                                    href="index.php?url=imunisasi-edit&id=<?= $d['id'] ?>"
                                    class="btn btn-warning btn-sm">

                                    <i class="fas fa-edit"></i>

                                </a>

                                <a
                                    href="index.php?url=imunisasi-delete&id=<?= $d['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin hapus data?')">

                                    <i class="fas fa-trash"></i>

                                </a>

                            </div>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6" class="text-center py-4">

                            Belum ada data imunisasi

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</div>