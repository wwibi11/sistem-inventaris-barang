<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

// ==========================
// DATA KEGIATAN
// ==========================
$stmt = $pdo->prepare("
    SELECT *
    FROM kegiatan
    WHERE id=?
");
$stmt->execute([$id]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data kegiatan tidak ditemukan");
}

// ==========================
// STATISTIK
// ==========================
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM kehadiran
    WHERE id_kegiatan=?
    AND status_hadir='hadir'
");
$stmt->execute([$id]);
$totalHadir = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM pemeriksaan
    WHERE id_kegiatan=?
");
$stmt->execute([$id]);
$totalPeriksa = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM imunisasi
    WHERE id_kegiatan=?
");
$stmt->execute([$id]);
$totalImunisasi = $stmt->fetchColumn();

// ==========================
// UPDATE
// ==========================
if (isset($_POST['update'])) {

    $stmt = $pdo->prepare("
        UPDATE kegiatan
        SET
            tanggal=?,
            lokasi=?,
            keterangan=?,
            pertemuan_ke=?,
            status=?
        WHERE id=?
    ");

    $stmt->execute([
        $_POST['tanggal'],
        $_POST['lokasi'],
        $_POST['keterangan'],
        $_POST['pertemuan_ke'],
        $_POST['status'],
        $id
    ]);

    echo "
    <script>
        alert('Kegiatan berhasil diperbarui');
        window.location='index.php?url=kegiatan';
    </script>
    ";
    exit;
}
?>

<style>

.card-modern{
    border:none;
    border-radius:18px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

.stat-card{
    border:none;
    border-radius:16px;
    box-shadow:0 4px 12px rgba(0,0,0,.05);
}

.stat-number{
    font-size:26px;
    font-weight:700;
}

.form-control,
.custom-select{
    border-radius:10px;
}

.badge-status{
    font-size:13px;
    padding:8px 14px;
}

</style>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Edit Kegiatan Posyandu
            </h3>

            <small class="text-muted">
                Perbarui informasi kegiatan dan status pelaksanaan
            </small>

        </div>

        <a href="index.php?url=kegiatan"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

    <!-- STATUS -->
    <div class="mb-4">

        <?php if($data['status']=='selesai'): ?>

            <div class="alert alert-success mb-0">

                <strong>Status:</strong>
                Kegiatan telah selesai dilaksanakan

            </div>

        <?php else: ?>

            <div class="alert alert-warning mb-0">

                <strong>Status:</strong>
                Kegiatan masih terjadwal

            </div>

        <?php endif; ?>

    </div>

    <!-- STATISTIK -->
    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card stat-card">

                <div class="card-body text-center">

                    <div class="stat-number text-primary">
                        <?= $totalHadir ?>
                    </div>

                    <small class="text-muted">
                        Kehadiran
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card stat-card">

                <div class="card-body text-center">

                    <div class="stat-number text-success">
                        <?= $totalPeriksa ?>
                    </div>

                    <small class="text-muted">
                        Pemeriksaan
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card stat-card">

                <div class="card-body text-center">

                    <div class="stat-number text-info">
                        <?= $totalImunisasi ?>
                    </div>

                    <small class="text-muted">
                        Imunisasi
                    </small>

                </div>

            </div>

        </div>

    </div>

    <!-- FORM -->
    <div class="card card-modern">

        <div class="card-body">

            <form method="POST">

                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>
                                Tanggal Kegiatan
                            </label>

                            <input
                                type="date"
                                name="tanggal"
                                value="<?= $data['tanggal'] ?>"
                                class="form-control"
                                required>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>
                                Pertemuan Ke
                            </label>

                            <input
                                type="number"
                                name="pertemuan_ke"
                                value="<?= $data['pertemuan_ke'] ?>"
                                class="form-control"
                                required>

                        </div>

                    </div>

                </div>

                <div class="form-group">

                    <label>
                        Lokasi
                    </label>

                    <input
                        type="text"
                        name="lokasi"
                        value="<?= htmlspecialchars($data['lokasi']) ?>"
                        class="form-control"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        Keterangan
                    </label>

                    <textarea
                        name="keterangan"
                        rows="4"
                        class="form-control"><?= htmlspecialchars($data['keterangan']) ?></textarea>

                </div>

                <div class="form-group">

                    <label>
                        Status Kegiatan
                    </label>

                    <select
                        name="status"
                        class="custom-select">

                        <option
                            value="scheduled"
                            <?= $data['status']=='scheduled' ? 'selected' : '' ?>>

                            Scheduled

                        </option>

                        <option
                            value="selesai"
                            <?= $data['status']=='selesai' ? 'selected' : '' ?>>

                            Selesai

                        </option>

                    </select>

                </div>

                <hr>

                <div class="d-flex justify-content-between">

                    <a
                        href="index.php?url=kegiatan-detail&id=<?= $id ?>"
                        class="btn btn-success">

                        Kelola Kegiatan

                    </a>

                    <button
                        type="submit"
                        name="update"
                        class="btn btn-primary">

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>