<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = $_GET['id_kegiatan'] ?? 0;

// DATA IBU HAMIL AKTIF
$ibuHamil = $pdo->query("SELECT * FROM ibu_hamil WHERE status='Aktif' ORDER BY nama_ibu ASC")->fetchAll(PDO::FETCH_ASSOC);

// DATA KEGIATAN
$kegiatan = $pdo->query("SELECT * FROM kegiatan ORDER BY tanggal DESC")->fetchAll(PDO::FETCH_ASSOC);

// DATA MASTER IMUNISASI IBU HAMIL
$masterImunisasi = $pdo->query("SELECT * FROM master_imunisasi WHERE kategori = 'Ibu Hamil' ORDER BY nama_imunisasi ASC")->fetchAll(PDO::FETCH_ASSOC);

// SIMPAN DATA
if (isset($_POST['simpan'])) {
    $stmt = $pdo->prepare("
        INSERT INTO imunisasi_ibu_hamil (ibu_hamil_id, imunisasi_id, tanggal, diberikan_oleh, keterangan)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_POST['ibu_hamil_id'],
        $_POST['imunisasi_id'],
        $_POST['tanggal'],
        $_SESSION['user']['id'],
        $_POST['keterangan'] ?? ''
    ]);
    
    echo "<script>window.location.href = 'index.php?url=imunisasi_ibu';</script>";
    exit;
}
?>

<style>
.imunisasi-ibu-input-container { padding: 10px 0; }

/* Header */
.imunisasi-ibu-input-header {
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

.imunisasi-ibu-input-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.imunisasi-ibu-input-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.imunisasi-ibu-input-header .header-left .sub-title {
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
.card-form-imunisasi-ibu {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-form-imunisasi-ibu .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    background: #2c6b9e;
    color: #ffffff;
}

.card-form-imunisasi-ibu .card-header-custom h6 {
    font-weight: 600;
    margin: 0;
    font-size: 14px;
}

.card-form-imunisasi-ibu .card-header-custom h6 i {
    margin-right: 8px;
}

.card-form-imunisasi-ibu .card-body-custom {
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

.btn-success {
    background: #28a745;
    border: none;
    color: #ffffff;
}

.btn-success:hover {
    background: #1e7e34;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.25);
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

@media (max-width: 768px) {
    .imunisasi-ibu-input-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .card-form-imunisasi-ibu .card-body-custom {
        padding: 16px;
    }
}
</style>

<div class="imunisasi-ibu-input-container">

    <!-- HEADER -->
    <div class="imunisasi-ibu-input-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-syringe"></i>
                Input Imunisasi Ibu Hamil
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Tambahkan data imunisasi ibu hamil (TT)
            </div>
        </div>
        <a href="index.php?url=imunisasi_ibu&id_kegiatan=<?= $id_kegiatan ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- FORM -->
    <div class="card-form-imunisasi-ibu">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-syringe"></i> Form Imunisasi Ibu Hamil
            </h6>
        </div>
        <div class="card-body-custom">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Ibu Hamil <span style="color: #dc2626;">*</span></label>
                            <select name="ibu_hamil_id" class="custom-select" required>
                                <option value="">-- Pilih Ibu Hamil --</option>
                                <?php foreach ($ibuHamil as $ih): ?>
                                    <option value="<?= $ih['id'] ?>">
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
                                <option value="">-- Pilih Imunisasi --</option>
                                <?php foreach ($masterImunisasi as $m): ?>
                                    <option value="<?= $m['id'] ?>">
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
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kegiatan Posyandu <span style="color: #dc2626;">*</span></label>
                            <select name="kegiatan_id" class="custom-select" required>
                                <option value="">-- Pilih Kegiatan --</option>
                                <?php foreach ($kegiatan as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= ($k['id'] == $id_kegiatan) ? 'selected' : '' ?>>
                                        Pertemuan <?= $k['pertemuan_ke'] ?> - <?= date('d M Y', strtotime($k['tanggal'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                </div>

                <hr style="margin: 20px 0;">

                <div class="d-flex" style="gap: 10px; flex-wrap: wrap;">
                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Imunisasi
                    </button>
                    <a href="index.php?url=imunisasi_ibu&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>