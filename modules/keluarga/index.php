<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// DATA
// ==========================
$data = $pdo->query("
  SELECT
    keluarga.id,
    keluarga.no_kk,
    keluarga.nama_kepala_keluarga,
    keluarga.nik_ayah,
    keluarga.nama_ayah,
    keluarga.nik_ibu,
    keluarga.nama_ibu,
    keluarga.alamat,
    keluarga.rt,
    keluarga.rw,
    keluarga.desa,
    keluarga.kecamatan,
    keluarga.no_hp,
    COUNT(anak.id) AS jumlah_anak
  FROM keluarga
  LEFT JOIN anak
    ON anak.id_keluarga = keluarga.id
  GROUP BY
    keluarga.id,
    keluarga.no_kk,
    keluarga.nama_kepala_keluarga,
    keluarga.nik_ayah,
    keluarga.nama_ayah,
    keluarga.nik_ibu,
    keluarga.nama_ibu,
    keluarga.alamat,
    keluarga.rt,
    keluarga.rw,
    keluarga.desa,
    keluarga.kecamatan,
    keluarga.no_hp
  ORDER BY keluarga.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<style>

.container-fluid{
  font-size:12px;
}

.card-modern{
  border:none;
  border-radius:14px;
  overflow:hidden;
}

.shadow-soft{
  box-shadow:0 2px 12px rgba(0,0,0,.05);
}

.table-modern{
  font-size:11px;
  margin-bottom:0;
}

.table-modern thead{
  background:#4e73df;
  color:#fff;
}

.table-modern th{
  border:none !important;
  padding:10px 8px !important;
  font-size:10px;
  text-transform:uppercase;
  white-space:nowrap;
}

.table-modern td{
  padding:10px 8px !important;
  vertical-align:middle !important;
}

.badge-soft{
  padding:5px 8px;
  border-radius:20px;
  font-size:10px;
}

.btn{
  font-size:11px;
  border-radius:8px;
}

.btn-icon{
  width:30px;
  height:30px;
  display:flex;
  align-items:center;
  justify-content:center;
}

.search-box{
  max-width:260px;
}

.small-text{
  font-size:10px;
}

.info-title{
  font-size:13px;
  font-weight:600;
}

.modal-content{
  border:none;
  border-radius:16px;
  overflow:hidden;
}

.modal-header{
  background:#4e73df;
  color:white;
  border:none;
}

.modal-footer{
  border:none;
}

iframe{
  width:100%;
  min-height:650px;
  border:none;
}

</style>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-3">

    <div>

      <h1 class="h5 mb-1 text-gray-800">
        <i class="fas fa-home text-primary"></i>
        Data Keluarga
      </h1>

      <div class="text-muted small-text">
        Manajemen data keluarga posyandu
      </div>

    </div>

    <!-- TOMBOL TAMBAH -->
    <button
      type="button"
      class="btn btn-primary btn-sm px-3 shadow-sm open-modal"
      data-title="Tambah Keluarga"
      data-url="index.php?url=keluarga-create">

      <i class="fas fa-plus mr-1"></i>
      Tambah

    </button>

  </div>

  <!-- CARD -->
  <div class="card card-modern shadow-soft">

    <div class="card-body">

      <!-- SEARCH -->
      <div class="mb-3">

        <div class="input-group search-box">

          <div class="input-group-prepend">

            <span class="input-group-text bg-white border-right-0">
              <i class="fas fa-search text-muted"></i>
            </span>

          </div>

          <input
            type="text"
            class="form-control border-left-0"
            id="searchInput"
            placeholder="Cari keluarga...">

        </div>

      </div>

      <!-- TABLE -->
      <div class="table-responsive">

        <table class="table table-sm table-hover table-modern">

          <thead>

            <tr>

              <th>No</th>
              <th>Keluarga</th>
              <th>Orang Tua</th>
              <th>Alamat</th>
              <th>Anak</th>
              <th>HP</th>
              <th width="110">Aksi</th>

            </tr>

          </thead>

          <tbody id="tableBody">

            <?php if(count($data) > 0): ?>

              <?php $no = 1; ?>
              <?php foreach ($data as $d): ?>

              <tr>

                <!-- NO -->
                <td width="40">
                  <strong><?= $no++ ?></strong>
                </td>

                <!-- KELUARGA -->
                <td style="min-width:180px">

                  <div class="info-title text-dark">
                    <?= htmlspecialchars($d['nama_kepala_keluarga'] ?? '-') ?>
                  </div>

                  <div class="small-text text-muted">
                    KK :
                    <?= htmlspecialchars($d['no_kk'] ?? '-') ?>
                  </div>

                </td>

                <!-- ORANG TUA -->
                <td style="min-width:170px">

                  <div>
                    <strong>Ayah :</strong>
                    <?= htmlspecialchars($d['nama_ayah'] ?? '-') ?>
                  </div>

                  <div class="mt-1">
                    <strong>Ibu :</strong>
                    <?= htmlspecialchars($d['nama_ibu'] ?? '-') ?>
                  </div>

                </td>

                <!-- ALAMAT -->
                <td style="min-width:220px">

                  <?= htmlspecialchars($d['alamat'] ?? '-') ?>

                  <div class="small-text text-muted mt-1">

                    RT <?= htmlspecialchars($d['rt'] ?? '-') ?>
                    /
                    RW <?= htmlspecialchars($d['rw'] ?? '-') ?>

                    <br>

                    <?= htmlspecialchars($d['desa'] ?? '-') ?>
                    -
                    <?= htmlspecialchars($d['kecamatan'] ?? '-') ?>

                  </div>

                </td>

                <!-- ANAK -->
                <td width="80">

                  <span class="badge badge-primary badge-soft">

                    <i class="fas fa-child"></i>

                    <?= $d['jumlah_anak'] ?>

                  </span>

                </td>

                <!-- HP -->
                <td width="120">

                  <i class="fas fa-phone text-success"></i>

                  <?= htmlspecialchars($d['no_hp'] ?? '-') ?>

                </td>

                <!-- AKSI -->
                <td>

                  <div class="d-flex">

                    <!-- EDIT -->
                    <button
                      type="button"
                      class="btn btn-warning btn-sm btn-icon mr-1 open-modal"
                      data-title="Edit Keluarga"
                      data-url="index.php?url=keluarga-edit&id=<?= $d['id'] ?>">

                      <i class="fas fa-edit"></i>

                    </button>

                    <!-- DELETE -->
                    <a
                      href="index.php?url=keluarga-delete&id=<?= $d['id'] ?>"
                      class="btn btn-danger btn-sm btn-icon"
                      onclick="return confirm('Yakin hapus data?')">

                      <i class="fas fa-trash"></i>

                    </a>

                  </div>

                </td>

              </tr>

              <?php endforeach; ?>

            <?php else: ?>

              <tr>

                <td colspan="7" class="text-center text-muted py-4">

                  Data keluarga belum tersedia

                </td>

              </tr>

            <?php endif; ?>

          </tbody>

        </table>

      </div>

    </div>

  </div>

</div>

<!-- MODAL GLOBAL -->
<div
  class="modal fade"
  id="globalModal"
  tabindex="-1"
  role="dialog"
  aria-hidden="true">

  <div class="modal-dialog modal-xl">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title" id="modalTitle">
          Modal
        </h5>

        <button
          type="button"
          class="close text-white"
          data-dismiss="modal"
          aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body p-0">

        <iframe
          id="modalFrame"
          loading="lazy">
        </iframe>

      </div>

    </div>

  </div>

</div>

<script>

// ==========================
// SEARCH
// ==========================
const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("keyup", function () {

  let filter = this.value.toLowerCase();

  let rows = document.querySelectorAll("#tableBody tr");

  rows.forEach(function(row){

    let text = row.innerText.toLowerCase();

    row.style.display =
      text.includes(filter)
      ? ""
      : "none";

  });

});

// ==========================
// OPEN MODAL
// ==========================
document
.querySelectorAll(".open-modal")
.forEach(function(button){

  button.addEventListener("click", function(){

    let title = this.dataset.title;
    let url   = this.dataset.url;

    document.getElementById("modalTitle").innerHTML = title;

    document.getElementById("modalFrame").src = url;

    $("#globalModal").modal({
      backdrop: 'static',
      keyboard: false
    });

  });

});

// ==========================
// RESET IFRAME
// ==========================
$('#globalModal').on('hidden.bs.modal', function () {

  document.getElementById("modalFrame").src = "";

});

</script>