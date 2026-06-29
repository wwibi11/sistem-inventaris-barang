<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = $_GET['id_kegiatan'] ?? '';

/*
|--------------------------------------------------------------------------
| DAFTAR KEGIATAN
|--------------------------------------------------------------------------
*/
$kegiatan = $pdo->query("
    SELECT *
    FROM kegiatan
    ORDER BY tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| DATA PEMERIKSAAN
|--------------------------------------------------------------------------
*/
$data = [];

if ($id_kegiatan) {
    $stmt = $pdo->prepare("
        SELECT
            p.*,
            a.nama,
            a.tanggal_lahir,
            u.nama AS petugas
        FROM pemeriksaan p
        JOIN anak a ON a.id = p.id_anak
        LEFT JOIN users u ON u.id = p.diukur_oleh
        WHERE p.id_kegiatan = ?
        ORDER BY a.nama ASC
    ");
    $stmt->execute([$id_kegiatan]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$totalPeriksa = count($data);

// Ambil info kegiatan
$infoKegiatan = null;
if ($id_kegiatan) {
    $stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE id = ?");
    $stmt->execute([$id_kegiatan]);
    $infoKegiatan = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<style>
/* ============================================
   STYLE DASHBOARD PEMERIKSAAN
   ============================================ */

.pemeriksaan-container { padding: 10px 0; }

/* Header */
.pemeriksaan-header {
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

.pemeriksaan-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.pemeriksaan-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.pemeriksaan-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Card Filter */
.card-filter {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 24px;
}

.card-filter .card-body {
    padding: 18px 22px;
}

.card-filter .form-control,
.card-filter .custom-select {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 10px 14px;
    height: 44px;
    background: #fafbfc;
    transition: all 0.2s ease;
}

.card-filter .form-control:focus,
.card-filter .custom-select:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

.btn-filter {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    width: 100%;
    transition: all 0.3s ease;
}

.btn-filter:hover {
    background: #1f507a;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(44, 107, 158, 0.25);
}

/* Stat Card */
.stat-card-pemeriksaan {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px 20px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
    transition: all 0.3s ease;
}

.stat-card-pemeriksaan:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-card-pemeriksaan .stat-icon {
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

.stat-card-pemeriksaan .stat-icon.primary { background: #2c6b9e; }

.stat-card-pemeriksaan .stat-number {
    font-size: 26px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

.stat-card-pemeriksaan .stat-label {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Card Tabel */
.card-table-pemeriksaan {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-table-pemeriksaan .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    background: #f8f9fc;
}

.card-table-pemeriksaan .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-table-pemeriksaan .card-header-custom h6 i {
    color: #2c6b9e;
    margin-right: 8px;
}

.btn-input-pemeriksaan {
    background: #28a745;
    color: #ffffff;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-input-pemeriksaan:hover {
    background: #1e7e34;
    color: #ffffff;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.25);
}

/* Tabel */
.table-pemeriksaan {
    font-size: 13px;
    margin: 0;
}

.table-pemeriksaan thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 14px;
    border-bottom: 2px solid #edf2f7;
    white-space: nowrap;
}

.table-pemeriksaan tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-pemeriksaan tbody tr:hover {
    background: #fafbfc;
}

.table-pemeriksaan tbody tr:last-child td {
    border-bottom: none;
}

/* Badge Gizi */
.badge-gizi {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-gizi.baik {
    background: #d1fae5;
    color: #047857;
}

.badge-gizi.kurang {
    background: #fef3c7;
    color: #92400e;
}

.badge-gizi.buruk {
    background: #fee2e2;
    color: #b91c1c;
}

/* Aksi Button */
.btn-action-pemeriksaan {
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    border: none;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-action-pemeriksaan.edit {
    background: #fef3c7;
    color: #92400e;
}

.btn-action-pemeriksaan.edit:hover {
    background: #92400e;
    color: #ffffff;
    text-decoration: none;
}

.btn-action-pemeriksaan.delete {
    background: #fee2e2;
    color: #b91c1c;
}

.btn-action-pemeriksaan.delete:hover {
    background: #b91c1c;
    color: #ffffff;
    text-decoration: none;
}

/* Empty State */
.empty-state-pemeriksaan {
    text-align: center;
    padding: 40px 20px;
}

.empty-state-pemeriksaan i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 12px;
    display: block;
}

.empty-state-pemeriksaan h6 {
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 4px;
}

.empty-state-pemeriksaan p {
    color: #8a94a6;
    font-size: 13px;
}

/* Responsive */
@media (max-width: 768px) {
    .pemeriksaan-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .card-table-pemeriksaan .card-header-custom {
        flex-direction: column;
        align-items: stretch;
    }
    .btn-input-pemeriksaan {
        justify-content: center;
    }
}
</style>

<div class="pemeriksaan-container">

    <!-- HEADER -->
    <div class="pemeriksaan-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-stethoscope"></i>
                Pemeriksaan Anak
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Monitoring hasil pemeriksaan Posyandu Bougenvil Belik
            </div>
        </div>
        <div>
            <span style="font-size: 13px; color: #8a94a6;">
                <i class="fas fa-calendar-alt"></i> 
                <?= date('d M Y') ?>
            </span>
        </div>
    </div>

    <!-- FILTER KEGIATAN -->
    <div class="card-filter">
        <div class="card-body">
            <form method="GET">
                <input type="hidden" name="url" value="pemeriksaan">
                <div class="row align-items-end">
                    <div class="col-md-9">
                        <div class="form-group mb-0">
                            <label style="font-weight: 600; color: #4a5568; font-size: 12px; margin-bottom: 4px;">
                                <i class="fas fa-calendar-alt"></i> Pilih Kegiatan
                            </label>
                            <select name="id_kegiatan" class="custom-select">
                                <option value="">-- Pilih Kegiatan Posyandu --</option>
                                <?php foreach($kegiatan as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= $id_kegiatan == $k['id'] ? 'selected' : '' ?>>
                                        Pertemuan <?= $k['pertemuan_ke'] ?> - 
                                        <?= date('d M Y', strtotime($k['tanggal'])) ?> - 
                                        <?= htmlspecialchars($k['lokasi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn-filter">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if($id_kegiatan): ?>

    <!-- STATISTIK -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="stat-card-pemeriksaan">
                <div class="stat-icon primary"><i class="fas fa-child"></i></div>
                <div class="stat-number"><?= $totalPeriksa ?></div>
                <div class="stat-label">
                    Total Anak Sudah Diperiksa 
                    <?php if($infoKegiatan): ?>
                        - Pertemuan <?= $infoKegiatan['pertemuan_ke'] ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card-table-pemeriksaan">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-table"></i> Data Pemeriksaan
            </h6>
            <a href="index.php?url=pemeriksaan-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn-input-pemeriksaan">
                <i class="fas fa-plus-circle"></i> Input Pemeriksaan
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-pemeriksaan">
                    <thead>
                        <tr>
                            <th>Nama Anak</th>
                            <th>BB (kg)</th>
                            <th>TB (cm)</th>
                            <th>LK (cm)</th>
                            <th>Status Gizi</th>
                            <th>Catatan</th>
                            <th>Petugas</th>
                            <th width="140" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($data)): ?>
                            <?php foreach($data as $d): 
                                $badgeClass = 'baik';
                                if ($d['status_gizi'] == 'Kurang') $badgeClass = 'kurang';
                                elseif ($d['status_gizi'] == 'Buruk') $badgeClass = 'buruk';
                            ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($d['nama']) ?></strong>
                                </td>
                                <td><?= $d['berat_badan'] ?></td>
                                <td><?= $d['tinggi_badan'] ?></td>
                                <td><?= $d['lingkar_kepala'] ?></td>
                                <td>
                                    <span class="badge-gizi <?= $badgeClass ?>">
                                        <?= htmlspecialchars($d['status_gizi'] ?? '-') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($d['catatan'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($d['petugas'] ?? '-') ?></td>
                                <td>
                                    <div class="d-flex justify-content-center" style="gap: 4px;">
                                        <!-- EDIT → ARAHKAN KE PEMERIKSAAN-INPUT DENGAN ID_ANAK -->
                                        <a href="index.php?url=pemeriksaan-input&id_kegiatan=<?= $id_kegiatan ?>&id_anak=<?= $d['id_anak'] ?>" 
                                        class="btn-action-pemeriksaan edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- DELETE -->
                                        <a href="index.php?url=pemeriksaan-delete&id=<?= $d['id'] ?>" 
                                        class="btn-action-pemeriksaan delete"
                                        onclick="return confirm('Yakin ingin menghapus data pemeriksaan ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state-pemeriksaan">
                                        <i class="fas fa-inbox"></i>
                                        <h6>Belum Ada Data Pemeriksaan</h6>
                                        <p>Klik tombol "Input Pemeriksaan" untuk menambahkan data</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php else: ?>

    <!-- Pilih Kegiatan -->
    <div class="text-center py-5" style="color: #8a94a6; background: #ffffff; border-radius: 12px; border: 1px solid #e8ecf1; padding: 40px;">
        <i class="fas fa-hand-point-up" style="font-size: 48px; display: block; margin-bottom: 12px; color: #d1d5db;"></i>
        <h6 style="color: #4a5568;">Pilih Kegiatan Terlebih Dahulu</h6>
        <p style="font-size: 13px;">Silahkan pilih kegiatan dari dropdown di atas untuk melihat data pemeriksaan</p>
    </div>

    <?php endif; ?>

</div>