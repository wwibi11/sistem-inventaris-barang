<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = (int) ($_GET['id_kegiatan'] ?? 0);

if (!$id_kegiatan) {
    header("Location: index.php?url=pemeriksaan");
    exit;
}

/*
|--------------------------------------------------------------------------
| DATA KEGIATAN
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE id = ?");
$stmt->execute([$id_kegiatan]);
$kegiatan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kegiatan) {
    die("
    <div style='padding:30px'>
        <h3>Kegiatan tidak ditemukan</h3>
        <a href='index.php?url=pemeriksaan'>Kembali</a>
    </div>
    ");
}

/*
|--------------------------------------------------------------------------
| ANAK HADIR
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT
        a.*,
        TIMESTAMPDIFF(YEAR, a.tanggal_lahir, CURDATE()) AS umur_tahun,
        TIMESTAMPDIFF(MONTH, a.tanggal_lahir, CURDATE()) % 12 AS umur_sisa_bulan,
        TIMESTAMPDIFF(MONTH, a.tanggal_lahir, CURDATE()) AS umur_bulan
    FROM kehadiran h
    JOIN anak a ON a.id = h.id_anak
    WHERE h.id_kegiatan = ? AND h.status_hadir='hadir'
    GROUP BY a.id
    ORDER BY a.nama
");
$stmt->execute([$id_kegiatan]);
$anak = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| PEMERIKSAAN EXISTING
|--------------------------------------------------------------------------
*/
$pemeriksaan = [];
$stmt = $pdo->prepare("SELECT * FROM pemeriksaan WHERE id_kegiatan = ?");
$stmt->execute([$id_kegiatan]);
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $pemeriksaan[$row['id_anak']] = $row;
}

