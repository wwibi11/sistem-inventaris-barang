<?php
require_once __DIR__ . '/../../config/database.php';

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/
$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

$sql = "
SELECT
    g.tanggal,
    g.pertemuan_ke,
    g.lokasi,
    a.nama,
    h.status_hadir
FROM kehadiran h
JOIN anak a ON a.id = h.id_anak
JOIN kegiatan g ON g.id = h.id_kegiatan
WHERE 1=1
";

$params = [];

if ($tanggal_awal != '') {
    $sql .= " AND g.tanggal >= ?";
    $params[] = $tanggal_awal;
}

if ($tanggal_akhir != '') {
    $sql .= " AND g.tanggal <= ?";
    $params[] = $tanggal_akhir;
}

$sql .= " ORDER BY g.tanggal DESC, a.nama ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($data);

// Hitung statistik
$totalHadir = 0;
$totalTidak = 0;
foreach($data as $d) {
    if($d['status_hadir'] == 'hadir') $totalHadir++;
    else $totalTidak++;
}
?>

<style>
.laporan-kehadiran-container { padding: 10px 0; }

/* Header */
.laporan-kehadiran-header {
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

.laporan-kehadiran-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.laporan-kehadiran-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.laporan-kehadiran-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

.btn-cetak-kehadiran {
    background: #28a745;
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

.btn-cetak-kehadiran:hover {
    background: #1e7e34;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.25);
}

/* Filter Card */
.card-filter-kehadiran {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 24px;
}

.card-filter-kehadiran .card-body {
    padding: 18px 22px;
}

.card-filter-kehadiran .form-control {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 10px 14px;
    height: 42px;
    background: #fafbfc;
    transition: all 0.2s ease;
}

.card-filter-kehadiran .form-control:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

.card-filter-kehadiran label {
    font-weight: 600;
    color: #4a5568;
    font-size: 12px;
    margin-bottom: 4px;
}

.btn-filter-kehadiran {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-filter-kehadiran:hover {
    background: #1f507a;
    color: #ffffff;
}

.btn-reset-kehadiran {
    background: #f0f4f8;
    color: #4a5568;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-reset-kehadiran:hover {
    background: #e2e8f0;
    color: #1a2634;
    text-decoration: none;
}

/* Stat Mini */
.stat-mini-kehadiran-laporan {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.stat-mini-kehadiran-laporan.hadir {
    background: #d1fae5;
    color: #047857;
}

.stat-mini-kehadiran-laporan.tidak {
    background: #fee2e2;
    color: #b91c1c;
}

/* Card Tabel */
.card-laporan-kehadiran {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-laporan-kehadiran .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    background: #f8f9fc;
}

.card-laporan-kehadiran .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-laporan-kehadiran .card-header-custom h6 i {
    color: #2c6b9e;
    margin-right: 8px;
}

.card-laporan-kehadiran .card-header-custom .badge-count {
    background: #e8f0fe;
    color: #2c6b9e;
    padding: 2px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Tabel */
.table-laporan-kehadiran {
    font-size: 13px;
    margin: 0;
}

.table-laporan-kehadiran thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 14px;
    border-bottom: 2px solid #edf2f7;
}

.table-laporan-kehadiran tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-laporan-kehadiran tbody tr:hover {
    background: #fafbfc;
}

.badge-status-kehadiran {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-status-kehadiran.hadir {
    background: #d1fae5;
    color: #047857;
}

.badge-status-kehadiran.tidak {
    background: #fee2e2;
    color: #b91c1c;
}

.empty-state-kehadiran {
    text-align: center;
    padding: 40px 20px;
}

.empty-state-kehadiran i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 12px;
    display: block;
}

@media (max-width: 768px) {
    .laporan-kehadiran-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .btn-cetak-kehadiran {
        justify-content: center;
    }
}

@media print {
    .btn-cetak-kehadiran { display: none; }
    .card-filter-kehadiran { display: none; }
    .laporan-kehadiran-header { box-shadow: none; border: 1px solid #ddd; }
    .card-laporan-kehadiran { border: 1px solid #ddd; box-shadow: none; }
    .table-laporan-kehadiran thead th { background: #f0f0f0 !important; }
}
</style>

<div class="laporan-kehadiran-container">

    <!-- HEADER -->
    <div class="laporan-kehadiran-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-user-check"></i>
                Laporan Kehadiran
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Rekap kehadiran anak pada kegiatan Posyandu Bougenvil Belik
            </div>
        </div>
        <button onclick="window.print()" class="btn-cetak-kehadiran">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <!-- FILTER -->
    <div class="card-filter-kehadiran">
        <div class="card-body">
            <form method="GET">
                <input type="hidden" name="url" value="laporan-kehadiran">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label>Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" value="<?= $tanggal_awal ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" value="<?= $tanggal_akhir ?>" class="form-control">
                    </div>
                    <div class="col-md-4" style="display: flex; gap: 8px;">
                        <button type="submit" class="btn-filter-kehadiran">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <a href="index.php?url=laporan-kehadiran" class="btn-reset-kehadiran">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card-laporan-kehadiran">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-list"></i> Data Kehadiran
                <span class="badge-count"><?= $total ?></span>
                <?php if($total > 0): ?>
                    <span class="stat-mini-kehadiran-laporan hadir">
                        <i class="fas fa-check-circle"></i> <?= $totalHadir ?>
                    </span>
                    <span class="stat-mini-kehadiran-laporan tidak">
                        <i class="fas fa-times-circle"></i> <?= $totalTidak ?>
                    </span>
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-laporan-kehadiran">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pertemuan</th>
                            <th>Nama Anak</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($data): ?>
                            <?php foreach($data as $d): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($d['tanggal'])) ?></td>
                                <td>Pertemuan <?= $d['pertemuan_ke'] ?></td>
                                <td><strong><?= htmlspecialchars($d['nama']) ?></strong></td>
                                <td><?= htmlspecialchars($d['lokasi']) ?></td>
                                <td>
                                    <span class="badge-status-kehadiran <?= $d['status_hadir'] ?>">
                                        <?= ucfirst($d['status_hadir']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state-kehadiran">
                                        <i class="fas fa-calendar-times"></i>
                                        <h6>Tidak Ada Data Kehadiran</h6>
                                        <p>Belum ada data kehadiran yang tercatat</p>
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