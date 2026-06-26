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

// ==========================================
// SIMPAN KEHADIRAN ANAK
// ==========================================
if (isset($_POST['simpan_kehadiran_anak'])) {
    foreach ($_POST['hadir_anak'] as $id_anak => $status) {
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

// ==========================================
// SIMPAN KEHADIRAN IBU HAMIL
// ==========================================
if (isset($_POST['simpan_kehadiran_ibu'])) {
    foreach ($_POST['hadir_ibu'] as $id_ibu => $status) {
        $stmt = $pdo->prepare("
            INSERT INTO kehadiran_ibu_hamil (ibu_hamil_id, id_kegiatan, status_hadir, dicatat_oleh)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status_hadir = VALUES(status_hadir)
        ");
        $stmt->execute([$id_ibu, $id_kegiatan, $status, $_SESSION['user']['id']]);
    }
    header("Location: index.php?url=kegiatan-detail&id=".$id_kegiatan);
    exit;
}

// ==========================================
// DATA ANAK
// ==========================================
$anak = $pdo->query("SELECT * FROM anak WHERE status='aktif' ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// KEHADIRAN ANAK
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

// DATA PEMERIKSAAN ANAK
$q = $pdo->prepare("
    SELECT 
        p.*, 
        a.nama,
        a.nik,
        a.tanggal_lahir,
        TIMESTAMPDIFF(MONTH, a.tanggal_lahir, CURDATE()) AS umur_bulan
    FROM pemeriksaan p
    JOIN anak a ON a.id = p.id_anak
    WHERE p.id_kegiatan = ?
    ORDER BY a.nama ASC
");
$q->execute([$id_kegiatan]);
$dataPemeriksaan = $q->fetchAll(PDO::FETCH_ASSOC);

// DATA IMUNISASI ANAK
$q = $pdo->prepare("
    SELECT 
        i.*, 
        a.nama,
        a.nik,
        mi.nama_imunisasi AS master_nama
    FROM imunisasi i
    JOIN anak a ON a.id = i.id_anak
    LEFT JOIN master_imunisasi mi ON mi.id = i.id_master_imunisasi
    WHERE i.id_kegiatan = ?
    ORDER BY a.nama ASC
");
$q->execute([$id_kegiatan]);
$dataImunisasi = $q->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// DATA IBU HAMIL
// ==========================================
$ibuHamil = $pdo->query("SELECT * FROM ibu_hamil WHERE status='Aktif' ORDER BY nama_ibu")->fetchAll(PDO::FETCH_ASSOC);

// KEHADIRAN IBU HAMIL
$q = $pdo->prepare("SELECT * FROM kehadiran_ibu_hamil WHERE id_kegiatan=?");
$q->execute([$id_kegiatan]);
$kehadiranIbuData = [];
foreach($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $kehadiranIbuData[$row['ibu_hamil_id']] = $row;
}

// IBU HAMIL HADIR
$q = $pdo->prepare("
SELECT ih.* FROM kehadiran_ibu_hamil h
JOIN ibu_hamil ih ON ih.id = h.ibu_hamil_id
WHERE h.id_kegiatan=? AND h.status_hadir='hadir'
ORDER BY ih.nama_ibu
");
$q->execute([$id_kegiatan]);
$ibuHamilHadir = $q->fetchAll(PDO::FETCH_ASSOC);

// DATA PEMERIKSAAN IBU HAMIL
$q = $pdo->prepare("
    SELECT 
        pih.*, 
        ih.nama_ibu,
        ih.nik,
        ih.usia_kehamilan
    FROM pemeriksaan_ibu_hamil pih
    JOIN ibu_hamil ih ON ih.id = pih.ibu_hamil_id
    WHERE pih.id_kegiatan = ?
    ORDER BY ih.nama_ibu ASC
");
$q->execute([$id_kegiatan]);
$dataPemeriksaanIbu = $q->fetchAll(PDO::FETCH_ASSOC);

// DATA IMUNISASI IBU HAMIL
$q = $pdo->prepare("
    SELECT 
        iih.*, 
        ih.nama_ibu,
        ih.nik,
        mi.nama_imunisasi AS master_nama,
        u.nama AS petugas
    FROM imunisasi_ibu_hamil iih
    JOIN ibu_hamil ih ON ih.id = iih.ibu_hamil_id
    LEFT JOIN master_imunisasi mi ON mi.id = iih.imunisasi_id
    LEFT JOIN users u ON u.id = iih.diberikan_oleh
    WHERE iih.tanggal = ?
    ORDER BY ih.nama_ibu ASC
");
$q->execute([$kegiatan['tanggal']]);
$dataImunisasiIbu = $q->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// STATISTIK
// ==========================================
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

// Statistik Ibu Hamil
$totalIbuHamil = count($ibuHamil);
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

// Progress Ibu Hamil
$progressIbu = 0;
if($totalHadirIbu > 0) {
    $progressIbu = (($totalPemeriksaanIbu + $totalImunisasiIbu) / ($totalHadirIbu * 2)) * 100;
    $progressIbu = min(100, round($progressIbu));
}

// ==========================================
// FUNGSI-FUNGSI (DEKLARASI SEKALI DI SINI)
// ==========================================

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

// ==========================================
// MULAI HTML
// ==========================================
?>

<style>
    .detail-kegiatan-container { padding: 10px 0; }
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
    .nav-tabs-custom-kegiatan {
        border-bottom: 1px solid #edf2f7;
        margin-bottom: 0;
        display: flex;
        flex-wrap: wrap;
    }
    .nav-tabs-custom-kegiatan .nav-link {
        border: none;
        color: #8a94a6;
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s ease;
        cursor: pointer;
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
    .tab-content-kegiatan {
        background: #ffffff;
        border: 1px solid #e8ecf1;
        border-top: none;
        border-radius: 0 0 12px 12px;
        padding: 20px;
        min-height: 200px;
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
    .table-kegiatan-detail tbody tr:last-child td { border-bottom: none; }
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
    .badge-pemeriksaan {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-pemeriksaan.Baik { background: #d1fae5; color: #047857; }
    .badge-pemeriksaan.Normal { background: #d1fae5; color: #047857; }
    .badge-pemeriksaan.Kurang { background: #fef3c7; color: #92400e; }
    .badge-pemeriksaan.Buruk { background: #fee2e2; color: #b91c1c; }
    .badge-pemeriksaan.Lebih { background: #dbeafe; color: #1d4ed8; }
    .badge-imunisasi-detail {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-imunisasi-detail.primary { background: #dbeafe; color: #1d4ed8; }
    .badge-imunisasi-detail.success { background: #d1fae5; color: #047857; }
    .badge-imunisasi-detail.warning { background: #fef3c7; color: #92400e; }
    .badge-imunisasi-detail.danger { background: #fee2e2; color: #b91c1c; }
    .badge-imunisasi-detail.info { background: #e0f7fa; color: #00838f; }
    .badge-imunisasi-detail.default { background: #f3f4f6; color: #6b7280; }
    .badge-trimester {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-trimester.t1 { background: #dbeafe; color: #1d4ed8; }
    .badge-trimester.t2 { background: #fef3c7; color: #92400e; }
    .badge-trimester.t3 { background: #fce4ec; color: #c62828; }
    .badge-trimester.t0 { background: #f3f4f6; color: #6b7280; }
    .badge-status-ibu {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-status-ibu.Aktif { background: #d1fae5; color: #047857; }
    .badge-status-ibu.Melahirkan { background: #dbeafe; color: #1d4ed8; }
    .badge-status-ibu.Pindah { background: #fef3c7; color: #92400e; }
    @media (max-width: 768px) {
        .card-header-kegiatan .d-flex {
            flex-direction: column;
            align-items: stretch !important;
            gap: 10px;
        }
        .card-header-kegiatan .text-right { text-align: left !important; }
        .nav-tabs-custom-kegiatan .nav-link { padding: 8px 12px; font-size: 12px; }
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

    <!-- TABS UTAMA -->
    <ul class="nav nav-tabs-custom-kegiatan">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#kehadiran-anak">
                <i class="fas fa-child"></i> Kehadiran Anak
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#pemeriksaan-anak">
                <i class="fas fa-stethoscope"></i> Pemeriksaan Anak
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#imunisasi-anak">
                <i class="fas fa-syringe"></i> Imunisasi Anak
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#ibu-hamil">
                <i class="fas fa-person-pregnant"></i> Ibu Hamil
            </a>
        </li>
    </ul>

    <div class="tab-content-kegiatan">
        
        <!-- TAB KEHADIRAN ANAK -->
        <div class="tab-pane fade show active" id="kehadiran-anak">
            <form method="POST">
                <div class="table-responsive">
                    <table class="table table-kegiatan-detail">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Anak</th>
                                <th>NIK</th>
                                <th width="200">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($anak) > 0): ?>
                                <?php $no = 1; ?>
                                <?php foreach($anak as $a): 
                                    $status = $kehadiranData[$a['id']]['status_hadir'] ?? 'hadir';
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($a['nama']) ?></strong></td>
                                    <td><?= htmlspecialchars($a['nik'] ?? '-') ?></td>
                                    <td>
                                        <select name="hadir_anak[<?= $a['id'] ?>]" class="form-control form-control-sm" style="border-radius: 8px;">
                                            <option value="hadir" <?= $status == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                            <option value="tidak" <?= $status == 'tidak' ? 'selected' : '' ?>>Tidak Hadir</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data anak</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" name="simpan_kehadiran_anak" class="btn btn-primary btn-sm-kegiatan">
                    <i class="fas fa-save"></i> Simpan Kehadiran Anak
                </button>
            </form>
        </div>

        <!-- TAB PEMERIKSAAN ANAK -->
        <div class="tab-pane fade" id="pemeriksaan-anak">
            <div class="alert-info-custom mb-3">
                <i class="fas fa-info-circle"></i> 
                <?php if($totalHadir > 0): ?>
                    Menampilkan data pemeriksaan untuk <?= $totalHadir ?> anak yang hadir.
                <?php else: ?>
                    Belum ada anak yang hadir pada kegiatan ini.
                <?php endif; ?>
            </div>
            <div class="text-right mb-3">
                <a href="index.php?url=pemeriksaan-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-success btn-sm-kegiatan">
                    <i class="fas fa-plus-circle"></i> Input Pemeriksaan Anak
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-kegiatan-detail">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Anak</th>
                            <th>Umur</th>
                            <th>BB (Kg)</th>
                            <th>TB (Cm)</th>
                            <th>LK (Cm)</th>
                            <th>Status Gizi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($dataPemeriksaan) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach($dataPemeriksaan as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($p['nama']) ?></strong></td>
                                <td>
                                    <?php 
                                    if(!empty($p['umur_bulan'])) {
                                        $tahun = floor($p['umur_bulan'] / 12);
                                        $bulan = $p['umur_bulan'] % 12;
                                        echo $tahun . ' Th ' . $bulan . ' Bl';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td><?= $p['berat_badan'] ?? '-' ?></td>
                                <td><?= $p['tinggi_badan'] ?? '-' ?></td>
                                <td><?= $p['lingkar_kepala'] ?? '-' ?></td>
                                <td>
                                    <?php 
                                    $status = $p['status_gizi'] ?? '';
                                    if(!empty($status)) {
                                        $class = '';
                                        switch(strtolower($status)) {
                                            case 'normal':
                                            case 'baik':
                                                $class = 'Baik';
                                                break;
                                            case 'kurang':
                                                $class = 'Kurang';
                                                break;
                                            case 'buruk':
                                                $class = 'Buruk';
                                                break;
                                            case 'lebih':
                                            case 'gemuk':
                                                $class = 'Lebih';
                                                break;
                                            default:
                                                $class = '';
                                        }
                                        ?>
                                        <span class="badge-pemeriksaan <?= $class ?>">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    <?php } else { ?>
                                        <span class="text-muted">-</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    Belum ada data pemeriksaan anak
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB IMUNISASI ANAK -->
        <div class="tab-pane fade" id="imunisasi-anak">
            <div class="alert-info-custom mb-3">
                <i class="fas fa-info-circle"></i> 
                <?php if($totalHadir > 0): ?>
                    Menampilkan data imunisasi untuk <?= $totalHadir ?> anak yang hadir.
                <?php else: ?>
                    Belum ada anak yang hadir pada kegiatan ini.
                <?php endif; ?>
            </div>
            <div class="text-right mb-3">
                <a href="index.php?url=imunisasi-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-primary btn-sm-kegiatan">
                    <i class="fas fa-plus-circle"></i> Input Imunisasi Anak
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-kegiatan-detail">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Anak</th>
                            <th>Jenis Imunisasi</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($dataImunisasi) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach($dataImunisasi as $i): 
                                $badgeClass = 'default';
                                $namaImunisasi = $i['master_nama'] ?? $i['jenis_imunisasi'] ?? 'Imunisasi';
                                if(strpos(strtolower($namaImunisasi), 'bcg') !== false) $badgeClass = 'success';
                                elseif(strpos(strtolower($namaImunisasi), 'polio') !== false) $badgeClass = 'info';
                                elseif(strpos(strtolower($namaImunisasi), 'campak') !== false) $badgeClass = 'danger';
                                elseif(strpos(strtolower($namaImunisasi), 'mr') !== false) $badgeClass = 'warning';
                                elseif(strpos(strtolower($namaImunisasi), 'dpt') !== false) $badgeClass = 'primary';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($i['nama']) ?></strong></td>
                                <td>
                                    <span class="badge-imunisasi-detail <?= $badgeClass ?>">
                                        <?= htmlspecialchars($namaImunisasi) ?>
                                    </span>
                                </td>
                                <td><?= $i['tanggal'] ? date('d M Y', strtotime($i['tanggal'])) : '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    Belum ada data imunisasi anak
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB IBU HAMIL -->
        <div class="tab-pane fade" id="ibu-hamil">
            <!-- Kehadiran Ibu Hamil -->
            <h6 class="mb-3" style="color: #1a2634; font-weight: 600;">
                <i class="fas fa-calendar-check" style="color: #2c6b9e;"></i> Kehadiran Ibu Hamil
            </h6>
            <form method="POST">
                <div class="table-responsive mb-4">
                    <table class="table table-kegiatan-detail">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Ibu</th>
                                <th>NIK</th>
                                <th>Usia Kehamilan</th>
                                <th>Trimester</th>
                                <th width="200">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($ibuHamil) > 0): ?>
                                <?php $no = 1; ?>
                                <?php foreach($ibuHamil as $ih): 
                                    $status = $kehadiranIbuData[$ih['id']]['status_hadir'] ?? 'hadir';
                                    $usia = $ih['usia_kehamilan'] ?? 0;
                                    $trimester = getTrimester($usia);
                                    $class = 't0';
                                    if($trimester == 1) $class = 't1';
                                    elseif($trimester == 2) $class = 't2';
                                    elseif($trimester == 3) $class = 't3';
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($ih['nama_ibu']) ?></strong></td>
                                    <td><?= htmlspecialchars($ih['nik'] ?? '-') ?></td>
                                    <td><?= $usia > 0 ? $usia . ' Minggu' : '-' ?></td>
                                    <td>
                                        <span class="badge-trimester <?= $class ?>">
                                            <?= $trimester > 0 ? 'Trimester ' . $trimester : '-' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <select name="hadir_ibu[<?= $ih['id'] ?>]" class="form-control form-control-sm" style="border-radius: 8px;">
                                            <option value="hadir" <?= $status == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                            <option value="tidak" <?= $status == 'tidak' ? 'selected' : '' ?>>Tidak Hadir</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data ibu hamil</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" name="simpan_kehadiran_ibu" class="btn btn-primary btn-sm-kegiatan mb-4">
                    <i class="fas fa-save"></i> Simpan Kehadiran Ibu Hamil
                </button>
            </form>

            <hr style="border-color: #edf2f7; margin: 20px 0;">

            <!-- Pemeriksaan Ibu Hamil -->
            <h6 class="mb-3" style="color: #1a2634; font-weight: 600;">
                <i class="fas fa-stethoscope" style="color: #2c6b9e;"></i> Pemeriksaan Ibu Hamil
            </h6>
            <div class="text-right mb-3">
                <a href="index.php?url=pemeriksaan-ibu-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-success btn-sm-kegiatan">
                    <i class="fas fa-plus-circle"></i> Input Pemeriksaan Ibu
                </a>
            </div>
            <div class="table-responsive mb-4">
                <table class="table table-kegiatan-detail">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Ibu</th>
                            <th>Usia Kehamilan</th>
                            <th>BB (Kg)</th>
                            <th>Tekanan Darah</th>
                            <th>Tinggi Fundus</th>
                            <th>Keluhan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($dataPemeriksaanIbu) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach($dataPemeriksaanIbu as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($p['nama_ibu']) ?></strong></td>
                                <td><?= $p['usia_kehamilan'] ?? '-' ?> Minggu</td>
                                <td><?= $p['berat_badan'] ?? '-' ?></td>
                                <td><?= htmlspecialchars($p['tekanan_darah'] ?? '-') ?></td>
                                <td><?= $p['tinggi_fundus'] ?? '-' ?> cm</td>
                                <td><?= htmlspecialchars($p['keluhan'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    Belum ada data pemeriksaan ibu hamil
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr style="border-color: #edf2f7; margin: 20px 0;">

            <!-- Imunisasi Ibu Hamil -->
            <h6 class="mb-3" style="color: #1a2634; font-weight: 600;">
                <i class="fas fa-syringe" style="color: #2c6b9e;"></i> Imunisasi Ibu Hamil
            </h6>
            <div class="text-right mb-3">
                <a href="index.php?url=imunisasi-ibu-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-primary btn-sm-kegiatan">
                    <i class="fas fa-plus-circle"></i> Input Imunisasi Ibu
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-kegiatan-detail">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Ibu</th>
                            <th>Jenis Imunisasi</th>
                            <th>Tanggal</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($dataImunisasiIbu) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach($dataImunisasiIbu as $i): 
                                $badgeClass = 'default';
                                $namaImunisasi = $i['master_nama'] ?? 'Imunisasi';
                                if(strpos(strtolower($namaImunisasi), 'tt 1') !== false) $badgeClass = 'primary';
                                elseif(strpos(strtolower($namaImunisasi), 'tt 2') !== false) $badgeClass = 'warning';
                                elseif(strpos(strtolower($namaImunisasi), 'tt booster') !== false) $badgeClass = 'success';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($i['nama_ibu']) ?></strong></td>
                                <td>
                                    <span class="badge-imunisasi-detail <?= $badgeClass ?>">
                                        <?= htmlspecialchars($namaImunisasi) ?>
                                    </span>
                                </td>
                                <td><?= formatDate($i['tanggal'] ?? '') ?></td>
                                <td><?= htmlspecialchars($i['petugas'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    Belum ada data imunisasi ibu hamil
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    $('.nav-tabs-custom-kegiatan .nav-link').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
});
</script>