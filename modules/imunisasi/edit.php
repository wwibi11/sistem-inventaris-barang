<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

/*
|--------------------------------------------------------------------------
| CEK DATA
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("SELECT * FROM imunisasi WHERE id = ?");
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
        UPDATE imunisasi SET id_anak=?, id_kegiatan=?, jenis_imunisasi=?, tanggal=?
        WHERE id=?
    ");
    $stmt->execute([
        $_POST['anak'],
        $_POST['kegiatan'],
        $_POST['jenis'],
        $_POST['tanggal'],
        $id
    ]);
    echo "<script>window.location.href = 'index.php?url=imunisasi';</script>";
    exit();
}

/*
|--------------------------------------------------------------------------
| DATA FORM
|--------------------------------------------------------------------------
*/
$anak = $pdo->query("SELECT * FROM anak ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
$kegiatan = $pdo->query("SELECT * FROM kegiatan ORDER BY tanggal DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.imunisasi-edit-container { padding: 10px 0; }

/* Header */
.imunisasi-edit-header {
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

.imunisasi-edit-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.imunisasi-edit-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.imunisasi-edit-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Card Form */
.card-form-imunisasi-edit {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-form-imunisasi-edit .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    background: #e8a317;
    color: #ffffff;
}

.card-form-imunisasi-edit .card-header-custom h6 {
    font-weight: 600;
    margin: 0;
    font-size: 14px;
}

.card-form-imunisasi-edit .card-header-custom h6 i {
    margin-right: 8px;
}

.card-form-imunisasi-edit .card-body-custom {
    padding: 22px 24px;
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

.btn {
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 24px;
    transition: all 0.2s ease;
}

.btn-warning {
    background: #e8a317;
    border: none;
    color: #ffffff;
}

.btn-warning:hover {
    background: #c47d0a;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(232, 163, 23, 0.3);
    color: #ffffff;
}

.btn-secondary {
    background: #f0f4f8;
    border: none;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #e2e8f0;
    color: #1a2634;
}

.btn-back {
    background: #f0f4f8;
    color: #4a5568;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-back:hover {
    background: #e2e8f0;
    color: #1a2634;
    text-decoration: none;
}

@media (max-width: 768px) {
    .imunisasi-edit-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .card-form-imunisasi-edit .card-body-custom {
        padding: 16px;
    }
}
</style>

<div class="imunisasi-edit-container">

    <!-- HEADER -->
    <div class="imunisasi-edit-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-edit"></i>
                Edit Imunisasi
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Perbarui data imunisasi anak
            </div>
        </div>
        <a href="index.php?url=imunisasi" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- FORM -->
    <div class="card-form-imunisasi-edit">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-edit"></i> Form Edit Imunisasi
            </h6>
        </div>
        <div class="card-body-custom">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Anak <span style="color: #dc2626;">*</span></label>
                            <select name="anak" class="custom-select" required>
                                <?php foreach($anak as $a): ?>
                                    <option value="<?= $a['id'] ?>" <?= $a['id'] == $data['id_anak'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($a['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jenis Imunisasi <span style="color: #dc2626;">*</span></label>
                            <select name="jenis" class="custom-select" required>
                                <option value="HB0" <?= $data['jenis_imunisasi'] == 'HB0' ? 'selected' : '' ?>>Hepatitis B (HB0)</option>
                                <option value="BCG" <?= $data['jenis_imunisasi'] == 'BCG' ? 'selected' : '' ?>>BCG (Tuberkulosis)</option>
                                <option value="Polio" <?= $data['jenis_imunisasi'] == 'Polio' ? 'selected' : '' ?>>Polio</option>
                                <option value="DPT-HB-Hib" <?= $data['jenis_imunisasi'] == 'DPT-HB-Hib' ? 'selected' : '' ?>>DPT-HB-Hib</option>
                                <option value="Campak" <?= $data['jenis_imunisasi'] == 'Campak' ? 'selected' : '' ?>>Campak</option>
                                <option value="MR" <?= $data['jenis_imunisasi'] == 'MR' ? 'selected' : '' ?>>Measles Rubella (MR)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Imunisasi <span style="color: #dc2626;">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kegiatan Posyandu <span style="color: #dc2626;">*</span></label>
                            <select name="kegiatan" class="custom-select" required>
                                <?php foreach($kegiatan as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= $k['id'] == $data['id_kegiatan'] ? 'selected' : '' ?>>
                                        Pertemuan <?= $k['pertemuan_ke'] ?> - <?= date('d M Y', strtotime($k['tanggal'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <hr style="margin: 20px 0;">

                <div class="d-flex" style="gap: 10px; flex-wrap: wrap;">
                    <button type="submit" name="update" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Imunisasi
                    </button>
                    <a href="index.php?url=imunisasi" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>