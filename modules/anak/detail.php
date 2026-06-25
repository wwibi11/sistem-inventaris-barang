<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

// DATA ANAK
$stmt = $pdo->prepare("
SELECT
    a.*,
    TIMESTAMPDIFF(YEAR, a.tanggal_lahir, CURDATE()) AS umur_tahun,
    TIMESTAMPDIFF(MONTH, a.tanggal_lahir, CURDATE()) % 12 AS umur_bulan
FROM anak a
WHERE a.id = ?
");
$stmt->execute([$id]);
$anak = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anak) {
    die("Data anak tidak ditemukan");
}

// TOTAL KEHADIRAN
$stmt = $pdo->prepare("SELECT COUNT(*) FROM kehadiran WHERE id_anak=? AND status_hadir='hadir'");
$stmt->execute([$id]);
$totalHadir = $stmt->fetchColumn();

// TOTAL PEMERIKSAAN
$stmt = $pdo->prepare("SELECT COUNT(*) FROM pemeriksaan WHERE id_anak=?");
$stmt->execute([$id]);
$totalPeriksa = $stmt->fetchColumn();

// TOTAL IMUNISASI
$stmt = $pdo->prepare("SELECT COUNT(*) FROM imunisasi WHERE id_anak=?");
$stmt->execute([$id]);
$totalImunisasi = $stmt->fetchColumn();

