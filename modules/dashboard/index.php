<?php
require_once __DIR__ . '/../../config/database.php';

// =======================
// DATA UTAMA
// =======================
$total_anak       = $pdo->query("SELECT COUNT(*) FROM anak WHERE status='aktif'")->fetchColumn();
$total_keluarga   = $pdo->query("SELECT COUNT(*) FROM keluarga")->fetchColumn();
$total_kegiatan   = $pdo->query("SELECT COUNT(*) FROM kegiatan")->fetchColumn();
$total_ibu_hamil  = $pdo->query("SELECT COUNT(*) FROM ibu_hamil WHERE status='Aktif'")->fetchColumn();

$today = date('Y-m-d');

// =======================
// KEHADIRAN HARI INI
// =======================
$stmt = $pdo->prepare("
  SELECT COUNT(*) 
  FROM kehadiran k
  JOIN kegiatan g ON k.id_kegiatan = g.id
  WHERE g.tanggal = ? AND k.status_hadir = 'hadir'
");
$stmt->execute([$today]);
$hadir_hari_ini = $stmt->fetchColumn();

// =======================
// KEHADIRAN IBU HAMIL HARI INI
// =======================
$stmt = $pdo->prepare("
  SELECT COUNT(*) 
  FROM kehadiran_ibu_hamil k
  JOIN kegiatan g ON k.id_kegiatan = g.id
  WHERE g.tanggal = ? AND k.status_hadir = 'hadir'
");
$stmt->execute([$today]);
$hadir_ibu_hari_ini = $stmt->fetchColumn();

// =======================
// TOTAL PEMERIKSAAN & IMUNISASI
// =======================
$total_pemeriksaan = $pdo->query("SELECT COUNT(*) FROM pemeriksaan")->fetchColumn();
$total_imunisasi = $pdo->query("SELECT COUNT(*) FROM imunisasi")->fetchColumn();
$total_pemeriksaan_ibu = $pdo->query("SELECT COUNT(*) FROM pemeriksaan_ibu_hamil")->fetchColumn();
$total_imunisasi_ibu = $pdo->query("SELECT COUNT(*) FROM imunisasi_ibu_hamil")->fetchColumn();

// =======================
// KEGIATAN TERBARU
// =======================
$kegiatan = $pdo->query("
  SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// =======================
// ANAK TERBARU
// =======================
$anak = $pdo->query("
  SELECT a.*, k.nama_kepala_keluarga 
  FROM anak a
  JOIN keluarga k ON a.id_keluarga = k.id
  ORDER BY a.created_at DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// =======================
// GRAFIK KEHADIRAN
// =======================
$grafik = $pdo->query("
SELECT
    DATE_FORMAT(g.tanggal,'%b %Y') AS bulan,
    COUNT(*) AS total
FROM kehadiran h
JOIN kegiatan g ON g.id = h.id_kegiatan
WHERE h.status_hadir = 'hadir'
GROUP BY YEAR(g.tanggal), MONTH(g.tanggal)
ORDER BY YEAR(g.tanggal), MONTH(g.tanggal)
")->fetchAll(PDO::FETCH_ASSOC);

$labelGrafik = [];
$dataGrafik  = [];

foreach($grafik as $g){
    $labelGrafik[] = $g['bulan'];
    $dataGrafik[]  = $g['total'];
}
?>

<style>
.dashboard-container { padding: 15px 0; }

/* HEADER */
.dashboard-header {
    background: #ffffff;
    border-radius: 12px;
    padding: 22px 28px;
    margin-bottom: 28px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.dashboard-header .header-title {
    font-size: 22px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.dashboard-header .header-title small {
    font-size: 14px;
    font-weight: 400;
    color: #8a94a6;
    margin-left: 12px;
}

.dashboard-header .header-sub {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 4px;
}

.dashboard-header .header-sub i { color: #2c6b9e; }
.dashboard-header .header-badge {
    background: #e8f0fe;
    color: #2c6b9e;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

/* STAT CARD */
.stat-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px 22px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    align-items: center;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-card .stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #ffffff;
    flex-shrink: 0;
    margin-right: 16px;
}

.stat-card .stat-icon.primary { background: #2c6b9e; }
.stat-card .stat-icon.success { background: #28a745; }
.stat-card .stat-icon.info { background: #17a2b8; }
.stat-card .stat-icon.pink { background: #e83e8c; }

.stat-card .stat-content { flex: 1; min-width: 0; }
.stat-card .stat-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #8a94a6;
    margin-bottom: 2px;
}
.stat-card .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

/* QUICK INFO */
.quick-info-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}

.quick-info-item {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px 20px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    text-align: center;
}

.quick-info-item .qi-value {
    font-size: 22px;
    font-weight: 700;
    color: #2c6b9e;
}

.quick-info-item .qi-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

/* CARD */
.card-modern {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    height: 100%;
}

.card-modern .card-header-custom {
    padding: 12px 18px;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: transparent;
}

.card-modern .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-modern .card-header-custom .badge-count {
    background: #edf2f7;
    color: #4a5568;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
}

.card-modern .card-body-custom {
    padding: 0;
}

/* GRAFIK */
.grafik-wrapper {
    padding: 12px 16px;
}

.grafik-wrapper canvas {
    max-height: 220px !important;
    height: 220px !important;
}

/* TABLE */
.table-elegant {
    margin: 0;
    font-size: 13px;
}

.table-elegant thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 8px 14px;
    border-bottom: 2px solid #edf2f7;
    position: sticky;
    top: 0;
    z-index: 2;
}

.table-elegant tbody td {
    padding: 8px 14px;
    color: #2d3748;
    border-bottom: 1px solid #f0f2f5;
}

.table-elegant tbody tr:last-child td { border-bottom: none; }
.table-elegant tbody tr:hover { background: #fafbfc; }

.table-elegant .empty-state {
    text-align: center;
    color: #a0aec0;
    padding: 20px 0;
}

.table-elegant .empty-state i {
    font-size: 20px;
    display: block;
    margin-bottom: 4px;
}

/* SISI KANAN - SCROLL */
.side-wrapper {
    display: flex;
    flex-direction: column;
    gap: 16px;
    height: 100%;
}

.side-wrapper .card-modern {
    flex: 1;
    overflow: hidden;
    min-height: 0;
}

.side-wrapper .card-modern .card-body-custom {
    max-height: 180px;
    overflow-y: auto;
}

.side-wrapper .card-modern .card-body-custom::-webkit-scrollbar {
    width: 4px;
}

.side-wrapper .card-modern .card-body-custom::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.side-wrapper .card-modern .card-body-custom::-webkit-scrollbar-thumb {
    background: #c1c7cd;
    border-radius: 4px;
}

.side-wrapper .card-modern .card-body-custom::-webkit-scrollbar-thumb:hover {
    background: #a0a6ad;
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .quick-info-grid { grid-template-columns: repeat(3, 1fr); }
    .side-wrapper .card-modern .card-body-custom {
        max-height: 140px;
    }
}

@media (max-width: 768px) {
    .dashboard-header { padding: 16px 20px; }
    .dashboard-header .header-title { font-size: 18px; }
    .dashboard-header .header-title small { display: block; margin-left: 0; margin-top: 4px; }
    .stat-card .stat-value { font-size: 22px; }
    .stat-card { padding: 16px 18px; }
    .stat-card .stat-icon { width: 44px; height: 44px; font-size: 18px; margin-right: 12px; }
    .quick-info-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; }
    .grafik-wrapper canvas { max-height: 160px !important; height: 160px !important; }
}

@media (max-width: 576px) {
    .quick-info-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .quick-info-item { padding: 12px 14px; }
    .quick-info-item .qi-value { font-size: 18px; }
    .card-modern .card-header-custom { padding: 10px 14px; flex-wrap: wrap; }
    .table-elegant thead th,
    .table-elegant tbody td { padding: 6px 10px; font-size: 12px; }
    .side-wrapper .card-modern .card-body-custom {
        max-height: 120px;
    }
}
</style>

<div class="dashboard-container">

    <!-- HEADER -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="header-title">
                    <i class="fas fa-chart-pie" style="color: #2c6b9e; margin-right: 10px;"></i>
                    Dashboard Posyandu
                    <small><i class="fas fa-calendar-alt"></i> <?= date('d M Y') ?></small>
                </div>
                <div class="header-sub">
                    <i class="fas fa-home"></i> E-Posyandu Bougenvil · Belik
                </div>
            </div>
            <div class="col-md-4 text-md-right mt-2 mt-md-0">
                <span class="header-badge">
                    <i class="fas fa-check-circle"></i> Sistem Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- QUICK INFO -->
    <div class="quick-info-grid">
        <div class="quick-info-item">
            <div class="qi-value"><?= $total_pemeriksaan ?></div>
            <div class="qi-label"><i class="fas fa-stethoscope"></i> Pemeriksaan Anak</div>
        </div>
        <div class="quick-info-item">
            <div class="qi-value"><?= $total_imunisasi ?></div>
            <div class="qi-label"><i class="fas fa-syringe"></i> Imunisasi Anak</div>
        </div>
        <div class="quick-info-item">
            <div class="qi-value"><?= $total_pemeriksaan_ibu ?></div>
            <div class="qi-label"><i class="fas fa-stethoscope"></i> Pemeriksaan Ibu</div>
        </div>
        <div class="quick-info-item">
            <div class="qi-value"><?= $total_imunisasi_ibu ?></div>
            <div class="qi-label"><i class="fas fa-syringe"></i> Imunisasi Ibu</div>
        </div>
        <div class="quick-info-item">
            <div class="qi-value"><?= $hadir_hari_ini ?></div>
            <div class="qi-label"><i class="fas fa-child"></i> Anak Hadir</div>
        </div>
        <div class="quick-info-item">
            <div class="qi-value"><?= $hadir_ibu_hari_ini ?></div>
            <div class="qi-label"><i class="fas fa-person-pregnant"></i> Ibu Hadir</div>
        </div>
    </div>

    <!-- STAT CARD -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon primary"><i class="fas fa-child"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Total Anak Aktif</div>
                    <div class="stat-value"><?= $total_anak ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon success"><i class="fas fa-home"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Total Keluarga</div>
                    <div class="stat-value"><?= $total_keluarga ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon pink"><i class="fas fa-person-pregnant"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Ibu Hamil Aktif</div>
                    <div class="stat-value"><?= $total_ibu_hamil ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon info"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Total Kegiatan</div>
                    <div class="stat-value"><?= $total_kegiatan ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRAFIK + SIDE -->
    <div class="row">
        <!-- GRAFIK - KIRI -->
        <div class="col-md-8 mb-4">
            <div class="card-modern" style="height: 100%;">
                <div class="card-header-custom">
                    <h6>
                        <i class="fas fa-chart-line" style="color: #2c6b9e; margin-right: 8px;"></i>
                        Grafik Kehadiran Anak
                    </h6>
                    <span class="badge-count">Bulanan</span>
                </div>
                <div class="card-body-custom grafik-wrapper">
                    <canvas id="grafikKegiatan"></canvas>
                </div>
            </div>
        </div>

        <!-- SISI KANAN -->
        <div class="col-md-4 mb-4">
            <div class="side-wrapper">
                <!-- Kegiatan Terbaru -->
                <div class="card-modern">
                    <div class="card-header-custom">
                        <h6>
                            <i class="fas fa-calendar-check" style="color: #17a2b8; margin-right: 8px;"></i>
                            Kegiatan Terbaru
                        </h6>
                        <span class="badge-count"><?= count($kegiatan) ?></span>
                    </div>
                    <div class="card-body-custom">
                        <table class="table table-elegant">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                    <th>Ke</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kegiatan as $k): ?>
                                <tr>
                                    <td><?= date('d M', strtotime($k['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($k['lokasi']) ?></td>
                                    <td><?= $k['pertemuan_ke'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (!$kegiatan): ?>
                                <tr><td colspan="3" class="empty-state"><i class="fas fa-inbox"></i> Tidak ada data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Anak Terdaftar Terbaru -->
                <div class="card-modern">
                    <div class="card-header-custom">
                        <h6>
                            <i class="fas fa-user-plus" style="color: #28a745; margin-right: 8px;"></i>
                            Anak Terdaftar Terbaru
                        </h6>
                        <span class="badge-count"><?= count($anak) ?></span>
                    </div>
                    <div class="card-body-custom">
                        <table class="table table-elegant">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Keluarga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($anak as $a): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($a['nama']) ?></strong></td>
                                    <td><?= htmlspecialchars($a['nama_kepala_keluarga']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (!$anak): ?>
                                <tr><td colspan="2" class="empty-state"><i class="fas fa-inbox"></i> Tidak ada data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('grafikKegiatan').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labelGrafik) ?>,
        datasets: [{
            label: 'Jumlah Kehadiran',
            data: <?= json_encode($dataGrafik) ?>,
            fill: true,
            backgroundColor: 'rgba(44, 107, 158, 0.08)',
            borderColor: '#2c6b9e',
            borderWidth: 2.5,
            tension: 0.3,
            pointBackgroundColor: '#2c6b9e',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 3.5,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: { size: 10 },
                    color: '#8a94a6'
                },
                grid: {
                    color: 'rgba(0,0,0,0.05)',
                    drawBorder: false
                }
            },
            x: {
                grid: { display: false },
                ticks: {
                    font: { size: 10 },
                    color: '#8a94a6'
                }
            }
        }
    }
});
</script>