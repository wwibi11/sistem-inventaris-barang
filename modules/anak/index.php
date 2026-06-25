<?php
require_once __DIR__ . '/../../config/database.php';

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

// Hitung total
$total_anak = count($data);
$total_laki = 0;
$total_perempuan = 0;
foreach ($data as $d) {
    if ($d['jenis_kelamin'] == 'L') $total_laki++;
    else $total_perempuan++;
}
?>

<style>
/* ============================================
   STYLE DASHBOARD ANAK
   ============================================ */

.anak-container {
    padding: 10px 0;
}

/* Header */
.anak-header {
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

.anak-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.anak-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.anak-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

.anak-header .header-right {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

/* Stat Mini */
.stat-mini {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8f9fc;
    padding: 8px 16px;
    border-radius: 10px;
    border: 1px solid #edf2f7;
}

.stat-mini .stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #ffffff;
}

.stat-mini .stat-icon.primary { background: #2c6b9e; }
.stat-mini .stat-icon.success { background: #28a745; }
.stat-mini .stat-icon.info { background: #17a2b8; }
.stat-mini .stat-icon.warning { background: #e8a317; }

.stat-mini .stat-number {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

.stat-mini .stat-label {
    font-size: 10px;
    color: #8a94a6;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Button Tambah */
.btn-tambah {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-tambah:hover {
    background: #1f507a;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(44, 107, 158, 0.25);
}

/* Card Utama */
.card-anak {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-anak .card-body {
    padding: 20px 22px;
}

/* Search Box */
.search-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    flex: 1;
    max-width: 340px;
}

.search-box .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 14px;
}

.search-box .form-control {
    padding: 10px 16px 10px 40px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    background: #fafbfc;
    transition: all 0.2s ease;
    height: 44px;
}

.search-box .form-control:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

/* Tabel */
.table-anak {
    font-size: 13px;
    margin: 0;
    width: 100%;
}

.table-anak thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 12px 14px;
    border-bottom: 2px solid #edf2f7;
    white-space: nowrap;
}

.table-anak thead th i {
    margin-right: 4px;
    color: #8a94a6;
}

.table-anak tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-anak tbody tr:hover {
    background: #fafbfc;
}

.table-anak tbody tr:last-child td {
    border-bottom: none;
}

/* Info Anak */
.info-anak .nama {
    font-weight: 600;
    color: #1a2634;
    font-size: 14px;
}

.info-anak .nik {
    font-size: 12px;
    color: #8a94a6;
}

/* Badge Status */
.badge-status {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-status.aktif {
    background: #d1fae5;
    color: #047857;
}

.badge-status.pindah {
    background: #fef3c7;
    color: #92400e;
}

.badge-status.meninggal {
    background: #fee2e2;
    color: #b91c1c;
}

/* Aksi Buttons */
.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    font-size: 13px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-action.view {
    background: #e8f0fe;
    color: #2c6b9e;
}

.btn-action.view:hover {
    background: #2c6b9e;
    color: #ffffff;
}

.btn-action.detail {
    background: #dbeafe;
    color: #1d4ed8;
}

.btn-action.detail:hover {
    background: #1d4ed8;
    color: #ffffff;
}

.btn-action.edit {
    background: #fef3c7;
    color: #92400e;
}

.btn-action.edit:hover {
    background: #92400e;
    color: #ffffff;
}

.btn-action.delete {
    background: #fee2e2;
    color: #b91c1c;
}

.btn-action.delete:hover {
    background: #b91c1c;
    color: #ffffff;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-state i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 12px;
    display: block;
}

.empty-state h6 {
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 4px;
}

.empty-state p {
    color: #8a94a6;
    font-size: 13px;
}

/* Modal */
.modal-keluarga .modal-content {
    border-radius: 14px;
    border: none;
    overflow: hidden;
}

.modal-keluarga .modal-header {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 16px 24px;
}

.modal-keluarga .modal-header .close {
    color: #ffffff;
    opacity: 0.8;
}

.modal-keluarga .modal-header .close:hover {
    opacity: 1;
}

.modal-keluarga .modal-body {
    padding: 0;
}

.modal-keluarga iframe {
    width: 100%;
    min-height: 650px;
    border: none;
}

/* Responsive */
@media (max-width: 768px) {
    .anak-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    
    .anak-header .header-right {
        justify-content: space-between;
    }
    
    .search-box {
        max-width: 100%;
    }
    
    .stat-mini {
        padding: 6px 12px;
    }
    
    .stat-mini .stat-number {
        font-size: 15px;
    }
    
    .btn-tambah {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .anak-header .header-right {
        flex-direction: column;
        align-items: stretch;
    }
    
    .stat-mini {
        justify-content: center;
    }
}
</style>

<div class="anak-container">

    <!-- ==========================================
    HEADER
    ========================================== -->
    <div class="anak-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-child"></i>
                Data Anak
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Data anak peserta Posyandu Bougenvil Belik
            </div>
        </div>
        <div class="header-right">
            <!-- Stat Mini -->
            <div class="stat-mini">
                <div class="stat-icon primary">
                    <i class="fas fa-child"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $total_anak ?></div>
                    <div class="stat-label">Total Anak</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-icon info">
                    <i class="fas fa-male"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $total_laki ?></div>
                    <div class="stat-label">Laki-laki</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-icon warning">
                    <i class="fas fa-female"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $total_perempuan ?></div>
                    <div class="stat-label">Perempuan</div>
                </div>
            </div>
            
            <!-- Tombol Tambah -->
            <button type="button" class="btn-tambah" data-toggle="modal" data-target="#modalTambah">
                <i class="fas fa-plus-circle"></i>
                Tambah Anak
            </button>
        </div>
    </div>

    <!-- ==========================================
    TABLE CARD
    ========================================== -->
    <div class="card-anak">
        <div class="card-body">

            <!-- Search -->
            <div class="search-wrapper">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="searchInput"
                           placeholder="Cari nama anak, NIK, atau keluarga...">
                </div>
                <span style="font-size: 12px; color: #8a94a6;">
                    <i class="fas fa-database"></i> <?= $total_anak ?> data
                </span>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-anak">
                    <thead>
                        <tr>
                            <th width="45">#</th>
                            <th><i class="fas fa-user"></i> Anak</th>
                            <th><i class="fas fa-users"></i> Keluarga</th>
                            <th><i class="fas fa-calendar"></i> Lahir</th>
                            <th width="90"><i class="fas fa-venus-mars"></i> JK</th>
                            <th width="100"><i class="fas fa-circle"></i> Status</th>
                            <th width="180" class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (count($data) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($data as $d): ?>
                            <tr>
                                <td>
                                    <span style="font-weight: 600; color: #8a94a6; font-size: 12px;"><?= $no++ ?></span>
                                </td>
                                <td>
                                    <div class="info-anak">
                                        <div class="nama"><?= htmlspecialchars($d['nama']) ?></div>
                                        <div class="nik">NIK: <?= htmlspecialchars($d['nik'] ?? '-') ?></div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($d['nama_kepala_keluarga'] ?? '-') ?></td>
                                <td>
                                    <?= htmlspecialchars($d['tempat_lahir'] ?? '-') ?>
                                    <div style="font-size: 11px; color: #8a94a6;">
                                        <?= $d['tanggal_lahir'] ? date('d M Y', strtotime($d['tanggal_lahir'])) : '-' ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($d['jenis_kelamin'] == 'L'): ?>
                                        <span style="color: #2c6b9e;"><i class="fas fa-male"></i> L</span>
                                    <?php else: ?>
                                        <span style="color: #e8a317;"><i class="fas fa-female"></i> P</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $status = $d['status'] ?? 'aktif';
                                    $class = 'aktif';
                                    if ($status == 'pindah') $class = 'pindah';
                                    if ($status == 'meninggal') $class = 'meninggal';
                                    ?>
                                    <span class="badge-status <?= $class ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2" style="gap: 5px;">
                                        <!-- View -->
                                        <button class="btn-action view" data-toggle="modal" data-target="#view<?= $d['id'] ?>" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- Detail -->
                                        <a href="index.php?url=anak-detail&id=<?= $d['id'] ?>" class="btn-action detail" title="Riwayat Kesehatan">
                                            <i class="fas fa-file-medical-alt"></i>
                                        </a>
                                        <!-- Edit -->
                                        <button class="btn-action edit" data-toggle="modal" data-target="#edit<?= $d['id'] ?>" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Delete -->
                                        <a href="index.php?url=anak-delete&id=<?= $d['id'] ?>" class="btn-action delete" 
                                           onclick="return confirm('Yakin ingin menghapus data anak <?= htmlspecialchars($d['nama']) ?>?')" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL VIEW -->
                            <div class="modal fade modal-keluarga" id="view<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-user-circle mr-2"></i>
                                                Detail Anak - <?= htmlspecialchars($d['nama']) ?>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" style="padding: 24px;">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Nama Anak</strong><br>
                                                    <span style="font-size: 15px; font-weight: 600;"><?= htmlspecialchars($d['nama']) ?></span>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">NIK</strong><br>
                                                    <?= htmlspecialchars($d['nik'] ?? '-') ?>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Tempat Lahir</strong><br>
                                                    <?= htmlspecialchars($d['tempat_lahir'] ?? '-') ?>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Tanggal Lahir</strong><br>
                                                    <?= $d['tanggal_lahir'] ? date('d M Y', strtotime($d['tanggal_lahir'])) : '-' ?>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Jenis Kelamin</strong><br>
                                                    <?= $d['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Anak Ke</strong><br>
                                                    <?= htmlspecialchars($d['anak_ke'] ?? '-') ?>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Berat Lahir</strong><br>
                                                    <?= htmlspecialchars($d['berat_lahir'] ?? '-') ?> Kg
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Panjang Lahir</strong><br>
                                                    <?= htmlspecialchars($d['panjang_lahir'] ?? '-') ?> Cm
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Nama Ayah</strong><br>
                                                    <?= htmlspecialchars($d['nama_ayah'] ?? '-') ?>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Nama Ibu</strong><br>
                                                    <?= htmlspecialchars($d['nama_ibu'] ?? '-') ?>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Status</strong><br>
                                                    <span class="badge-status <?= $class ?>">
                                                        <?= ucfirst($status) ?>
                                                    </span>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #4a5568;">Keluarga</strong><br>
                                                    <?= htmlspecialchars($d['nama_kepala_keluarga']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL EDIT -->
                            <div class="modal fade modal-keluarga" id="edit<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-edit mr-2"></i>
                                                Edit Anak - <?= htmlspecialchars($d['nama']) ?>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe src="index.php?url=anak-edit&id=<?= $d['id'] ?>" loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h6>Belum Ada Data Anak</h6>
                                        <p>Klik tombol "Tambah Anak" untuk menambahkan data baru</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<!-- ==========================================
MODAL TAMBAH
========================================== -->
<div class="modal fade modal-keluarga" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tambah Anak Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="index.php?url=anak-create" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
SCRIPT
========================================== -->
<script>
// Search
document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#tableBody tr");
    rows.forEach(function(row) {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});

// Reset iframe saat modal ditutup
$('.modal').on('hidden.bs.modal', function() {
    $(this).find('iframe').attr('src', '');
});
</script>