// RIWAYAT KEHADIRAN
$stmt = $pdo->prepare("
SELECT k.tanggal, k.pertemuan_ke, k.lokasi, h.status_hadir
FROM kehadiran h
JOIN kegiatan k ON k.id = h.id_kegiatan
WHERE h.id_anak=?
ORDER BY k.tanggal DESC
");
$stmt->execute([$id]);
$riwayatHadir = $stmt->fetchAll(PDO::FETCH_ASSOC);

// RIWAYAT PEMERIKSAAN
$stmt = $pdo->prepare("
SELECT p.*, k.tanggal
FROM pemeriksaan p
JOIN kegiatan k ON k.id = p.id_kegiatan
WHERE p.id_anak=?
ORDER BY k.tanggal DESC
");
$stmt->execute([$id]);
$riwayatPeriksa = $stmt->fetchAll(PDO::FETCH_ASSOC);

// RIWAYAT IMUNISASI
$stmt = $pdo->prepare("
SELECT i.*, k.tanggal AS tanggal_kegiatan, k.pertemuan_ke, u.nama AS petugas
FROM imunisasi i
LEFT JOIN kegiatan k ON k.id = i.id_kegiatan
LEFT JOIN users u ON u.id = i.diberikan_oleh
WHERE i.id_anak = ?
ORDER BY i.tanggal DESC
");
$stmt->execute([$id]);
$imunisasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.detail-container { padding: 15px 0; }

/* Header */
.detail-header {
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

.detail-header h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.detail-header h4 i { color: #2c6b9e; margin-right: 10px; }

.detail-header .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Card */
.card-detail {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 20px;
}

.card-detail .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    font-weight: 600;
    color: #1a2634;
    font-size: 14px;
    background: #f8f9fc;
}

.card-detail .card-header-custom i { margin-right: 8px; color: #2c6b9e; }

.card-detail .card-body-custom { padding: 20px; }

/* Info Table */
.table-info {
    font-size: 13px;
    margin: 0;
}

.table-info tr td { padding: 6px 0; border: none; }
.table-info tr td:first-child { width: 180px; font-weight: 600; color: #4a5568; }

/* Stat Cards */
.stat-card-detail {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    padding: 16px 20px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
}

.stat-card-detail .stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #1a2634;
}

.stat-card-detail .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

.stat-card-detail .stat-icon {
    font-size: 24px;
    margin-bottom: 6px;
    display: block;
}

.stat-card-detail.primary .stat-icon { color: #2c6b9e; }
.stat-card-detail.success .stat-icon { color: #28a745; }
.stat-card-detail.info .stat-icon { color: #17a2b8; }

/* Tabs */
.nav-tabs-custom .nav-link {
    border: none;
    color: #8a94a6;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.nav-tabs-custom .nav-link:hover {
    background: #f0f4f8;
    color: #2c6b9e;
}

.nav-tabs-custom .nav-link.active {
    background: #e8f0fe;
    color: #2c6b9e;
    font-weight: 600;
}

/* Table */
.table-riwayat {
    font-size: 13px;
    margin: 0;
}

.table-riwayat thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 14px;
    border-bottom: 2px solid #edf2f7;
}

.table-riwayat tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.btn-back {
    background: #f0f4f8;
    color: #4a5568;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-back:hover {
    background: #e2e8f0;
    color: #1a2634;
    text-decoration: none;
}

.btn-growth {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-growth:hover {
    background: #1f507a;
    color: #ffffff;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(44, 107, 158, 0.25);
}

.badge-imunisasi {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-imunisasi.hb0 { background: #e5e7eb; color: #374151; }
.badge-imunisasi.bcg { background: #d1fae5; color: #047857; }
.badge-imunisasi.polio { background: #dbeafe; color: #1d4ed8; }
.badge-imunisasi.dpt { background: #ede9fe; color: #6d28d9; }
.badge-imunisasi.campak { background: #fee2e2; color: #b91c1c; }
.badge-imunisasi.mr { background: #fef3c7; color: #92400e; }

@media (max-width: 768px) {
    .detail-header { flex-direction: column; align-items: stretch; }
    .detail-header .text-right { text-align: left !important; margin-top: 10px; }
    .table-info tr td:first-child { width: 120px; }
}
</style>

<div class="detail-container">

    <!-- HEADER -->
    <div class="detail-header">
        <div>
            <h4><i class="fas fa-child"></i> Detail Anak</h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Informasi dan riwayat kesehatan anak
            </div>
        </div>
        <div class="d-flex gap-2" style="gap: 8px;">
            <a href="index.php?url=anak-pertumbuhan&id=<?= $anak['id'] ?>" class="btn-growth">
                <i class="fas fa-chart-line"></i> Grafik Pertumbuhan
            </a>
            <a href="index.php?url=anak" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- BIODATA -->
    <div class="card-detail">
        <div class="card-header-custom">
            <i class="fas fa-id-card"></i> Biodata Anak
        </div>
        <div class="card-body-custom">
            <div class="row">
                <div class="col-md-8">
                    <h5 style="color: #1a2634; font-weight: 700; margin-bottom: 12px;">
                        <?= htmlspecialchars($anak['nama']) ?>
                    </h5>
                    <table class="table table-info">
                        <tr><td>NIK</td><td><?= htmlspecialchars($anak['nik'] ?? '-') ?></td></tr>
                        <tr><td>Jenis Kelamin</td><td><?= $anak['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td></tr>
                        <tr><td>Tanggal Lahir</td><td><?= date('d M Y', strtotime($anak['tanggal_lahir'])) ?></td></tr>
                        <tr><td>Umur</td><td><?= $anak['umur_tahun'] ?> Tahun <?= $anak['umur_bulan'] ?> Bulan</td></tr>
                        <tr><td>Nama Ayah</td><td><?= htmlspecialchars($anak['nama_ayah'] ?? '-') ?></td></tr>
                        <tr><td>Nama Ibu</td><td><?= htmlspecialchars($anak['nama_ibu'] ?? '-') ?></td></tr>
                        <tr><td>Status</td><td><span class="badge-status <?= $anak['status'] ?? 'aktif' ?>"><?= ucfirst($anak['status'] ?? 'aktif') ?></span></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- RINGKASAN -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card-detail primary">
                <span class="stat-icon"><i class="fas fa-calendar-check"></i></span>
                <div class="stat-number"><?= $totalHadir ?></div>
                <div class="stat-label">Total Kehadiran</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-detail success">
                <span class="stat-icon"><i class="fas fa-stethoscope"></i></span>
                <div class="stat-number"><?= $totalPeriksa ?></div>
                <div class="stat-label">Total Pemeriksaan</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-detail info">
                <span class="stat-icon"><i class="fas fa-syringe"></i></span>
                <div class="stat-number"><?= $totalImunisasi ?></div>
                <div class="stat-label">Total Imunisasi</div>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs-custom mb-3" style="border-bottom: 1px solid #edf2f7;">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#kehadiran">
                <i class="fas fa-calendar-check"></i> Kehadiran
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#pemeriksaan">
                <i class="fas fa-stethoscope"></i> Pemeriksaan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#imunisasi">
                <i class="fas fa-syringe"></i> Imunisasi
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- KEHADIRAN -->
        <div class="tab-pane fade show active" id="kehadiran">
            <div class="card-detail">
                <div class="card-body-custom p-0">
                    <div class="table-responsive">
                        <table class="table table-riwayat">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pertemuan</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($riwayatHadir): ?>
                                    <?php foreach($riwayatHadir as $r): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
                                        <td><?= $r['pertemuan_ke'] ?></td>
                                        <td><?= htmlspecialchars($r['lokasi']) ?></td>
                                        <td><span class="badge-status aktif"><?= ucfirst($r['status_hadir']) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data kehadiran</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- PEMERIKSAAN -->
        <div class="tab-pane fade" id="pemeriksaan">
            <div class="card-detail">
                <div class="card-body-custom p-0">
                    <div class="table-responsive">
                        <table class="table table-riwayat">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>BB (Kg)</th>
                                    <th>TB (Cm)</th>
                                    <th>LK (Cm)</th>
                                    <th>Status Gizi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($riwayatPeriksa): ?>
                                    <?php foreach($riwayatPeriksa as $r): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
                                        <td><?= $r['berat_badan'] ?></td>
                                        <td><?= $r['tinggi_badan'] ?></td>
                                        <td><?= $r['lingkar_kepala'] ?></td>
                                        <td><span class="badge-status <?= $r['status_gizi'] == 'normal' ? 'aktif' : 'pindah' ?>"><?= ucfirst($r['status_gizi'] ?? '-') ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data pemeriksaan</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- IMUNISASI -->
        <div class="tab-pane fade" id="imunisasi">
            <div class="card-detail">
                <div class="card-body-custom p-0">
                    <div class="table-responsive">
                        <table class="table table-riwayat">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis Imunisasi</th>
                                    <th>Kegiatan</th>
                                    <th>Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($imunisasi): ?>
                                    <?php foreach($imunisasi as $i): 
                                        $badgeClass = 'hb0';
                                        $namaImunisasi = $i['jenis_imunisasi'];
                                        switch($i['jenis_imunisasi']){
                                            case 'HB0': $badgeClass = 'hb0'; $namaImunisasi = 'Hepatitis B (HB0)'; break;
                                            case 'BCG': $badgeClass = 'bcg'; $namaImunisasi = 'BCG'; break;
                                            case 'Polio': $badgeClass = 'polio'; $namaImunisasi = 'Polio'; break;
                                            case 'DPT-HB-Hib': $badgeClass = 'dpt'; $namaImunisasi = 'DPT-HB-Hib'; break;
                                            case 'Campak': $badgeClass = 'campak'; $namaImunisasi = 'Campak'; break;
                                            case 'MR': $badgeClass = 'mr'; $namaImunisasi = 'Measles Rubella (MR)'; break;
                                        }
                                    ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($i['tanggal'])) ?></td>
                                        <td><span class="badge-imunisasi <?= $badgeClass ?>"><?= htmlspecialchars($namaImunisasi) ?></span></td>
                                        <td><?= $i['pertemuan_ke'] ? 'Pertemuan '.$i['pertemuan_ke'] : '-' ?></td>
                                        <td><?= htmlspecialchars($i['petugas'] ?? '-') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data imunisasi</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>