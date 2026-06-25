<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// DATA MASTER IMUNISASI
// ==========================
$data = $pdo->query("
  SELECT * FROM master_imunisasi 
  ORDER BY kategori, nama_imunisasi ASC
")->fetchAll();

// Hitung total per kategori
$total_anak = 0;
$total_ibu_hamil = 0;

foreach ($data as $d) {
    if ($d['kategori'] == 'Anak') $total_anak++;
    else $total_ibu_hamil++;
}
?>

<style>
/* ============================================
   STYLE MASTER IMUNISASI
   ============================================ */

.master-imunisasi-container { padding: 10px 0; }

/* Header */
.master-imunisasi-header {
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

.master-imunisasi-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.master-imunisasi-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.master-imunisasi-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

.master-imunisasi-header .header-right {
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
.card-master-imunisasi {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-master-imunisasi .card-body {
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

/* Filter Kategori */
.filter-kategori {
    display: flex;
    gap: 8px;
}

.filter-kategori .btn-filter {
    padding: 6px 16px;
    border-radius: 20px;
    border: 1.5px solid #e2e8f0;
    background: #fafbfc;
    color: #4a5568;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.2s ease;
    cursor: pointer;
}

.filter-kategori .btn-filter:hover {
    border-color: #2c6b9e;
    color: #2c6b9e;
}

.filter-kategori .btn-filter.active {
    background: #2c6b9e;
    color: #ffffff;
    border-color: #2c6b9e;
}

/* Tabel */
.table-master-imunisasi {
    font-size: 13px;
    margin: 0;
    width: 100%;
}

.table-master-imunisasi thead th {
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

.table-master-imunisasi thead th i {
    margin-right: 4px;
    color: #8a94a6;
}

.table-master-imunisasi tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-master-imunisasi tbody tr:hover {
    background: #fafbfc;
}

.table-master-imunisasi tbody tr:last-child td {
    border-bottom: none;
}

/* Badge Kategori */
.badge-kategori {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-kategori.anak {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-kategori.ibu-hamil {
    background: #fce4ec;
    color: #9c27b0;
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
.modal-master .modal-content {
    border-radius: 14px;
    border: none;
    overflow: hidden;
}

.modal-master .modal-header {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 16px 24px;
}

.modal-master .modal-header .close {
    color: #ffffff;
    opacity: 0.8;
    font-size: 28px;
    border: none;
    background: transparent;
}

.modal-master .modal-header .close:hover {
    opacity: 1;
}

.modal-master .modal-body {
    padding: 0;
}

.modal-master iframe {
    width: 100%;
    min-height: 450px;
    border: none;
}

/* Responsive */
@media (max-width: 768px) {
    .master-imunisasi-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    
    .master-imunisasi-header .header-right {
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
    
    .filter-kategori {
        flex-wrap: wrap;
    }
}

@media (max-width: 576px) {
    .master-imunisasi-header .header-right {
        flex-direction: column;
        align-items: stretch;
    }
    
    .stat-mini {
        justify-content: center;
    }
}
</style>

<div class="master-imunisasi-container">

    <!-- ==========================================
    HEADER
    ========================================== -->
    <div class="master-imunisasi-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-syringe"></i>
                Master Imunisasi
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Daftar jenis imunisasi untuk anak dan ibu hamil
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
                    <div class="stat-label">Imunisasi Anak</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-icon success">
                    <i class="fas fa-person-pregnant"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $total_ibu_hamil ?></div>
                    <div class="stat-label">Imunisasi Ibu Hamil</div>
                </div>
            </div>
            
            <!-- Tombol Tambah -->
            <button type="button" class="btn-tambah" data-toggle="modal" data-target="#modalTambah">
                <i class="fas fa-plus-circle"></i>
                Tambah Imunisasi
            </button>
        </div>
    </div>

    <!-- ==========================================
    TABLE CARD
    ========================================== -->
    <div class="card-master-imunisasi">
        <div class="card-body">

            <!-- Search & Filter -->
            <div class="search-wrapper">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="searchInput"
                           placeholder="Cari nama imunisasi...">
                </div>
                <div class="filter-kategori">
                    <button class="btn-filter active" data-filter="all">Semua</button>
                    <button class="btn-filter" data-filter="Anak">Anak</button>
                    <button class="btn-filter" data-filter="Ibu Hamil">Ibu Hamil</button>
                </div>
                <span style="font-size: 12px; color: #8a94a6;">
                    <i class="fas fa-database"></i> <?= count($data) ?> data
                </span>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-master-imunisasi">
                    <thead>
                        <tr>
                            <th width="45">#</th>
                            <th><i class="fas fa-syringe"></i> Nama Imunisasi</th>
                            <th><i class="fas fa-tag"></i> Kategori</th>
                            <th><i class="fas fa-info-circle"></i> Keterangan</th>
                            <th width="180" class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (count($data) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($data as $d): ?>
                            <tr data-kategori="<?= $d['kategori'] ?>">
                                <td>
                                    <span style="font-weight: 600; color: #8a94a6; font-size: 12px;"><?= $no++ ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($d['nama_imunisasi']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge-kategori <?= strtolower(str_replace(' ', '-', $d['kategori'])) ?>">
                                        <?= htmlspecialchars($d['kategori']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($d['keterangan'] ?? '-') ?></td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2" style="gap: 5px;">
                                        <!-- View Detail -->
                                        <button class="btn-action view" data-toggle="modal" data-target="#view<?= $d['id'] ?>" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- Edit -->
                                        <button class="btn-action edit" data-toggle="modal" data-target="#edit<?= $d['id'] ?>" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Delete -->
                                        <a href="index.php?url=master_imunisasi-delete&id=<?= $d['id'] ?>" class="btn-action delete" 
                                           onclick="return confirm('Yakin ingin menghapus imunisasi <?= htmlspecialchars($d['nama_imunisasi']) ?>?')" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL VIEW DETAIL -->
                            <div class="modal fade modal-master" id="view<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-syringe mr-2"></i>
                                                Detail Imunisasi
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" style="padding: 24px;">
                                            <div class="row">
                                                <div class="col-md-6 detail-item">
                                                    <label style="font-size: 11px; font-weight: 600; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 4px; display: block;">Nama Imunisasi</label>
                                                    <div style="font-size: 15px; font-weight: 600; color: #1a2634;"><?= htmlspecialchars($d['nama_imunisasi']) ?></div>
                                                </div>
                                                <div class="col-md-6 detail-item">
                                                    <label style="font-size: 11px; font-weight: 600; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 4px; display: block;">Kategori</label>
                                                    <div style="font-size: 15px; font-weight: 600; color: #1a2634;">
                                                        <span class="badge-kategori <?= strtolower(str_replace(' ', '-', $d['kategori'])) ?>">
                                                            <?= htmlspecialchars($d['kategori']) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 detail-item">
                                                    <label style="font-size: 11px; font-weight: 600; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 4px; display: block;">Keterangan</label>
                                                    <div style="font-size: 15px; font-weight: 600; color: #1a2634;"><?= htmlspecialchars($d['keterangan'] ?? '-') ?></div>
                                                </div>
                                                <div class="col-md-12 detail-item">
                                                    <label style="font-size: 11px; font-weight: 600; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 4px; display: block;">Tanggal Dibuat</label>
                                                    <div style="font-size: 15px; font-weight: 600; color: #1a2634;"><?= $d['created_at'] ? date('d M Y H:i', strtotime($d['created_at'])) : '-' ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL EDIT -->
                            <div class="modal fade modal-master" id="edit<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-edit mr-2"></i>
                                                Edit Imunisasi - <?= htmlspecialchars($d['nama_imunisasi']) ?>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe src="index.php?url=master_imunisasi-edit&id=<?= $d['id'] ?>" loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-syringe"></i>
                                        <h6>Belum Ada Data Imunisasi</h6>
                                        <p>Klik tombol "Tambah Imunisasi" untuk menambahkan data baru</p>
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
<div class="modal fade modal-master" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tambah Imunisasi Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="index.php?url=master_imunisasi-create" loading="lazy"></iframe>
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

// Filter Kategori
document.querySelectorAll('.btn-filter').forEach(function(btn) {
    btn.addEventListener('click', function() {
        // Active class
        document.querySelectorAll('.btn-filter').forEach(function(b) {
            b.classList.remove('active');
        });
        this.classList.add('active');
        
        let filter = this.dataset.filter;
        let rows = document.querySelectorAll("#tableBody tr");
        
        rows.forEach(function(row) {
            if (filter === 'all') {
                row.style.display = "";
            } else {
                let kategori = row.dataset.kategori;
                row.style.display = kategori === filter ? "" : "none";
            }
        });
    });
});

// Reset iframe saat modal ditutup
$('.modal').on('hidden.bs.modal', function() {
    $(this).find('iframe').attr('src', '');
});
</script>