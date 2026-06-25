<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE id=?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    header("Location: index.php?url=kegiatan");
    exit;
}

// STATISTIK
$stmt = $pdo->prepare("SELECT COUNT(*) FROM kehadiran WHERE id_kegiatan=? AND status_hadir='hadir'");
$stmt->execute([$id]);
$totalHadir = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM pemeriksaan WHERE id_kegiatan=?");
$stmt->execute([$id]);
$totalPeriksa = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM imunisasi WHERE id_kegiatan=?");
$stmt->execute([$id]);
$totalImunisasi = $stmt->fetchColumn();

if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("
        UPDATE kegiatan SET tanggal=?, lokasi=?, keterangan=?, pertemuan_ke=?, status=?
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
.kegiatan-edit-container { padding: 15px 0; }

/* Header */
.kegiatan-edit-header {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.kegiatan-edit-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.kegiatan-edit-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.kegiatan-edit-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Alert Status */
.alert-status {
    border-radius: 10px;
    border: none;
    padding: 12px 18px;
}

.alert-status.alert-success {
    background: #d1fae5;
    color: #047857;
}

.alert-status.alert-warning {
    background: #fef3c7;
    color: #92400e;
}

/* Stat Cards */
.stat-card-edit {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px 20px;
    border: 1px solid #e8ecf1;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
}

.stat-card-edit .stat-number {
    font-size: 26px;
    font-weight: 700;
    color: #1a2634;
}

.stat-card-edit .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

.stat-card-edit.primary .stat-number { color: #2c6b9e; }
.stat-card-edit.success .stat-number { color: #28a745; }
.stat-card-edit.info .stat-number { color: #17a2b8; }

/* Card Form */
.card-form {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-form .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    font-weight: 600;
    color: #1a2634;
    font-size: 14px;
    background: #f8f9fc;
}

.card-form .card-header-custom i {
    color: #2c6b9e;
    margin-right: 8px;
}

.card-form .card-body-custom {
    padding: 20px 22px;
}

.form-group label {
    font-weight: 600;
    color: #4a5568;
    font-size: 12px;
    margin-bottom: 4px;
}

.form-control, .custom-select {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 10px 14px;
    transition: all 0.2s ease;
    background: #fafbfc;
    height: 44px;
}

.form-control:focus, .custom-select:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

textarea.form-control {
    height: auto;
    min-height: 100px;
}

.btn {
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 24px;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #2c6b9e;
    border: none;
    color: #ffffff;
}

.btn-primary:hover {
    background: #1f507a;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(44, 107, 158, 0.25);
    color: #ffffff;
}

.btn-success {
    background: #28a745;
    border: none;
    color: #ffffff;
}

.btn-success:hover {
    background: #1e7e34;
    color: #ffffff;
}

.btn-light {
    background: #f0f4f8;
    border: none;
    color: #4a5568;
}

.btn-light:hover {
    background: #e2e8f0;
    color: #1a2634;
}

@media (max-width: 768px) {
    .kegiatan-edit-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .kegiatan-edit-header .d-flex {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<div class="kegiatan-edit-container">

    <!-- HEADER -->
    <div class="kegiatan-edit-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-edit"></i>
                Edit Kegiatan Posyandu
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Perbarui informasi kegiatan di Posyandu Bougenvil Belik
            </div>
        </div>
        <div>
            <a href="index.php?url=kegiatan" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- STATUS -->
    <?php if($data['status'] == 'selesai'): ?>
        <div class="alert-status alert-success mb-4">
            <i class="fas fa-check-circle"></i> <strong>Status:</strong> Kegiatan telah selesai dilaksanakan
        </div>
    <?php else: ?>
        <div class="alert-status alert-warning mb-4">
            <i class="fas fa-clock"></i> <strong>Status:</strong> Kegiatan masih terjadwal
        </div>
    <?php endif; ?>

    <!-- STATISTIK -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card-edit primary">
                <div class="stat-number"><?= $totalHadir ?></div>
                <div class="stat-label"><i class="fas fa-user-check"></i> Kehadiran</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-edit success">
                <div class="stat-number"><?= $totalPeriksa ?></div>
                <div class="stat-label"><i class="fas fa-stethoscope"></i> Pemeriksaan</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-edit info">
                <div class="stat-number"><?= $totalImunisasi ?></div>
                <div class="stat-label"><i class="fas fa-syringe"></i> Imunisasi</div>
            </div>
        </div>
    </div>

    <!-- FORM -->
    <div class="card-form">
        <div class="card-header-custom">
            <i class="fas fa-pen"></i> Informasi Kegiatan
        </div>
        <div class="card-body-custom">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Kegiatan <span style="color: #dc2626;">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pertemuan Ke <span style="color: #dc2626;">*</span></label>
                            <input type="number" name="pertemuan_ke" class="form-control" value="<?= $data['pertemuan_ke'] ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Lokasi <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($data['lokasi']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" rows="3" class="form-control"><?= htmlspecialchars($data['keterangan']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Status Kegiatan</label>
                    <select name="status" class="custom-select">
                        <option value="scheduled" <?= $data['status'] == 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                        <option value="selesai" <?= $data['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap mt-4" style="border-top: 1px solid #edf2f7; padding-top: 20px; gap: 10px;">
                    <a href="index.php?url=kegiatan-detail&id=<?= $id ?>" class="btn btn-success">
                        <i class="fas fa-chevron-right"></i> Kelola Kegiatan
                    </a>
                    <div>
                        <a href="index.php?url=kegiatan" class="btn btn-light mr-2">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" name="update" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>