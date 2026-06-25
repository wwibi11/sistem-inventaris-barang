<?php
require_once __DIR__ . '/../../config/database.php';

$data = $pdo->query("
SELECT
    p.*,
    a.nama AS nama_anak,
    k.tanggal
FROM pemeriksaan p
JOIN anak a ON a.id = p.id_anak
JOIN kegiatan k ON k.id = p.id_kegiatan
ORDER BY k.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total = count($data);

// Statistik status gizi
$giziBaik = 0;
$giziKurang = 0;
$giziBuruk = 0;
foreach($data as $d) {
    if($d['status_gizi'] == 'Baik') $giziBaik++;
    elseif($d['status_gizi'] == 'Kurang') $giziKurang++;
    elseif($d['status_gizi'] == 'Buruk') $giziBuruk++;
}
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

/* Card */
.card-laporan-pemeriksaan {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
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

.badge-gizi-laporan.Kurang {
    background: #fef3c7;
    color: #92400e;
}

.badge-gizi-laporan.Buruk {
    background: #fee2e2;
    color: #b91c1c;
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

@media (max-width: 768px) {
    .laporan-pemeriksaan-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .btn-cetak-pemeriksaan {
        justify-content: center;
    }
}

@media print {
    .btn-cetak-pemeriksaan { display: none; }
    .laporan-pemeriksaan-header { box-shadow: none; border: 1px solid #ddd; }
    .card-laporan-pemeriksaan { border: 1px solid #ddd; box-shadow: none; }
    .table-laporan-pemeriksaan thead th { background: #f0f0f0 !important; }
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
                Riwayat seluruh pemeriksaan balita Posyandu Bougenvil Belik
            </div>
        </div>
        <button onclick="window.print()" class="btn-cetak-pemeriksaan">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <!-- TABLE -->
    <div class="card-laporan-pemeriksaan">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-list"></i> Data Pemeriksaan
                <span class="badge-count"><?= $total ?></span>
                <?php if($total > 0): ?>
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
                        <?php if($data): ?>
                            <?php foreach($data as $d): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($d['tanggal'])) ?></td>
                                <td><strong><?= htmlspecialchars($d['nama_anak']) ?></strong></td>
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
                                        <h6>Belum Ada Data Pemeriksaan</h6>
                                        <p>Belum ada data pemeriksaan yang tercatat</p>
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