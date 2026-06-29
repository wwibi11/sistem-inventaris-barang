<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = $_GET['id'] ?? 0;
$tab = $_GET['tab'] ?? 'kehadiran_anak';

// DATA KEGIATAN
$stmt = $pdo->prepare("
SELECT k.*, u.nama AS pembuat
FROM kegiatan k
LEFT JOIN users u ON u.id = k.created_by
WHERE k.id=?
");
$stmt->execute([$id_kegiatan]);
$kegiatan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kegiatan) {
    echo "<script>window.location='index.php?url=kegiatan';</script>";
    exit;
}

// STATISTIK
$totalHadir = $pdo->prepare("SELECT COUNT(*) FROM kehadiran WHERE id_kegiatan=? AND status_hadir='hadir'");
$totalHadir->execute([$id_kegiatan]);
$totalHadir = $totalHadir->fetchColumn();

$totalPemeriksaan = $pdo->prepare("SELECT COUNT(*) FROM pemeriksaan WHERE id_kegiatan=?");
$totalPemeriksaan->execute([$id_kegiatan]);
$totalPemeriksaan = $totalPemeriksaan->fetchColumn();

$totalImunisasi = $pdo->prepare("SELECT COUNT(*) FROM imunisasi WHERE id_kegiatan=?");
$totalImunisasi->execute([$id_kegiatan]);
$totalImunisasi = $totalImunisasi->fetchColumn();

$totalHadirIbu = $pdo->prepare("SELECT COUNT(*) FROM kehadiran_ibu_hamil WHERE id_kegiatan=? AND status_hadir='hadir'");
$totalHadirIbu->execute([$id_kegiatan]);
$totalHadirIbu = $totalHadirIbu->fetchColumn();

$totalPemeriksaanIbu = $pdo->prepare("SELECT COUNT(*) FROM pemeriksaan_ibu_hamil WHERE id_kegiatan=?");
$totalPemeriksaanIbu->execute([$id_kegiatan]);
$totalPemeriksaanIbu = $totalPemeriksaanIbu->fetchColumn();

$totalImunisasiIbu = $pdo->prepare("SELECT COUNT(*) FROM imunisasi_ibu_hamil WHERE tanggal=?");
$totalImunisasiIbu->execute([$kegiatan['tanggal']]);
$totalImunisasiIbu = $totalImunisasiIbu->fetchColumn();

// Progress
$progress = 0;
if($totalHadir > 0) {
    $progress = (($totalPemeriksaan + $totalImunisasi) / ($totalHadir * 2)) * 100;
    $progress = min(100, round($progress));
}

$progressIbu = 0;
if($totalHadirIbu > 0) {
    $progressIbu = (($totalPemeriksaanIbu + $totalImunisasiIbu) / ($totalHadirIbu * 2)) * 100;
    $progressIbu = min(100, round($progressIbu));
}

// Tentukan file yang akan di-include
$tabFiles = [
    'kehadiran_anak' => 'detail_kehadiran_anak.php',
    'pemeriksaan_anak' => 'detail_pemeriksaan_anak.php',
    'imunisasi_anak' => 'detail_imunisasi_anak.php',
    'kehadiran_ibu' => 'detail_kehadiran_ibu.php',
    'pemeriksaan_ibu' => 'detail_pemeriksaan_ibu.php',
    'imunisasi_ibu' => 'detail_imunisasi_ibu.php',
];

$tabFile = $tabFiles[$tab] ?? 'detail_kehadiran_anak.php';
$tabPath = __DIR__ . '/' . $tabFile;

// Jika file tidak ada, redirect ke tab default
if (!file_exists($tabPath)) {
    echo "<script>window.location='index.php?url=kegiatan-detail&id=".$id_kegiatan."&tab=kehadiran_anak';</script>";
    exit;
}
?>

<style>
.detail-kegiatan-container { padding: 10px 0; }

