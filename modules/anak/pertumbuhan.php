<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

/*
|--------------------------------------------------------------------------
| DATA ANAK
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
SELECT *
FROM anak
WHERE id = ?
");

$stmt->execute([$id]);

$anak = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$anak){
    die('Data anak tidak ditemukan');
}

/*
|--------------------------------------------------------------------------
| DATA PERTUMBUHAN
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
SELECT
    p.*,
    k.tanggal

FROM pemeriksaan p

JOIN kegiatan k
    ON k.id = p.id_kegiatan

WHERE p.id_anak = ?

ORDER BY k.tanggal ASC
");

$stmt->execute([$id]);

$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| DATA GRAFIK
|--------------------------------------------------------------------------
*/
$label = [];
$bb = [];
$tb = [];
$lk = [];

foreach($riwayat as $r){

    $label[] = date(
        'd M Y',
        strtotime($r['tanggal'])
    );

    $bb[] = (float)$r['berat_badan'];
    $tb[] = (float)$r['tinggi_badan'];
    $lk[] = (float)$r['lingkar_kepala'];
}
?>

<?php
$terakhir = !empty($riwayat)
    ? end($riwayat)
    : [
        'berat_badan'   => 0,
        'tinggi_badan'  => 0,
        'lingkar_kepala'=> 0
    ];
?>

<?php

$sebelumnya = count($riwayat) >= 2
    ? $riwayat[count($riwayat)-2]
    : null;

$deltaBB = $sebelumnya
    ? $terakhir['berat_badan'] - $sebelumnya['berat_badan']
    : 0;

$deltaTB = $sebelumnya
    ? $terakhir['tinggi_badan'] - $sebelumnya['tinggi_badan']
    : 0;

$deltaLK = $sebelumnya
    ? $terakhir['lingkar_kepala'] - $sebelumnya['lingkar_kepala']
    : 0;
?>


<div class="container-fluid">

<div class="row mb-4">

    <div class="col-md-4">

        <div class="card border-left-primary shadow-sm">

            <div class="card-body">

                <h3 class="mb-1">
                    <?= $terakhir['berat_badan'] ?> Kg
                </h3>

                <div class="small <?= $deltaBB >= 0 ? 'text-success' : 'text-danger' ?>">

                    <i class="fas fa-arrow-<?= $deltaBB >= 0 ? 'up' : 'down' ?>"></i>

                    <?= $deltaBB >= 0 ? '+' : '' ?>
                    <?= number_format($deltaBB, 1) ?> Kg

                </div>

                <small class="text-muted">
                    Berat Badan Terakhir
                </small>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-left-success shadow-sm">

            <div class="card-body">

                <h3 class="mb-1">
                    <?= $terakhir['tinggi_badan'] ?> Cm
                </h3>

                <div class="small <?= $deltaTB >= 0 ? 'text-success' : 'text-danger' ?>">

                    <i class="fas fa-arrow-<?= $deltaTB >= 0 ? 'up' : 'down' ?>"></i>

                    <?= $deltaTB >= 0 ? '+' : '' ?>
                    <?= number_format($deltaTB, 1) ?> Cm

                </div>

                <small class="text-muted">
                    Tinggi Badan Terakhir
                </small>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-left-info shadow-sm">

            <div class="card-body">

                <h3 class="mb-1">
                    <?= $terakhir['lingkar_kepala'] ?> Cm
                </h3>

                <div class="small <?= $deltaLK >= 0 ? 'text-success' : 'text-danger' ?>">

                    <i class="fas fa-arrow-<?= $deltaLK >= 0 ? 'up' : 'down' ?>"></i>

                    <?= $deltaLK >= 0 ? '+' : '' ?>
                    <?= number_format($deltaLK, 1) ?> Cm

                </div>

                <small class="text-muted">
                    Lingkar Kepala Terakhir
                </small>

            </div>

        </div>

    </div>

</div>

<div class="card shadow mb-4">

    <div class="card-header bg-primary text-white">

        Grafik Pertumbuhan

    </div>

    <div class="card-body">

        <div style="height:350px;">

            <canvas id="grafikPertumbuhan"></canvas>

        </div>

    </div>

</div>

    <div class="card shadow">

        <div class="card-header">

            Riwayat Pemeriksaan

        </div>

        <div class="card-body p-0">

            <table class="table table-bordered mb-0">

                <thead>

                    <tr>

                        <th>Tanggal</th>
                        <th>BB (Kg)</th>
                        <th>TB (Cm)</th>
                        <th>LK (Cm)</th>
                        <th>Gizi</th>

                    </tr>

                </thead>

                <tbody>

                <?php if(count($riwayat)): ?>

                    <?php foreach($riwayat as $r): ?>

                    <tr>

                        <td>
                            <?= date('d M Y', strtotime($r['tanggal'])) ?>
                        </td>

                        <td>
                            <?= $r['berat_badan'] ?>
                        </td>

                        <td>
                            <?= $r['tinggi_badan'] ?>
                        </td>

                        <td>
                            <?= $r['lingkar_kepala'] ?>
                        </td>

                        <td>
                            <?= $r['status_gizi'] ?>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="5"
                            class="text-center text-muted">

                            Belum ada data pemeriksaan

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

new Chart(
    document.getElementById('grafikPertumbuhan'),
    {
        type:'line',

        data:{
            labels: <?= json_encode($label) ?>,

          datasets: [
            {
                label:'Berat Badan (Kg)',
                data: <?= json_encode($bb) ?>,
                borderColor:'#007bff',
                backgroundColor:'#007bff',
                borderWidth:3,
                tension:0.3
            },
            {
                label:'Tinggi Badan (Cm)',
                data: <?= json_encode($tb) ?>,
                borderColor:'#28a745',
                backgroundColor:'#28a745',
                borderWidth:3,
                tension:0.3
            },
            {
                label:'Lingkar Kepala (Cm)',
                data: <?= json_encode($lk) ?>,
                borderColor:'#17a2b8',
                backgroundColor:'#17a2b8',
                borderWidth:3,
                tension:0.3
            }
            ]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false
        }
    }
);

</script>