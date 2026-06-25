<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// SIMPAN DATA
// ==========================
if (isset($_POST['simpan'])) {
    // Validasi
    $errors = [];
    
    if (empty($_POST['kategori'])) {
        $errors[] = "Kategori harus dipilih";
    }
    if (empty($_POST['nama_imunisasi'])) {
        $errors[] = "Nama imunisasi harus diisi";
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO master_imunisasi (
                kategori, nama_imunisasi, keterangan
            ) VALUES (?, ?, ?)
        ");

        $result = $stmt->execute([
            $_POST['kategori'],
            $_POST['nama_imunisasi'],
            !empty($_POST['keterangan']) ? $_POST['keterangan'] : null
        ]);

        if ($result) {
            echo "
            <script>
                alert('Data imunisasi berhasil ditambahkan');
                window.parent.location='index.php?url=master_imunisasi';
            </script>
            ";
        } else {
            echo "
            <script>
                alert('Gagal menambahkan data. Silahkan coba lagi.');
                window.history.back();
            </script>
            ";
        }
        exit;
    } else {
        $error_message = implode("\\n", $errors);
        echo "
        <script>
            alert('" . $error_message . "');
            window.history.back();
        </script>
        ";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Imunisasi</title>
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
        
        .sidebar, .main-sidebar, .left-side, .navbar,
        .main-header, .content-header, .footer, .main-footer {
            display: none !important;
        }

        .content-wrapper, .main-content, #wrapper,
        #content-wrapper, .content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            min-height: auto !important;
            background: #ffffff !important;
        }

        .form-wrapper {
            padding: 30px 35px;
            max-width: 800px;
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

        .form-control-textarea {
            min-height: 100px;
            resize: vertical;
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

        .required { color: #dc2626; }
        .help-text {
            font-size: 11px;
            color: #8a94a6;
            margin-top: 4px;
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
        <i class="fas fa-plus-circle" style="color: #2c6b9e;"></i> Tambah Imunisasi
    </div>
    <div class="form-subtitle">
        <i class="fas fa-chevron-right" style="font-size: 10px; color: #8a94a6;"></i> 
        Tambah jenis imunisasi baru untuk anak atau ibu hamil
    </div>

    <form method="POST">

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Kategori <span class="required">*</span></label>
                    <select name="kategori" class="custom-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Anak">Anak</option>
                        <option value="Ibu Hamil">Ibu Hamil</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Imunisasi <span class="required">*</span></label>
                    <input type="text" name="nama_imunisasi" class="form-control" required placeholder="Masukkan nama imunisasi" maxlength="100">
                    <div class="help-text">Contoh: BCG, Polio, TT 1, dll</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control form-control-textarea" placeholder="Masukkan keterangan (opsional)"></textarea>
            <div class="help-text">Informasi tambahan tentang imunisasi ini</div>
        </div>

        <!-- BUTTON -->
        <div class="text-right mt-4" style="border-top: 1px solid #edf2f7; padding-top: 20px;">
            <button type="button" class="btn btn-secondary mr-2" onclick="window.parent.location='index.php?url=master_imunisasi'">
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