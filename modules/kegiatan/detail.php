<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = $_GET['id'] ?? 0;

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
    header("Location: index.php?url=kegiatan");
    exit;
}

// SIMPAN KEHADIRAN
if (isset($_POST['simpan_kehadiran'])) {
    foreach ($_POST['hadir'] as $id_anak => $status) {
        $stmt = $pdo->prepare("
            INSERT INTO kehadiran (id_anak, id_kegiatan, status_hadir, dicatat_oleh)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status_hadir = VALUES(status_hadir)
        ");
        $stmt->execute([$id_anak, $id_kegiatan, $status, $_SESSION['user']['id']]);
    }
    header("Location: index.php?url=kegiatan-detail&id=".$id_kegiatan);
    exit;
}

// DATA ANAK
$anak = $pdo->query("SELECT * FROM anak WHERE status='aktif' ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// KEHADIRAN
$q = $pdo->prepare("SELECT * FROM kehadiran WHERE id_kegiatan=?");
$q->execute([$id_kegiatan]);
$kehadiranData = [];
foreach($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $kehadiranData[$row['id_anak']] = $row;
}

// ANAK HADIR
$q = $pdo->prepare("
SELECT a.* FROM kehadiran h
JOIN anak a ON a.id = h.id_anak
WHERE h.id_kegiatan=? AND h.status_hadir='hadir'
ORDER BY a.nama
");
$q->execute([$id_kegiatan]);
$anakHadir = $q->fetchAll(PDO::FETCH_ASSOC);

// STATISTIK
$totalAnak = count($anak);
$totalHadir = $pdo->prepare("SELECT COUNT(*) FROM kehadiran WHERE id_kegiatan=? AND status_hadir='hadir'");
$totalHadir->execute([$id_kegiatan]);
$totalHadir = $totalHadir->fetchColumn();

$totalPemeriksaan = $pdo->prepare("SELECT COUNT(*) FROM pemeriksaan WHERE id_kegiatan=?");
$totalPemeriksaan->execute([$id_kegiatan]);
$totalPemeriksaan = $totalPemeriksaan->fetchColumn();

$totalImunisasi = $pdo->prepare("SELECT COUNT(*) FROM imunisasi WHERE id_kegiatan=?");
$totalImunisasi->execute([$id_kegiatan]);
$totalImunisasi = $totalImunisasi->fetchColumn();

$progress = 0;
if($totalHadir > 0) {
    $progress = (($totalPemeriksaan + $totalImunisasi) / ($totalHadir * 2)) * 100;
    $progress = min(100, round($progress));
}
?>

<style>
.detail-kegiatan-container { padding: 10px 0; }

/* Card Header */
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

.card-header-kegiatan .badge-status-kegiatan {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-status-kegiatan.selesai {
    background: #d1fae5;
    color: #047857;
}

.badge-status-kegiatan.scheduled {
    background: #fef3c7;
    color: #92400e;
}

/* Stat Cards */
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

/* Progress */
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

/* Tabs */
.nav-tabs-custom-kegiatan .nav-link {
    border: none;
    color: #8a94a6;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 8px 8px 0 0;
    transition: all 0.2s ease;
}

.nav-tabs-custom-kegiatan .nav-link:hover {
    background: #f0f4f8;
    color: #2c6b9e;
}

.nav-tabs-custom-kegiatan .nav-link.active {
    background: #e8f0fe;
    color: #2c6b9e;
    font-weight: 600;
    border-bottom: 3px solid #2c6b9e;
}

/* Tab Content */
.tab-content-kegiatan {
    background: #ffffff;
    border: 1px solid #e8ecf1;
    border-top: none;
    border-radius: 0 0 12px 12px;
    padding: 20px;
}

.table-kegiatan-detail {
    font-size: 13px;
    margin: 0;
}

.table-kegiatan-detail thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 14px;
    border-bottom: 2px solid #edf2f7;
}

.table-kegiatan-detail tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.alert-info-custom {
    border-radius: 10px;
    border: none;
    background: #e8f0fe;
    color: #1a2634;
    padding: 12px 16px;
}

.alert-info-custom i { color: #2c6b9e; margin-right: 8px; }

.btn-sm-kegiatan {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .card-header-kegiatan .d-flex {
        flex-direction: column;
        align-items: stretch !important;
        gap: 10px;
    }
    .card-header-kegiatan .text-right {
        text-align: left !important;
    }
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
        <div class="col-md-4 mb-3">
            <div class="stat-card-detail-kegiatan primary">
                <div class="stat-number"><?= $totalHadir ?></div>
                <div class="stat-label"><i class="fas fa-user-check"></i> Anak Hadir</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-detail-kegiatan success">
                <div class="stat-number"><?= $totalPemeriksaan ?></div>
                <div class="stat-label"><i class="fas fa-stethoscope"></i> Pemeriksaan</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-detail-kegiatan info">
                <div class="stat-number"><?= $totalImunisasi ?></div>
                <div class="stat-label"><i class="fas fa-syringe"></i> Imunisasi</div>
            </div>
        </div>
    </div>

    <!-- PROGRESS -->
    <div class="card mb-4" style="border: 1px solid #e8ecf1; border-radius: 12px;">
        <div class="card-body" style="padding: 16px 20px;">
            <div class="d-flex justify-content-between mb-2" style="font-size: 13px;">
                <strong style="color: #4a5568;">Progress Kegiatan</strong>
                <strong style="color: #1a2634;"><?= $progress ?>%</strong>
            </div>
            <div class="progress-kegiatan-detail">
                <div class="progress-bar" style="width: <?= $progress ?>%;"></div>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs-custom-kegiatan">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#kehadiran">
                <i class="fas fa-user-check"></i> Kehadiran
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

    <div class="tab-content-kegiatan">
        <!-- KEHADIRAN -->
        <div class="tab-pane fade show active" id="kehadiran">
            <form method="POST">
                <div class="table-responsive">
                    <table class="table table-kegiatan-detail">
                        <thead>
                            <tr>
                                <th>Nama Anak</th>
                                <th width="200">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($anak as $a): 
                                $status = $kehadiranData[$a['id']]['status_hadir'] ?? 'hadir';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($a['nama']) ?></td>
                                <td>
                                    <select name="hadir[<?= $a['id'] ?>]" class="form-control form-control-sm" style="border-radius: 8px;">
                                        <option value="hadir" <?= $status == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                        <option value="tidak" <?= $status == 'tidak' ? 'selected' : '' ?>>Tidak Hadir</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(count($anak) == 0): ?>
                            <tr><td colspan="2" class="text-center text-muted py-3">Belum ada data anak</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" name="simpan_kehadiran" class="btn btn-primary btn-sm-kegiatan">
                    <i class="fas fa-save"></i> Simpan Kehadiran
                </button>
            </form>
        </div>

        <!-- PEMERIKSAAN -->
        <div class="tab-pane fade" id="pemeriksaan">
            <div class="alert-info-custom mb-3">
                <i class="fas fa-info-circle"></i> Hanya anak yang hadir yang dapat diperiksa.
            </div>
            <div class="text-right mb-3">
                <a href="index.php?url=pemeriksaan-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-success btn-sm-kegiatan">
                    <i class="fas fa-plus-circle"></i> Input Pemeriksaan
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-kegiatan-detail">
                    <thead>
                        <tr><th>Nama Anak</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($anakHadir as $a): ?>
                        <tr><td><?= htmlspecialchars($a['nama']) ?></td></tr>
                        <?php endforeach; ?>
                        <?php if(count($anakHadir) == 0): ?>
                        <tr><td class="text-center text-muted py-3">Tidak ada anak yang hadir</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- IMUNISASI -->
        <div class="tab-pane fade" id="imunisasi">
            <div class="alert-info-custom mb-3">
                <i class="fas fa-info-circle"></i> Hanya anak yang hadir yang dapat diimunisasi.
            </div>
            <div class="text-right mb-3">
                <a href="index.php?url=imunisasi-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-primary btn-sm-kegiatan">
                    <i class="fas fa-plus-circle"></i> Input Imunisasi
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-kegiatan-detail">
                    <thead>
                        <tr><th>Nama Anak</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($anakHadir as $a): ?>
                        <tr><td><?= htmlspecialchars($a['nama']) ?></td></tr>
                        <?php endforeach; ?>
                        <?php if(count($anakHadir) == 0): ?>
                        <tr><td class="text-center text-muted py-3">Tidak ada anak yang hadir</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>