<?php
require_once __DIR__ . '/../../config/database.php';

// ============================================================
// DATA STATISTIK
// ============================================================
$totalAnak = $pdo->query("SELECT COUNT(*) FROM anak WHERE status='aktif'")->fetchColumn();
$totalKeluarga = $pdo->query("SELECT COUNT(*) FROM keluarga")->fetchColumn();
$totalKegiatan = $pdo->query("SELECT COUNT(*) FROM kegiatan")->fetchColumn();
$totalPemeriksaan = $pdo->query("SELECT COUNT(*) FROM pemeriksaan")->fetchColumn();
$totalImunisasi = $pdo->query("SELECT COUNT(*) FROM imunisasi")->fetchColumn();
$totalIbuHamil = $pdo->query("SELECT COUNT(*) FROM ibu_hamil WHERE status='Aktif'")->fetchColumn();

$totalHadir = $pdo->query("SELECT COUNT(*) FROM kehadiran WHERE status_hadir='hadir'")->fetchColumn();
$totalTidak = $pdo->query("SELECT COUNT(*) FROM kehadiran WHERE status_hadir='tidak'")->fetchColumn();
$totalUndangan = $totalHadir + $totalTidak;

$persentaseHadir = $totalUndangan > 0 ? round(($totalHadir / $totalUndangan) * 100, 1) : 0;
$persentaseTidak = $totalUndangan > 0 ? round(($totalTidak / $totalUndangan) * 100, 1) : 0;

$totalAnakImunisasi = $pdo->query("SELECT COUNT(DISTINCT id_anak) FROM imunisasi")->fetchColumn();
$totalAnakPeriksa = $pdo->query("SELECT COUNT(DISTINCT id_anak) FROM pemeriksaan")->fetchColumn();

