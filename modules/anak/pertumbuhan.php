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

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Grafik Pertumbuhan Anak
            </h3>

            <small class="text-muted">
                <?= htmlspecialchars($anak['nama']) ?>
            </small>

        </div>

        <a href="index.php?url=anak-detail&id=<?= $anak['id'] ?>"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>
            Kembali

        </a>

    </div>

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card border-left-primary shadow-sm">

                <div class="card-body">

                    <h4>
                        <?= $terakhir['berat_badan'] ?> Kg
                    </h4>

                    <small>
                        Berat Badan Terakhir
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-left-success shadow-sm">

                <div class="card-body">

                    <h4>
                        <?= $terakhir['tinggi_badan'] ?> Cm
                    </h4>

                    <small>
                        Tinggi Badan Terakhir
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-left-info shadow-sm">

                <div class="card-body">

                    <h4>
                        <?= $terakhir['lingkar_kepala'] ?> Cm
                    </h4>

                    <small>
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

            <canvas id="grafikPertumbuhan"></canvas>

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

            datasets:[
                {
                    label:'Berat Badan (Kg)',
                    data: <?= json_encode($bb) ?>,
                    borderWidth:3,
                    tension:0.3
                },
                {
                    label:'Tinggi Badan (Cm)',
                    data: <?= json_encode($tb) ?>,
                    borderWidth:3,
                    tension:0.3
                },
                {
                    label:'Lingkar Kepala (Cm)',
                    data: <?= json_encode($lk) ?>,
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