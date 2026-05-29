<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// SIMPAN DATA
// ==========================
if (isset($_POST['simpan'])) {

  $stmt = $pdo->prepare("
    INSERT INTO keluarga (
      no_kk,
      nama_kepala_keluarga,
      nik_ayah,
      nama_ayah,
      nik_ibu,
      nama_ibu,
      alamat,
      rt,
      rw,
      desa,
      kecamatan,
      no_hp
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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

.form-control{
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

    <div class="form-group">

      <label>No KK</label>

      <input
        type="text"
        name="no_kk"
        class="form-control"
        required>

    </div>

    <div class="form-group">

      <label>Kepala Keluarga</label>

      <input
        type="text"
        name="nama_kepala_keluarga"
        class="form-control"
        required>

    </div>

    <div class="row">

      <div class="col-md-6">

        <div class="form-group">

          <label>NIK Ayah</label>

          <input
            type="text"
            name="nik_ayah"
            class="form-control">

        </div>

      </div>

      <div class="col-md-6">

        <div class="form-group">

          <label>Nama Ayah</label>

          <input
            type="text"
            name="nama_ayah"
            class="form-control">

        </div>

      </div>

    </div>

    <div class="row">

      <div class="col-md-6">

        <div class="form-group">

          <label>NIK Ibu</label>

          <input
            type="text"
            name="nik_ibu"
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

    <div class="form-group">

      <label>Alamat</label>

      <textarea
        name="alamat"
        rows="3"
        class="form-control"></textarea>

    </div>

    <div class="row">

      <div class="col-md-3">

        <div class="form-group">

          <label>RT</label>

          <input
            type="text"
            name="rt"
            class="form-control">

        </div>

      </div>

      <div class="col-md-3">

        <div class="form-group">

          <label>RW</label>

          <input
            type="text"
            name="rw"
            class="form-control">

        </div>

      </div>

      <div class="col-md-6">

        <div class="form-group">

          <label>Desa</label>

          <input
            type="text"
            name="desa"
            class="form-control">

        </div>

      </div>

    </div>

    <div class="form-group">

      <label>Kecamatan</label>

      <input
        type="text"
        name="kecamatan"
        class="form-control">

    </div>

    <div class="form-group">

      <label>No HP</label>

      <input
        type="text"
        name="no_hp"
        class="form-control">

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