// ============================================================
// DATA GRAFIK KEHADIRAN PER BULAN
// ============================================================
$grafikBulan = $pdo->query("
SELECT
    DATE_FORMAT(g.tanggal,'%b %Y') AS bulan,
    COUNT(CASE WHEN h.status_hadir='hadir' THEN 1 END) AS hadir,
    COUNT(CASE WHEN h.status_hadir='tidak' THEN 1 END) AS tidak
FROM kehadiran h
JOIN kegiatan g ON g.id = h.id_kegiatan
GROUP BY YEAR(g.tanggal), MONTH(g.tanggal)
ORDER BY YEAR(g.tanggal), MONTH(g.tanggal)
")->fetchAll(PDO::FETCH_ASSOC);

$labelGrafik = [];
$dataHadir = [];
$dataTidak = [];

foreach($grafikBulan as $g){
    $labelGrafik[] = $g['bulan'];
    $dataHadir[] = (int)$g['hadir'];
    $dataTidak[] = (int)$g['tidak'];
}

// ============================================================
// DATA GRAFIK STATUS GIZI
// ============================================================
$gizi = $pdo->query("
SELECT
    status_gizi,
    COUNT(*) AS total
FROM pemeriksaan
GROUP BY status_gizi
")->fetchAll(PDO::FETCH_ASSOC);

$labelGizi = [];
$dataGizi = [];
foreach($gizi as $g){
    $labelGizi[] = $g['status_gizi'] ?: 'Tidak Diketahui';
    $dataGizi[] = (int)$g['total'];
}
?>

<style>
.statistik-container { padding: 10px 0; }

/* Header */
.statistik-header {
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

.statistik-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.statistik-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.statistik-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

.btn-cetak-statistik {
    background: #6f42c1;
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

.btn-cetak-statistik:hover {
    background: #5530a3;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(111, 66, 193, 0.25);
}

/* Stat Card */
.stat-card-statistik {
    background: #ffffff;
    border-radius: 12px;
    padding: 18px 20px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
    transition: all 0.3s ease;
    text-align: center;
}

.stat-card-statistik:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-card-statistik .stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #ffffff;
    margin: 0 auto 10px auto;
}

.stat-card-statistik .stat-icon.primary { background: #2c6b9e; }
.stat-card-statistik .stat-icon.success { background: #28a745; }
.stat-card-statistik .stat-icon.info { background: #17a2b8; }
.stat-card-statistik .stat-icon.warning { background: #e8a317; }
.stat-card-statistik .stat-icon.danger { background: #dc3545; }
.stat-card-statistik .stat-icon.secondary { background: #6c757d; }
.stat-card-statistik .stat-icon.purple { background: #6f42c1; }
.stat-card-statistik .stat-icon.pink { background: #e83e8c; }
.stat-card-statistik .stat-icon.orange { background: #fd7e14; }
.stat-card-statistik .stat-icon.teal { background: #20c997; }

.stat-card-statistik .stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

.stat-card-statistik .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 4px;
}

/* Card Grafik */
.card-grafik {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    height: 100%;
    overflow: hidden;
}

.card-grafik:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
}

.card-grafik .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8f9fc;
}

.card-grafik .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-grafik .card-header-custom h6 i {
    margin-right: 8px;
}

.card-grafik .card-body-custom {
    padding: 16px 20px;
}

.grafik-wrapper {
    padding: 4px 0;
}

.grafik-wrapper canvas {
    max-height: 200px !important;
    height: 200px !important;
}

.donut-wrapper {
    padding: 4px 0;
    max-width: 280px;
    margin: 0 auto;
}

.donut-wrapper canvas {
    max-height: 200px !important;
    height: 200px !important;
}

@media (max-width: 768px) {
    .statistik-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .btn-cetak-statistik {
        justify-content: center;
    }
    .stat-card-statistik .stat-number {
        font-size: 22px;
    }
    .grafik-wrapper canvas {
        max-height: 150px !important;
        height: 150px !important;
    }
    .donut-wrapper canvas {
        max-height: 150px !important;
        height: 150px !important;
    }
}

@media print {
    .btn-cetak-statistik { display: none; }
    .statistik-header { box-shadow: none; border: 1px solid #ddd; }
    .stat-card-statistik { border: 1px solid #ddd; box-shadow: none; }
    .card-grafik { border: 1px solid #ddd; box-shadow: none; }
}
</style>

<div class="statistik-container">

    <!-- HEADER -->
    <div class="statistik-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-chart-pie"></i>
                Statistik Posyandu
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Ringkasan data Posyandu Bougenvil Belik secara keseluruhan
            </div>
        </div>
        <button onclick="window.print()" class="btn-cetak-statistik">
            <i class="fas fa-print"></i> Cetak Statistik
        </button>
    </div>

    <!-- STATISTIK CARD - 4 KOLOM -->
    <div class="row">
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon primary"><i class="fas fa-child"></i></div>
                <div class="stat-number"><?= $totalAnak ?></div>
                <div class="stat-label">Total Anak Aktif</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon pink"><i class="fas fa-person-pregnant"></i></div>
                <div class="stat-number"><?= $totalIbuHamil ?></div>
                <div class="stat-label">Ibu Hamil Aktif</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon success"><i class="fas fa-home"></i></div>
                <div class="stat-number"><?= $totalKeluarga ?></div>
                <div class="stat-label">Total Keluarga</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon info"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-number"><?= $totalKegiatan ?></div>
                <div class="stat-label">Total Kegiatan</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon warning"><i class="fas fa-user-check"></i></div>
                <div class="stat-number"><?= $totalHadir ?></div>
                <div class="stat-label">Total Kehadiran</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon secondary"><i class="fas fa-stethoscope"></i></div>
                <div class="stat-number"><?= $totalPemeriksaan ?></div>
                <div class="stat-label">Total Pemeriksaan</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon danger"><i class="fas fa-syringe"></i></div>
                <div class="stat-number"><?= $totalImunisasi ?></div>
                <div class="stat-label">Total Imunisasi</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon purple"><i class="fas fa-percent"></i></div>
                <div class="stat-number"><?= $persentaseHadir ?>%</div>
                <div class="stat-label">Persentase Kehadiran</div>
            </div>
        </div>
    </div>

    <!-- GRAFIK -->
    <div class="row">
        <!-- Grafik Kehadiran per Bulan -->
        <div class="col-lg-7 mb-4">
            <div class="card-grafik">
                <div class="card-header-custom">
                    <h6>
                        <i class="fas fa-chart-bar" style="color: #2c6b9e;"></i>
                        Kehadiran per Bulan
                    </h6>
                    <span class="badge-count" style="background: #edf2f7; color: #4a5568; padding: 2px 12px; border-radius: 20px; font-size: 11px;">
                        <?= count($grafikBulan) ?> Bulan
                    </span>
                </div>
                <div class="card-body-custom grafik-wrapper">
                    <canvas id="grafikKehadiran"></canvas>
                </div>
            </div>
        </div>

        <!-- Donut Persentase Kehadiran + Status Gizi -->
        <div class="col-lg-5 mb-4">
            <div class="row">
                <!-- Donut Persentase -->
                <div class="col-md-6 mb-4">
                    <div class="card-grafik">
                        <div class="card-header-custom">
                            <h6>
                                <i class="fas fa-chart-pie" style="color: #6f42c1;"></i>
                                Kehadiran
                            </h6>
                        </div>
                        <div class="card-body-custom donut-wrapper">
                            <canvas id="donutKehadiran"></canvas>
                            <div class="text-center mt-2" style="font-size: 12px; color: #8a94a6;">
                                <span style="color: #28a745;">● Hadir <?= $persentaseHadir ?>%</span>
                                <span style="color: #dc3545; margin-left: 12px;">● Tidak <?= $persentaseTidak ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Donut Status Gizi -->
                <div class="col-md-6 mb-4">
                    <div class="card-grafik">
                        <div class="card-header-custom">
                            <h6>
                                <i class="fas fa-chart-pie" style="color: #28a745;"></i>
                                Status Gizi
                            </h6>
                        </div>
                        <div class="card-body-custom donut-wrapper">
                            <canvas id="donutGizi"></canvas>
                            <div class="text-center mt-2" style="font-size: 11px; color: #8a94a6;">
                                <?php 
                                $warnaGizi = ['#28a745', '#e8a317', '#dc3545', '#6c757d'];
                                $labelGiziShort = ['Baik', 'Kurang', 'Buruk', 'Lainnya'];
                                foreach($labelGizi as $i => $lbl): 
                                ?>
                                <span style="color: <?= $warnaGizi[$i] ?? '#6c757d' ?>;">● <?= $lbl ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================ -->
<!-- CHART.JS -->
<!-- ============================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// 1. Grafik Batang Kehadiran per Bulan
const ctx1 = document.getElementById('grafikKehadiran').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labelGrafik) ?>,
        datasets: [
            {
                label: 'Hadir',
                data: <?= json_encode($dataHadir) ?>,
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: '#28a745',
                borderWidth: 1,
                borderRadius: 4
            },
            {
                label: 'Tidak Hadir',
                data: <?= json_encode($dataTidak) ?>,
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                borderColor: '#dc3545',
                borderWidth: 1,
                borderRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2.5,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: { size: 11 },
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 20
                }
            }
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

// 2. Donut Kehadiran
const ctx2 = document.getElementById('donutKehadiran').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Tidak Hadir'],
        datasets: [{
            data: [<?= $totalHadir ?>, <?= $totalTidak ?>],
            backgroundColor: ['#28a745', '#dc3545'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        cutout: '65%'
    }
});

// 3. Donut Status Gizi
const ctx3 = document.getElementById('donutGizi').getContext('2d');
new Chart(ctx3, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($labelGizi) ?>,
        datasets: [{
            data: <?= json_encode($dataGizi) ?>,
            backgroundColor: ['#28a745', '#e8a317', '#dc3545', '#6c757d'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        cutout: '65%'
    }
});
</script>