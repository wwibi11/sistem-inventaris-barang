<?php
require_once __DIR__ . '/../../config/database.php';

/*
|--------------------------------------------------------------------------
| DATA IMUNISASI
|--------------------------------------------------------------------------
*/
$data = $pdo->query("
    SELECT
        i.*,
        a.nama AS nama_anak,
        u.nama AS petugas,
        k.pertemuan_ke
    FROM imunisasi i
    JOIN anak a ON a.id = i.id_anak
    LEFT JOIN users u ON u.id = i.diberikan_oleh
    LEFT JOIN kegiatan k ON k.id = i.id_kegiatan
    ORDER BY i.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total_imunisasi = count($data);
$total_anak_imunisasi = $pdo->query("SELECT COUNT(DISTINCT id_anak) FROM imunisasi")->fetchColumn();
$bulan_ini = $pdo->query("
    SELECT COUNT(*) FROM imunisasi 
    WHERE MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())
")->fetchColumn();
?>

<style>
/* ============================================
   STYLE DASHBOARD IMUNISASI
   ============================================ */

.imunisasi-container { padding: 10px 0; }

/* Header */
.imunisasi-header {
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

.imunisasi-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.imunisasi-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.imunisasi-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Button Tambah */
.btn-tambah-imunisasi {
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
    text-decoration: none;
}

.btn-tambah-imunisasi:hover {
    background: #1f507a;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(44, 107, 158, 0.25);
    text-decoration: none;
}

/* Stat Cards */
.stat-card-imunisasi {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px 20px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
    transition: all 0.3s ease;
}

.stat-card-imunisasi:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-card-imunisasi .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #ffffff;
    margin-bottom: 10px;
}

.stat-card-imunisasi .stat-icon.primary { background: #2c6b9e; }
.stat-card-imunisasi .stat-icon.success { background: #28a745; }
.stat-card-imunisasi .stat-icon.info { background: #17a2b8; }

.stat-card-imunisasi .stat-number {
    font-size: 26px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

.stat-card-imunisasi .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Card Filter */
.card-filter-imunisasi {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 24px;
}

.card-filter-imunisasi .card-body {
    padding: 16px 20px;
}

.card-filter-imunisasi .form-control,
.card-filter-imunisasi .custom-select {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 10px 14px;
    height: 42px;
    background: #fafbfc;
    transition: all 0.2s ease;
}

.card-filter-imunisasi .form-control:focus,
.card-filter-imunisasi .custom-select:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

.btn-filter-imunisasi {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    width: 100%;
    transition: all 0.3s ease;
}

.btn-filter-imunisasi:hover {
    background: #1f507a;
    color: #ffffff;
}

/* Card Tabel */
.card-table-imunisasi {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-table-imunisasi .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    background: #f8f9fc;
}

.card-table-imunisasi .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-table-imunisasi .card-header-custom h6 i {
    color: #2c6b9e;
    margin-right: 8px;
}

.card-table-imunisasi .card-header-custom .badge-count {
    background: #e8f0fe;
    color: #2c6b9e;
    padding: 2px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Tabel */
.table-imunisasi {
    font-size: 13px;
    margin: 0;
}

.table-imunisasi thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 14px;
    border-bottom: 2px solid #edf2f7;
    white-space: nowrap;
}

.table-imunisasi tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-imunisasi tbody tr:hover {
    background: #fafbfc;
}

.table-imunisasi tbody tr:last-child td {
    border-bottom: none;
}

/* Badge Imunisasi */
.badge-imunisasi {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-imunisasi.hb0 {
    background: #e5e7eb;
    color: #374151;
}

.badge-imunisasi.bcg {
    background: #d1fae5;
    color: #047857;
}

.badge-imunisasi.polio {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-imunisasi.dpt {
    background: #ede9fe;
    color: #6d28d9;
}

.badge-imunisasi.campak {
    background: #fee2e2;
    color: #b91c1c;
}

.badge-imunisasi.mr {
    background: #fef3c7;
    color: #92400e;
}

/* Aksi Button */
.btn-action-imunisasi {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    font-size: 13px;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-action-imunisasi.view {
    background: #e8f0fe;
    color: #2c6b9e;
}

.btn-action-imunisasi.view:hover {
    background: #2c6b9e;
    color: #ffffff;
}

.btn-action-imunisasi.edit {
    background: #fef3c7;
    color: #92400e;
}

.btn-action-imunisasi.edit:hover {
    background: #92400e;
    color: #ffffff;
}

.btn-action-imunisasi.delete {
    background: #fee2e2;
    color: #b91c1c;
}

.btn-action-imunisasi.delete:hover {
    background: #b91c1c;
    color: #ffffff;
}

/* Empty State */
.empty-state-imunisasi {
    text-align: center;
    padding: 40px 20px;
}

.empty-state-imunisasi i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 12px;
    display: block;
}

.empty-state-imunisasi h6 {
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 4px;
}

.empty-state-imunisasi p {
    color: #8a94a6;
    font-size: 13px;
}

/* Responsive */
@media (max-width: 768px) {
    .imunisasi-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .btn-tambah-imunisasi {
        width: 100%;
        justify-content: center;
    }
    .card-table-imunisasi .card-header-custom {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

<div class="imunisasi-container">

    <!-- HEADER -->
    <div class="imunisasi-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-syringe"></i>
                Imunisasi Anak
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Monitoring dan pencatatan imunisasi balita Posyandu Bougenvil Belik
            </div>
        </div>
        <a href="index.php?url=imunisasi-input" class="btn-tambah-imunisasi">
            <i class="fas fa-plus-circle"></i> Tambah Imunisasi
        </a>
    </div>

    <!-- STATISTIK -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card-imunisasi">
                <div class="stat-icon primary"><i class="fas fa-syringe"></i></div>
                <div class="stat-number"><?= $total_imunisasi ?></div>
                <div class="stat-label">Total Imunisasi</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-imunisasi">
                <div class="stat-icon success"><i class="fas fa-child"></i></div>
                <div class="stat-number"><?= $total_anak_imunisasi ?></div>
                <div class="stat-label">Anak Diimunisasi</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-imunisasi">
                <div class="stat-icon info"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-number"><?= $bulan_ini ?></div>
                <div class="stat-label">Bulan Ini</div>
            </div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="card-filter-imunisasi">
        <div class="card-body">
            <form method="GET" id="filterForm">
                <input type="hidden" name="url" value="imunisasi">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" name="cari" class="form-control" placeholder="Cari nama anak..." value="<?= $_GET['cari'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="jenis" class="custom-select">
                            <option value="">Semua Imunisasi</option>
                            <option value="HB0" <?= ($_GET['jenis'] ?? '') == 'HB0' ? 'selected' : '' ?>>HB0</option>
                            <option value="BCG" <?= ($_GET['jenis'] ?? '') == 'BCG' ? 'selected' : '' ?>>BCG</option>
                            <option value="Polio" <?= ($_GET['jenis'] ?? '') == 'Polio' ? 'selected' : '' ?>>Polio</option>
                            <option value="DPT-HB-Hib" <?= ($_GET['jenis'] ?? '') == 'DPT-HB-Hib' ? 'selected' : '' ?>>DPT-HB-Hib</option>
                            <option value="Campak" <?= ($_GET['jenis'] ?? '') == 'Campak' ? 'selected' : '' ?>>Campak</option>
                            <option value="MR" <?= ($_GET['jenis'] ?? '') == 'MR' ? 'selected' : '' ?>>MR</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn-filter-imunisasi">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card-table-imunisasi">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-history"></i> Riwayat Imunisasi Anak
                <span class="badge-count"><?= $total_imunisasi ?></span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-imunisasi">
                    <thead>
                        <tr>
                            <th>Nama Anak</th>
                            <th>Jenis Imunisasi</th>
                            <th>Tanggal</th>
                            <th>Kegiatan</th>
                            <th>Petugas</th>
                            <th width="130" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($data)): ?>
                            <?php foreach($data as $d): 
                                $badgeClass = 'hb0';
                                $namaImunisasi = $d['jenis_imunisasi'];
                                switch($d['jenis_imunisasi']){
                                    case 'HB0': $badgeClass = 'hb0'; $namaImunisasi = 'Hepatitis B (HB0)'; break;
                                    case 'BCG': $badgeClass = 'bcg'; $namaImunisasi = 'BCG'; break;
                                    case 'Polio': $badgeClass = 'polio'; $namaImunisasi = 'Polio'; break;
                                    case 'DPT-HB-Hib': $badgeClass = 'dpt'; $namaImunisasi = 'DPT-HB-Hib'; break;
                                    case 'Campak': $badgeClass = 'campak'; $namaImunisasi = 'Campak'; break;
                                    case 'MR': $badgeClass = 'mr'; $namaImunisasi = 'Measles Rubella (MR)'; break;
                                }
                            ?>
                            <tr>
                                <td>
                                    <a href="index.php?url=anak-detail&id=<?= $d['id_anak'] ?>" style="color: #1a2634; font-weight: 600; text-decoration: none;">
                                        <?= htmlspecialchars($d['nama_anak']) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge-imunisasi <?= $badgeClass ?>">
                                        <?= $namaImunisasi ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($d['tanggal'])) ?></td>
                                <td>Pertemuan <?= $d['pertemuan_ke'] ?? '-' ?></td>
                                <td><?= htmlspecialchars($d['petugas'] ?? '-') ?></td>
                                <td>
                                    <div class="d-flex justify-content-center" style="gap: 4px;">
                                        <a href="index.php?url=anak-detail&id=<?= $d['id_anak'] ?>" class="btn-action-imunisasi view" title="Lihat Anak">
                                            <i class="fas fa-user"></i>
                                        </a>
                                        <a href="index.php?url=imunisasi-edit&id=<?= $d['id'] ?>" class="btn-action-imunisasi edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?url=imunisasi-delete&id=<?= $d['id'] ?>" class="btn-action-imunisasi delete" 
                                           onclick="return confirm('Yakin ingin menghapus data imunisasi ini?')" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state-imunisasi">
                                        <i class="fas fa-syringe"></i>
                                        <h6>Belum Ada Data Imunisasi</h6>
                                        <p>Klik tombol "Tambah Imunisasi" untuk menambahkan data</p>
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