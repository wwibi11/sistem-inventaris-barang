<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM anak WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "
    <script>
        alert('Data tidak ditemukan');
        window.parent.location='index.php?url=anak';
    </script>
    ";
    exit;
}

if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("
        UPDATE anak SET
            id_keluarga = ?, nik = ?, nama = ?, tempat_lahir = ?,
            tanggal_lahir = ?, jenis_kelamin = ?, anak_ke = ?,
            berat_lahir = ?, panjang_lahir = ?, nama_ayah = ?,
            nama_ibu = ?, status = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $_POST['id_keluarga'] ?? '',
        $_POST['nik'] ?? '',
        $_POST['nama'] ?? '',
        $_POST['tempat_lahir'] ?? '',
        $_POST['tanggal_lahir'] ?? '',
        $_POST['jenis_kelamin'] ?? '',
        $_POST['anak_ke'] ?? '',
        $_POST['berat_lahir'] ?? '',
        $_POST['panjang_lahir'] ?? '',
        $_POST['nama_ayah'] ?? '',
        $_POST['nama_ibu'] ?? '',
        $_POST['status'] ?? 'aktif',
        $id
    ]);

    echo "
    <script>
        alert('Data anak berhasil diupdate');
        window.parent.location='index.php?url=anak';
    </script>
    ";
    exit;
}

$keluarga = $pdo->query("
    SELECT * FROM keluarga ORDER BY nama_kepala_keluarga ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anak</title>
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

        @media (max-width: 768px) {
            .form-wrapper { padding: 20px; }
            .form-title { font-size: 17px; }
        }
    </style>
</head>
<body>

<div class="form-wrapper">
    <div class="form-title">
        <i class="fas fa-edit" style="color: #e8a317;"></i> Edit Anak
    </div>
    <div class="form-subtitle">
        <i class="fas fa-chevron-right" style="font-size: 10px; color: #8a94a6;"></i> 
        Edit data anak di Posyandu Bougenvil Belik
    </div>

    <form method="POST">

        <!-- DATA KELUARGA -->
        <div class="section-label"><i class="fas fa-users"></i> Data Keluarga</div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Keluarga <span class="required">*</span></label>
                    <select id="id_keluarga" name="id_keluarga" class="custom-select" required>
                        <option value="">-- Pilih Keluarga --</option>
                        <?php foreach($keluarga as $k): ?>
                        <option value="<?= $k['id'] ?>" 
                                data-ayah="<?= htmlspecialchars($k['nama_ayah']) ?>" 
                                data-ibu="<?= htmlspecialchars($k['nama_ibu']) ?>"
                                <?= $data['id_keluarga'] == $k['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama_kepala_keluarga']) ?> (KK: <?= htmlspecialchars($k['no_kk']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>NIK Anak</label>
                    <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($data['nik']) ?>">
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- DATA ANAK -->
        <div class="section-label"><i class="fas fa-child"></i> Data Anak</div>
        <div class="form-group">
            <label>Nama Anak <span class="required">*</span></label>
            <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($data['nama']) ?>">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="<?= htmlspecialchars($data['tempat_lahir']) ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($data['tanggal_lahir']) ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Jenis Kelamin <span class="required">*</span></label>
                    <select name="jenis_kelamin" class="custom-select" required>
                        <option value="L" <?= $data['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $data['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Anak Ke</label>
                    <input type="number" name="anak_ke" class="form-control" value="<?= htmlspecialchars($data['anak_ke']) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="custom-select">
                        <option value="aktif" <?= $data['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="pindah" <?= $data['status'] == 'pindah' ? 'selected' : '' ?>>Pindah</option>
                        <option value="meninggal" <?= $data['status'] == 'meninggal' ? 'selected' : '' ?>>Meninggal</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Berat Lahir (Kg)</label>
                    <input type="number" step="0.01" name="berat_lahir" class="form-control" value="<?= htmlspecialchars($data['berat_lahir']) ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Panjang Lahir (Cm)</label>
                    <input type="number" step="0.01" name="panjang_lahir" class="form-control" value="<?= htmlspecialchars($data['panjang_lahir']) ?>">
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- ORANG TUA -->
        <div class="section-label"><i class="fas fa-user-friends"></i> Data Orang Tua</div>
        
        <div class="form-group">
            <label>Sumber Data Orang Tua</label>
            <div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="pakai_kk" name="sumber_orangtua" value="kk" class="custom-control-input" checked>
                    <label class="custom-control-label" for="pakai_kk">Sesuaikan Kartu Keluarga</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="manual" name="sumber_orangtua" value="manual" class="custom-control-input">
                    <label class="custom-control-label" for="manual">Input Manual</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Ayah</label>
                    <input type="text" id="nama_ayah" name="nama_ayah" class="form-control" value="<?= htmlspecialchars($data['nama_ayah']) ?>" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Ibu</label>
                    <input type="text" id="nama_ibu" name="nama_ibu" class="form-control" value="<?= htmlspecialchars($data['nama_ibu']) ?>" readonly>
                </div>
            </div>
        </div>

        <!-- BUTTON -->
        <div class="text-right mt-4" style="border-top: 1px solid #edf2f7; padding-top: 20px;">
            <button type="button" class="btn btn-secondary mr-2" onclick="window.parent.location='index.php?url=anak'">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" name="update" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Data
            </button>
        </div>

    </form>
</div>

<script>
    const keluarga = document.getElementById('id_keluarga');
    const ayah = document.getElementById('nama_ayah');
    const ibu = document.getElementById('nama_ibu');

    function loadKK() {
        let selected = keluarga.options[keluarga.selectedIndex];
        ayah.value = selected.dataset.ayah || '';
        ibu.value = selected.dataset.ibu || '';
    }

    keluarga.addEventListener('change', function() {
        if (document.getElementById('pakai_kk').checked) loadKK();
    });

    document.getElementById('pakai_kk').addEventListener('change', function() {
        ayah.readOnly = true;
        ibu.readOnly = true;
        loadKK();
    });

    document.getElementById('manual').addEventListener('change', function() {
        ayah.readOnly = false;
        ibu.readOnly = false;
        ayah.value = '';
        ibu.value = '';
    });

    window.onload = function() { loadKK(); };
</script>

</body>
</html>