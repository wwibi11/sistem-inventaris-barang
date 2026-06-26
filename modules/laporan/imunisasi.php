<?php
require_once __DIR__ . '/../../config/database.php';

$tab = $_GET['tab'] ?? 'anak';

// ============================================================
// DATA IMUNISASI ANAK
// ============================================================
$data_anak = $pdo->query("
SELECT
    i.*,
    a.nama AS nama_pasien,
    k.pertemuan_ke,
    u.nama AS petugas,
    mi.nama_imunisasi AS master_nama
FROM imunisasi i
JOIN anak a ON a.id = i.id_anak
LEFT JOIN kegiatan k ON k.id = i.id_kegiatan
LEFT JOIN users u ON u.id = i.diberikan_oleh
LEFT JOIN master_imunisasi mi ON mi.id = i.id_master_imunisasi
ORDER BY i.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total_anak = count($data_anak);

// ============================================================
// DATA IMUNISASI IBU HAMIL
// ============================================================
$data_ibu = $pdo->query("
SELECT
    iih.*,
    ih.nama_ibu AS nama_pasien,
    u.nama AS petugas,
    mi.nama_imunisasi AS master_nama
FROM imunisasi_ibu_hamil iih
JOIN ibu_hamil ih ON ih.id = iih.ibu_hamil_id
LEFT JOIN users u ON u.id = iih.diberikan_oleh
LEFT JOIN master_imunisasi mi ON mi.id = iih.imunisasi_id
ORDER BY iih.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total_ibu = count($data_ibu);
?>

<style>
.laporan-container { padding: 10px 0; }

/* Header */
.laporan-header {
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

.laporan-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.laporan-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.laporan-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

.btn-cetak {
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

.btn-cetak:hover {
    background: #1f507a;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(44, 107, 158, 0.25);
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
.card-laporan {
    background: #ffffff;
    border-radius: 0 0 12px 12px;
    border: 1px solid #e8ecf1;
    border-top: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-laporan .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    background: #f8f9fc;
}

.card-laporan .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-laporan .card-header-custom h6 i {
    color: #2c6b9e;
    margin-right: 8px;
}

.card-laporan .card-header-custom .badge-count {
    background: #e8f0fe;
    color: #2c6b9e;
    padding: 2px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Tabel */
.table-laporan {
    font-size: 13px;
    margin: 0;
}

.table-laporan thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 14px;
    border-bottom: 2px solid #edf2f7;
}

.table-laporan tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-laporan tbody tr:hover {
    background: #fafbfc;
}

.table-laporan tbody tr:last-child td {
    border-bottom: none;
}

/* Badge Imunisasi */
.badge-imunisasi-laporan {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-imunisasi-laporan.bcg { background: #d1fae5; color: #047857; }
.badge-imunisasi-laporan.polio { background: #dbeafe; color: #1d4ed8; }
.badge-imunisasi-laporan.dpt { background: #ede9fe; color: #6d28d9; }
.badge-imunisasi-laporan.campak { background: #fee2e2; color: #b91c1c; }
.badge-imunisasi-laporan.mr { background: #fef3c7; color: #92400e; }
.badge-imunisasi-laporan.default { background: #f3f4f6; color: #6b7280; }

/* Badge Imunisasi Ibu */
.badge-imunisasi-ibu-laporan {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-imunisasi-ibu-laporan.tt1 { background: #dbeafe; color: #1d4ed8; }
.badge-imunisasi-ibu-laporan.tt2 { background: #fef3c7; color: #92400e; }
.badge-imunisasi-ibu-laporan.booster { background: #d1fae5; color: #047857; }
.badge-imunisasi-ibu-laporan.default { background: #f3f4f6; color: #6b7280; }

/* Tab Content */
.tab-content-imunisasi {
    padding: 0;
}

.tab-pane-imunisasi {
    display: none;
}

.tab-pane-imunisasi.active {
    display: block;
}

.empty-state-laporan {
    text-align: center;
    padding: 40px 20px;
}

.empty-state-laporan i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 12px;
    display: block;
}

.empty-state-laporan h6 {
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 4px;
}

.empty-state-laporan p {
    color: #8a94a6;
    font-size: 13px;
}

@media (max-width: 768px) {
    .laporan-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .btn-cetak {
        justify-content: center;
    }
    .nav-tabs-custom .nav-link {
        padding: 8px 14px;
        font-size: 13px;
    }
}

@media print {
    .btn-cetak { display: none; }
    .laporan-header { box-shadow: none; border: 1px solid #ddd; }
    .card-laporan { border: 1px solid #ddd; box-shadow: none; }
    .table-laporan thead th { background: #f0f0f0 !important; }
    .nav-tabs-custom .nav-link { display: none; }
    .nav-tabs-custom .nav-link.active { display: block; }
}
</style>

<div class="laporan-container">

    <!-- HEADER -->
    <div class="laporan-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-syringe"></i>
                Laporan Imunisasi
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Rekap seluruh imunisasi anak dan ibu hamil Posyandu
            </div>
        </div>
        <button onclick="window.print()" class="btn-cetak">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs-custom">
        <li class="nav-item">
            <a class="nav-link <?= $tab == 'anak' ? 'active' : '' ?>" 
               href="index.php?url=laporan-imunisasi&tab=anak">
                <i class="fas fa-child"></i> Anak
                <span class="badge-tab"><?= $total_anak ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab == 'ibu' ? 'active' : '' ?>" 
               href="index.php?url=laporan-imunisasi&tab=ibu">
                <i class="fas fa-person-pregnant"></i> Ibu Hamil
                <span class="badge-tab"><?= $total_ibu ?></span>
            </a>
        </li>
    </ul>

    <!-- TAB CONTENT -->
    <div class="card-laporan">
        
        <!-- TAB ANAK -->
        <div class="tab-pane-imunisasi <?= $tab == 'anak' ? 'active' : '' ?>">
            <div class="card-header-custom">
                <h6>
                    <i class="fas fa-list"></i> Data Imunisasi Anak
                    <span class="badge-count"><?= $total_anak ?></span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-laporan">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Anak</th>
                                <th>Jenis Imunisasi</th>
                                <th>Pertemuan</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($data_anak): ?>
                                <?php foreach($data_anak as $d): 
                                    $badgeClass = 'default';
                                    $namaImunisasi = $d['master_nama'] ?? $d['jenis_imunisasi'] ?? 'Imunisasi';
                                    if(strpos(strtolower($namaImunisasi), 'bcg') !== false) $badgeClass = 'bcg';
                                    elseif(strpos(strtolower($namaImunisasi), 'polio') !== false) $badgeClass = 'polio';
                                    elseif(strpos(strtolower($namaImunisasi), 'campak') !== false) $badgeClass = 'campak';
                                    elseif(strpos(strtolower($namaImunisasi), 'mr') !== false) $badgeClass = 'mr';
                                    elseif(strpos(strtolower($namaImunisasi), 'dpt') !== false) $badgeClass = 'dpt';
                                ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($d['tanggal'])) ?></td>
                                    <td><strong><?= htmlspecialchars($d['nama_pasien']) ?></strong></td>
                                    <td>
                                        <span class="badge-imunisasi-laporan <?= $badgeClass ?>">
                                            <?= htmlspecialchars($namaImunisasi) ?>
                                        </span>
                                    </td>
                                    <td><?= $d['pertemuan_ke'] ?? '-' ?></td>
                                    <td><?= htmlspecialchars($d['petugas'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state-laporan">
                                            <i class="fas fa-syringe"></i>
                                            <h6>Belum Ada Data Imunisasi Anak</h6>
                                            <p>Belum ada data imunisasi anak yang tercatat</p>
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
        <div class="tab-pane-imunisasi <?= $tab == 'ibu' ? 'active' : '' ?>">
            <div class="card-header-custom">
                <h6>
                    <i class="fas fa-list"></i> Data Imunisasi Ibu Hamil
                    <span class="badge-count"><?= $total_ibu ?></span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-laporan">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Ibu</th>
                                <th>Jenis Imunisasi</th>
                                <th>Petugas</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($data_ibu): ?>
                                <?php foreach($data_ibu as $d): 
                                    $badgeClass = 'default';
                                    $namaImunisasi = $d['master_nama'] ?? 'Imunisasi';
                                    if(strpos(strtolower($namaImunisasi), 'tt 1') !== false) $badgeClass = 'tt1';
                                    elseif(strpos(strtolower($namaImunisasi), 'tt 2') !== false) $badgeClass = 'tt2';
                                    elseif(strpos(strtolower($namaImunisasi), 'booster') !== false) $badgeClass = 'booster';
                                ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($d['tanggal'])) ?></td>
                                    <td><strong><?= htmlspecialchars($d['nama_pasien']) ?></strong></td>
                                    <td>
                                        <span class="badge-imunisasi-ibu-laporan <?= $badgeClass ?>">
                                            <?= htmlspecialchars($namaImunisasi) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($d['petugas'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($d['keterangan'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state-laporan">
                                            <i class="fas fa-syringe"></i>
                                            <h6>Belum Ada Data Imunisasi Ibu Hamil</h6>
                                            <p>Belum ada data imunisasi ibu hamil yang tercatat</p>
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