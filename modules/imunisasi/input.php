<?php
require_once __DIR__ . '/../../config/database.php';

/*
|--------------------------------------------------------------------------
| SIMPAN DATA
|--------------------------------------------------------------------------
*/
if(isset($_POST['simpan'])){

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

    header("Location:index.php?url=imunisasi");
    exit;
}

/*
|--------------------------------------------------------------------------
| DATA ANAK
|--------------------------------------------------------------------------
*/
$anak = $pdo->query("
    SELECT *
    FROM anak
    ORDER BY nama ASC
")->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| DATA KEGIATAN
|--------------------------------------------------------------------------
*/
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
                Input Imunisasi Anak
            </h3>

            <small class="text-muted">
                Tambahkan data imunisasi balita
            </small>

        </div>

        <a href="index.php?url=imunisasi"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>
            Kembali

        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">

            <i class="fas fa-syringe"></i>
            Form Imunisasi

        </div>

        <div class="card-body">

            <form method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>
                            Nama Anak
                        </label>

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

                        <label>
                            Jenis Imunisasi
                        </label>

                        <select
                            name="jenis"
                            class="form-control"
                            required>

                            <option value="">
                                Pilih Imunisasi
                            </option>

                            <option value="HB0">
                                Hepatitis B (HB0)
                            </option>

                            <option value="BCG">
                                BCG (Tuberkulosis)
                            </option>

                            <option value="Polio">
                                Polio
                            </option>

                            <option value="DPT-HB-Hib">
                                DPT-HB-Hib
                            </option>

                            <option value="Campak">
                                Campak
                            </option>

                            <option value="MR">
                                Measles Rubella (MR)
                            </option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>
                            Tanggal Imunisasi
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            value="<?= date('Y-m-d') ?>"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>
                            Kegiatan Posyandu
                        </label>

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

                <hr>

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-success">

                    <i class="fas fa-save"></i>
                    Simpan

                </button>

                <a href="index.php?url=imunisasi"
                   class="btn btn-secondary">

                    Batal

                </a>

            </form>

        </div>

    </div>

</div>