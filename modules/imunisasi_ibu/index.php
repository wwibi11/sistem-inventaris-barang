<?php
require_once __DIR__ . '/../../config/database.php';

// Ambil parameter filter
$id_kegiatan = $_GET['id_kegiatan'] ?? 0;
$cari = $_GET['cari'] ?? '';
$jenis = $_GET['jenis'] ?? '';

// Query dasar
$sql = "
    SELECT
        iih.*,
        ih.nama_ibu,
        ih.nik,
        ih.usia_kehamilan,
        ih.status,
        mi.nama_imunisasi,
        u.nama AS petugas,
        k.pertemuan_ke,
        k.tanggal AS tanggal_kegiatan
    FROM imunisasi_ibu_hamil iih
    JOIN ibu_hamil ih ON ih.id = iih.ibu_hamil_id
    LEFT JOIN master_imunisasi mi ON mi.id = iih.imunisasi_id
    LEFT JOIN users u ON u.id = iih.diberikan_oleh
    LEFT JOIN kegiatan k ON k.tanggal = iih.tanggal
    WHERE 1=1
";

$params = [];

if ($id_kegiatan) {
    $sql .= " AND k.id = ?";
    $params[] = $id_kegiatan;
}

if ($cari) {
    $sql .= " AND ih.nama_ibu LIKE ?";
    $params[] = "%$cari%";
}

if ($jenis) {
    $sql .= " AND mi.nama_imunisasi = ?";
    $params[] = $jenis;
}

$sql .= " ORDER BY iih.tanggal DESC, ih.nama_ibu ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistik
$total = count($data);
$total_ibu = $pdo->query("SELECT COUNT(DISTINCT ibu_hamil_id) FROM imunisasi_ibu_hamil")->fetchColumn();
$bulan_ini = $pdo->query("
    SELECT COUNT(*) FROM imunisasi_ibu_hamil 
    WHERE MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())
")->fetchColumn();

// Data kegiatan untuk dropdown
$kegiatanList = $pdo->query("SELECT * FROM kegiatan ORDER BY tanggal DESC")->fetchAll(PDO::FETCH_ASSOC);

// Master imunisasi untuk filter
$masterList = $pdo->query("SELECT * FROM master_imunisasi WHERE kategori = 'Ibu Hamil' ORDER BY nama_imunisasi")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.imunisasi-ibu-container { padding: 10px 0; }

