<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = (int) ($_GET['id_kegiatan'] ?? 0);

if (!$id_kegiatan) {
    header("Location:index.php?url=pemeriksaan");
    exit;
}

/*
|--------------------------------------------------------------------------
| DATA KEGIATAN
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT *
    FROM kegiatan
    WHERE id = ?
");
$stmt->execute([$id_kegiatan]);

$kegiatan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kegiatan) {
    die("
    <div style='padding:30px'>
        <h3>Kegiatan tidak ditemukan</h3>
        <a href='index.php?url=pemeriksaan'>
            Kembali
        </a>
    </div>
    ");
}

/*
|--------------------------------------------------------------------------
| ANAK HADIR
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
    ) % 12 AS umur_sisa_bulan,

    TIMESTAMPDIFF(
        MONTH,
        a.tanggal_lahir,
        CURDATE()
    ) AS umur_bulan

FROM kehadiran h

JOIN anak a
    ON a.id = h.id_anak

WHERE h.id_kegiatan = ?
AND h.status_hadir='hadir'

GROUP BY a.id

ORDER BY a.nama
");

$stmt->execute([$id_kegiatan]);

$anak = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| PEMERIKSAAN EXISTING
|--------------------------------------------------------------------------
*/
$pemeriksaan = [];

$stmt = $pdo->prepare("
    SELECT *
    FROM pemeriksaan
    WHERE id_kegiatan = ?
");

$stmt->execute([$id_kegiatan]);

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $pemeriksaan[$row['id_anak']] = $row;
}

