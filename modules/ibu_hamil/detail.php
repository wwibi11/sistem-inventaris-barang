<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

/* DATA IBU HAMIL */
$stmt = $pdo->prepare("
SELECT
    ih.*,
    k.no_kk,
    k.nama_kepala_keluarga,
    k.nama_ayah,
    k.alamat,
    TIMESTAMPDIFF(YEAR, ih.tanggal_lahir, CURDATE()) AS umur
FROM ibu_hamil ih
JOIN keluarga k ON k.id = ih.id_keluarga
WHERE ih.id = ?
");

$stmt->execute([$id]);
$ibu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ibu) {
    echo "
    <script>
        alert('Data ibu hamil tidak ditemukan');
        window.parent.location='index.php?url=ibu_hamil';
    </script>
    ";
    exit;
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

/* TOTAL PEMERIKSAAN */
$stmt = $pdo->prepare("
SELECT COUNT(*)
FROM pemeriksaan_ibu_hamil
WHERE ibu_hamil_id = ?
");
$stmt->execute([$id]);
$totalPeriksa = $stmt->fetchColumn();

/* TOTAL IMUNISASI */
$stmt = $pdo->prepare("
SELECT COUNT(*)
FROM imunisasi_ibu_hamil
WHERE ibu_hamil_id = ?
");
$stmt->execute([$id]);
$totalImunisasi = $stmt->fetchColumn();

/* RIWAYAT PEMERIKSAAN */
$stmt = $pdo->prepare("
SELECT *
FROM pemeriksaan_ibu_hamil
WHERE ibu_hamil_id = ?
ORDER BY tanggal_periksa DESC
");
$stmt->execute([$id]);
$riwayatPeriksa = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* RIWAYAT IMUNISASI */
$stmt = $pdo->prepare("
SELECT
    iih.*,
    mi.nama_imunisasi,
    u.nama AS petugas
FROM imunisasi_ibu_hamil iih
LEFT JOIN master_imunisasi mi
    ON mi.id = iih.imunisasi_id
LEFT JOIN users u
    ON u.id = iih.diberikan_oleh
WHERE iih.ibu_hamil_id = ?
ORDER BY iih.tanggal DESC
");
$stmt->execute([$id]);
$riwayatImunisasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung usia kehamilan dalam minggu dan hari
$usia_minggu = $ibu['usia_kehamilan'] ?? 0;
$trimester = getTrimester($usia_minggu);
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
    cursor: pointer;
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
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-back:hover {
    background: #e2e8f0;
    color: #1a2634;
    text-decoration: none;
}

/* Badge Status */
.badge-status {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-status.Aktif { background: #d1fae5; color: #047857; }
.badge-status.Melahirkan { background: #dbeafe; color: #1d4ed8; }
.badge-status.Pindah { background: #fef3c7; color: #92400e; }

.badge-trimester {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-trimester.t1 { background: #dbeafe; color: #1d4ed8; }
.badge-trimester.t2 { background: #fef3c7; color: #92400e; }
.badge-trimester.t3 { background: #fce4ec; color: #9c27b0; }
.badge-trimester.t0 { background: #f3f4f6; color: #6b7280; }

.badge-imunisasi {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-imunisasi.tt1 { background: #dbeafe; color: #1d4ed8; }
.badge-imunisasi.tt2 { background: #fef3c7; color: #92400e; }
.badge-imunisasi.ttb { background: #ede9fe; color: #6d28d9; }
.badge-imunisasi.default { background: #e5e7eb; color: #374151; }

@media (max-width: 768px) {
    .detail-header { flex-direction: column; align-items: stretch; }
    .detail-header .text-right { text-align: left !important; margin-top: 10px; }
    .table-info tr td:first-child { width: 120px; }
    .nav-tabs-custom .nav-link { padding: 8px 12px; font-size: 12px; }
}
</style>

<div class="detail-container">

    <!-- HEADER -->
    <div class="detail-header">
        <div>
            <h4><i class="fas fa-person-pregnant"></i> Detail Ibu Hamil</h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Informasi dan riwayat kesehatan ibu hamil
            </div>
        </div>
        <div>
            <a href="index.php?url=ibu_hamil" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- BIODATA -->
    <div class="card-detail">
        <div class="card-header-custom">
            <i class="fas fa-id-card"></i> Biodata Ibu Hamil
        </div>
        <div class="card-body-custom">
            <div class="row">
                <div class="col-md-8">
                    <h5 style="color: #1a2634; font-weight: 700; margin-bottom: 12px;">
                        <i class="fas fa-female" style="color: #2c6b9e;"></i> <?= htmlspecialchars($ibu['nama_ibu']) ?>
                    </h5>
                    <table class="table table-info">
                        <tr><td>NIK</td><td><?= htmlspecialchars($ibu['nik'] ?? '-') ?></td></tr>
                        <tr><td>Keluarga</td><td><?= htmlspecialchars($ibu['nama_kepala_keluarga'] ?? '-') ?></td></tr>
                        <tr><td>No KK</td><td><?= htmlspecialchars($ibu['no_kk'] ?? '-') ?></td></tr>
                        <tr><td>Tempat Lahir</td><td><?= htmlspecialchars($ibu['tempat_lahir'] ?? '-') ?></td></tr>
                        <tr><td>Tanggal Lahir</td><td><?= formatDate($ibu['tanggal_lahir'] ?? '') ?></td></tr>
                        <tr><td>Umur</td><td><?= ($ibu['umur'] ?? 0) . ' Tahun' ?></td></tr>
                        <tr><td>Hamil Ke</td><td><?= htmlspecialchars($ibu['hamil_ke'] ?? '-') ?></td></tr>
                        <tr><td>Usia Kehamilan</td><td>
                            <?php 
                            $usia = $ibu['usia_kehamilan'] ?? 0;
                            echo $usia > 0 ? $usia . ' Minggu' : '-';
                            ?>
                        </td></tr>
                        <tr><td>Trimester</td><td>
                            <?php
                            $class = 't0';
                            if ($trimester == 1) $class = 't1';
                            elseif ($trimester == 2) $class = 't2';
                            elseif ($trimester == 3) $class = 't3';
                            ?>
                            <span class="badge-trimester <?= $class ?>">
                                <?= $trimester > 0 ? 'Trimester ' . $trimester : '-' ?>
                            </span>
                        </td></tr>
                        <tr><td>HPHT</td><td><?= formatDate($ibu['hpht'] ?? '') ?></td></tr>
                        <tr><td>HPL</td><td><?= formatDate($ibu['hpl'] ?? '') ?></td></tr>
                        <tr><td>No HP</td><td><?= htmlspecialchars($ibu['no_hp'] ?? '-') ?></td></tr>
                        <tr><td>Alamat</td><td><?= htmlspecialchars($ibu['alamat'] ?? '-') ?></td></tr>
                        <tr><td>Status</td><td>
                            <span class="badge-status <?= $ibu['status'] ?? 'Aktif' ?>">
                                <?= $ibu['status'] ?? 'Aktif' ?>
                            </span>
                        </td></tr>
                        <tr><td>Tanggal Daftar</td><td><?= $ibu['created_at'] ? date('d M Y H:i', strtotime($ibu['created_at'])) : '-' ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- RINGKASAN -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="stat-card-detail primary">
                <span class="stat-icon"><i class="fas fa-stethoscope"></i></span>
                <div class="stat-number"><?= $totalPeriksa ?></div>
                <div class="stat-label">Total Pemeriksaan</div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="stat-card-detail success">
                <span class="stat-icon"><i class="fas fa-syringe"></i></span>
                <div class="stat-number"><?= $totalImunisasi ?></div>
                <div class="stat-label">Total Imunisasi</div>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs-custom mb-3" style="border-bottom: 1px solid #edf2f7;">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#pemeriksaan">
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
        <!-- PEMERIKSAAN -->
        <div class="tab-pane fade show active" id="pemeriksaan">
            <div class="card-detail">
                <div class="card-body-custom p-0">
                    <div class="table-responsive">
                        <table class="table table-riwayat">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Berat Badan (Kg)</th>
                                    <th>Tekanan Darah</th>
                                    <th>Tinggi Fundus (cm)</th>
                                    <th>Keluhan</th>
                                    <th>Tindakan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($riwayatPeriksa): ?>
                                    <?php foreach($riwayatPeriksa as $r): ?>
                                    <tr>
                                        <td><?= formatDate($r['tanggal_periksa']) ?></td>
                                        <td><?= $r['berat_badan'] ?? '-' ?></td>
                                        <td><?= htmlspecialchars($r['tekanan_darah'] ?? '-') ?></td>
                                        <td><?= $r['tinggi_fundus'] ?? '-' ?></td>
                                        <td><?= htmlspecialchars($r['keluhan'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($r['tindakan'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-3">Belum ada data pemeriksaan</td></tr>
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
                                    <th>Petugas</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($riwayatImunisasi): ?>
                                    <?php foreach($riwayatImunisasi as $r): 
                                        $badgeClass = 'default';
                                        $namaImunisasi = $r['nama_imunisasi'] ?? 'Imunisasi';
                                        if (strpos($namaImunisasi, 'TT 1') !== false) $badgeClass = 'tt1';
                                        elseif (strpos($namaImunisasi, 'TT 2') !== false) $badgeClass = 'tt2';
                                        elseif (strpos($namaImunisasi, 'TT Booster') !== false) $badgeClass = 'ttb';
                                    ?>
                                    <tr>
                                        <td><?= formatDate($r['tanggal']) ?></td>
                                        <td>
                                            <span class="badge-imunisasi <?= $badgeClass ?>">
                                                <?= htmlspecialchars($namaImunisasi) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($r['petugas'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
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

<!-- Scripts untuk tab -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    // Active tab
    $('.nav-tabs-custom .nav-link').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
});
</script>