<?php
require_once __DIR__ . '/../../config/database.php';

/*
|--------------------------------------------------------------------------
| SIMPAN DATA
|--------------------------------------------------------------------------
*/
if (isset($_POST['simpan'])) {

    $stmt = $pdo->prepare("
        INSERT INTO imunisasi
        (
            id_anak,
            id_kegiatan,
            jenis_imunisasi,
            tanggal,
            diberikan_oleh
        )
        VALUES
        (
            ?, ?, ?, ?, ?
        )
    ");

    $stmt->execute([
        $_POST['anak'],
        $_POST['kegiatan'],
        $_POST['jenis'],
        $_POST['tanggal'],
        $_SESSION['user']['id']
    ]);

    header("Location: index.php?url=imunisasi");
    exit;
}

/*
|--------------------------------------------------------------------------
| DATA FORM
|--------------------------------------------------------------------------
*/
$anak = $pdo->query("
    SELECT *
    FROM anak
    ORDER BY nama ASC
")->fetchAll(PDO::FETCH_ASSOC);

$kegiatan = $pdo->query("
    SELECT *
    FROM kegiatan
    ORDER BY tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

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

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Imunisasi Anak
            </h3>

            <small class="text-muted">
                Pencatatan dan monitoring imunisasi
            </small>

        </div>

    </div>

    <!-- FORM INPUT -->

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-primary text-white">

            Input Imunisasi

        </div>

        <div class="card-body">

            <form method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>Nama Anak</label>

                        <select
                            name="anak"
                            class="form-control"
                            required>

                            <option value="">
                                Pilih Anak
                            </option>

                            <?php foreach($anak as $a): ?>

                                <option value="<?= $a['id'] ?>">

                                    <?= htmlspecialchars($a['nama']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Jenis Imunisasi</label>

                        <select
                            name="jenis"
                            class="form-control"
                            required>

                            <option value="">
                                Pilih Imunisasi
                            </option>

                            <option value="HB0">HB0</option>
                            <option value="BCG">BCG</option>
                            <option value="Polio">Polio</option>
                            <option value="DPT-HB-Hib">DPT-HB-Hib</option>
                            <option value="Campak">Campak</option>
                            <option value="MR">MR</option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Tanggal Imunisasi</label>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Kegiatan Posyandu</label>

                        <select
                            name="kegiatan"
                            class="form-control"
                            required>

                            <option value="">
                                Pilih Kegiatan
                            </option>

                            <?php foreach($kegiatan as $k): ?>

                                <option value="<?= $k['id'] ?>">

                                    Pertemuan <?= $k['pertemuan_ke'] ?>
                                    -
                                    <?= date('d M Y', strtotime($k['tanggal'])) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-success">

                    Simpan Imunisasi

                </button>

            </form>

        </div>

    </div>

    <!-- DATA -->

    <div class="card shadow-sm">

        <div class="card-header bg-white">

            <h5 class="mb-0">
                Riwayat Imunisasi
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
                            <th width="140">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if(count($data)): ?>

                        <?php foreach($data as $d): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($d['nama_anak']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($d['jenis_imunisasi']) ?>
                            </td>

                            <td>
                                <?= date('d M Y', strtotime($d['tanggal'])) ?>
                            </td>

                            <td>
                                Pertemuan <?= $d['pertemuan_ke'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($d['petugas'] ?? '-') ?>
                            </td>

                            <td>

                                <a
                                    href="index.php?url=imunisasi-edit&id=<?= $d['id'] ?>"
                                    class="btn btn-warning btn-sm">

                                    Edit

                                </a>

                                <a
                                    href="index.php?url=imunisasi-delete&id=<?= $d['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin hapus data?')">

                                    Hapus

                                </a>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="6"
                                class="text-center text-muted py-4">

                                Belum ada data imunisasi.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>