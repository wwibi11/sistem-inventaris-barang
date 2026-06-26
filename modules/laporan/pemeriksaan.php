<?php
require_once __DIR__ . '/../../config/database.php';

$tab = $_GET['tab'] ?? 'anak';

// ============================================================
// DATA PEMERIKSAAN ANAK
// ============================================================
$data_anak = $pdo->query("
SELECT
    p.*,
    a.nama AS nama_pasien,
    k.tanggal,
    k.pertemuan_ke
FROM pemeriksaan p
JOIN anak a ON a.id = p.id_anak
JOIN kegiatan k ON k.id = p.id_kegiatan
ORDER BY k.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total_anak = count($data_anak);

// Statistik status gizi anak
$giziBaik = 0;
$giziKurang = 0;
$giziBuruk = 0;
foreach($data_anak as $d) {
    if($d['status_gizi'] == 'Baik' || $d['status_gizi'] == 'Normal') $giziBaik++;
    elseif($d['status_gizi'] == 'Kurang') $giziKurang++;
    elseif($d['status_gizi'] == 'Buruk') $giziBuruk++;
}

// ============================================================
// DATA PEMERIKSAAN IBU HAMIL
// ============================================================
$data_ibu = $pdo->query("
SELECT
    p.*,
    ih.nama_ibu AS nama_pasien,
    ih.usia_kehamilan,
    k.tanggal,
    k.pertemuan_ke
FROM pemeriksaan_ibu_hamil p
JOIN ibu_hamil ih ON ih.id = p.ibu_hamil_id
LEFT JOIN kegiatan k ON k.id = p.id_kegiatan
ORDER BY p.tanggal_periksa DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total_ibu = count($data_ibu);
?>

<style>
.laporan-pemeriksaan-container { padding: 10px 0; }

/* Header */
.laporan-pemeriksaan-header {
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

.laporan-pemeriksaan-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.laporan-pemeriksaan-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.laporan-pemeriksaan-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

.btn-cetak-pemeriksaan {
    background: #17a2b8;
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

.btn-cetak-pemeriksaan:hover {
    background: #117a8b;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.25);
}

/* Tabs */
.nav-tabs-custom {
    border-bottom: 1px solid #edf2f7;
    margin-bottom: 0;
    display: flex;
    flex-wrap: wrap;
    background: #f8f9fc;
    border-radius: 12px 12px 0 0;
    padding: 0 4px;
    padding-top: 4px;
}

.nav-tabs-custom .nav-item {
    margin-right: 2px;
}

.nav-tabs-custom .nav-link {
    border: none;
    color: #8a94a6;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 8px 8px 0 0;
    transition: all 0.2s ease;
    cursor: pointer;
    background: transparent;
}

.nav-tabs-custom .nav-link:hover {
    background: #f0f4f8;
    color: #2c6b9e;
}

.nav-tabs-custom .nav-link.active {
    background: #ffffff;
    color: #2c6b9e;
    font-weight: 600;
    border-bottom: 3px solid #2c6b9e;
}

.nav-tabs-custom .nav-link .badge-tab {
    background: #e8f0fe;
    color: #2c6b9e;
    padding: 1px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    margin-left: 4px;
}

.nav-tabs-custom .nav-link.active .badge-tab {
    background: #2c6b9e;
    color: #ffffff;
}

/* Card */
.card-laporan-pemeriksaan {
    background: #ffffff;
    border-radius: 0 0 12px 12px;
    border: 1px solid #e8ecf1;
    border-top: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-laporan-pemeriksaan .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    background: #f8f9fc;
}

.card-laporan-pemeriksaan .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-laporan-pemeriksaan .card-header-custom h6 i {
    color: #2c6b9e;
    margin-right: 8px;
}

.card-laporan-pemeriksaan .card-header-custom .badge-count {
    background: #e8f0fe;
    color: #2c6b9e;
    padding: 2px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Stat Gizi */
.stat-gizi-laporan {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.stat-gizi-laporan.baik {
    background: #d1fae5;
    color: #047857;
}

.stat-gizi-laporan.kurang {
    background: #fef3c7;
    color: #92400e;
}

.stat-gizi-laporan.buruk {
    background: #fee2e2;
    color: #b91c1c;
}

/* Tabel */
.table-laporan-pemeriksaan {
    font-size: 13px;
    margin: 0;
}

.table-laporan-pemeriksaan thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 14px;
    border-bottom: 2px solid #edf2f7;
}

.table-laporan-pemeriksaan tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-laporan-pemeriksaan tbody tr:hover {
    background: #fafbfc;
}

.badge-gizi-laporan {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-gizi-laporan.Baik {
    background: #d1fae5;
    color: #047857;
}

.badge-gizi-laporan.Normal {
    background: #d1fae5;
    color: #047857;
}

.badge-gizi-laporan.Kurang {
    background: #fef3c7;
    color: #92400e;
}

.badge-gizi-laporan.Buruk {
    background: #fee2e2;
    color: #b91c1c;
}

.badge-trimester-laporan {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-trimester-laporan.t1 { background: #dbeafe; color: #1d4ed8; }
.badge-trimester-laporan.t2 { background: #fef3c7; color: #92400e; }
.badge-trimester-laporan.t3 { background: #fce4ec; color: #c62828; }
.badge-trimester-laporan.t0 { background: #f3f4f6; color: #6b7280; }

/* Tab Content */
.tab-content-pemeriksaan {
    padding: 0;
}

.tab-pane-pemeriksaan {
    display: none;
}

.tab-pane-pemeriksaan.active {
    display: block;
}

.empty-state-pemeriksaan {
    text-align: center;
    padding: 40px 20px;
}

.empty-state-pemeriksaan i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 12px;
    display: block;
}

.empty-state-pemeriksaan h6 {
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 4px;
}

.empty-state-pemeriksaan p {
    color: #8a94a6;
    font-size: 13px;
}

@media (max-width: 768px) {
    .laporan-pemeriksaan-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .btn-cetak-pemeriksaan {
        justify-content: center;
    }
    .nav-tabs-custom .nav-link {
        padding: 8px 14px;
        font-size: 13px;
    }
}

@media print {
    .btn-cetak-pemeriksaan { display: none; }
    .laporan-pemeriksaan-header { box-shadow: none; border: 1px solid #ddd; }
    .card-laporan-pemeriksaan { border: 1px solid #ddd; box-shadow: none; }
    .table-laporan-pemeriksaan thead th { background: #f0f0f0 !important; }
    .nav-tabs-custom .nav-link { display: none; }
    .nav-tabs-custom .nav-link.active { display: block; }
}
</style>

<div class="laporan-pemeriksaan-container">

    <!-- HEADER -->
    <div class="laporan-pemeriksaan-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-stethoscope"></i>
                Laporan Pemeriksaan
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Riwayat seluruh pemeriksaan anak dan ibu hamil Posyandu
            </div>
        </div>
        <button onclick="window.print()" class="btn-cetak-pemeriksaan">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs-custom">
        <li class="nav-item">
            <a class="nav-link <?= $tab == 'anak' ? 'active' : '' ?>" 
               href="index.php?url=laporan-pemeriksaan&tab=anak">
                <i class="fas fa-child"></i> Anak
                <span class="badge-tab"><?= $total_anak ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab == 'ibu' ? 'active' : '' ?>" 
               href="index.php?url=laporan-pemeriksaan&tab=ibu">
                <i class="fas fa-person-pregnant"></i> Ibu Hamil
                <span class="badge-tab"><?= $total_ibu ?></span>
            </a>
        </li>
    </ul>

    <!-- TAB CONTENT -->
    <div class="card-laporan-pemeriksaan">
        
        <!-- TAB ANAK -->
        <div class="tab-pane-pemeriksaan <?= $tab == 'anak' ? 'active' : '' ?>">
            <div class="card-header-custom">
                <h6>
                    <i class="fas fa-list"></i> Data Pemeriksaan Anak
                    <span class="badge-count"><?= $total_anak ?></span>
                    <?php if($total_anak > 0): ?>
                        <span class="stat-gizi-laporan baik">
                            <i class="fas fa-check-circle"></i> <?= $giziBaik ?> Baik
                        </span>
                        <span class="stat-gizi-laporan kurang">
                            <i class="fas fa-exclamation-circle"></i> <?= $giziKurang ?> Kurang
                        </span>
                        <span class="stat-gizi-laporan buruk">
                            <i class="fas fa-times-circle"></i> <?= $giziBuruk ?> Buruk
                        </span>
                    <?php endif; ?>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-laporan-pemeriksaan">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Anak</th>
                                <th>BB</th>
                                <th>TB</th>
                                <th>LK</th>
                                <th>Status Gizi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($data_anak): ?>
                                <?php foreach($data_anak as $d): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($d['tanggal'])) ?></td>
                                    <td><strong><?= htmlspecialchars($d['nama_pasien']) ?></strong></td>
                                    <td><?= $d['berat_badan'] ?> Kg</td>
                                    <td><?= $d['tinggi_badan'] ?> Cm</td>
                                    <td><?= $d['lingkar_kepala'] ?> Cm</td>
                                    <td>
                                        <span class="badge-gizi-laporan <?= $d['status_gizi'] ?>">
                                            <?= $d['status_gizi'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state-pemeriksaan">
                                            <i class="fas fa-stethoscope"></i>
                                            <h6>Belum Ada Data Pemeriksaan Anak</h6>
                                            <p>Belum ada data pemeriksaan anak yang tercatat</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB IBU HAMIL -->
        <div class="tab-pane-pemeriksaan <?= $tab == 'ibu' ? 'active' : '' ?>">
            <div class="card-header-custom">
                <h6>
                    <i class="fas fa-list"></i> Data Pemeriksaan Ibu Hamil
                    <span class="badge-count"><?= $total_ibu ?></span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-laporan-pemeriksaan">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Ibu</th>
                                <th>Usia</th>
                                <th>Trimester</th>
                                <th>BB</th>
                                <th>TD</th>
                                <th>LILA</th>
                                <th>TFU</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($data_ibu): ?>
                                <?php foreach($data_ibu as $d): 
                                    $trimester = 0;
                                    $usia = $d['usia_kehamilan'] ?? 0;
                                    if ($usia <= 13) $trimester = 1;
                                    elseif ($usia <= 27) $trimester = 2;
                                    elseif ($usia > 27) $trimester = 3;
                                    $class = 't0';
                                    if ($trimester == 1) $class = 't1';
                                    elseif ($trimester == 2) $class = 't2';
                                    elseif ($trimester == 3) $class = 't3';
                                ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($d['tanggal_periksa'])) ?></td>
                                    <td><strong><?= htmlspecialchars($d['nama_pasien']) ?></strong></td>
                                    <td><?= $usia ?> Minggu</td>
                                    <td>
                                        <span class="badge-trimester-laporan <?= $class ?>">
                                            <?= $trimester > 0 ? 'T' . $trimester : '-' ?>
                                        </span>
                                    </td>
                                    <td><?= $d['berat_badan'] ?> Kg</td>
                                    <td><?= htmlspecialchars($d['tekanan_darah'] ?? '-') ?></td>
                                    <td><?= $d['lingkar_lengan'] ?> Cm</td>
                                    <td><?= $d['tinggi_fundus'] ?> Cm</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state-pemeriksaan">
                                            <i class="fas fa-stethoscope"></i>
                                            <h6>Belum Ada Data Pemeriksaan Ibu Hamil</h6>
                                            <p>Belum ada data pemeriksaan ibu hamil yang tercatat</p>
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

</div>