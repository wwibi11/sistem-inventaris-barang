<?php
require_once __DIR__ . '/../../config/database.php';

$totalAnak = $pdo->query("SELECT COUNT(*) FROM anak")->fetchColumn();
$totalKeluarga = $pdo->query("SELECT COUNT(*) FROM keluarga")->fetchColumn();
$totalKegiatan = $pdo->query("SELECT COUNT(*) FROM kegiatan")->fetchColumn();
$totalPemeriksaan = $pdo->query("SELECT COUNT(*) FROM pemeriksaan")->fetchColumn();
$totalImunisasi = $pdo->query("SELECT COUNT(*) FROM imunisasi")->fetchColumn();

$totalHadir = $pdo->query("
    SELECT COUNT(*)
    FROM kehadiran
    WHERE status_hadir='hadir'
")->fetchColumn();

$totalUndangan = $pdo->query("
    SELECT COUNT(*)
    FROM kehadiran
")->fetchColumn();

$persentaseHadir = $totalUndangan > 0
    ? round(($totalHadir / $totalUndangan) * 100, 1)
    : 0;

$totalAnakImunisasi = $pdo->query("
    SELECT COUNT(DISTINCT id_anak)
    FROM imunisasi
")->fetchColumn();

$totalAnakPeriksa = $pdo->query("
    SELECT COUNT(DISTINCT id_anak)
    FROM pemeriksaan
")->fetchColumn();
?>

<div class="container-fluid">

    <div class="mb-4">
        <h3 class="mb-1">Statistik Posyandu</h3>
        <small class="text-muted">Ringkasan data Posyandu secara keseluruhan</small>
    </div>

    <div class="row">

        <!-- TOTAL ANAK -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Anak
                            </div>
                            <div class="h3 font-weight-bold"><?= $totalAnak ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-child fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOTAL KELUARGA -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Keluarga
                            </div>
                            <div class="h3 font-weight-bold"><?= $totalKeluarga ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOTAL KEGIATAN -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Kegiatan
                            </div>
                            <div class="h3 font-weight-bold"><?= $totalKegiatan ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOTAL KEHADIRAN -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Kehadiran
                            </div>
                            <div class="h3 font-weight-bold"><?= $totalHadir ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOTAL PEMERIKSAAN -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Total Pemeriksaan
                            </div>
                            <div class="h3 font-weight-bold"><?= $totalPemeriksaan ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-stethoscope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOTAL IMUNISASI -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Imunisasi
                            </div>
                            <div class="h3 font-weight-bold"><?= $totalImunisasi ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-syringe fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ANAK DIPERIKSA -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Anak Pernah Diperiksa
                            </div>
                            <div class="h3 font-weight-bold"><?= $totalAnakPeriksa ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-notes-medical fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ANAK IMUNISASI -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Anak Sudah Imunisasi
                            </div>
                            <div class="h3 font-weight-bold"><?= $totalAnakImunisasi ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-syringe fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERSENTASE HADIR -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Persentase Kehadiran
                            </div>
                            <div class="h3 font-weight-bold"><?= $persentaseHadir ?>%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>