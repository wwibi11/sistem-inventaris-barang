<?php
require_once __DIR__ . '/../../config/database.php';

$data = $pdo->query("
SELECT
    i.*,
    a.nama AS nama_anak,
    k.pertemuan_ke,
    u.nama AS petugas
FROM imunisasi i
JOIN anak a ON a.id = i.id_anak
LEFT JOIN kegiatan k ON k.id = i.id_kegiatan
LEFT JOIN users u ON u.id = i.diberikan_oleh
ORDER BY i.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total = count($data);
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

/* Card */
.card-laporan {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
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

.badge-imunisasi-laporan {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-imunisasi-laporan.hb0 { background: #e5e7eb; color: #374151; }
.badge-imunisasi-laporan.bcg { background: #d1fae5; color: #047857; }
.badge-imunisasi-laporan.polio { background: #dbeafe; color: #1d4ed8; }
.badge-imunisasi-laporan.dpt { background: #ede9fe; color: #6d28d9; }
.badge-imunisasi-laporan.campak { background: #fee2e2; color: #b91c1c; }
.badge-imunisasi-laporan.mr { background: #fef3c7; color: #92400e; }

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
}

@media print {
    .btn-cetak { display: none; }
    .laporan-header { box-shadow: none; border: 1px solid #ddd; }
    .card-laporan { border: 1px solid #ddd; box-shadow: none; }
    .table-laporan thead th { background: #f0f0f0 !important; }
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
                Rekap seluruh imunisasi balita Posyandu Bougenvil Belik
            </div>
        </div>
        <button onclick="window.print()" class="btn-cetak">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <!-- TABLE -->
    <div class="card-laporan">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-list"></i> Data Imunisasi
                <span class="badge-count"><?= $total ?></span>
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
                        <?php if($data): ?>
                            <?php foreach($data as $d): 
                                $badgeClass = 'hb0';
                                switch($d['jenis_imunisasi']){
                                    case 'HB0': $badgeClass = 'hb0'; break;
                                    case 'BCG': $badgeClass = 'bcg'; break;
                                    case 'Polio': $badgeClass = 'polio'; break;
                                    case 'DPT-HB-Hib': $badgeClass = 'dpt'; break;
                                    case 'Campak': $badgeClass = 'campak'; break;
                                    case 'MR': $badgeClass = 'mr'; break;
                                }
                            ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($d['tanggal'])) ?></td>
                                <td><strong><?= htmlspecialchars($d['nama_anak']) ?></strong></td>
                                <td>
                                    <span class="badge-imunisasi-laporan <?= $badgeClass ?>">
                                        <?= $d['jenis_imunisasi'] ?>
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
                                        <h6>Belum Ada Data Imunisasi</h6>
                                        <p>Belum ada data imunisasi yang tercatat</p>
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