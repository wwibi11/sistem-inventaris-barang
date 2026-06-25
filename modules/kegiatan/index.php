<?php
require_once __DIR__ . '/../../config/database.php';

$totalAnak = $pdo->query("SELECT COUNT(*) FROM anak WHERE status='aktif'")->fetchColumn();
$totalKegiatan = $pdo->query("SELECT COUNT(*) FROM kegiatan")->fetchColumn();
$totalSelesai = $pdo->query("SELECT COUNT(*) FROM kegiatan WHERE status='selesai'")->fetchColumn();
$totalScheduled = $pdo->query("SELECT COUNT(*) FROM kegiatan WHERE status='scheduled'")->fetchColumn();

$data = $pdo->query("
SELECT
    k.*,
    u.nama AS pembuat,
    COUNT(DISTINCT CASE WHEN h.status_hadir='hadir' THEN h.id_anak END) AS total_hadir,
    COUNT(DISTINCT CASE WHEN h.status_hadir='tidak' THEN h.id_anak END) AS total_tidak_hadir,
    COUNT(DISTINCT p.id_anak) AS total_pemeriksaan,
    COUNT(DISTINCT i.id_anak) AS total_imunisasi
FROM kegiatan k
LEFT JOIN users u ON u.id = k.created_by
LEFT JOIN kehadiran h ON h.id_kegiatan = k.id
LEFT JOIN pemeriksaan p ON p.id_kegiatan = k.id
LEFT JOIN imunisasi i ON i.id_kegiatan = k.id
GROUP BY k.id
ORDER BY k.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* ============================================
   STYLE DASHBOARD KEGIATAN
   ============================================ */

.kegiatan-container { padding: 10px 0; }

/* Header */
.kegiatan-header {
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

.kegiatan-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.kegiatan-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.kegiatan-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Stat Cards */
.stat-card-kegiatan {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px 20px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
    transition: all 0.3s ease;
}

.stat-card-kegiatan:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-card-kegiatan .stat-icon {
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

.stat-card-kegiatan .stat-icon.primary { background: #2c6b9e; }
.stat-card-kegiatan .stat-icon.success { background: #28a745; }
.stat-card-kegiatan .stat-icon.info { background: #17a2b8; }
.stat-card-kegiatan .stat-icon.warning { background: #e8a317; }

.stat-card-kegiatan .stat-number {
    font-size: 26px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

.stat-card-kegiatan .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Button Tambah */
.btn-tambah-kegiatan {
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
    text-decoration: none;
}

.btn-tambah-kegiatan:hover {
    background: #1f507a;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(44, 107, 158, 0.25);
    text-decoration: none;
}

/* Card Kegiatan */
.card-kegiatan {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.card-kegiatan:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
}

.card-kegiatan .card-body {
    padding: 20px 22px;
}

.card-kegiatan .kegiatan-title {
    font-size: 16px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.card-kegiatan .kegiatan-date {
    font-size: 13px;
    color: #8a94a6;
}

.card-kegiatan .kegiatan-location {
    font-size: 13px;
    color: #4a5568;
}

.card-kegiatan .kegiatan-location i {
    color: #dc3545;
}

/* Badge Status */
.badge-status-kegiatan {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-status-kegiatan.selesai {
    background: #d1fae5;
    color: #047857;
}

.badge-status-kegiatan.scheduled {
    background: #fef3c7;
    color: #92400e;
}

/* Progress */
.progress-kegiatan {
    height: 6px;
    border-radius: 4px;
    background: #edf2f7;
}

.progress-kegiatan .progress-bar {
    height: 100%;
    border-radius: 4px;
    background: #28a745;
    transition: width 0.6s ease;
}

/* Stat Mini dalam Card */
.stat-mini-card {
    text-align: center;
}

.stat-mini-card .stat-mini-number {
    font-size: 20px;
    font-weight: 700;
    color: #1a2634;
}

.stat-mini-card .stat-mini-label {
    font-size: 11px;
    color: #8a94a6;
}

/* Aksi Button */
.btn-action-kegiatan {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    border: none;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-action-kegiatan.edit {
    background: #fef3c7;
    color: #92400e;
}

.btn-action-kegiatan.edit:hover {
    background: #92400e;
    color: #ffffff;
    text-decoration: none;
}

.btn-action-kegiatan.delete {
    background: #fee2e2;
    color: #b91c1c;
}

.btn-action-kegiatan.delete:hover {
    background: #b91c1c;
    color: #ffffff;
    text-decoration: none;
}

.btn-action-kegiatan.manage {
    background: #2c6b9e;
    color: #ffffff;
    width: 100%;
    justify-content: center;
    padding: 10px;
}

.btn-action-kegiatan.manage:hover {
    background: #1f507a;
    color: #ffffff;
    text-decoration: none;
}

/* Responsive */
@media (max-width: 768px) {
    .kegiatan-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .btn-tambah-kegiatan {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="kegiatan-container">

    <!-- HEADER -->
    <div class="kegiatan-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-calendar-alt"></i>
                Jadwal Posyandu
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Monitoring seluruh kegiatan Posyandu Bougenvil Belik
            </div>
        </div>
        <a href="index.php?url=kegiatan-create" class="btn-tambah-kegiatan">
            <i class="fas fa-plus-circle"></i> Tambah Kegiatan
        </a>
    </div>

    <!-- STATISTIK -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-kegiatan">
                <div class="stat-icon primary"><i class="fas fa-child"></i></div>
                <div class="stat-number"><?= $totalAnak ?></div>
                <div class="stat-label">Total Anak Aktif</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-kegiatan">
                <div class="stat-icon success"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-number"><?= $totalKegiatan ?></div>
                <div class="stat-label">Total Kegiatan</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-kegiatan">
                <div class="stat-icon info"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number"><?= $totalSelesai ?></div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-kegiatan">
                <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                <div class="stat-number"><?= $totalScheduled ?></div>
                <div class="stat-label">Terjadwal</div>
            </div>
        </div>
    </div>

    <!-- LIST KEGIATAN -->
    <div class="row">
        <?php foreach($data as $d): 
            $progress = 0;
            if($totalAnak > 0) {
                $progress = round(($d['total_hadir'] / $totalAnak) * 100);
            }
        ?>
        <div class="col-md-6 mb-4">
            <div class="card-kegiatan">
                <div class="card-body">
                    <!-- Header Card -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="kegiatan-title">
                                Pertemuan Ke <?= $d['pertemuan_ke'] ?>
                            </div>
                            <div class="kegiatan-date">
                                <i class="far fa-calendar-alt"></i> 
                                <?= date('d M Y', strtotime($d['tanggal'])) ?>
                            </div>
                        </div>
                        <span class="badge-status-kegiatan <?= $d['status'] ?>">
                            <?= ucfirst($d['status']) ?>
                        </span>
                    </div>

                    <hr style="margin: 12px 0;">

                    <!-- Lokasi -->
                    <div class="kegiatan-location mb-3">
                        <i class="fas fa-map-marker-alt"></i> 
                        <?= htmlspecialchars($d['lokasi']) ?>
                    </div>

                    <!-- Stat Mini -->
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="stat-mini-card">
                                <div class="stat-mini-number text-primary"><?= $d['total_hadir'] ?></div>
                                <div class="stat-mini-label">Hadir</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini-card">
                                <div class="stat-mini-number text-success"><?= $d['total_pemeriksaan'] ?></div>
                                <div class="stat-mini-label">Pemeriksaan</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini-card">
                                <div class="stat-mini-number text-info"><?= $d['total_imunisasi'] ?></div>
                                <div class="stat-mini-label">Imunisasi</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between" style="font-size: 12px;">
                            <span style="color: #4a5568;">Kehadiran</span>
                            <span style="color: #1a2634; font-weight: 600;"><?= $progress ?>%</span>
                        </div>
                        <div class="progress-kegiatan">
                            <div class="progress-bar" style="width: <?= $progress ?>%;"></div>
                        </div>
                    </div>

                    <!-- Aksi -->
                    <a href="index.php?url=kegiatan-detail&id=<?= $d['id'] ?>" class="btn-action-kegiatan manage">
                        <i class="fas fa-chevron-right"></i> Kelola Kegiatan
                    </a>

                    <div class="mt-2 text-right" style="gap: 6px;">
                        <a href="index.php?url=kegiatan-edit&id=<?= $d['id'] ?>" class="btn-action-kegiatan edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="index.php?url=kegiatan-delete&id=<?= $d['id'] ?>" 
                           class="btn-action-kegiatan delete"
                           onclick="return confirm('Yakin ingin menghapus kegiatan ini?')">
                            <i class="fas fa-trash-alt"></i> Hapus
                        </a>
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
                <p style="font-size: 13px;">Klik tombol "Tambah Kegiatan" untuk membuat jadwal baru</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>