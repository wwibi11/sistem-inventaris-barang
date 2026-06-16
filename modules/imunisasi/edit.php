<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

/*
|--------------------------------------------------------------------------
| CEK DATA
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT *
    FROM imunisasi
    WHERE id = ?
");

$stmt->execute([$id]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('Data imunisasi tidak ditemukan');
}

/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/
if (isset($_POST['update'])) {

    $stmt = $pdo->prepare("
        UPDATE imunisasi
        SET
            id_anak = ?,
            id_kegiatan = ?,
            jenis_imunisasi = ?,
            tanggal = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $_POST['anak'],
        $_POST['kegiatan'],
        $_POST['jenis'],
        $_POST['tanggal'],
        $id
    ]);

    header("Location:index.php?url=imunisasi");
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
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Edit Imunisasi
            </h3>

            <small class="text-muted">
                Perbarui data imunisasi anak
            </small>

        </div>

        <a href="index.php?url=imunisasi"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>
            Kembali

        </a>

    </div>

    <div class="card shadow">

        <div class="card-header bg-warning text-dark">

            <strong>
                Form Edit Imunisasi
            </strong>

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

                            <?php foreach($anak as $a): ?>

                                <option
                                    value="<?= $a['id'] ?>"
                                    <?= $a['id'] == $data['id_anak'] ? 'selected' : '' ?>>

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

                            <option value="HB0"
                                <?= $data['jenis_imunisasi']=='HB0' ? 'selected' : '' ?>>
                                Hepatitis B (HB0)
                            </option>

                            <option value="BCG"
                                <?= $data['jenis_imunisasi']=='BCG' ? 'selected' : '' ?>>
                                BCG (Tuberkulosis)
                            </option>

                            <option value="Polio"
                                <?= $data['jenis_imunisasi']=='Polio' ? 'selected' : '' ?>>
                                Polio
                            </option>

                            <option value="DPT-HB-Hib"
                                <?= $data['jenis_imunisasi']=='DPT-HB-Hib' ? 'selected' : '' ?>>
                                DPT-HB-Hib
                            </option>

                            <option value="Campak"
                                <?= $data['jenis_imunisasi']=='Campak' ? 'selected' : '' ?>>
                                Campak
                            </option>

                            <option value="MR"
                                <?= $data['jenis_imunisasi']=='MR' ? 'selected' : '' ?>>
                                Measles Rubella (MR)
                            </option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Tanggal Imunisasi</label>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            value="<?= $data['tanggal'] ?>"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Kegiatan Posyandu</label>

                        <select
                            name="kegiatan"
                            class="form-control"
                            required>

                            <?php foreach($kegiatan as $k): ?>

                                <option
                                    value="<?= $k['id'] ?>"
                                    <?= $k['id'] == $data['id_kegiatan'] ? 'selected' : '' ?>>

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
                    name="update"
                    class="btn btn-warning">

                    <i class="fas fa-save"></i>
                    Update Imunisasi

                </button>

            </form>

        </div>

    </div>

</div>