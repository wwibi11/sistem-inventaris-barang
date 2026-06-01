<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// AMBIL DATA
// ==========================
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
  SELECT *
  FROM anak
  WHERE id = ?
");

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

// ==========================
// UPDATE DATA
// ==========================
if (isset($_POST['update'])) {

  $stmt = $pdo->prepare("
    UPDATE anak
    SET
      id_keluarga   = ?,
      nik           = ?,
      nama          = ?,
      tempat_lahir  = ?,
      tanggal_lahir = ?,
      jenis_kelamin = ?,
      anak_ke       = ?,
      berat_lahir   = ?,
      panjang_lahir = ?,
      nama_ayah     = ?,
      nama_ibu      = ?,
      status        = ?
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

// ==========================
// DATA KELUARGA
// ==========================
$keluarga = $pdo->query("
  SELECT *
  FROM keluarga
  ORDER BY nama_kepala_keluarga ASC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anak</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            background: #fff !important;
            margin: 0;
            padding: 0;
            font-size: 13px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* HILANGKAN TEMPLATE */
        .sidebar, .main-sidebar, .left-side, .navbar,
        .main-header, .content-header, .footer, .main-footer {
            display: none !important;
        }

        /* FULL WIDTH - tanpa margin/padding */
        .content-wrapper, .main-content, #wrapper,
        #content-wrapper, .content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background: #fff !important;
        }

        /* FORM WRAPPER - full width, tanpa ruang kosong */
        .form-wrapper {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 20px;
        }

        /* FORM CARD */
        .form-card {
            background: #fff;
            border: none;
            width: 100%;
        }

        /* FORM GROUP */
        .form-group {
            margin-bottom: 16px;
        }

        label {
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 13px;
            color: #333;
            display: block;
        }

        .form-control, .custom-select {
            border-radius: 6px;
            font-size: 13px;
            height: 38px;
            border: 1px solid #ddd;
            padding: 6px 12px;
            width: 100%;
        }

        .form-control:focus, .custom-select:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.1);
        }

        .custom-control-label {
            font-weight: normal;
            font-size: 13px;
        }

        .btn-primary {
            border-radius: 6px;
            font-size: 13px;
            padding: 8px 30px;
            font-weight: 600;
        }

        .text-right {
            text-align: right;
        }

        /* Hilangkan gap pada row */
        .row {
            margin-right: -10px;
            margin-left: -10px;
        }
        
        .col-md-6, .col-md-4 {
            padding-right: 10px;
            padding-left: 10px;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .form-wrapper {
                padding: 15px;
            }
            .btn-primary {
                width: 100%;
            }
            .text-right {
                text-align: center;
            }
            .row {
                margin-right: -8px;
                margin-left: -8px;
            }
            .col-md-6, .col-md-4 {
                padding-right: 8px;
                padding-left: 8px;
            }
        }
    </style>
</head>
<body>

<div class="form-wrapper">
    <div class="form-card">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Keluarga</label>
                        <select id="id_keluarga" name="id_keluarga" class="custom-select" required>
                            <option value="">-- Pilih Keluarga --</option>
                            <?php foreach($keluarga as $k): ?>
                            <option value="<?= $k['id'] ?>"
                                data-ayah="<?= htmlspecialchars($k['nama_ayah']) ?>"
                                data-ibu="<?= htmlspecialchars($k['nama_ibu']) ?>"
                                <?= $data['id_keluarga'] == $k['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama_kepala_keluarga']) ?>
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

            <div class="form-group">
                <label>Nama Anak</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
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
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="custom-select">
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

            <div class="text-right">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
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
        if(document.getElementById('pakai_kk').checked) {
            loadKK();
        }
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

    window.onload = function() {
        loadKK();
    };
</script>

</body>
</html>