<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// SIMPAN DATA
// ==========================
if (isset($_POST['simpan'])) {
    $stmt = $pdo->prepare("
        INSERT INTO keluarga (
            no_kk, nama_kepala_keluarga, nik_ayah, nama_ayah,
            nik_ibu, nama_ibu, alamat, rt, rw, desa, kecamatan, no_hp
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['no_kk'] ?? '',
        $_POST['nama_kepala_keluarga'] ?? '',
        $_POST['nik_ayah'] ?? '',
        $_POST['nama_ayah'] ?? '',
        $_POST['nik_ibu'] ?? '',
        $_POST['nama_ibu'] ?? '',
        $_POST['alamat'] ?? '',
        $_POST['rt'] ?? '',
        $_POST['rw'] ?? '',
        $_POST['desa'] ?? '',
        $_POST['kecamatan'] ?? '',
        $_POST['no_hp'] ?? ''
    ]);

    echo "
    <script>
        alert('Data keluarga berhasil ditambahkan');
        window.parent.location='index.php?url=keluarga';
    </script>
    ";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Keluarga</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            background: #ffffff !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            padding: 0;
            margin: 0;
        }
        
        /* Hilangkan template */
        .sidebar, .main-sidebar, .left-side, .navbar, .main-header,
        .content-header, .footer, .main-footer { display: none !important; }
        
        .content-wrapper, .main-content, #wrapper, #content-wrapper, .content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            min-height: auto !important;
            background: #ffffff !important;
        }

        .form-wrapper {
            padding: 30px 35px;
            max-width: 900px;
            margin: 0 auto;
        }

        .form-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a2634;
            margin-bottom: 6px;
        }
        
        .form-subtitle {
            font-size: 13px;
            color: #8a94a6;
            margin-bottom: 24px;
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
        }

        .form-control:focus {
            border-color: #2c6b9e;
            box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
            background: #ffffff;
        }

        .form-control::placeholder {
            color: #a0aec0;
            font-size: 12px;
        }

        .btn {
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 28px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #2c6b9e;
            border: none;
        }

        .btn-primary:hover {
            background: #1f507a;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(44, 107, 158, 0.25);
        }

        .btn-secondary {
            background: #f0f4f8;
            border: none;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .section-divider {
            border-top: 1px solid #edf2f7;
            margin: 20px 0;
        }

        .section-label {
            font-size: 14px;
            font-weight: 600;
            color: #1a2634;
            margin-bottom: 16px;
        }

        .section-label i {
            color: #2c6b9e;
            margin-right: 8px;
        }

        .required {
            color: #dc2626;
        }

        @media (max-width: 768px) {
            .form-wrapper { padding: 20px; }
            .form-title { font-size: 17px; }
        }
    </style>
</head>
<body>

<div class="form-wrapper">
    <div class="form-title">
        <i class="fas fa-plus-circle" style="color: #28a745;"></i> Tambah Keluarga
    </div>
    <div class="form-subtitle">
        <i class="fas fa-chevron-right" style="font-size: 10px; color: #8a94a6;"></i> 
        Tambah data keluarga baru ke Posyandu Bougenvil Belik
    </div>

    <form method="POST">

        <!-- ==================== -->
        <!-- DATA KELUARGA -->
        <!-- ==================== -->
        <div class="section-label"><i class="fas fa-users"></i> Data Keluarga</div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>No KK <span class="required">*</span></label>
                    <input type="text" name="no_kk" class="form-control" required placeholder="Masukkan No KK">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Kepala Keluarga <span class="required">*</span></label>
                    <input type="text" name="nama_kepala_keluarga" class="form-control" required placeholder="Nama Kepala Keluarga">
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- ==================== -->
        <!-- DATA AYAH -->
        <!-- ==================== -->
        <div class="section-label"><i class="fas fa-male" style="color: #2c6b9e;"></i> Data Ayah</div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>NIK Ayah</label>
                    <input type="text" name="nik_ayah" class="form-control" placeholder="NIK Ayah">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Ayah</label>
                    <input type="text" name="nama_ayah" class="form-control" placeholder="Nama Ayah">
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- ==================== -->
        <!-- DATA IBU -->
        <!-- ==================== -->
        <div class="section-label"><i class="fas fa-female" style="color: #e8a317;"></i> Data Ibu</div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>NIK Ibu</label>
                    <input type="text" name="nik_ibu" class="form-control" placeholder="NIK Ibu">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Ibu</label>
                    <input type="text" name="nama_ibu" class="form-control" placeholder="Nama Ibu">
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- ==================== -->
        <!-- ALAMAT -->
        <!-- ==================== -->
        <div class="section-label"><i class="fas fa-map-marker-alt" style="color: #dc3545;"></i> Alamat</div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" rows="3" class="form-control" placeholder="Alamat lengkap"></textarea>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>RT</label>
                    <input type="text" name="rt" class="form-control" placeholder="RT">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>RW</label>
                    <input type="text" name="rw" class="form-control" placeholder="RW">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Desa</label>
                    <input type="text" name="desa" class="form-control" placeholder="Desa/Kelurahan">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Kecamatan</label>
                    <input type="text" name="kecamatan" class="form-control" placeholder="Kecamatan">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="Nomor HP">
                </div>
            </div>
        </div>

        <!-- ==================== -->
        <!-- BUTTON -->
        <!-- ==================== -->
        <div class="text-right mt-4" style="border-top: 1px solid #edf2f7; padding-top: 20px;">
            <button type="button" class="btn btn-secondary mr-2" onclick="window.parent.location='index.php?url=keluarga'">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" name="simpan" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Data
            </button>
        </div>

    </form>
</div>

</body>
</html>