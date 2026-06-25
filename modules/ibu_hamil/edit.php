<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM ibu_hamil WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "
    <script>
        alert('Data tidak ditemukan');
        window.parent.location='index.php?url=ibu_hamil';
    </script>
    ";
    exit;
}

// ==========================
// UPDATE DATA
// ==========================
if (isset($_POST['update'])) {
    // Validasi
    $errors = [];
    
    if (empty($_POST['id_keluarga'])) {
        $errors[] = "Keluarga harus dipilih";
    }
    if (empty($_POST['nama_ibu'])) {
        $errors[] = "Nama ibu harus diisi";
    }
    
    if (empty($errors)) {
        // Hitung trimester dari usia kehamilan
        $usia_kehamilan = !empty($_POST['usia_kehamilan']) ? (int)$_POST['usia_kehamilan'] : 0;
        
        $stmt = $pdo->prepare("
            UPDATE ibu_hamil SET
                id_keluarga = ?,
                nik = ?,
                nama_ibu = ?,
                tempat_lahir = ?,
                tanggal_lahir = ?,
                hamil_ke = ?,
                usia_kehamilan = ?,
                hpht = ?,
                hpl = ?,
                no_hp = ?,
                status = ?
            WHERE id = ?
        ");

        $result = $stmt->execute([
            $_POST['id_keluarga'],
            !empty($_POST['nik']) ? $_POST['nik'] : null,
            $_POST['nama_ibu'],
            !empty($_POST['tempat_lahir']) ? $_POST['tempat_lahir'] : null,
            !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null,
            !empty($_POST['hamil_ke']) ? (int)$_POST['hamil_ke'] : 1,
            $usia_kehamilan,
            !empty($_POST['hpht']) ? $_POST['hpht'] : null,
            !empty($_POST['hpl']) ? $_POST['hpl'] : null,
            !empty($_POST['no_hp']) ? $_POST['no_hp'] : null,
            $_POST['status'] ?? 'Aktif',
            $id
        ]);

        if ($result) {
            echo "
            <script>
                alert('Data ibu hamil berhasil diupdate');
                window.parent.location='index.php?url=ibu_hamil';
            </script>
            ";
        } else {
            echo "
            <script>
                alert('Gagal mengupdate data. Silahkan coba lagi.');
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

// ==========================
// DATA KELUARGA
// ==========================
$keluarga = $pdo->query("
    SELECT * FROM keluarga ORDER BY nama_kepala_keluarga ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ibu Hamil</title>
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
            max-width: 1000px;
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

        .form-control:disabled {
            background: #f3f4f6;
            color: #4a5568;
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

        .required { color: #dc2626; }
        .help-text {
            font-size: 11px;
            color: #8a94a6;
            margin-top: 4px;
        }

        .help-text i {
            color: #2c6b9e;
        }

        .hpl-result {
            background: #f0f7ff;
            border: 1px solid #b3d4f0;
            border-radius: 8px;
            padding: 8px 14px;
            margin-top: 6px;
            font-size: 13px;
            color: #1a2634;
        }

        .hpl-result strong {
            color: #2c6b9e;
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
        <i class="fas fa-edit" style="color: #e8a317;"></i> Edit Ibu Hamil
    </div>
    <div class="form-subtitle">
        <i class="fas fa-chevron-right" style="font-size: 10px; color: #8a94a6;"></i> 
        Edit data ibu hamil di Posyandu Bougenvil Belik
    </div>

    <form method="POST">

        <!-- DATA KELUARGA -->
        <div class="section-label"><i class="fas fa-users"></i> Data Keluarga</div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Pilih Keluarga <span class="required">*</span></label>
                    <select name="id_keluarga" class="custom-select" required>
                        <option value="">-- Pilih Keluarga --</option>
                        <?php foreach($keluarga as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $data['id_keluarga'] == $k['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama_kepala_keluarga']) ?> (KK: <?= htmlspecialchars($k['no_kk']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>NIK Ibu</label>
                    <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($data['nik'] ?? '') ?>" maxlength="30" placeholder="Masukkan NIK ibu">
                    <div class="help-text">Maksimal 30 karakter</div>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- DATA PRIBADI IBU -->
        <div class="section-label"><i class="fas fa-user"></i> Data Pribadi Ibu</div>
        <div class="form-group">
            <label>Nama Lengkap Ibu <span class="required">*</span></label>
            <input type="text" name="nama_ibu" class="form-control" required value="<?= htmlspecialchars($data['nama_ibu']) ?>" maxlength="100" placeholder="Masukkan nama lengkap ibu">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="<?= htmlspecialchars($data['tempat_lahir'] ?? '') ?>" maxlength="100" placeholder="Masukkan tempat lahir">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($data['tanggal_lahir'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Hamil Ke</label>
                    <input type="number" name="hamil_ke" class="form-control" value="<?= htmlspecialchars($data['hamil_ke'] ?? 1) ?>" min="1" placeholder="1">
                    <div class="help-text">Anak ke berapa kehamilan ini</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>No HP / Telepon</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($data['no_hp'] ?? '') ?>" maxlength="20" placeholder="Masukkan nomor HP">
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- DATA KEHAMILAN -->
        <div class="section-label"><i class="fas fa-person-pregnant"></i> Data Kehamilan</div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>HPHT (Hari Pertama Haid Terakhir)</label>
                    <input type="date" name="hpht" class="form-control" id="hpht" value="<?= htmlspecialchars($data['hpht'] ?? '') ?>">
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i> 
                        Tanggal hari pertama haid terakhir
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>HPL (Hari Perkiraan Lahir)</label>
                    <input type="date" name="hpl" class="form-control" id="hpl" value="<?= htmlspecialchars($data['hpl'] ?? '') ?>" readonly>
                    <div class="help-text">
                        <i class="fas fa-sync-alt"></i> 
                        Otomatis terisi dari HPHT (<strong>Rumus Naegele</strong>)
                    </div>
                    <div id="hplInfo" class="hpl-result" style="<?= !empty($data['hpl']) ? 'display: block;' : 'display: none;' ?>">
                        <i class="fas fa-calendar-check"></i> 
                        HPL: <strong id="hplDisplay"><?= !empty($data['hpl']) ? date('d F Y', strtotime($data['hpl'])) : '-' ?></strong>
                        <span style="font-size: 12px; color: #8a94a6; margin-left: 10px;">
                            (HPHT + 280 hari)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Usia Kehamilan (Minggu)</label>
                    <input type="number" name="usia_kehamilan" class="form-control" id="usia_kehamilan" value="<?= htmlspecialchars($data['usia_kehamilan'] ?? 0) ?>" min="0" max="42" placeholder="0">
                    <div class="help-text">Usia kehamilan dalam minggu</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Trimester (Otomatis)</label>
                    <input type="text" class="form-control" id="trimester_display" disabled 
                           value="<?php 
                                $usia = $data['usia_kehamilan'] ?? 0;
                                if ($usia <= 0) echo 'Belum masuk';
                                elseif ($usia <= 13) echo 'Trimester 1 (0-13 minggu)';
                                elseif ($usia <= 27) echo 'Trimester 2 (14-27 minggu)';
                                elseif ($usia <= 42) echo 'Trimester 3 (28-42 minggu)';
                                else echo '> 42 minggu (telah lewat)';
                           ?>">
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i> 
                        Trimester 1: 0-13 minggu | Trimester 2: 14-27 minggu | Trimester 3: 28-42 minggu
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status <span class="required">*</span></label>
                    <select name="status" class="custom-select" required>
                        <option value="Aktif" <?= ($data['status'] ?? 'Aktif') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Melahirkan" <?= ($data['status'] ?? '') == 'Melahirkan' ? 'selected' : '' ?>>Melahirkan</option>
                        <option value="Pindah" <?= ($data['status'] ?? '') == 'Pindah' ? 'selected' : '' ?>>Pindah</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- BUTTON -->
        <div class="text-right mt-4" style="border-top: 1px solid #edf2f7; padding-top: 20px;">
            <button type="button" class="btn btn-secondary mr-2" onclick="window.parent.location='index.php?url=ibu_hamil'">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" name="update" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Data
            </button>
        </div>

    </form>
</div>

<script>
// ==========================================
// FUNGSI HITUNG HPL DARI HPHT
// ==========================================
function hitungHPL(hphtDate) {
    if (!hphtDate) return null;
    
    // Parse tanggal
    const parts = hphtDate.split('-');
    const hpht = new Date(parts[0], parts[1] - 1, parts[2]);
    
    // Tambah 280 hari (40 minggu)
    const hpl = new Date(hpht);
    hpl.setDate(hpl.getDate() + 280);
    
    // Format ke YYYY-MM-DD
    const year = hpl.getFullYear();
    const month = String(hpl.getMonth() + 1).padStart(2, '0');
    const day = String(hpl.getDate()).padStart(2, '0');
    
    return year + '-' + month + '-' + day;
}

// ==========================================
// FUNGSI FORMAT TANGGAL KE INDONESIA
// ==========================================
function formatTanggal(dateStr) {
    if (!dateStr) return '-';
    const parts = dateStr.split('-');
    const date = new Date(parts[0], parts[1] - 1, parts[2]);
    const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return date.getDate() + ' ' + bulan[date.getMonth()] + ' ' + date.getFullYear();
}

// ==========================================
// EVENT HPHT CHANGE
// ==========================================
document.getElementById('hpht').addEventListener('change', function() {
    const hphtValue = this.value;
    const hplInput = document.getElementById('hpl');
    const hplInfo = document.getElementById('hplInfo');
    const hplDisplay = document.getElementById('hplDisplay');
    
    if (hphtValue) {
        // Hitung HPL
        const hpl = hitungHPL(hphtValue);
        if (hpl) {
            hplInput.value = hpl;
            hplDisplay.textContent = formatTanggal(hpl);
            hplInfo.style.display = 'block';
            
            // Hitung usia kehamilan dari HPHT
            hitungUsiaKehamilan(hphtValue);
        }
    } else {
        hplInput.value = '';
        hplInfo.style.display = 'none';
    }
});

// ==========================================
// HITUNG USIA KEHAMILAN DARI HPHT
// ==========================================
function hitungUsiaKehamilan(hphtDate) {
    const sekarang = new Date();
    const parts = hphtDate.split('-');
    const hpht = new Date(parts[0], parts[1] - 1, parts[2]);
    
    // Selisih hari
    const diffTime = sekarang - hpht;
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    const minggu = Math.floor(diffDays / 7);
    
    if (minggu >= 0 && minggu <= 42) {
        document.getElementById('usia_kehamilan').value = minggu;
        updateTrimester(minggu);
    }
}

// ==========================================
// UPDATE TRIMESTER
// ==========================================
function updateTrimester(usia) {
    const trimesterDisplay = document.getElementById('trimester_display');
    let trimesterText = '';
    
    if (usia <= 0) {
        trimesterText = 'Belum masuk';
    } else if (usia <= 13) {
        trimesterText = 'Trimester 1 (0-13 minggu)';
    } else if (usia <= 27) {
        trimesterText = 'Trimester 2 (14-27 minggu)';
    } else if (usia <= 42) {
        trimesterText = 'Trimester 3 (28-42 minggu)';
    } else {
        trimesterText = '> 42 minggu (telah lewat)';
    }
    
    trimesterDisplay.value = trimesterText;
}

// ==========================================
// EVENT USIA KEHAMILAN CHANGE
// ==========================================
document.getElementById('usia_kehamilan').addEventListener('input', function() {
    const usia = parseInt(this.value) || 0;
    updateTrimester(usia);
});

// ==========================================
// INITIAL - CEK APAKAH ADA HPHT AWAL
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    // Jika ada hpht awal, langsung hitung
    const hphtInput = document.getElementById('hpht');
    if (hphtInput.value) {
        hphtInput.dispatchEvent(new Event('change'));
    }
});
</script>

</body>
</html>