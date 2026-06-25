<?php
require_once __DIR__ . '/../../config/database.php';

$totalAnak = $pdo->query("SELECT COUNT(*) FROM anak")->fetchColumn();
$totalKeluarga = $pdo->query("SELECT COUNT(*) FROM keluarga")->fetchColumn();
$totalKegiatan = $pdo->query("SELECT COUNT(*) FROM kegiatan")->fetchColumn();
$totalPemeriksaan = $pdo->query("SELECT COUNT(*) FROM pemeriksaan")->fetchColumn();
$totalImunisasi = $pdo->query("SELECT COUNT(*) FROM imunisasi")->fetchColumn();

$totalHadir = $pdo->query("SELECT COUNT(*) FROM kehadiran WHERE status_hadir='hadir'")->fetchColumn();
$totalUndangan = $pdo->query("SELECT COUNT(*) FROM kehadiran")->fetchColumn();

$persentaseHadir = $totalUndangan > 0 ? round(($totalHadir / $totalUndangan) * 100, 1) : 0;

$totalAnakImunisasi = $pdo->query("SELECT COUNT(DISTINCT id_anak) FROM imunisasi")->fetchColumn();
$totalAnakPeriksa = $pdo->query("SELECT COUNT(DISTINCT id_anak) FROM pemeriksaan")->fetchColumn();
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
}

.stat-card-statistik:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-card-statistik .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #ffffff;
    margin-bottom: 10px;
}

.stat-card-statistik .stat-icon.primary { background: #2c6b9e; }
.stat-card-statistik .stat-icon.success { background: #28a745; }
.stat-card-statistik .stat-icon.info { background: #17a2b8; }
.stat-card-statistik .stat-icon.warning { background: #e8a317; }
.stat-card-statistik .stat-icon.danger { background: #dc3545; }
.stat-card-statistik .stat-icon.secondary { background: #6c757d; }
.stat-card-statistik .stat-icon.purple { background: #6f42c1; }

.stat-card-statistik .stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

.stat-card-statistik .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
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
}

@media print {
    .btn-cetak-statistik { display: none; }
    .statistik-header { box-shadow: none; border: 1px solid #ddd; }
    .stat-card-statistik { border: 1px solid #ddd; box-shadow: none; }
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

    <!-- STATISTIK -->
    <div class="row">
        <!-- Total Anak -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon primary"><i class="fas fa-child"></i></div>
                <div class="stat-number"><?= $totalAnak ?></div>
                <div class="stat-label">Total Anak</div>
            </div>
        </div>

        <!-- Total Keluarga -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon success"><i class="fas fa-home"></i></div>
                <div class="stat-number"><?= $totalKeluarga ?></div>
                <div class="stat-label">Total Keluarga</div>
            </div>
        </div>

        <!-- Total Kegiatan -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon info"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-number"><?= $totalKegiatan ?></div>
                <div class="stat-label">Total Kegiatan</div>
            </div>
        </div>

        <!-- Total Kehadiran -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon warning"><i class="fas fa-user-check"></i></div>
                <div class="stat-number"><?= $totalHadir ?></div>
                <div class="stat-label">Total Kehadiran</div>
            </div>
        </div>

        <!-- Total Pemeriksaan -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon secondary"><i class="fas fa-stethoscope"></i></div>
                <div class="stat-number"><?= $totalPemeriksaan ?></div>
                <div class="stat-label">Total Pemeriksaan</div>
            </div>
        </div>

        <!-- Total Imunisasi -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon danger"><i class="fas fa-syringe"></i></div>
                <div class="stat-number"><?= $totalImunisasi ?></div>
                <div class="stat-label">Total Imunisasi</div>
            </div>
        </div>

        <!-- Anak Pernah Diperiksa -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon success"><i class="fas fa-notes-medical"></i></div>
                <div class="stat-number"><?= $totalAnakPeriksa ?></div>
                <div class="stat-label">Anak Pernah Diperiksa</div>
            </div>
        </div>

        <!-- Anak Sudah Imunisasi -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon info"><i class="fas fa-syringe"></i></div>
                <div class="stat-number"><?= $totalAnakImunisasi ?></div>
                <div class="stat-label">Anak Sudah Imunisasi</div>
            </div>
        </div>

        <!-- Persentase Kehadiran -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="stat-card-statistik">
                <div class="stat-icon purple"><i class="fas fa-chart-pie"></i></div>
                <div class="stat-number"><?= $persentaseHadir ?>%</div>
                <div class="stat-label">Persentase Kehadiran</div>
            </div>
        </div>
    </div>

</div>