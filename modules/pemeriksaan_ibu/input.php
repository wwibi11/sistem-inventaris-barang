<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = (int) ($_GET['id_kegiatan'] ?? 0);

if (!$id_kegiatan) {
    echo "<script>window.location='index.php?url=pemeriksaan-ibu-hamil';</script>";
    exit;
}

// DATA KEGIATAN
$stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE id = ?");
$stmt->execute([$id_kegiatan]);
$kegiatan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kegiatan) {
    die("<div style='padding:30px'><h3>Kegiatan tidak ditemukan</h3><a href='index.php?url=pemeriksaan-ibu-hamil'>Kembali</a></div>");
}

// IBU HAMIL HADIR
$stmt = $pdo->prepare("
    SELECT
        ih.*,
        TIMESTAMPDIFF(WEEK, ih.hpht, CURDATE()) AS usia_kehamilan_minggu,
        TIMESTAMPDIFF(MONTH, ih.hpht, CURDATE()) AS usia_kehamilan_bulan
    FROM kehadiran_ibu_hamil h
    JOIN ibu_hamil ih ON ih.id = h.ibu_hamil_id
    WHERE h.id_kegiatan = ? AND h.status_hadir = 'hadir'
    GROUP BY ih.id
    ORDER BY ih.nama_ibu
");
$stmt->execute([$id_kegiatan]);
$ibuHamil = $stmt->fetchAll(PDO::FETCH_ASSOC);

// PEMERIKSAAN EXISTING
$pemeriksaan = [];
$stmt = $pdo->prepare("SELECT * FROM pemeriksaan_ibu_hamil WHERE id_kegiatan = ?");
$stmt->execute([$id_kegiatan]);
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $pemeriksaan[$row['ibu_hamil_id']] = $row;
}