/* HEADER */
.card-header-kegiatan {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.card-header-kegiatan .title {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}
.card-header-kegiatan .date {
    font-size: 13px;
    color: #8a94a6;
}

.badge-status-kegiatan {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-status-kegiatan.selesai { background: #d1fae5; color: #047857; }
.badge-status-kegiatan.scheduled { background: #fef3c7; color: #92400e; }

/* STATISTIK */
.stat-card-detail-kegiatan {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px 20px;
    border: 1px solid #e8ecf1;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
}
.stat-card-detail-kegiatan .stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #1a2634;
}
.stat-card-detail-kegiatan .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}
.stat-card-detail-kegiatan.primary .stat-number { color: #2c6b9e; }
.stat-card-detail-kegiatan.success .stat-number { color: #28a745; }
.stat-card-detail-kegiatan.info .stat-number { color: #17a2b8; }
.stat-card-detail-kegiatan.warning .stat-number { color: #e8a317; }

.progress-kegiatan-detail {
    height: 8px;
    border-radius: 4px;
    background: #edf2f7;
}
.progress-kegiatan-detail .progress-bar {
    height: 100%;
    border-radius: 4px;
    background: #28a745;
    transition: width 0.6s ease;
}

/* TABS - MENU KOTAK */
.tabs-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}

.tab-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 14px 10px;
    border: 2px solid #e8ecf1;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    color: #4a5568;
    font-size: 12px;
    font-weight: 500;
}

.tab-card:hover {
    border-color: #2c6b9e;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    text-decoration: none;
    color: #1a2634;
}

.tab-card.active {
    border-color: #2c6b9e;
    background: #e8f0fe;
    color: #2c6b9e;
}

.tab-card .tab-icon {
    font-size: 20px;
    display: block;
    margin-bottom: 4px;
}
.tab-card .tab-badge {
    display: inline-block;
    background: #edf2f7;
    color: #4a5568;
    padding: 0 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
    margin-top: 3px;
}
.tab-card.active .tab-badge {
    background: #2c6b9e;
    color: #ffffff;
}
.tab-card .tab-badge.hadir { background: #d1fae5; color: #047857; }
.tab-card .tab-badge.warning { background: #fef3c7; color: #92400e; }

/* TAB CONTENT */
.tab-content-wrapper {
    background: #ffffff;
    border: 1px solid #e8ecf1;
    border-radius: 12px;
    padding: 20px;
    min-height: 300px;
}

@media (max-width: 992px) {
    .tabs-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 576px) {
    .tabs-grid { grid-template-columns: repeat(2, 1fr); }
    .tab-card { padding: 10px 6px; font-size: 11px; }
    .tab-card .tab-icon { font-size: 16px; }
}
</style>

<div class="detail-kegiatan-container">

    <!-- HEADER -->
    <div class="card-header-kegiatan">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
            <div>
                <div class="title">
                    <i class="fas fa-calendar-alt" style="color: #2c6b9e; margin-right: 8px;"></i>
                    <?= htmlspecialchars($kegiatan['lokasi']) ?>
                </div>
                <div class="date">
                    <i class="far fa-calendar-alt"></i> 
                    <?= date('d M Y', strtotime($kegiatan['tanggal'])) ?>
                    <span class="mx-2">|</span>
                    <span class="badge-status-kegiatan <?= $kegiatan['status'] ?>">
                        <?= ucfirst($kegiatan['status']) ?>
                    </span>
                    <span class="mx-2">|</span>
                    Pertemuan Ke <?= $kegiatan['pertemuan_ke'] ?>
                </div>
            </div>
            <div class="text-right">
                <small class="text-muted">Dibuat oleh</small>
                <div style="font-weight: 500; color: #1a2634;">
                    <?= htmlspecialchars($kegiatan['pembuat'] ?? '-') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- STATISTIK -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card-detail-kegiatan primary">
                <div class="stat-number"><?= $totalHadir ?></div>
                <div class="stat-label"><i class="fas fa-child"></i> Anak Hadir</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-detail-kegiatan success">
                <div class="stat-number"><?= $totalPemeriksaan ?></div>
                <div class="stat-label"><i class="fas fa-stethoscope"></i> Pemeriksaan Anak</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-detail-kegiatan info">
                <div class="stat-number"><?= $totalImunisasi ?></div>
                <div class="stat-label"><i class="fas fa-syringe"></i> Imunisasi Anak</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-detail-kegiatan warning">
                <div class="stat-number"><?= $totalHadirIbu ?></div>
                <div class="stat-label"><i class="fas fa-person-pregnant"></i> Ibu Hamil Hadir</div>
            </div>
        </div>
    </div>

    <!-- PROGRESS -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card" style="border: 1px solid #e8ecf1; border-radius: 12px;">
                <div class="card-body" style="padding: 16px 20px;">
                    <div class="d-flex justify-content-between mb-2" style="font-size: 13px;">
                        <strong style="color: #4a5568;">Progress Anak</strong>
                        <strong style="color: #1a2634;"><?= $progress ?>%</strong>
                    </div>
                    <div class="progress-kegiatan-detail">
                        <div class="progress-bar" style="width: <?= $progress ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card" style="border: 1px solid #e8ecf1; border-radius: 12px;">
                <div class="card-body" style="padding: 16px 20px;">
                    <div class="d-flex justify-content-between mb-2" style="font-size: 13px;">
                        <strong style="color: #4a5568;">Progress Ibu Hamil</strong>
                        <strong style="color: #1a2634;"><?= $progressIbu ?>%</strong>
                    </div>
                    <div class="progress-kegiatan-detail">
                        <div class="progress-bar" style="width: <?= $progressIbu ?>%; background: #e8a317;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS MENU -->
    <div class="tabs-grid">
        <a href="?url=kegiatan-detail&id=<?= $id_kegiatan ?>&tab=kehadiran_anak" class="tab-card <?= $tab == 'kehadiran_anak' ? 'active' : '' ?>">
            <span class="tab-icon">👶</span>
            Kehadiran Anak
            <span class="tab-badge hadir"><?= $totalHadir ?></span>
        </a>
        <a href="?url=kegiatan-detail&id=<?= $id_kegiatan ?>&tab=pemeriksaan_anak" class="tab-card <?= $tab == 'pemeriksaan_anak' ? 'active' : '' ?>">
            <span class="tab-icon">📋</span>
            Pemeriksaan Anak
            <span class="tab-badge"><?= $totalPemeriksaan ?></span>
        </a>
        <a href="?url=kegiatan-detail&id=<?= $id_kegiatan ?>&tab=imunisasi_anak" class="tab-card <?= $tab == 'imunisasi_anak' ? 'active' : '' ?>">
            <span class="tab-icon">💉</span>
            Imunisasi Anak
            <span class="tab-badge"><?= $totalImunisasi ?></span>
        </a>
        <a href="?url=kegiatan-detail&id=<?= $id_kegiatan ?>&tab=kehadiran_ibu" class="tab-card <?= $tab == 'kehadiran_ibu' ? 'active' : '' ?>">
            <span class="tab-icon">🤰</span>
            Kehadiran Ibu
            <span class="tab-badge hadir"><?= $totalHadirIbu ?></span>
        </a>
        <a href="?url=kegiatan-detail&id=<?= $id_kegiatan ?>&tab=pemeriksaan_ibu" class="tab-card <?= $tab == 'pemeriksaan_ibu' ? 'active' : '' ?>">
            <span class="tab-icon">🩺</span>
            Pemeriksaan Ibu
            <span class="tab-badge"><?= $totalPemeriksaanIbu ?></span>
        </a>
        <a href="?url=kegiatan-detail&id=<?= $id_kegiatan ?>&tab=imunisasi_ibu" class="tab-card <?= $tab == 'imunisasi_ibu' ? 'active' : '' ?>">
            <span class="tab-icon">💉</span>
            Imunisasi Ibu
            <span class="tab-badge"><?= $totalImunisasiIbu ?></span>
        </a>
    </div>

    <!-- TAB CONTENT -->
    <div class="tab-content-wrapper">
        <?php include $tabPath; ?>
    </div>

</div>