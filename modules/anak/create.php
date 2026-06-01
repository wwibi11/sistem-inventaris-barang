<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// SIMPAN DATA
// ==========================
if (isset($_POST['simpan'])) {

    // Ambil data keluarga
    $qKeluarga = $pdo->prepare("
        SELECT nama_ayah, nama_ibu
        FROM keluarga
        WHERE id = ?
    ");

    $qKeluarga->execute([
        $_POST['id_keluarga']
    ]);

    $kel = $qKeluarga->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        INSERT INTO anak
        (
            id_keluarga,
            nik,
            nama,
            tempat_lahir,
            tanggal_lahir,
            jenis_kelamin,
            anak_ke,
            berat_lahir,
            panjang_lahir,
            nama_ayah,
            nama_ibu,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([

        $_POST['id_keluarga'],
        $_POST['nik'] ?: null,
        $_POST['nama'],
        $_POST['tempat_lahir'] ?: null,
        $_POST['tanggal_lahir'] ?: null,
        $_POST['jenis_kelamin'] ?: null,
        $_POST['anak_ke'] ?: null,
        $_POST['berat_lahir'] ?: null,
        $_POST['panjang_lahir'] ?: null,

        $kel['nama_ayah'] ?? null,
        $kel['nama_ibu'] ?? null,

        $_POST['status'] ?? 'aktif'
    ]);

    echo "
    <script>
        alert('Data anak berhasil ditambahkan');
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

<title>Tambah Anak</title>

<link
  rel="stylesheet"
  href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<style>

html,
body{
  background:#fff !important;
  margin:0;
  padding:0;
  overflow-x:hidden;
  font-size:12px;
  font-family:sans-serif;
}

/* HILANGKAN TEMPLATE */
.sidebar,
.main-sidebar,
.left-side,
.navbar,
.main-header,
.content-header,
.footer,
.main-footer{
  display:none !important;
}

/* FULL WIDTH */
.content-wrapper,
.main-content,
#wrapper,
#content-wrapper,
.content{
  margin:0 !important;
  padding:0 !important;
  width:100% !important;
  min-height:auto !important;
  background:#fff !important;
}

/* FORM */
.form-wrapper{
  padding:20px;
}

.form-control,
.custom-select{
  border-radius:8px;
  font-size:12px;
}

.btn{
  border-radius:8px;
  font-size:12px;
}

label{
  font-weight:600;
  margin-bottom:6px;
}

</style>

</head>

<body>

<div class="form-wrapper">

  <form method="POST">

    <div class="row">

      <div class="col-md-6">

        <div class="form-group">

          <label>Keluarga</label>

          <select
            name="id_keluarga"
            class="custom-select"
            required>

            <option value="">
              -- Pilih Keluarga --
            </option>

            <?php foreach($keluarga as $k): ?>

            <option value="<?= $k['id'] ?>">

              <?= htmlspecialchars($k['nama_kepala_keluarga']) ?>

            </option>

            <?php endforeach; ?>

          </select>

        </div>

      </div>

      <div class="col-md-6">

        <div class="form-group">

          <label>NIK Anak</label>

          <input
            type="text"
            name="nik"
            class="form-control">

        </div>

      </div>

    </div>

    <div class="form-group">

      <label>Nama Anak</label>

      <input
        type="text"
        name="nama"
        class="form-control"
        required>

    </div>

    <div class="row">

      <div class="col-md-6">

        <div class="form-group">

          <label>Tempat Lahir</label>

          <input
            type="text"
            name="tempat_lahir"
            class="form-control">

        </div>

      </div>

      <div class="col-md-6">

        <div class="form-group">

          <label>Tanggal Lahir</label>

          <input
            type="date"
            name="tanggal_lahir"
            class="form-control">

        </div>

      </div>

    </div>

    <div class="row">

      <div class="col-md-4">

        <div class="form-group">

          <label>Jenis Kelamin</label>

          <select
            name="jenis_kelamin"
            class="custom-select">

            <option value="L">
              Laki-laki
            </option>

            <option value="P">
              Perempuan
            </option>

          </select>

        </div>

      </div>

      <div class="col-md-4">

        <div class="form-group">

          <label>Anak Ke</label>

          <input
            type="number"
            name="anak_ke"
            class="form-control">

        </div>

      </div>

      <div class="col-md-4">

        <div class="form-group">

          <label>Status</label>

          <select
            name="status"
            class="custom-select">

            <option value="aktif">
              Aktif
            </option>

            <option value="pindah">
              Pindah
            </option>

            <option value="meninggal">
              Meninggal
            </option>

          </select>

        </div>

      </div>

    </div>

    <div class="row">

      <div class="col-md-6">

        <div class="form-group">

          <label>Berat Lahir (Kg)</label>

          <input
            type="number"
            step="0.01"
            name="berat_lahir"
            class="form-control">

        </div>

      </div>

      <div class="col-md-6">

        <div class="form-group">

          <label>Panjang Lahir (Cm)</label>

          <input
            type="number"
            step="0.01"
            name="panjang_lahir"
            class="form-control">

        </div>

      </div>

    </div>

   <div class="row">

  <div class="col-md-6">

    <div class="form-group">

      <label>Nama Ayah</label>

      <input
        type="text"
        name="nama_ayah"
        class="form-control">

    </div>

  </div>

  <div class="col-md-6">

    <div class="form-group">

      <label>Nama Ibu</label>

      <input
        type="text"
        name="nama_ibu"
        class="form-control">

    </div>

  </div>

</div>

    <div class="text-right mt-4">

      <button
        type="submit"
        name="simpan"
        class="btn btn-primary px-4">

        Simpan

      </button>

    </div>

  </form>

</div>

</body>
</html>