/*
|--------------------------------------------------------------------------
| SIMPAN
|--------------------------------------------------------------------------
*/
if (isset($_POST['simpan'])) {

    foreach ($_POST['bb'] as $id_anak => $bb) {

        $stmt = $pdo->prepare("
            INSERT INTO pemeriksaan
            (
                id_anak,
                id_kegiatan,
                umur_bulan,
                berat_badan,
                tinggi_badan,
                lingkar_kepala,
                status_gizi,
                catatan,
                diukur_oleh
            )
            VALUES
            (
                ?,?,?,?,?,?,?,?,?
            )

            ON DUPLICATE KEY UPDATE

                umur_bulan     = VALUES(umur_bulan),
                berat_badan    = VALUES(berat_badan),
                tinggi_badan   = VALUES(tinggi_badan),
                lingkar_kepala = VALUES(lingkar_kepala),
                status_gizi    = VALUES(status_gizi),
                catatan        = VALUES(catatan),
                diukur_oleh    = VALUES(diukur_oleh)
        ");

        $stmt->execute([

            $id_anak,
            $id_kegiatan,

            $_POST['umur'][$id_anak] ?? 0,
            $_POST['bb'][$id_anak] ?? null,
            $_POST['tb'][$id_anak] ?? null,
            $_POST['lk'][$id_anak] ?? null,
            $_POST['gizi'][$id_anak] ?? '',
            $_POST['catatan'][$id_anak] ?? '',

            $_SESSION['user']['id']
        ]);
    }

    header(
        "Location:index.php?url=pemeriksaan&id_kegiatan=".$id_kegiatan
    );
    exit;
}

$totalAnak = count($anak);
?>

<div class="container-fluid">

    <!-- HEADER -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h3 class="mb-1">
                        Input Pemeriksaan Anak
                    </h3>

                    <div class="text-muted">

                        Pertemuan
                        <?= $kegiatan['pertemuan_ke'] ?>

                        •

                        <?= date(
                            'd M Y',
                            strtotime($kegiatan['tanggal'])
                        ) ?>

                        •

                        <?= htmlspecialchars(
                            $kegiatan['lokasi']
                        ) ?>

                    </div>

                </div>

                <div class="text-right">

                    <h2 class="mb-0">
                        <?= $totalAnak ?>
                    </h2>

                    <small class="text-muted">
                        Anak Hadir
                    </small>

                </div>

            </div>

        </div>

    </div>

    <!-- ACTION -->

    <div class="mb-3">

        <a href="index.php?url=pemeriksaan&id_kegiatan=<?= $id_kegiatan ?>"
           class="btn btn-secondary">

            ← Monitoring

        </a>

        <a href="index.php?url=kegiatan-detail&id=<?= $id_kegiatan ?>"
           class="btn btn-info">

            Detail Kegiatan

        </a>

    </div>

    <?php if(!$totalAnak): ?>

        <div class="alert alert-warning">

            Belum ada anak yang tercatat hadir
            pada kegiatan ini.

        </div>

    <?php else: ?>

    <form method="POST">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between">

                    <h5 class="mb-0">

                        Data Pemeriksaan

                    </h5>
                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0">

                    <thead class="thead-light">

                    <tr>

                        <th width="250">
                            Nama Anak
                        </th>

                        <th width="90">
                            Umur
                        </th>

                        <th width="120">
                            BB (Kg)
                        </th>

                        <th width="120">
                            TB (Cm)
                        </th>

                        <th width="120">
                            LK (Cm)
                        </th>

                        <th width="180">
                            Status Gizi
                        </th>

                        <th>
                            Catatan
                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php foreach($anak as $a): ?>

                    <?php
                    $p = $pemeriksaan[$a['id']] ?? [];
                    ?>

                    <tr>

                        <td>

                            <strong>
                                <?= htmlspecialchars($a['nama']) ?>
                            </strong>

                            <br>

                            <small class="text-muted">

                                NIK:
                                <?= htmlspecialchars(
                                    $a['nik'] ?? '-'
                                ) ?>

                            </small>

                        </td>

                    <td>

                            <?php if($a['umur_tahun'] > 0): ?>

                                <strong>
                                    <?= $a['umur_tahun'] ?> Th
                                </strong>

                                <?php if($a['umur_sisa_bulan'] > 0): ?>
                                    <br>
                                    <small class="text-muted">
                                        <?= $a['umur_sisa_bulan'] ?> Bulan
                                    </small>
                                <?php endif; ?>

                            <?php else: ?>

                                <strong>
                                    <?= $a['umur_bulan'] ?> Bulan
                                </strong>

                            <?php endif; ?>

                            <input
                                type="hidden"
                                name="umur[<?= $a['id'] ?>]"
                                value="<?= $a['umur_bulan'] ?>">

                        </td>

                        <td>

                            <input
                                type="number"
                                step="0.01"
                                name="bb[<?= $a['id'] ?>]"
                                value="<?= $p['berat_badan'] ?? '' ?>"
                                class="form-control">

                        </td>

                        <td>

                            <input
                                type="number"
                                step="0.01"
                                name="tb[<?= $a['id'] ?>]"
                                value="<?= $p['tinggi_badan'] ?? '' ?>"
                                class="form-control">

                        </td>

                        <td>

                            <input
                                type="number"
                                step="0.01"
                                name="lk[<?= $a['id'] ?>]"
                                value="<?= $p['lingkar_kepala'] ?? '' ?>"
                                class="form-control">

                        </td>

                        <td>

                            <select
                                name="gizi[<?= $a['id'] ?>]"
                                class="form-control">

                                <option value="Baik"
                                <?= (($p['status_gizi'] ?? '') == 'Baik') ? 'selected' : '' ?>>
                                    Baik
                                </option>

                                <option value="Kurang"
                                <?= (($p['status_gizi'] ?? '') == 'Kurang') ? 'selected' : '' ?>>
                                    Kurang
                                </option>

                                <option value="Buruk"
                                <?= (($p['status_gizi'] ?? '') == 'Buruk') ? 'selected' : '' ?>>
                                    Buruk
                                </option>

                            </select>

                        </td>

                        <td>

                            <textarea
                                rows="2"
                                class="form-control"
                                name="catatan[<?= $a['id'] ?>]"><?= htmlspecialchars(
                                    $p['catatan'] ?? ''
                                ) ?></textarea>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

           <div class="card-footer bg-white text-right">

                <a href="index.php?url=pemeriksaan&id_kegiatan=<?= $id_kegiatan ?>"
                class="btn btn-secondary">

                    Kembali

                </a>

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-success">

                    Simpan Pemeriksaan

                </button>

            </div>

        </div>

    </form>

    <?php endif; ?>

</div>