/* Header */
.imunisasi-ibu-header {
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

.imunisasi-ibu-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.imunisasi-ibu-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.imunisasi-ibu-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Button Tambah - HIJAU */
.btn-tambah-imunisasi-ibu {
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

.btn-tambah-imunisasi-ibu:hover {
    background: #1e7e34;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.25);
    text-decoration: none;
}

/* Stat Cards */
.stat-card-imunisasi-ibu {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px 20px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
    transition: all 0.3s ease;
}

.stat-card-imunisasi-ibu:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-card-imunisasi-ibu .stat-icon {
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

.stat-card-imunisasi-ibu .stat-icon.primary { background: #2c6b9e; }
.stat-card-imunisasi-ibu .stat-icon.success { background: #28a745; }
.stat-card-imunisasi-ibu .stat-icon.info { background: #17a2b8; }

.stat-card-imunisasi-ibu .stat-number {
    font-size: 26px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

.stat-card-imunisasi-ibu .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Card Filter */
.card-filter-imunisasi-ibu {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 24px;
}

.card-filter-imunisasi-ibu .card-body {
    padding: 16px 20px;
}

.card-filter-imunisasi-ibu .form-control,
.card-filter-imunisasi-ibu .custom-select {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 10px 14px;
    height: 42px;
    background: #fafbfc;
    transition: all 0.2s ease;
}

.card-filter-imunisasi-ibu .form-control:focus,
.card-filter-imunisasi-ibu .custom-select:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

/* Button Cari - BIRU */
.btn-filter-imunisasi-ibu {
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

.btn-filter-imunisasi-ibu:hover {
    background: #1f507a;
    color: #ffffff;
}

/* Card Tabel */
.card-table-imunisasi-ibu {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-table-imunisasi-ibu .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    background: #f8f9fc;
}

.card-table-imunisasi-ibu .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-table-imunisasi-ibu .card-header-custom h6 i {
    color: #2c6b9e;
    margin-right: 8px;
}

.card-table-imunisasi-ibu .card-header-custom .badge-count {
    background: #e8f0fe;
    color: #2c6b9e;
    padding: 2px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Tabel */
.table-imunisasi-ibu {
    font-size: 13px;
    margin: 0;
}

.table-imunisasi-ibu thead th {
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

.table-imunisasi-ibu tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-imunisasi-ibu tbody tr:hover {
    background: #fafbfc;
}

.table-imunisasi-ibu tbody tr:last-child td {
    border-bottom: none;
}

/* Badge Imunisasi Ibu */
.badge-imunisasi-ibu {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-imunisasi-ibu.tt1 {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-imunisasi-ibu.tt2 {
    background: #fef3c7;
    color: #92400e;
}

.badge-imunisasi-ibu.tt-booster {
    background: #d1fae5;
    color: #047857;
}

.badge-imunisasi-ibu.default {
    background: #f3f4f6;
    color: #6b7280;
}

/* Aksi Button */
.btn-action-imunisasi-ibu {
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

.btn-action-imunisasi-ibu.view {
    background: #e8f0fe;
    color: #2c6b9e;
}

.btn-action-imunisasi-ibu.view:hover {
    background: #2c6b9e;
    color: #ffffff;
}

.btn-action-imunisasi-ibu.edit {
    background: #fef3c7;
    color: #92400e;
}

.btn-action-imunisasi-ibu.edit:hover {
    background: #92400e;
    color: #ffffff;
}

.btn-action-imunisasi-ibu.delete {
    background: #fee2e2;
    color: #b91c1c;
}

.btn-action-imunisasi-ibu.delete:hover {
    background: #b91c1c;
    color: #ffffff;
}

.badge-status-ibu {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-status-ibu.Aktif { background: #d1fae5; color: #047857; }
.badge-status-ibu.Melahirkan { background: #dbeafe; color: #1d4ed8; }
.badge-status-ibu.Pindah { background: #fef3c7; color: #92400e; }

/* Empty State */
.empty-state-imunisasi-ibu {
    text-align: center;
    padding: 40px 20px;
}

.empty-state-imunisasi-ibu i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 12px;
    display: block;
}

.empty-state-imunisasi-ibu h6 {
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 4px;
}

.empty-state-imunisasi-ibu p {
    color: #8a94a6;
    font-size: 13px;
}

/* Responsive */
@media (max-width: 768px) {
    .imunisasi-ibu-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .btn-tambah-imunisasi-ibu {
        width: 100%;
        justify-content: center;
    }
    .card-table-imunisasi-ibu .card-header-custom {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

<div class="imunisasi-ibu-container">

    <!-- HEADER -->
    <div class="imunisasi-ibu-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-syringe"></i>
                Imunisasi Ibu Hamil
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Monitoring dan pencatatan imunisasi ibu hamil (TT)
            </div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="index.php?url=kegiatan-detail&id=<?= $id_kegiatan ?>" class="btn btn-light btn-sm">
                <i class="fas fa-calendar-alt"></i> Detail Kegiatan
            </a>
            <a href="index.php?url=imunisasi_ibu-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn-tambah-imunisasi-ibu">
                <i class="fas fa-plus-circle"></i> Tambah Imunisasi Ibu
            </a>
        </div>
    </div>

    <!-- STATISTIK -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card-imunisasi-ibu">
                <div class="stat-icon primary"><i class="fas fa-syringe"></i></div>
                <div class="stat-number"><?= $total ?></div>
                <div class="stat-label">Total Imunisasi</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-imunisasi-ibu">
                <div class="stat-icon success"><i class="fas fa-person-pregnant"></i></div>
                <div class="stat-number"><?= $total_ibu ?></div>
                <div class="stat-label">Ibu Diimunisasi</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-imunisasi-ibu">
                <div class="stat-icon info"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-number"><?= $bulan_ini ?></div>
                <div class="stat-label">Bulan Ini</div>
            </div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="card-filter-imunisasi-ibu">
        <div class="card-body">
            <form method="GET" id="filterForm">
                <input type="hidden" name="url" value="imunisasi_ibu">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="cari" class="form-control" placeholder="Cari nama ibu..." value="<?= htmlspecialchars($cari) ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="jenis" class="custom-select">
                            <option value="">Semua Imunisasi</option>
                            <?php foreach ($masterList as $m): ?>
                                <option value="<?= $m['nama_imunisasi'] ?>" <?= ($jenis == $m['nama_imunisasi']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nama_imunisasi']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn-filter-imunisasi-ibu">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card-table-imunisasi-ibu">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-history"></i> Riwayat Imunisasi Ibu Hamil
                <span class="badge-count"><?= $total ?></span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-imunisasi-ibu">
                    <thead>
                        <tr>
                            <th>Nama Ibu</th>
                            <th>Jenis Imunisasi</th>
                            <th>Tanggal</th>
                            <th>Kegiatan</th>
                            <th>Status</th>
                            <th>Petugas</th>
                            <th width="130" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data)): ?>
                            <?php foreach ($data as $d): 
                                $badgeClass = 'default';
                                $namaImunisasi = $d['nama_imunisasi'] ?? 'Imunisasi';
                                if (strpos(strtolower($namaImunisasi), 'tt 1') !== false) $badgeClass = 'tt1';
                                elseif (strpos(strtolower($namaImunisasi), 'tt 2') !== false) $badgeClass = 'tt2';
                                elseif (strpos(strtolower($namaImunisasi), 'booster') !== false) $badgeClass = 'tt-booster';
                            ?>
                            <tr>
                                <td>
                                    <a href="index.php?url=ibu_hamil-detail&id=<?= $d['ibu_hamil_id'] ?>" style="color: #1a2634; font-weight: 600; text-decoration: none;">
                                        <?= htmlspecialchars($d['nama_ibu']) ?>
                                    </a>
                                    <br>
                                    <small class="text-muted">NIK: <?= htmlspecialchars($d['nik'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <span class="badge-imunisasi-ibu <?= $badgeClass ?>">
                                        <?= htmlspecialchars($namaImunisasi) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($d['tanggal'])) ?></td>
                                <td>Pertemuan <?= $d['pertemuan_ke'] ?? '-' ?></td>
                                <td>
                                    <span class="badge-status-ibu <?= $d['status'] ?? 'Aktif' ?>">
                                        <?= $d['status'] ?? 'Aktif' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($d['petugas'] ?? '-') ?></td>
                                <td>
                                    <div class="d-flex justify-content-center" style="gap: 4px;">
                                        <a href="index.php?url=ibu_hamil-detail&id=<?= $d['ibu_hamil_id'] ?>" class="btn-action-imunisasi-ibu view" title="Lihat Ibu">
                                            <i class="fas fa-user"></i>
                                        </a>
                                        <a href="index.php?url=imunisasi_ibu-edit&id=<?= $d['id'] ?>" class="btn-action-imunisasi-ibu edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?url=imunisasi_ibu-delete&id=<?= $d['id'] ?>" class="btn-action-imunisasi-ibu delete" 
                                           onclick="return confirm('Yakin ingin menghapus data imunisasi ini?')" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state-imunisasi-ibu">
                                        <i class="fas fa-syringe"></i>
                                        <h6>Belum Ada Data Imunisasi Ibu Hamil</h6>
                                        <p>Klik tombol "Tambah Imunisasi Ibu" untuk menambahkan data</p>
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