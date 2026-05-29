<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// SIMPAN
// ==========================
if (isset($_POST['simpan'])) {

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
    $_POST['nik'],
    $_POST['nama'],
    $_POST['tempat_lahir'],
    $_POST['tanggal_lahir'],
    $_POST['jenis_kelamin'],
    $_POST['anak_ke'],
    $_POST['berat_lahir'],
    $_POST['panjang_lahir'],
    $_POST['nama_ayah'],
    $_POST['nama_ibu'],
    $_POST['status']
  ]);

  echo "
  <script>
    alert('Data anak berhasil ditambahkan');
    location='index.php?url=anak';
  </script>
  ";
}

// ==========================
// HAPUS
// ==========================
if (isset($_GET['hapus'])) {

  $stmt = $pdo->prepare("DELETE FROM anak WHERE id=?");
  $stmt->execute([$_GET['hapus']]);

  echo "
  <script>
    alert('Data berhasil dihapus');
    location='index.php?url=anak';
  </script>
  ";
}

// ==========================
// DATA KELUARGA
// ==========================
$keluarga = $pdo->query("
  SELECT *
  FROM keluarga
  ORDER BY nama_kepala_keluarga ASC
")->fetchAll();

// ==========================
// DATA ANAK
// ==========================
$data = $pdo->query("
  SELECT
    a.*,
    k.nama_kepala_keluarga
  FROM anak a
  LEFT JOIN keluarga k
    ON k.id = a.id_keluarga
  ORDER BY a.id DESC
")->fetchAll();
?>

<style>

.card-modern{
  border:none;
  border-radius:16px;
  overflow:hidden;
}

.shadow-soft{
  box-shadow:0 4px 18px rgba(0,0,0,.05);
}

.table-modern{
  margin-bottom:0;
}

.table-modern thead{
  background:#4e73df;
  color:white;
}

.table-modern th{
  border:none !important;
  padding:12px 10px !important;
  font-size:11px;
  white-space:nowrap;
}

.table-modern td{
  padding:12px 10px !important;
  vertical-align:middle !important;
  font-size:12px;
}

.table-modern tbody tr:hover{
  background:#f8faff;
}

.btn{
  border-radius:10px !important;
  font-size:12px;
}

.btn-icon{
  width:32px;
  height:32px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:8px;
}

.form-control,
.custom-select{
  border-radius:10px;
  font-size:12px;
  height:38px;
}

textarea.form-control{
  height:auto;
}

.modal-content{
  border:none;
  border-radius:18px;
  overflow:hidden;
}

.modal-header{
  background:linear-gradient(90deg,#4e73df,#657ff1);
  color:white;
  border:none;
}

.modal-title{
  font-size:15px;
  font-weight:700;
}

.badge-status{
  padding:6px 10px;
  border-radius:30px;
  font-size:10px;
  font-weight:700;
}

.badge-aktif{
  background:#d1fae5;
  color:#047857;
}

.badge-pindah{
  background:#fef3c7;
  color:#92400e;
}

.badge-meninggal{
  background:#fee2e2;
  color:#b91c1c;
}

.info-name{
  font-size:13px;
  font-weight:700;
  color:#2e3a59;
}

.small-text{
  font-size:11px;
}

</style>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-3">

    <div>

      <h1 class="h5 mb-1 text-gray-800">
        <i class="fas fa-child text-primary"></i>
        Data Anak
      </h1>

      <div class="text-muted small-text">
        Data anak peserta posyandu
      </div>

    </div>

    <button class="btn btn-primary shadow-sm"
            data-toggle="modal"
            data-target="#modalTambah">

      <i class="fas fa-plus mr-1"></i>
      Tambah Anak

    </button>

  </div>

  <!-- CARD -->
  <div class="card card-modern shadow-soft">

    <div class="card-body">

      <!-- SEARCH -->
      <div class="mb-3" style="max-width:260px;">

        <div class="input-group">

          <div class="input-group-prepend">
            <span class="input-group-text bg-white border-right-0">
              <i class="fas fa-search text-muted"></i>
            </span>
          </div>

          <input type="text"
                 id="searchInput"
                 class="form-control border-left-0"
                 placeholder="Cari anak...">

        </div>

      </div>

      <!-- TABLE -->
      <div class="table-responsive">

        <table class="table table-hover table-modern">

          <thead>
            <tr>
              <th>No</th>
              <th>Anak</th>
              <th>Keluarga</th>
              <th>Lahir</th>
              <th>JK</th>
              <th>Status</th>
              <th width="140">Aksi</th>
            </tr>
          </thead>

          <tbody id="tableBody">

            <?php
            $no = 1;
            foreach ($data as $d):
            ?>

            <tr>

              <td width="50">
                <strong><?= $no++ ?></strong>
              </td>

              <td style="min-width:220px;">

                <div class="info-name">
                  <?= $d['nama'] ?>
                </div>

                <div class="small-text text-muted">
                  NIK :
                  <?= $d['nik'] ?: '-' ?>
                </div>

              </td>

              <td>

                <?= $d['nama_kepala_keluarga'] ?: '-' ?>

              </td>

              <td width="160">

                <?= $d['tempat_lahir'] ?: '-' ?>

                <div class="small-text text-muted">
                  <?= $d['tanggal_lahir'] ?: '-' ?>
                </div>

              </td>

              <td width="90">

                <?=
                  $d['jenis_kelamin'] == 'L'
                  ? 'Laki-laki'
                  : 'Perempuan'
                ?>

              </td>

              <td width="100">

                <?php
                $status = $d['status'];

                $class = 'badge-aktif';

                if ($status == 'pindah') {
                  $class = 'badge-pindah';
                }

                if ($status == 'meninggal') {
                  $class = 'badge-meninggal';
                }
                ?>

                <span class="badge-status <?= $class ?>">
                  <?= ucfirst($status) ?>
                </span>

              </td>

              <td>

                <div class="d-flex">

                  <!-- VIEW -->
                  <button class="btn btn-info btn-sm btn-icon mr-1"
                          data-toggle="modal"
                          data-target="#view<?= $d['id'] ?>">

                    <i class="fas fa-eye"></i>

                  </button>

                  <!-- HAPUS -->
                  <a href="index.php?url=anak&hapus=<?= $d['id'] ?>"
                     class="btn btn-danger btn-sm btn-icon"
                     onclick="return confirm('Yakin hapus data?')">

                    <i class="fas fa-trash"></i>

                  </a>

                </div>

              </td>

            </tr>

            <!-- MODAL VIEW -->
            <div class="modal fade"
                 id="view<?= $d['id'] ?>"
                 tabindex="-1">

              <div class="modal-dialog modal-lg">

                <div class="modal-content">

                  <div class="modal-header">

                    <h5 class="modal-title">
                      <i class="fas fa-user-circle mr-2"></i>
                      Detail Anak
                    </h5>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal">

                      <span>&times;</span>

                    </button>

                  </div>

                  <div class="modal-body">

                    <div class="row">

                      <div class="col-md-6 mb-3">
                        <strong>Nama Anak</strong><br>
                        <?= $d['nama'] ?>
                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>NIK</strong><br>
                        <?= $d['nik'] ?: '-' ?>
                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>Tempat Lahir</strong><br>
                        <?= $d['tempat_lahir'] ?: '-' ?>
                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>Tanggal Lahir</strong><br>
                        <?= $d['tanggal_lahir'] ?: '-' ?>
                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>Jenis Kelamin</strong><br>

                        <?=
                          $d['jenis_kelamin'] == 'L'
                          ? 'Laki-laki'
                          : 'Perempuan'
                        ?>

                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>Anak Ke</strong><br>
                        <?= $d['anak_ke'] ?: '-' ?>
                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>Berat Lahir</strong><br>
                        <?= $d['berat_lahir'] ?: '-' ?> Kg
                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>Panjang Lahir</strong><br>
                        <?= $d['panjang_lahir'] ?: '-' ?> Cm
                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>Nama Ayah</strong><br>
                        <?= $d['nama_ayah'] ?: '-' ?>
                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>Nama Ibu</strong><br>
                        <?= $d['nama_ibu'] ?: '-' ?>
                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>Status</strong><br>
                        <?= ucfirst($d['status']) ?>
                      </div>

                      <div class="col-md-6 mb-3">
                        <strong>Keluarga</strong><br>
                        <?= $d['nama_kepala_keluarga'] ?>
                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </div>

            <?php endforeach; ?>

          </tbody>

        </table>

      </div>

    </div>

  </div>

</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1">

  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title">
          <i class="fas fa-plus-circle mr-2"></i>
          Tambah Anak
        </h5>

        <button type="button"
                class="close text-white"
                data-dismiss="modal">

          <span>&times;</span>

        </button>

      </div>

      <form method="POST">

        <div class="modal-body">

          <div class="row">

            <div class="col-md-6">

              <div class="form-group">
                <label>Keluarga</label>

                <select name="id_keluarga"
                        class="custom-select"
                        required>

                  <option value="">-- Pilih Keluarga --</option>

                  <?php foreach($keluarga as $k): ?>

                  <option value="<?= $k['id'] ?>">
                    <?= $k['nama_kepala_keluarga'] ?>
                  </option>

                  <?php endforeach; ?>

                </select>

              </div>

            </div>

            <div class="col-md-6">

              <div class="form-group">
                <label>NIK Anak</label>

                <input type="text"
                       name="nik"
                       class="form-control">
              </div>

            </div>

          </div>

          <div class="form-group">
            <label>Nama Anak</label>

            <input type="text"
                   name="nama"
                   class="form-control"
                   required>
          </div>

          <div class="row">

            <div class="col-md-6">

              <div class="form-group">
                <label>Tempat Lahir</label>

                <input type="text"
                       name="tempat_lahir"
                       class="form-control">
              </div>

            </div>

            <div class="col-md-6">

              <div class="form-group">
                <label>Tanggal Lahir</label>

                <input type="date"
                       name="tanggal_lahir"
                       class="form-control">
              </div>

            </div>

          </div>

          <div class="row">

            <div class="col-md-4">

              <div class="form-group">
                <label>Jenis Kelamin</label>

                <select name="jenis_kelamin"
                        class="custom-select">

                  <option value="L">Laki-laki</option>
                  <option value="P">Perempuan</option>

                </select>

              </div>

            </div>

            <div class="col-md-4">

              <div class="form-group">
                <label>Anak Ke</label>

                <input type="number"
                       name="anak_ke"
                       class="form-control">
              </div>

            </div>

            <div class="col-md-4">

              <div class="form-group">
                <label>Status</label>

                <select name="status"
                        class="custom-select">

                  <option value="aktif">Aktif</option>
                  <option value="pindah">Pindah</option>
                  <option value="meninggal">Meninggal</option>

                </select>

              </div>

            </div>

          </div>

          <div class="row">

            <div class="col-md-6">

              <div class="form-group">
                <label>Berat Lahir</label>

                <input type="number"
                       step="0.01"
                       name="berat_lahir"
                       class="form-control">
              </div>

            </div>

            <div class="col-md-6">

              <div class="form-group">
                <label>Panjang Lahir</label>

                <input type="number"
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

                <input type="text"
                       name="nama_ayah"
                       class="form-control">
              </div>

            </div>

            <div class="col-md-6">

              <div class="form-group">
                <label>Nama Ibu</label>

                <input type="text"
                       name="nama_ibu"
                       class="form-control">
              </div>

            </div>

          </div>

        </div>

        <div class="modal-footer">

          <button type="button"
                  class="btn btn-light"
                  data-dismiss="modal">

            Batal

          </button>

          <button type="submit"
                  name="simpan"
                  class="btn btn-primary">

            <i class="fas fa-save mr-1"></i>
            Simpan

          </button>

        </div>

      </form>

    </div>

  </div>

</div>

<script>

document.getElementById("searchInput")
.addEventListener("keyup", function(){

  let filter = this.value.toLowerCase();

  let rows = document.querySelectorAll("#tableBody tr");

  rows.forEach(row => {

    let text = row.innerText.toLowerCase();

    row.style.display = text.includes(filter)
      ? ""
      : "none";

  });

});

</script>