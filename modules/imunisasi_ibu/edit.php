<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo "<script>window.location='index.php?url=imunisasi_ibu';</script>";
    exit;
}

// Ambil data imunisasi
$stmt = $pdo->prepare("
    SELECT 
        iih.*,
        ih.nama_ibu,
        ih.nik,
        mi.nama_imunisasi
    FROM imunisasi_ibu_hamil iih
    JOIN ibu_hamil ih ON ih.id = iih.ibu_hamil_id
    LEFT JOIN master_imunisasi mi ON mi.id = iih.imunisasi_id
    WHERE iih.id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>window.location.href = 'index.php?url=imunisasi_ibu';</script>";
    exit;
}

// Data ibu hamil untuk dropdown
$ibuHamil = $pdo->query("SELECT * FROM ibu_hamil WHERE status='Aktif' ORDER BY nama_ibu ASC")->fetchAll(PDO::FETCH_ASSOC);

// Data master imunisasi
$masterImunisasi = $pdo->query("SELECT * FROM master_imunisasi WHERE kategori = 'Ibu Hamil' ORDER BY nama_imunisasi ASC")->fetchAll(PDO::FETCH_ASSOC);

// Data kegiatan
$kegiatan = $pdo->query("SELECT * FROM kegiatan ORDER BY tanggal DESC")->fetchAll(PDO::FETCH_ASSOC);

// Proses update
if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("
        UPDATE imunisasi_ibu_hamil 
        SET ibu_hamil_id = ?, imunisasi_id = ?, tanggal = ?, keterangan = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $_POST['ibu_hamil_id'],
        $_POST['imunisasi_id'],
        $_POST['tanggal'],
        $_POST['keterangan'],
        $id
    ]);
    
    echo "<script>
        alert('Data imunisasi berhasil diperbarui');
        window.location='index.php?url=imunisasi_ibu&id_kegiatan=" . ($_POST['kegiatan_id'] ?? 0) . "';
    </script>";
    exit;
}
?>

<style>
.imunisasi-ibu-edit-container { padding: 10px 0; }

/* Header */
.imunisasi-ibu-edit-header {
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

.imunisasi-ibu-edit-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.imunisasi-ibu-edit-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.imunisasi-ibu-edit-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Button Back */
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

/* Card Form */
.card-form-imunisasi-ibu-edit {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-form-imunisasi-ibu-edit .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    background: #2c6b9e;
    color: #ffffff;
}

.card-form-imunisasi-ibu-edit .card-header-custom h6 {
    font-weight: 600;
    margin: 0;
    font-size: 14px;
}

.card-form-imunisasi-ibu-edit .card-header-custom h6 i {
    margin-right: 8px;
}

.card-form-imunisasi-ibu-edit .card-body-custom {
    padding: 22px 24px;
}

.form-group label {
    font-weight: 600;
    color: #4a5568;
    font-size: 12px;
    margin-bottom: 4px;
}

.form-control,
.custom-select {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 10px 14px;
    transition: all 0.2s ease;
    background: #fafbfc;
    height: 44px;
}

.form-control:focus,
.custom-select:focus {
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

.btn-primary {
    background: #2c6b9e;
    border: none;
    color: #ffffff;
}

.btn-primary:hover {
    background: #1f507a;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(44, 107, 158, 0.25);
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

.alert-info-custom {
    border-radius: 10px;
    border: none;
    background: #e8f0fe;
    color: #1a2634;
    padding: 12px 16px;
    margin-bottom: 20px;
}
.alert-info-custom i { color: #2c6b9e; margin-right: 8px; }

@media (max-width: 768px) {
    .imunisasi-ibu-edit-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .card-form-imunisasi-ibu-edit .card-body-custom {
        padding: 16px;
    }
}
</style>

<div class="imunisasi-ibu-edit-container">

    <!-- HEADER -->
    <div class="imunisasi-ibu-edit-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-edit"></i>
                Edit Imunisasi Ibu Hamil
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Perbarui data imunisasi ibu hamil
            </div>
        </div>
        <a href="index.php?url=imunisasi_ibu" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- ALERT -->
    <div class="alert-info-custom">
        <i class="fas fa-info-circle"></i>
        <strong>Data Imunisasi:</strong> <?= htmlspecialchars($data['nama_ibu']) ?> - <?= htmlspecialchars($data['nama_imunisasi']) ?>
    </div>

    <!-- FORM -->
    <div class="card-form-imunisasi-ibu-edit">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-syringe"></i> Form Edit Imunisasi Ibu Hamil
            </h6>
        </div>
        <div class="card-body-custom">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Ibu Hamil <span style="color: #dc2626;">*</span></label>
                            <select name="ibu_hamil_id" class="custom-select" required>
                                <?php foreach ($ibuHamil as $ih): ?>
                                    <option value="<?= $ih['id'] ?>" <?= ($ih['id'] == $data['ibu_hamil_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ih['nama_ibu']) ?> 
                                        (<?= $ih['usia_kehamilan'] ?? 0 ?> minggu)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jenis Imunisasi <span style="color: #dc2626;">*</span></label>
                            <select name="imunisasi_id" class="custom-select" required>
                                <?php foreach ($masterImunisasi as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= ($m['id'] == $data['imunisasi_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['nama_imunisasi']) ?>
                                    </option>
                                <?php endforeach; ?>
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
                            <label>Kegiatan Posyandu</label>
                            <select name="kegiatan_id" class="custom-select">
                                <option value="0">-- Pilih Kegiatan --</option>
                                <?php foreach ($kegiatan as $k): ?>
                                    <option value="<?= $k['id'] ?>">
                                        Pertemuan <?= $k['pertemuan_ke'] ?> - <?= date('d M Y', strtotime($k['tanggal'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">* Untuk referensi kegiatan</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
                </div>

                <hr style="margin: 20px 0;">

                <div class="d-flex" style="gap: 10px; flex-wrap: wrap;">
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Imunisasi
                    </button>
                    <a href="index.php?url=imunisasi_ibu" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>