<?php
// modules/kegiatan/create.php
// PAKAI JAVASCRIPT UNTUK REDIRECT

require_once __DIR__ . '/../../config/database.php';

$pdo = new PDO("mysql:host=localhost;dbname=posyandu_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['simpan'])) {
    $stmt = $pdo->prepare("
        INSERT INTO kegiatan (tanggal, lokasi, keterangan, pertemuan_ke, created_by, status)
        VALUES (?, ?, ?, ?, ?, 'scheduled')
    ");
    $stmt->execute([
        $_POST['tanggal'],
        $_POST['lokasi'],
        $_POST['keterangan'],
        $_POST['pertemuan_ke'],
        $_SESSION['user']['id'] ?? 1
    ]);
    
    // PAKAI JAVASCRIPT, BUKAN HEADER()
    echo "
    <script>
        alert('Kegiatan berhasil ditambahkan');
        window.location='index.php?url=kegiatan';
    </script>
    ";
    exit;
}
?>

<style>
.kegiatan-form-container { padding: 15px 0; }

/* Header */
.kegiatan-form-header {
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

.kegiatan-form-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.kegiatan-form-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.kegiatan-form-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

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

.btn-light {
    background: #f0f4f8;
    border: none;
    color: #4a5568;
}

.btn-light:hover {
    background: #e2e8f0;
    color: #1a2634;
}

.alert-info-custom {
    border-radius: 10px;
    border: none;
    background: #e8f0fe;
    color: #1a2634;
    padding: 14px 18px;
    margin-top: 16px;
}

.alert-info-custom i {
    color: #2c6b9e;
    margin-right: 8px;
}

@media (max-width: 768px) {
    .kegiatan-form-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
}
</style>

<div class="kegiatan-form-container">

    <!-- HEADER -->
    <div class="kegiatan-form-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-calendar-plus"></i>
                Tambah Kegiatan Posyandu
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Buat jadwal kegiatan baru di Posyandu Bougenvil Belik
            </div>
        </div>
        <div>
            <a href="index.php?url=kegiatan" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- FORM -->
    <div class="card-form">
        <div class="card-header-custom">
            <i class="fas fa-info-circle"></i> Informasi Kegiatan
        </div>
        <div class="card-body-custom">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Kegiatan <span style="color: #dc2626;">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pertemuan Ke <span style="color: #dc2626;">*</span></label>
                            <input type="number" name="pertemuan_ke" class="form-control" placeholder="Contoh: 1" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Lokasi Kegiatan <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Posyandu Bougenvil" required>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" placeholder="Masukkan keterangan kegiatan..."></textarea>
                </div>

                <div class="alert-info-custom">
                    <i class="fas fa-info-circle"></i>
                    <strong>Informasi</strong>
                    <br>
                    Setelah kegiatan dibuat, pencatatan kehadiran, pemeriksaan, dan imunisasi dilakukan melalui halaman detail kegiatan.
                </div>

                <div class="text-right mt-4" style="border-top: 1px solid #edf2f7; padding-top: 20px;">
                    <a href="index.php?url=kegiatan" class="btn btn-light mr-2">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" name="simpan" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Kegiatan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>