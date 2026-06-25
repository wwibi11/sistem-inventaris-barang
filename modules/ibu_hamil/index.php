<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// DATA IBU HAMIL
// ==========================
$data = $pdo->query("
  SELECT
    ih.*,
    k.nama_kepala_keluarga
  FROM ibu_hamil ih
  LEFT JOIN keluarga k ON k.id = ih.id_keluarga
  ORDER BY ih.id DESC
")->fetchAll();

// Hitung total
$total_ibu_hamil = count($data);
$total_trimester1 = 0;
$total_trimester2 = 0;
$total_trimester3 = 0;

foreach ($data as $d) {
    $usia = $d['usia_kehamilan'] ?? 0;
    if ($usia > 0 && $usia <= 13) $total_trimester1++;
    elseif ($usia > 13 && $usia <= 27) $total_trimester2++;
    elseif ($usia > 27) $total_trimester3++;
}

// Fungsi untuk menentukan trimester
function getTrimester($usia) {
    if ($usia <= 0) return 0;
    elseif ($usia <= 13) return 1;
    elseif ($usia <= 27) return 2;
    else return 3;
}

// Fungsi format tanggal
function formatDate($date) {
    if (!$date || $date == '0000-00-00') return '-';
    return date('d M Y', strtotime($date));
}
?>

<style>
/* ============================================
   STYLE DASHBOARD IBU HAMIL
   ============================================ */

.ibu-hamil-container {
    padding: 10px 0;
}

/* Header */
.ibu-hamil-header {
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

.ibu-hamil-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.ibu-hamil-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.ibu-hamil-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

.ibu-hamil-header .header-right {
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
.card-ibu-hamil {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-ibu-hamil .card-body {
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
.table-ibu-hamil {
    font-size: 13px;
    margin: 0;
    width: 100%;
}

.table-ibu-hamil thead th {
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

.table-ibu-hamil thead th i {
    margin-right: 4px;
    color: #8a94a6;
}

.table-ibu-hamil tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-ibu-hamil tbody tr:hover {
    background: #fafbfc;
}

.table-ibu-hamil tbody tr:last-child td {
    border-bottom: none;
}

/* Info Ibu Hamil */
.info-ibu .nama {
    font-weight: 600;
    color: #1a2634;
    font-size: 14px;
}

.info-ibu .nama i {
    color: #2c6b9e;
    margin-right: 5px;
}

.info-ibu .nik {
    font-size: 12px;
    color: #8a94a6;
}

/* Badge Trimester */
.badge-trimester {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.badge-trimester.t1 {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-trimester.t2 {
    background: #fef3c7;
    color: #92400e;
}

.badge-trimester.t3 {
    background: #fce4ec;
    color: #9c27b0;
}

.badge-trimester.t0 {
    background: #f3f4f6;
    color: #6b7280;
}

/* Badge Status */
.badge-status-ibu {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.badge-status-ibu.Aktif {
    background: #d1fae5;
    color: #047857;
}

.badge-status-ibu.Melahirkan {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-status-ibu.Pindah {
    background: #fef3c7;
    color: #92400e;
}

/* Aksi Buttons */
.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    font-size: 13px;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
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
.modal-ibu .modal-content {
    border-radius: 14px;
    border: none;
    overflow: hidden;
}

.modal-ibu .modal-header {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 16px 24px;
}

.modal-ibu .modal-header .close {
    color: #ffffff;
    opacity: 0.8;
    font-size: 28px;
    border: none;
    background: transparent;
}

.modal-ibu .modal-header .close:hover {
    opacity: 1;
}

.modal-ibu .modal-body {
    padding: 0;
}

.modal-ibu iframe {
    width: 100%;
    min-height: 650px;
    border: none;
}

/* Detail View Modal */
.modal-detail .modal-body {
    padding: 24px;
}

.detail-item {
    margin-bottom: 16px;
}

.detail-item label {
    font-size: 11px;
    font-weight: 600;
    color: #8a94a6;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 4px;
    display: block;
}

.detail-item .value {
    font-size: 15px;
    font-weight: 600;
    color: #1a2634;
}

/* Responsive */
@media (max-width: 768px) {
    .ibu-hamil-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    
    .ibu-hamil-header .header-right {
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
    .ibu-hamil-header .header-right {
        flex-direction: column;
        align-items: stretch;
    }
    
    .stat-mini {
        justify-content: center;
    }
}
</style>

<div class="ibu-hamil-container">

    <!-- ==========================================
    HEADER
    ========================================== -->
    <div class="ibu-hamil-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-person-pregnant"></i>
                Data Ibu Hamil
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Data ibu hamil peserta Posyandu Bougenvil Belik
            </div>
        </div>
        <div class="header-right">
            <!-- Stat Mini -->
            <div class="stat-mini">
                <div class="stat-icon primary">
                    <i class="fas fa-person-pregnant"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $total_ibu_hamil ?></div>
                    <div class="stat-label">Total Ibu Hamil</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-icon info">
                    <i class="fas fa-calendar"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $total_trimester1 ?></div>
                    <div class="stat-label">Trimester 1</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-icon warning">
                    <i class="fas fa-calendar"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $total_trimester2 ?></div>
                    <div class="stat-label">Trimester 2</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-icon success">
                    <i class="fas fa-calendar"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $total_trimester3 ?></div>
                    <div class="stat-label">Trimester 3</div>
                </div>
            </div>
            
            <!-- Tombol Tambah -->
            <button type="button" class="btn-tambah" data-toggle="modal" data-target="#modalTambah">
                <i class="fas fa-plus-circle"></i>
                Tambah Ibu Hamil
            </button>
        </div>
    </div>

    <!-- ==========================================
    TABLE CARD
    ========================================== -->
    <div class="card-ibu-hamil">
        <div class="card-body">

            <!-- Search -->
            <div class="search-wrapper">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="searchInput"
                           placeholder="Cari nama ibu, NIK, atau keluarga...">
                </div>
                <span style="font-size: 12px; color: #8a94a6;">
                    <i class="fas fa-database"></i> <?= $total_ibu_hamil ?> data
                </span>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-ibu-hamil">
                    <thead>
                        <tr>
                            <th width="45">#</th>
                            <th><i class="fas fa-user"></i> Ibu Hamil</th>
                            <th><i class="fas fa-users"></i> Keluarga</th>
                            <th><i class="fas fa-calendar"></i> Usia Kehamilan</th>
                            <th width="90"><i class="fas fa-baby"></i> Trimester</th>
                            <th width="100"><i class="fas fa-circle"></i> Status</th>
                            <th width="180" class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (count($data) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($data as $d): 
                                $usia = $d['usia_kehamilan'] ?? 0;
                                $trimester = getTrimester($usia);
                                $class = 't0';
                                if ($trimester == 1) $class = 't1';
                                elseif ($trimester == 2) $class = 't2';
                                elseif ($trimester == 3) $class = 't3';
                            ?>
                            <tr>
                                <td>
                                    <span style="font-weight: 600; color: #8a94a6; font-size: 12px;"><?= $no++ ?></span>
                                </td>
                                <td>
                                    <div class="info-ibu">
                                        <div class="nama"><i class="fas fa-female"></i> <?= htmlspecialchars($d['nama_ibu']) ?></div>
                                        <div class="nik">NIK: <?= htmlspecialchars($d['nik'] ?? '-') ?></div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($d['nama_kepala_keluarga'] ?? '-') ?></td>
                                <td>
                                    <?= $usia > 0 ? $usia . ' Minggu' : '-' ?>
                                    <?php if ($d['hpl'] && $d['hpl'] != '0000-00-00'): ?>
                                        <br><small class="text-muted">HPL: <?= date('d M Y', strtotime($d['hpl'])) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($trimester > 0): ?>
                                        <span class="badge-trimester <?= $class ?>">
                                            Trimester <?= $trimester ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-trimester t0">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $status = $d['status'] ?? 'Aktif';
                                    $class = 'Aktif';
                                    if ($status == 'Melahirkan') $class = 'Melahirkan';
                                    if ($status == 'Pindah') $class = 'Pindah';
                                    ?>
                                    <span class="badge-status-ibu <?= $class ?>">
                                        <?= $status ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2" style="gap: 5px;">
                                        <!-- View Detail -->
                                        <button class="btn-action view" data-toggle="modal" data-target="#view<?= $d['id'] ?>" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- Riwayat Pemeriksaan -->
                                        <a href="index.php?url=ibu_hamil-detail&id=<?= $d['id'] ?>" class="btn-action detail" title="Riwayat Pemeriksaan">
                                            <i class="fas fa-file-medical-alt"></i>
                                        </a>
                                        <!-- Edit -->
                                        <button class="btn-action edit" data-toggle="modal" data-target="#edit<?= $d['id'] ?>" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Delete -->
                                        <a href="index.php?url=ibu_hamil-delete&id=<?= $d['id'] ?>" class="btn-action delete" 
                                           onclick="return confirm('Yakin ingin menghapus data ibu hamil <?= htmlspecialchars($d['nama_ibu']) ?>?')" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL VIEW DETAIL -->
                            <div class="modal fade modal-ibu modal-detail" id="view<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-user-circle mr-2"></i>
                                                Detail Ibu Hamil
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 detail-item">
                                                    <label>Nama Ibu</label>
                                                    <div class="value"><?= htmlspecialchars($d['nama_ibu']) ?></div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label>NIK</label>
                                                    <div class="value"><?= htmlspecialchars($d['nik'] ?? '-') ?></div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label>Tempat Lahir</label>
                                                    <div class="value"><?= htmlspecialchars($d['tempat_lahir'] ?? '-') ?></div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label>Tanggal Lahir</label>
                                                    <div class="value"><?= $d['tanggal_lahir'] ? date('d M Y', strtotime($d['tanggal_lahir'])) : '-' ?></div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label>Hamil Ke</label>
                                                    <div class="value"><?= htmlspecialchars($d['hamil_ke'] ?? '-') ?></div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label>Usia Kehamilan</label>
                                                    <div class="value"><?= $d['usia_kehamilan'] > 0 ? $d['usia_kehamilan'] . ' Minggu' : '-' ?></div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label>Trimester</label>
                                                    <div class="value">
                                                        <?php 
                                                        $tr = getTrimester($d['usia_kehamilan'] ?? 0);
                                                        $cls = 't0';
                                                        if ($tr == 1) $cls = 't1';
                                                        elseif ($tr == 2) $cls = 't2';
                                                        elseif ($tr == 3) $cls = 't3';
                                                        ?>
                                                        <span class="badge-trimester <?= $cls ?>">
                                                            <?= $tr > 0 ? 'Trimester ' . $tr : '-' ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label>HPHT (Hari Pertama Haid Terakhir)</label>
                                                    <div class="value"><?= $d['hpht'] ? date('d M Y', strtotime($d['hpht'])) : '-' ?></div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label>HPL (Hari Perkiraan Lahir)</label>
                                                    <div class="value"><?= $d['hpl'] ? date('d M Y', strtotime($d['hpl'])) : '-' ?></div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label>No HP</label>
                                                    <div class="value"><?= htmlspecialchars($d['no_hp'] ?? '-') ?></div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label>Status</label>
                                                    <div class="value">
                                                        <?php
                                                        $status = $d['status'] ?? 'Aktif';
                                                        $cls = 'Aktif';
                                                        if ($status == 'Melahirkan') $cls = 'Melahirkan';
                                                        if ($status == 'Pindah') $cls = 'Pindah';
                                                        ?>
                                                        <span class="badge-status-ibu <?= $cls ?>">
                                                            <?= $status ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 detail-item">
                                                    <label>Keluarga</label>
                                                    <div class="value"><?= htmlspecialchars($d['nama_kepala_keluarga'] ?? '-') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL EDIT -->
                            <div class="modal fade modal-ibu" id="edit<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-edit mr-2"></i>
                                                Edit Ibu Hamil - <?= htmlspecialchars($d['nama_ibu']) ?>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe src="index.php?url=ibu_hamil-edit&id=<?= $d['id'] ?>" loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-person-pregnant"></i>
                                        <h6>Belum Ada Data Ibu Hamil</h6>
                                        <p>Klik tombol "Tambah Ibu Hamil" untuk menambahkan data baru</p>
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
<div class="modal fade modal-ibu" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tambah Ibu Hamil Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="index.php?url=ibu_hamil-create" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
SCRIPT
========================================== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

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