// SIMPAN
if (isset($_POST['simpan'])) {
    foreach ($_POST['ibu_hamil_id'] as $id_ibu) {
        $stmt = $pdo->prepare("
            INSERT INTO pemeriksaan_ibu_hamil (
                ibu_hamil_id, id_kegiatan, tanggal_periksa, usia_kehamilan,
                berat_badan, tekanan_darah, lingkar_lengan, tinggi_fundus,
                keluhan, tindakan, keterangan
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                tanggal_periksa = VALUES(tanggal_periksa),
                usia_kehamilan = VALUES(usia_kehamilan),
                berat_badan = VALUES(berat_badan),
                tekanan_darah = VALUES(tekanan_darah),
                lingkar_lengan = VALUES(lingkar_lengan),
                tinggi_fundus = VALUES(tinggi_fundus),
                keluhan = VALUES(keluhan),
                tindakan = VALUES(tindakan),
                keterangan = VALUES(keterangan)
        ");
        $stmt->execute([
            $id_ibu,
            $id_kegiatan,
            $_POST['tanggal_periksa'][$id_ibu] ?? date('Y-m-d'),
            $_POST['usia_kehamilan'][$id_ibu] ?? 0,
            $_POST['berat_badan'][$id_ibu] ?? null,
            $_POST['tekanan_darah'][$id_ibu] ?? '',
            $_POST['lingkar_lengan'][$id_ibu] ?? null,
            $_POST['tinggi_fundus'][$id_ibu] ?? null,
            $_POST['keluhan'][$id_ibu] ?? '',
            $_POST['tindakan'][$id_ibu] ?? '',
            $_POST['keterangan'][$id_ibu] ?? ''
        ]);
    }
    echo "<script>window.location='index.php?url=pemeriksaan-ibu-hamil&id_kegiatan=".$id_kegiatan."';</script>";
    exit;
}

$totalIbu = count($ibuHamil);
?>

<style>
.pemeriksaan-ibu-input-container { padding: 10px 0; }
.pemeriksaan-ibu-input-header {
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
.pemeriksaan-ibu-input-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}
.pemeriksaan-ibu-input-header .header-left h4 i {
    color: #e83e8c;
    margin-right: 10px;
}
.pemeriksaan-ibu-input-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}
.pemeriksaan-ibu-input-header .header-right .total {
    font-size: 28px;
    font-weight: 700;
    color: #e83e8c;
}
.pemeriksaan-ibu-input-header .header-right .label {
    font-size: 12px;
    color: #8a94a6;
}
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
.alert-warning-custom {
    border-radius: 10px;
    border: none;
    background: #fef3c7;
    color: #92400e;
    padding: 14px 18px;
}
.card-form-pemeriksaan-ibu {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}
.card-form-pemeriksaan-ibu .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    background: #f8f9fc;
}
.card-form-pemeriksaan-ibu .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}
.card-form-pemeriksaan-ibu .card-header-custom h6 i {
    color: #e83e8c;
    margin-right: 8px;
}
.table-input-pemeriksaan-ibu {
    font-size: 13px;
    margin: 0;
}
.table-input-pemeriksaan-ibu thead th {
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
.table-input-pemeriksaan-ibu tbody td {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}
.table-input-pemeriksaan-ibu tbody tr:hover {
    background: #fafbfc;
}
.table-input-pemeriksaan-ibu .form-control,
.table-input-pemeriksaan-ibu .custom-select {
    border-radius: 6px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 6px 10px;
    height: 36px;
    background: #fafbfc;
    transition: all 0.2s ease;
}
.table-input-pemeriksaan-ibu .form-control:focus,
.table-input-pemeriksaan-ibu .custom-select:focus {
    border-color: #e83e8c;
    box-shadow: 0 0 0 3px rgba(232, 62, 140, 0.1);
    background: #ffffff;
}
.table-input-pemeriksaan-ibu textarea.form-control {
    height: auto;
    min-height: 50px;
}
.card-footer-form {
    background: #fafbfc;
    border-top: 1px solid #edf2f7;
    padding: 14px 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}
.btn-simpan-pemeriksaan-ibu {
    background: #e83e8c;
    color: #ffffff;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-simpan-pemeriksaan-ibu:hover {
    background: #c2186b;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(232, 62, 140, 0.25);
}
.badge-trimester {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-trimester.t1 { background: #dbeafe; color: #1d4ed8; }
.badge-trimester.t2 { background: #fef3c7; color: #92400e; }
.badge-trimester.t3 { background: #fce4ec; color: #c62828; }
.badge-trimester.t0 { background: #f3f4f6; color: #6b7280; }

@media (max-width: 768px) {
    .pemeriksaan-ibu-input-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .pemeriksaan-ibu-input-header .header-right {
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

<div class="pemeriksaan-ibu-input-container">

    <!-- HEADER -->
    <div class="pemeriksaan-ibu-input-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-stethoscope"></i>
                Input Pemeriksaan Ibu Hamil
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Pertemuan <?= $kegiatan['pertemuan_ke'] ?> • 
                <?= date('d M Y', strtotime($kegiatan['tanggal'])) ?> • 
                <?= htmlspecialchars($kegiatan['lokasi']) ?>
            </div>
        </div>
        <div class="header-right">
            <div class="total"><?= $totalIbu ?></div>
            <div class="label"><i class="fas fa-person-pregnant"></i> Ibu Hamil Hadir</div>
        </div>
    </div>

    <!-- ACTION -->
    <div class="mb-3" style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="index.php?url=pemeriksaan-ibu-hamil&id_kegiatan=<?= $id_kegiatan ?>" class="btn-action-input secondary">
            <i class="fas fa-arrow-left"></i> Monitoring
        </a>
        <a href="index.php?url=kegiatan-detail&id=<?= $id_kegiatan ?>" class="btn-action-input info">
            <i class="fas fa-calendar-alt"></i> Detail Kegiatan
        </a>
    </div>

    <?php if (!$totalIbu): ?>
        <div class="alert-warning-custom">
            <i class="fas fa-exclamation-triangle"></i> 
            Belum ada ibu hamil yang tercatat hadir pada kegiatan ini.
        </div>
    <?php else: ?>

    <form method="POST">
        <div class="card-form-pemeriksaan-ibu">
            <div class="card-header-custom">
                <h6>
                    <i class="fas fa-edit"></i> Data Pemeriksaan Ibu Hamil
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-input-pemeriksaan-ibu">
                    <thead>
                        <tr>
                            <th width="180">Nama Ibu</th>
                            <th width="80">Usia</th>
                            <th width="70">Trimester</th>
                            <th width="100">BB (Kg)</th>
                            <th width="100">Tekanan Darah</th>
                            <th width="90">LILA (cm)</th>
                            <th width="90">TFU (cm)</th>
                            <th>Keluhan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ibuHamil as $ih):
                            $p = $pemeriksaan[$ih['id']] ?? [];
                            $trimester = 0;
                            $usia = $ih['usia_kehamilan_minggu'] ?? 0;
                            if ($usia <= 13) $trimester = 1;
                            elseif ($usia <= 27) $trimester = 2;
                            elseif ($usia > 27) $trimester = 3;
                            $class = 't0';
                            if ($trimester == 1) $class = 't1';
                            elseif ($trimester == 2) $class = 't2';
                            elseif ($trimester == 3) $class = 't3';
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($ih['nama_ibu']) ?></strong>
                                <br>
                                <small class="text-muted">NIK: <?= htmlspecialchars($ih['nik'] ?? '-') ?></small>
                                <input type="hidden" name="ibu_hamil_id[]" value="<?= $ih['id'] ?>">
                                <input type="hidden" name="tanggal_periksa[<?= $ih['id'] ?>]" value="<?= date('Y-m-d') ?>">
                                <input type="hidden" name="usia_kehamilan[<?= $ih['id'] ?>]" value="<?= $usia ?>">
                            </td>
                            <td><?= $usia > 0 ? $usia . ' Minggu' : '-' ?></td>
                            <td>
                                <span class="badge-trimester <?= $class ?>">
                                    <?= $trimester > 0 ? 'T' . $trimester : '-' ?>
                                </span>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="berat_badan[<?= $ih['id'] ?>]" 
                                       value="<?= $p['berat_badan'] ?? '' ?>" class="form-control" placeholder="0.00">
                            </td>
                            <td>
                                <input type="text" name="tekanan_darah[<?= $ih['id'] ?>]" 
                                       value="<?= htmlspecialchars($p['tekanan_darah'] ?? '') ?>" class="form-control" placeholder="120/80">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="lingkar_lengan[<?= $ih['id'] ?>]" 
                                       value="<?= $p['lingkar_lengan'] ?? '' ?>" class="form-control" placeholder="0.00">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="tinggi_fundus[<?= $ih['id'] ?>]" 
                                       value="<?= $p['tinggi_fundus'] ?? '' ?>" class="form-control" placeholder="0.00">
                            </td>
                            <td>
                                <textarea rows="2" class="form-control" name="keluhan[<?= $ih['id'] ?>]"><?= htmlspecialchars($p['keluhan'] ?? '') ?></textarea>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer-form">
                <a href="index.php?url=pemeriksaan-ibu-hamil&id_kegiatan=<?= $id_kegiatan ?>" class="btn-action-input secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" name="simpan" class="btn-simpan-pemeriksaan-ibu">
                    <i class="fas fa-save"></i> Simpan Pemeriksaan
                </button>
            </div>
        </div>
    </form>

    <?php endif; ?>

</div>