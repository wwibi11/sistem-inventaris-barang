<?php
require_once __DIR__ . '/../../config/database.php';

if (isset($_POST['simpan'])) {

   $stmt = $pdo->prepare("
    INSERT INTO kegiatan
    (
        tanggal,
        lokasi,
        keterangan,
        pertemuan_ke,
        created_by
    )
    VALUES (?, ?, ?, ?, ?)
");

    $stmt->execute([
    $_POST['tanggal'],
    $_POST['lokasi'],
    $_POST['keterangan'],
    $_POST['pertemuan_ke'],
    $_SESSION['user']['id']
]);

    header("Location: index.php?url=kegiatan");
    exit;
}
?>

<style>

.page-header{
    margin-bottom:20px;
}

.page-title{
    font-size:24px;
    font-weight:700;
    color:#2e3a59;
}

.page-subtitle{
    color:#858796;
    font-size:13px;
}

.card-modern{
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

.card-header-modern{
    background:linear-gradient(
        135deg,
        #4e73df,
        #224abe
    );
    color:white;
    padding:18px 25px;
}

.form-label{
    font-size:12px;
    font-weight:600;
    margin-bottom:6px;
    color:#444;
}

.form-control,
.custom-select{
    border-radius:10px;
    font-size:13px;
    min-height:42px;
}

textarea.form-control{
    min-height:120px;
}

.alert-modern{
    border:none;
    border-radius:12px;
    background:#eef4ff;
    color:#2e3a59;
}

.btn{
    border-radius:10px;
}

</style>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="page-header">

        <div class="page-title">
            <i class="fas fa-calendar-plus text-primary mr-2"></i>
            Tambah Kegiatan Posyandu
        </div>

        <div class="page-subtitle">
            Buat jadwal kegiatan posyandu baru
        </div>

    </div>

    <form method="POST">

        <div class="card card-modern">

            <div class="card-header-modern">

                <h5 class="mb-0">
                    Informasi Kegiatan
                </h5>

            </div>

            <div class="card-body">

                <div class="row">

                    <!-- TANGGAL -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Tanggal Kegiatan
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            required>

                    </div>

                    <!-- LOKASI -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Lokasi Kegiatan
                        </label>

                        <input
                            type="text"
                            name="lokasi"
                            class="form-control"
                            placeholder="Contoh: Posyandu Melati"
                            required>

                    </div>

                    <!-- PERTEMUAN -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Pertemuan Ke
                        </label>

                        <input
                            type="number"
                            name="pertemuan_ke"
                            class="form-control"
                            placeholder="Contoh: 1">

                    </div>

                    <!-- KETERANGAN -->
                    <div class="col-12">

                        <label class="form-label">
                            Keterangan
                        </label>

                        <textarea
                            name="keterangan"
                            class="form-control"
                            placeholder="Masukkan keterangan kegiatan..."></textarea>

                    </div>

                </div>

            </div>

        </div>

        <!-- INFO -->
        <div class="alert alert-modern mt-3">

            <strong>
                Informasi
            </strong>

            <br>

            Setelah kegiatan dibuat,
            pencatatan kehadiran,
            pemeriksaan,
            dan imunisasi dilakukan melalui
            halaman detail kegiatan.

        </div>

        <!-- BUTTON -->
        <div class="text-right mt-3">

            <a
                href="index.php?url=kegiatan"
                class="btn btn-light">

                <i class="fas fa-arrow-left mr-1"></i>
                Batal

            </a>

            <button
                type="submit"
                name="simpan"
                class="btn btn-primary">

                <i class="fas fa-save mr-1"></i>
                Simpan Kegiatan

            </button>

        </div>

    </form>

</div>