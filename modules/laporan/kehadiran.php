<?php
require_once __DIR__ . '/../../config/database.php';

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/
$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

$sql = "
SELECT
    g.tanggal,
    g.pertemuan_ke,
    g.lokasi,
    a.nama,
    h.status_hadir

FROM kehadiran h

JOIN anak a
    ON a.id = h.id_anak

JOIN kegiatan g
    ON g.id = h.id_kegiatan

WHERE 1=1
";

$params = [];

if ($tanggal_awal != '') {
    $sql .= " AND g.tanggal >= ?";
    $params[] = $tanggal_awal;
}

if ($tanggal_akhir != '') {
    $sql .= " AND g.tanggal <= ?";
    $params[] = $tanggal_akhir;
}

$sql .= "
ORDER BY g.tanggal DESC,
         a.nama ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($data);
?>

<div class="container-fluid">

```
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">
            Laporan Kehadiran
        </h3>

        <small class="text-muted">
            Rekap kehadiran anak pada kegiatan Posyandu
        </small>

    </div>

    <button
        onclick="window.print()"
        class="btn btn-success">

        <i class="fas fa-print"></i>
        Cetak

    </button>

</div>

<div class="card shadow-sm mb-4">

    <div class="card-body">

        <form method="GET">

            <input
                type="hidden"
                name="url"
                value="laporan-kehadiran">

            <div class="row">

                <div class="col-md-4">

                    <label>
                        Tanggal Awal
                    </label>

                    <input
                        type="date"
                        name="tanggal_awal"
                        value="<?= $tanggal_awal ?>"
                        class="form-control">

                </div>

                <div class="col-md-4">

                    <label>
                        Tanggal Akhir
                    </label>

                    <input
                        type="date"
                        name="tanggal_akhir"
                        value="<?= $tanggal_akhir ?>"
                        class="form-control">

                </div>

                <div class="col-md-4 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-primary mr-2">

                        <i class="fas fa-search"></i>
                        Tampilkan

                    </button>

                    <a
                        href="index.php?url=laporan-kehadiran"
                        class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>

<div class="card shadow">

    <div class="card-header bg-white d-flex justify-content-between align-items-center">

        <h5 class="mb-0">

            Data Kehadiran

            <span class="badge badge-primary ml-2">

                <?= $total ?>

            </span>

        </h5>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0">

                <thead class="thead-light">

                    <tr>

                        <th width="140">
                            Tanggal
                        </th>

                        <th width="120">
                            Pertemuan
                        </th>

                        <th>
                            Nama Anak
                        </th>

                        <th>
                            Lokasi
                        </th>

                        <th width="120">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if(count($data)): ?>

                    <?php foreach($data as $d): ?>

                    <tr>

                        <td>

                            <?= date(
                                'd M Y',
                                strtotime($d['tanggal'])
                            ) ?>

                        </td>

                        <td>

                            Pertemuan
                            <?= $d['pertemuan_ke'] ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($d['nama']) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($d['lokasi']) ?>

                        </td>

                        <td>

                            <?php if($d['status_hadir']=='hadir'): ?>

                                <span class="badge badge-success">
                                    Hadir
                                </span>

                            <?php else: ?>

                                <span class="badge badge-danger">
                                    Tidak Hadir
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="5"
                            class="text-center py-4">

                            Tidak ada data kehadiran

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
```

</div>