/*
|--------------------------------------------------------------------------
| SIMPAN
|--------------------------------------------------------------------------
*/
if (isset($_POST['simpan'])) {
    foreach ($_POST['bb'] as $id_anak => $bb) {
        $stmt = $pdo->prepare("
            INSERT INTO pemeriksaan (
                id_anak, id_kegiatan, umur_bulan, berat_badan,
                tinggi_badan, lingkar_kepala, status_gizi, catatan, diukur_oleh
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                umur_bulan = VALUES(umur_bulan),
                berat_badan = VALUES(berat_badan),
                tinggi_badan = VALUES(tinggi_badan),
                lingkar_kepala = VALUES(lingkar_kepala),
                status_gizi = VALUES(status_gizi),
                catatan = VALUES(catatan),
                diukur_oleh = VALUES(diukur_oleh)
        ");
        $stmt->execute([
            $id_anak,
            $id_kegiatan,
            $_POST['umur'][$id_anak] ?? 0,
            $_POST['bb'][$id_anak] ?? null,
            $_POST['tb'][$id_anak] ?? null,
            $_POST['lk'][$id_anak] ?? null,
            $_POST['gizi'][$id_anak] ?? '',
            $_POST['catatan'][$id_anak] ?? '',
            $_SESSION['user']['id']
        ]);
    }
    echo "<script>window.location='index.php?url=pemeriksaan&id_kegiatan=".$id_kegiatan."';</script>";
    exit;
}

$totalAnak = count($anak);
?>

<style>
.pemeriksaan-input-container { padding: 10px 0; }

/* Header */
.pemeriksaan-input-header {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 20px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.pemeriksaan-input-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.pemeriksaan-input-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.pemeriksaan-input-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

.pemeriksaan-input-header .header-right {
    text-align: right;
}

.pemeriksaan-input-header .header-right .total {
    font-size: 28px;
    font-weight: 700;
    color: #2c6b9e;
}

.pemeriksaan-input-header .header-right .label {
    font-size: 12px;
    color: #8a94a6;
}

/* Action Buttons */
.btn-action-input {
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    border: none;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-action-input.secondary {
    background: #f0f4f8;
    color: #4a5568;
}

.btn-action-input.secondary:hover {
    background: #e2e8f0;
    color: #1a2634;
    text-decoration: none;
}

.btn-action-input.info {
    background: #e8f0fe;
    color: #2c6b9e;
}

.btn-action-input.info:hover {
    background: #2c6b9e;
    color: #ffffff;
    text-decoration: none;
}

/* Alert */
.alert-warning-custom {
    border-radius: 10px;
    border: none;
    background: #fef3c7;
    color: #92400e;
    padding: 14px 18px;
}

/* Card Form */
.card-form-pemeriksaan {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-form-pemeriksaan .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    background: #f8f9fc;
}

.card-form-pemeriksaan .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-form-pemeriksaan .card-header-custom h6 i {
    color: #2c6b9e;
    margin-right: 8px;
}

/* Tabel Input */
.table-input-pemeriksaan {
    font-size: 13px;
    margin: 0;
}

.table-input-pemeriksaan thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 12px;
    border-bottom: 2px solid #edf2f7;
    white-space: nowrap;
}

.table-input-pemeriksaan tbody td {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-input-pemeriksaan tbody tr:hover {
    background: #fafbfc;
}

.table-input-pemeriksaan .form-control,
.table-input-pemeriksaan .custom-select {
    border-radius: 6px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 6px 10px;
    height: 36px;
    background: #fafbfc;
    transition: all 0.2s ease;
}

.table-input-pemeriksaan .form-control:focus,
.table-input-pemeriksaan .custom-select:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

.table-input-pemeriksaan textarea.form-control {
    height: auto;
    min-height: 50px;
}

/* Card Footer */
.card-footer-form {
    background: #fafbfc;
    border-top: 1px solid #edf2f7;
    padding: 14px 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-simpan-pemeriksaan {
    background: #28a745;
    color: #ffffff;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-simpan-pemeriksaan:hover {
    background: #1e7e34;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.25);
}

@media (max-width: 768px) {
    .pemeriksaan-input-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .pemeriksaan-input-header .header-right {
        text-align: left;
    }
    .card-footer-form {
        flex-direction: column;
    }
    .card-footer-form .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="pemeriksaan-input-container">

    <!-- HEADER -->
    <div class="pemeriksaan-input-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-stethoscope"></i>
                Input Pemeriksaan Anak
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Pertemuan <?= $kegiatan['pertemuan_ke'] ?> • 
                <?= date('d M Y', strtotime($kegiatan['tanggal'])) ?> • 
                <?= htmlspecialchars($kegiatan['lokasi']) ?>
            </div>
        </div>
        <div class="header-right">
            <div class="total"><?= $totalAnak ?></div>
            <div class="label"><i class="fas fa-child"></i> Anak Hadir</div>
        </div>
    </div>

    <!-- ACTION -->
    <div class="mb-3" style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="index.php?url=pemeriksaan&id_kegiatan=<?= $id_kegiatan ?>" class="btn-action-input secondary">
            <i class="fas fa-arrow-left"></i> Monitoring
        </a>
        <a href="index.php?url=kegiatan-detail&id=<?= $id_kegiatan ?>" class="btn-action-input info">
            <i class="fas fa-calendar-alt"></i> Detail Kegiatan
        </a>
    </div>

    <?php if(!$totalAnak): ?>
        <div class="alert-warning-custom">
            <i class="fas fa-exclamation-triangle"></i> 
            Belum ada anak yang tercatat hadir pada kegiatan ini.
        </div>
    <?php else: ?>

    <form method="POST">
        <div class="card-form-pemeriksaan">
            <div class="card-header-custom">
                <h6>
                    <i class="fas fa-edit"></i> Data Pemeriksaan
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-input-pemeriksaan">
                    <thead>
                        <tr>
                            <th width="200">Nama Anak</th>
                            <th width="80">Umur</th>
                            <th width="110">BB (Kg)</th>
                            <th width="110">TB (Cm)</th>
                            <th width="110">LK (Cm)</th>
                            <th width="160">Status Gizi</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($anak as $a): 
                            $p = $pemeriksaan[$a['id']] ?? [];
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($a['nama']) ?></strong>
                                <br>
                                <small class="text-muted">NIK: <?= htmlspecialchars($a['nik'] ?? '-') ?></small>
                            </td>
                            <td>
                                <?php if($a['umur_tahun'] > 0): ?>
                                    <strong><?= $a['umur_tahun'] ?> Th</strong>
                                    <?php if($a['umur_sisa_bulan'] > 0): ?>
                                        <br><small class="text-muted"><?= $a['umur_sisa_bulan'] ?> Bln</small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <strong><?= $a['umur_bulan'] ?> Bulan</strong>
                                <?php endif; ?>
                                <input type="hidden" name="umur[<?= $a['id'] ?>]" value="<?= $a['umur_bulan'] ?>">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="bb[<?= $a['id'] ?>]" 
                                       value="<?= $p['berat_badan'] ?? '' ?>" class="form-control" placeholder="0.00">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="tb[<?= $a['id'] ?>]" 
                                       value="<?= $p['tinggi_badan'] ?? '' ?>" class="form-control" placeholder="0.00">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="lk[<?= $a['id'] ?>]" 
                                       value="<?= $p['lingkar_kepala'] ?? '' ?>" class="form-control" placeholder="0.00">
                            </td>
                            <td>
                                <select name="gizi[<?= $a['id'] ?>]" class="custom-select">
                                    <option value="Baik" <?= (($p['status_gizi'] ?? '') == 'Baik') ? 'selected' : '' ?>>Baik</option>
                                    <option value="Kurang" <?= (($p['status_gizi'] ?? '') == 'Kurang') ? 'selected' : '' ?>>Kurang</option>
                                    <option value="Buruk" <?= (($p['status_gizi'] ?? '') == 'Buruk') ? 'selected' : '' ?>>Buruk</option>
                                </select>
                            </td>
                            <td>
                                <textarea rows="2" class="form-control" name="catatan[<?= $a['id'] ?>]"><?= htmlspecialchars($p['catatan'] ?? '') ?></textarea>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer-form">
                <a href="index.php?url=pemeriksaan&id_kegiatan=<?= $id_kegiatan ?>" class="btn-action-input secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" name="simpan" class="btn-simpan-pemeriksaan">
                    <i class="fas fa-save"></i> Simpan Pemeriksaan
                </button>
            </div>
        </div>
    </form>

    <?php endif; ?>

</div>