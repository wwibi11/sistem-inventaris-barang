<?php
require_once __DIR__ . '/../../config/database.php';

$data = $pdo->query("
SELECT
    k.*,
    COUNT(DISTINCT a.id) AS total_anak,
    COUNT(DISTINCT CASE WHEN h.status_hadir='hadir' THEN h.id_anak END) AS total_hadir,
    COUNT(DISTINCT CASE WHEN h.status_hadir='tidak' THEN h.id_anak END) AS total_tidak
FROM kegiatan k
LEFT JOIN anak a ON a.status='aktif'
LEFT JOIN kehadiran h ON h.id_kegiatan = k.id
GROUP BY k.id
ORDER BY k.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Hitung total keseluruhan
$totalSemuaHadir = 0;
$totalSemuaTidak = 0;
$totalSemuaAnak = 0;
foreach($data as $d) {
    $totalSemuaHadir += $d['total_hadir'];
    $totalSemuaTidak += $d['total_tidak'];
    $totalSemuaAnak += $d['total_anak'];
}
?>

<style>
/* ============================================
   STYLE DASHBOARD KEHADIRAN
   ============================================ */

.kehadiran-container { padding: 10px 0; }

/* Header */
.kehadiran-header {
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

.kehadiran-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.kehadiran-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.kehadiran-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Stat Cards */
.stat-card-kehadiran {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px 20px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
    transition: all 0.3s ease;
}

.stat-card-kehadiran:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-card-kehadiran .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #ffffff;
    margin-bottom: 10px;
}

.stat-card-kehadiran .stat-icon.primary { background: #2c6b9e; }
.stat-card-kehadiran .stat-icon.success { background: #28a745; }
.stat-card-kehadiran .stat-icon.danger { background: #dc3545; }
.stat-card-kehadiran .stat-icon.info { background: #17a2b8; }

.stat-card-kehadiran .stat-number {
    font-size: 26px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

.stat-card-kehadiran .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Card Kegiatan */
.card-kehadiran {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.card-kehadiran:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
}

.card-kehadiran .card-body {
    padding: 20px 22px;
}

.card-kehadiran .kegiatan-title {
    font-size: 16px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.card-kehadiran .kegiatan-date {
    font-size: 13px;
    color: #8a94a6;
}

.card-kehadiran .kegiatan-location {
    font-size: 13px;
    color: #4a5568;
}

.card-kehadiran .kegiatan-location i {
    color: #dc3545;
}

/* Badge Status */
.badge-status-kehadiran {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-status-kehadiran.selesai {
    background: #d1fae5;
    color: #047857;
}

.badge-status-kehadiran.scheduled {
    background: #fef3c7;
    color: #92400e;
}

/* Progress */
.progress-kehadiran {
    height: 6px;
    border-radius: 4px;
    background: #edf2f7;
}

.progress-kehadiran .progress-bar {
    height: 100%;
    border-radius: 4px;
    background: #28a745;
    transition: width 0.6s ease;
}

/* Stat Mini dalam Card */
.stat-mini-kehadiran {
    text-align: center;
}

.stat-mini-kehadiran .stat-mini-number {
    font-size: 20px;
    font-weight: 700;
    color: #1a2634;
}

.stat-mini-kehadiran .stat-mini-number.hadir { color: #28a745; }
.stat-mini-kehadiran .stat-mini-number.tidak { color: #dc3545; }
.stat-mini-kehadiran .stat-mini-number.total { color: #2c6b9e; }

.stat-mini-kehadiran .stat-mini-label {
    font-size: 11px;
    color: #8a94a6;
}

/* Card Footer */
.card-footer-kehadiran {
    background: #fafbfc;
    border-top: 1px solid #edf2f7;
    padding: 14px 22px;
}

.btn-manage-kehadiran {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    width: 100%;
    transition: all 0.3s ease;
    text-align: center;
    display: block;
    text-decoration: none;
}

.btn-manage-kehadiran:hover {
    background: #1f507a;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(44, 107, 158, 0.25);
    text-decoration: none;
}

.btn-manage-kehadiran i {
    margin-right: 6px;
}

.footer-info {
    font-size: 11px;
    color: #8a94a6;
    text-align: center;
    margin-top: 8px;
}

.footer-info i {
    margin: 0 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .kehadiran-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
}
</style>

<div class="kehadiran-container">

    <!-- HEADER -->
    <div class="kehadiran-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-user-check"></i>
                Kehadiran Posyandu
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Monitoring dan pencatatan kehadiran anak pada setiap kegiatan
            </div>
        </div>
        <div>
            <span style="font-size: 13px; color: #8a94a6;">
                <i class="fas fa-calendar-alt"></i> 
                <?= date('d M Y') ?>
            </span>
        </div>
    </div>

    <!-- STATISTIK -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-kehadiran">
                <div class="stat-icon primary"><i class="fas fa-users"></i></div>
                <div class="stat-number"><?= $totalSemuaAnak ?></div>
                <div class="stat-label">Total Anak Aktif</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-kehadiran">
                <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number"><?= $totalSemuaHadir ?></div>
                <div class="stat-label">Total Kehadiran</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-kehadiran">
                <div class="stat-icon danger"><i class="fas fa-times-circle"></i></div>
                <div class="stat-number"><?= $totalSemuaTidak ?></div>
                <div class="stat-label">Total Tidak Hadir</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-kehadiran">
                <div class="stat-icon info"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-number"><?= count($data) ?></div>
                <div class="stat-label">Total Kegiatan</div>
            </div>
        </div>
    </div>

    <!-- LIST KEGIATAN -->
    <div class="row">
        <?php foreach($data as $d): 
            $persen = 0;
            if ($d['total_anak'] > 0) {
                $persen = round(($d['total_hadir'] / $d['total_anak']) * 100);
            }
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card-kehadiran">
                <div class="card-body">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="kegiatan-title">
                                Pertemuan <?= $d['pertemuan_ke'] ?>
                            </div>
                            <div class="kegiatan-date">
                                <i class="far fa-calendar-alt"></i> 
                                <?= date('d M Y', strtotime($d['tanggal'])) ?>
                            </div>
                        </div>
                        <span class="badge-status-kehadiran <?= $d['status'] ?>">
                            <?= ucfirst($d['status']) ?>
                        </span>
                    </div>

                    <!-- Lokasi -->
                    <div class="kegiatan-location mb-3">
                        <i class="fas fa-map-marker-alt"></i> 
                        <?= htmlspecialchars($d['lokasi']) ?>
                    </div>

                    <hr style="margin: 12px 0;">

                    <!-- Stat Mini -->
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-mini-kehadiran">
                                <div class="stat-mini-number hadir"><?= $d['total_hadir'] ?></div>
                                <div class="stat-mini-label"><i class="fas fa-check-circle" style="color: #28a745;"></i> Hadir</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini-kehadiran">
                                <div class="stat-mini-number tidak"><?= $d['total_tidak'] ?></div>
                                <div class="stat-mini-label"><i class="fas fa-times-circle" style="color: #dc3545;"></i> Tidak</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini-kehadiran">
                                <div class="stat-mini-number total"><?= $d['total_anak'] ?></div>
                                <div class="stat-mini-label"><i class="fas fa-users" style="color: #2c6b9e;"></i> Anak</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="mt-3">
                        <div class="d-flex justify-content-between" style="font-size: 12px;">
                            <span style="color: #4a5568;">Progress Kehadiran</span>
                            <span style="color: #1a2634; font-weight: 600;"><?= $persen ?>%</span>
                        </div>
                        <div class="progress-kehadiran">
                            <div class="progress-bar" style="width: <?= $persen ?>%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="card-footer-kehadiran">
                    <a href="index.php?url=kegiatan-detail&id=<?= $d['id'] ?>" class="btn-manage-kehadiran">
                        <i class="fas fa-chevron-right"></i> Kelola Kegiatan
                    </a>
                    <div class="footer-info">
                        <i class="fas fa-user-check"></i> Kehadiran 
                        <i class="fas fa-circle" style="font-size: 4px; color: #d1d5db; margin: 0 4px;"></i>
                        <i class="fas fa-stethoscope"></i> Pemeriksaan
                        <i class="fas fa-circle" style="font-size: 4px; color: #d1d5db; margin: 0 4px;"></i>
                        <i class="fas fa-syringe"></i> Imunisasi
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if(count($data) == 0): ?>
        <div class="col-12">
            <div class="text-center py-5" style="color: #8a94a6;">
                <i class="fas fa-calendar-times" style="font-size: 48px; display: block; margin-bottom: 12px; color: #d1d5db;"></i>
                <h6 style="color: #4a5568;">Belum Ada Kegiatan</h6>
                <p style="font-size: 13px;">Tambahkan kegiatan terlebih dahulu untuk mulai mencatat kehadiran</p>
                <a href="index.php?url=kegiatan-create" class="btn btn-primary" style="border-radius: 8px; padding: 8px 20px;">
                    <i class="fas fa-plus-circle"></i> Tambah Kegiatan
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>