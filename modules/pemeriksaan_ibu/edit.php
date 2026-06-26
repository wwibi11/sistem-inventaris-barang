<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo "<script>window.location='index.php?url=pemeriksaan_ibu';</script>";
    exit;
}

// Ambil data pemeriksaan
$stmt = $pdo->prepare("
    SELECT 
        p.*,
        ih.nama_ibu,
        ih.nik,
        ih.usia_kehamilan,
        k.pertemuan_ke,
        k.tanggal AS tanggal_kegiatan
    FROM pemeriksaan_ibu_hamil p
    JOIN ibu_hamil ih ON ih.id = p.ibu_hamil_id
    LEFT JOIN kegiatan k ON k.id = p.id_kegiatan
    WHERE p.id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>window.location='index.php?url=pemeriksaan_ibu';</script>";
    exit;
}

// Proses update
if (isset($_POST['update'])) {
    // Proses nilai kosong menjadi NULL untuk field decimal
    $berat_badan = !empty($_POST['berat_badan']) ? $_POST['berat_badan'] : null;
    $lingkar_lengan = !empty($_POST['lingkar_lengan']) ? $_POST['lingkar_lengan'] : null;
    $tinggi_fundus = !empty($_POST['tinggi_fundus']) ? $_POST['tinggi_fundus'] : null;
    
    $stmt = $pdo->prepare("
        UPDATE pemeriksaan_ibu_hamil 
        SET tanggal_periksa = ?, berat_badan = ?, tekanan_darah = ?, 
            lingkar_lengan = ?, tinggi_fundus = ?, keluhan = ?, tindakan = ?, keterangan = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $_POST['tanggal_periksa'],
        $berat_badan,
        $_POST['tekanan_darah'],
        $lingkar_lengan,
        $tinggi_fundus,
        $_POST['keluhan'],
        $_POST['tindakan'],
        $_POST['keterangan'],
        $id
    ]);
    
    echo "<script>
        alert('Data pemeriksaan berhasil diperbarui');
        window.location='index.php?url=pemeriksaan_ibu&id_kegiatan=" . ($data['id_kegiatan'] ?? 0) . "';
    </script>";
    exit;
}
?>

<style>
.pemeriksaan-ibu-edit-container { padding: 10px 0; }

.pemeriksaan-ibu-edit-header {
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

.pemeriksaan-ibu-edit-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.pemeriksaan-ibu-edit-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.pemeriksaan-ibu-edit-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
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

.card-form-pemeriksaan-ibu-edit {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-form-pemeriksaan-ibu-edit .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    background: #2c6b9e;
    color: #ffffff;
}

.card-form-pemeriksaan-ibu-edit .card-header-custom h6 {
    font-weight: 600;
    margin: 0;
    font-size: 14px;
}

.card-form-pemeriksaan-ibu-edit .card-header-custom h6 i {
    margin-right: 8px;
}

.card-form-pemeriksaan-ibu-edit .card-body-custom {
    padding: 22px 24px;
}

.form-group label {
    font-weight: 600;
    color: #4a5568;
    font-size: 12px;
    margin-bottom: 4px;
}

.form-control {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 10px 14px;
    transition: all 0.2s ease;
    background: #fafbfc;
    height: 44px;
}

.form-control:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

textarea.form-control {
    height: auto;
    min-height: 80px;
}

.btn-primary {
    background: #2c6b9e;
    border: none;
    color: #ffffff;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 24px;
    transition: all 0.2s ease;
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
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 24px;
    transition: all 0.2s ease;
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
    .pemeriksaan-ibu-edit-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
}
</style>

<div class="pemeriksaan-ibu-edit-container">

    <!-- HEADER -->
    <div class="pemeriksaan-ibu-edit-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-edit"></i>
                Edit Pemeriksaan Ibu Hamil
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <?= htmlspecialchars($data['nama_ibu']) ?> • Pertemuan <?= $data['pertemuan_ke'] ?? '-' ?>
            </div>
        </div>
        <a href="index.php?url=pemeriksaan_ibu&id_kegiatan=<?= $data['id_kegiatan'] ?? 0 ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- ALERT -->
    <div class="alert-info-custom">
        <i class="fas fa-info-circle"></i>
        <strong>Nama Ibu:</strong> <?= htmlspecialchars($data['nama_ibu']) ?> 
        | <strong>NIK:</strong> <?= htmlspecialchars($data['nik'] ?? '-') ?>
        | <strong>Usia:</strong> <?= $data['usia_kehamilan'] ?? 0 ?> Minggu
    </div>

    <!-- FORM -->
    <div class="card-form-pemeriksaan-ibu-edit">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-edit"></i> Form Edit Pemeriksaan
            </h6>
        </div>
        <div class="card-body-custom">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Periksa <span style="color: #dc2626;">*</span></label>
                            <input type="date" name="tanggal_periksa" class="form-control" value="<?= $data['tanggal_periksa'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Berat Badan (Kg)</label>
                            <input type="number" step="0.01" name="berat_badan" class="form-control" value="<?= $data['berat_badan'] ?? '' ?>" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tekanan Darah</label>
                            <input type="text" name="tekanan_darah" class="form-control" value="<?= htmlspecialchars($data['tekanan_darah'] ?? '') ?>" placeholder="120/80">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Lingkar Lengan (cm)</label>
                            <input type="number" step="0.01" name="lingkar_lengan" class="form-control" value="<?= $data['lingkar_lengan'] ?? '' ?>" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tinggi Fundus (cm)</label>
                            <input type="number" step="0.01" name="tinggi_fundus" class="form-control" value="<?= $data['tinggi_fundus'] ?? '' ?>" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tindakan</label>
                            <input type="text" name="tindakan" class="form-control" value="<?= htmlspecialchars($data['tindakan'] ?? '') ?>" placeholder="Tindakan yang diberikan">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keluhan</label>
                    <textarea name="keluhan" class="form-control" rows="3" placeholder="Keluhan ibu hamil..."><?= htmlspecialchars($data['keluhan'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan..."><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
                </div>

                <hr style="margin: 20px 0;">

                <div class="d-flex" style="gap: 10px; flex-wrap: wrap;">
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Pemeriksaan
                    </button>
                    <a href="index.php?url=pemeriksaan_ibu&id_kegiatan=<?= $data['id_kegiatan'] ?? 0 ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>