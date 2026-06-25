<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

// DATA ANAK
$stmt = $pdo->prepare("SELECT * FROM anak WHERE id = ?");
$stmt->execute([$id]);
$anak = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$anak) die('Data anak tidak ditemukan');

// DATA PERTUMBUHAN
$stmt = $pdo->prepare("
SELECT p.*, k.tanggal
FROM pemeriksaan p
JOIN kegiatan k ON k.id = p.id_kegiatan
WHERE p.id_anak = ?
ORDER BY k.tanggal ASC
");
$stmt->execute([$id]);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DATA GRAFIK
$label = []; $bb = []; $tb = []; $lk = [];
foreach($riwayat as $r){
    $label[] = date('d M Y', strtotime($r['tanggal']));
    $bb[] = (float)$r['berat_badan'];
    $tb[] = (float)$r['tinggi_badan'];
    $lk[] = (float)$r['lingkar_kepala'];
}

$terakhir = !empty($riwayat) ? end($riwayat) : ['berat_badan' => 0, 'tinggi_badan' => 0, 'lingkar_kepala' => 0];
$sebelumnya = count($riwayat) >= 2 ? $riwayat[count($riwayat)-2] : null;

$deltaBB = $sebelumnya ? $terakhir['berat_badan'] - $sebelumnya['berat_badan'] : 0;
$deltaTB = $sebelumnya ? $terakhir['tinggi_badan'] - $sebelumnya['tinggi_badan'] : 0;
$deltaLK = $sebelumnya ? $terakhir['lingkar_kepala'] - $sebelumnya['lingkar_kepala'] : 0;
?>

<style>
.pertumbuhan-container { padding: 15px 0; }

.pertumbuhan-header {
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

.pertumbuhan-header h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.pertumbuhan-header h4 i { color: #2c6b9e; margin-right: 10px; }

.pertumbuhan-header .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

.stat-card-growth {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    padding: 16px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
}

.stat-card-growth .stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #1a2634;
}

.stat-card-growth .stat-delta {
    font-size: 12px;
    font-weight: 600;
}

.stat-card-growth .stat-delta.positive { color: #28a745; }
.stat-card-growth .stat-delta.negative { color: #dc2626; }

.stat-card-growth .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

.card-growth {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 20px;
}

.card-growth .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    font-weight: 600;
    color: #1a2634;
    font-size: 14px;
    background: #f8f9fc;
}

.card-growth .card-header-custom i { margin-right: 8px; color: #2c6b9e; }
.card-growth .card-body-custom { padding: 20px; }

.chart-wrapper { height: 350px; position: relative; }

.table-growth {
    font-size: 13px;
    margin: 0;
}

.table-growth thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 14px;
    border-bottom: 2px solid #edf2f7;
}

.table-growth tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.btn-back-growth {
    background: #f0f4f8;
    color: #4a5568;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-back-growth:hover {
    background: #e2e8f0;
    color: #1a2634;
    text-decoration: none;
}

@media (max-width: 768px) {
    .pertumbuhan-header { flex-direction: column; align-items: stretch; }
    .chart-wrapper { height: 250px; }
}
</style>

<div class="pertumbuhan-container">

    <!-- HEADER -->
    <div class="pertumbuhan-header">
        <div>
            <h4><i class="fas fa-chart-line"></i> Grafik Pertumbuhan</h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <?= htmlspecialchars($anak['nama']) ?> - <?= date('d M Y', strtotime($anak['tanggal_lahir'])) ?>
            </div>
        </div>
        <a href="index.php?url=anak-detail&id=<?= $id ?>" class="btn-back-growth">
            <i class="fas fa-arrow-left"></i> Kembali ke Detail
        </a>
    </div>

    <!-- STAT CARD -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card-growth">
                <div class="stat-value"><?= $terakhir['berat_badan'] ?> Kg</div>
                <div class="stat-delta <?= $deltaBB >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $deltaBB >= 0 ? 'up' : 'down' ?>"></i>
                    <?= $deltaBB >= 0 ? '+' : '' ?><?= number_format($deltaBB, 1) ?> Kg
                </div>
                <div class="stat-label">Berat Badan Terakhir</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-growth">
                <div class="stat-value"><?= $terakhir['tinggi_badan'] ?> Cm</div>
                <div class="stat-delta <?= $deltaTB >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $deltaTB >= 0 ? 'up' : 'down' ?>"></i>
                    <?= $deltaTB >= 0 ? '+' : '' ?><?= number_format($deltaTB, 1) ?> Cm
                </div>
                <div class="stat-label">Tinggi Badan Terakhir</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card-growth">
                <div class="stat-value"><?= $terakhir['lingkar_kepala'] ?> Cm</div>
                <div class="stat-delta <?= $deltaLK >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $deltaLK >= 0 ? 'up' : 'down' ?>"></i>
                    <?= $deltaLK >= 0 ? '+' : '' ?><?= number_format($deltaLK, 1) ?> Cm
                </div>
                <div class="stat-label">Lingkar Kepala Terakhir</div>
            </div>
        </div>
    </div>

    <!-- GRAFIK -->
    <div class="card-growth">
        <div class="card-header-custom">
            <i class="fas fa-chart-line"></i> Grafik Pertumbuhan Anak
        </div>
        <div class="card-body-custom">
            <div class="chart-wrapper">
                <canvas id="grafikPertumbuhan"></canvas>
            </div>
        </div>
    </div>

    <!-- RIWAYAT -->
    <div class="card-growth">
        <div class="card-header-custom">
            <i class="fas fa-table"></i> Riwayat Pemeriksaan
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-growth">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>BB (Kg)</th>
                            <th>TB (Cm)</th>
                            <th>LK (Cm)</th>
                            <th>Status Gizi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($riwayat): ?>
                            <?php foreach($riwayat as $r): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
                                <td><?= $r['berat_badan'] ?></td>
                                <td><?= $r['tinggi_badan'] ?></td>
                                <td><?= $r['lingkar_kepala'] ?></td>
                                <td><span class="badge-status <?= $r['status_gizi'] == 'normal' ? 'aktif' : 'pindah' ?>"><?= ucfirst($r['status_gizi'] ?? '-') ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data pemeriksaan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('grafikPertumbuhan'), {
    type: 'line',
    data: {
        labels: <?= json_encode($label) ?>,
        datasets: [
            {
                label: 'Berat Badan (Kg)',
                data: <?= json_encode($bb) ?>,
                borderColor: '#2c6b9e',
                backgroundColor: 'rgba(44,107,158,0.1)',
                borderWidth: 3,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#2c6b9e',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5
            },
            {
                label: 'Tinggi Badan (Cm)',
                data: <?= json_encode($tb) ?>,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40,167,69,0.1)',
                borderWidth: 3,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#28a745',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5
            },
            {
                label: 'Lingkar Kepala (Cm)',
                data: <?= json_encode($lk) ?>,
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23,162,184,0.1)',
                borderWidth: 3,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#17a2b8',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: { size: 12, family: 'Segoe UI' },
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
                    font: { size: 11, family: 'Segoe UI' },
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
                    font: { size: 11, family: 'Segoe UI' },
                    color: '#8a94a6'
                }
            }
        }
    }
});
</script>