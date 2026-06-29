<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = (int) ($_GET['id_kegiatan'] ?? 0);
$id_ibu = (int) ($_GET['id_ibu'] ?? 0);

if (!$id_kegiatan) {
    echo "<script>window.location='index.php?url=pemeriksaan_ibu';</script>";
    exit;
}

// DATA KEGIATAN
$stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE id = ?");
$stmt->execute([$id_kegiatan]);
$kegiatan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kegiatan) {
    die("<div style='padding:30px'><h3>Kegiatan tidak ditemukan</h3><a href='index.php?url=pemeriksaan_ibu'>Kembali</a></div>");
}

// IBU HAMIL HADIR
$stmt = $pdo->prepare("
    SELECT
        ih.*,
        TIMESTAMPDIFF(WEEK, ih.hpht, CURDATE()) AS usia_kehamilan_minggu,
        CASE WHEN p.id IS NOT NULL THEN 1 ELSE 0 END AS sudah_diperiksa
    FROM kehadiran_ibu_hamil h
    JOIN ibu_hamil ih ON ih.id = h.ibu_hamil_id
    LEFT JOIN pemeriksaan_ibu_hamil p ON p.ibu_hamil_id = ih.id AND p.id_kegiatan = h.id_kegiatan
    WHERE h.id_kegiatan = ? AND h.status_hadir = 'hadir'
    GROUP BY ih.id
    ORDER BY ih.nama_ibu
");
$stmt->execute([$id_kegiatan]);
$ibuHamil = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Data ibu yang dipilih
$dataIbu = null;
if ($id_ibu > 0) {
    foreach ($ibuHamil as $ib) {
        if ($ib['id'] == $id_ibu) {
            $dataIbu = $ib;
            break;
        }
    }
    if (!$dataIbu) {
        echo "<script>
            alert('Ibu hamil tidak ditemukan atau tidak hadir!');
            window.location='index.php?url=pemeriksaan_ibu&id_kegiatan=".$id_kegiatan."';
        </script>";
        exit;
    }
}

// PEMERIKSAAN EXISTING
$pemeriksaan = [];
if ($id_ibu > 0) {
    $stmt = $pdo->prepare("SELECT * FROM pemeriksaan_ibu_hamil WHERE id_kegiatan = ? AND ibu_hamil_id = ?");
    $stmt->execute([$id_kegiatan, $id_ibu]);
    $pemeriksaan = $stmt->fetch(PDO::FETCH_ASSOC);
}

// SIMPAN
if (isset($_POST['simpan'])) {
    // Proses nilai kosong menjadi NULL untuk field decimal
    $berat_badan = !empty($_POST['berat_badan']) ? $_POST['berat_badan'] : null;
    $lingkar_lengan = !empty($_POST['lingkar_lengan']) ? $_POST['lingkar_lengan'] : null;
    $tinggi_fundus = !empty($_POST['tinggi_fundus']) ? $_POST['tinggi_fundus'] : null;
    
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
        $_POST['id_ibu'],
        $id_kegiatan,
        $_POST['tanggal_periksa'] ?? date('Y-m-d'),
        $_POST['usia_kehamilan'] ?? 0,
        $berat_badan,
        $_POST['tekanan_darah'] ?? '',
        $lingkar_lengan,
        $tinggi_fundus,
        $_POST['keluhan'] ?? '',
        $_POST['tindakan'] ?? '',
        $_POST['keterangan'] ?? ''
    ]);
    
    echo "<script>
        alert('Data pemeriksaan berhasil disimpan!');
        window.location='index.php?url=pemeriksaan_ibu-input&id_kegiatan=".$id_kegiatan."';
    </script>";
    exit;
}

$totalIbu = count($ibuHamil);
?>

<style>
.pemeriksaan-ibu-input-container { padding: 10px 0; }

/* Header */
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
    color: #2c6b9e;
    margin-right: 10px;
}

.pemeriksaan-ibu-input-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
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

/* Card Daftar Ibu */
.card-daftar-ibu {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 20px;
}

.card-daftar-ibu .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    background: #f8f9fc;
}

.card-daftar-ibu .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-daftar-ibu .card-header-custom h6 i {
    color: #2c6b9e;
    margin-right: 8px;
}

/* Search Box */
.search-box {
    position: relative;
    max-width: 300px;
}

.search-box .search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 14px;
}

.search-box .form-control {
    padding: 8px 16px 8px 38px;
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    background: #fafbfc;
    height: 38px;
    transition: all 0.2s ease;
}

.search-box .form-control:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

/* Tabel */
.table-ibu-hadir {
    font-size: 13px;
    margin: 0;
}

.table-ibu-hadir thead th {
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

.table-ibu-hadir tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-ibu-hadir tbody tr:hover {
    background: #fafbfc;
}

.table-ibu-hadir tbody tr:last-child td {
    border-bottom: none;
}

/* Badge */
.badge-status {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-status.sudah {
    background: #d1fae5;
    color: #047857;
}

.badge-status.belum {
    background: #fef3c7;
    color: #92400e;
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

/* Tombol Input */
.btn-input-pemeriksaan-ibu {
    padding: 6px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    border: none;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-input-pemeriksaan-ibu.primary {
    background: #2c6b9e;
    color: #ffffff;
}

.btn-input-pemeriksaan-ibu.primary:hover {
    background: #1f507a;
    color: #ffffff;
    text-decoration: none;
}

.btn-input-pemeriksaan-ibu.success {
    background: #28a745;
    color: #ffffff;
}

.btn-input-pemeriksaan-ibu.success:hover {
    background: #1e7e34;
    color: #ffffff;
    text-decoration: none;
}

.btn-input-pemeriksaan-ibu.active {
    background: #e8f0fe;
    color: #2c6b9e;
    cursor: default;
}

/* Card Form */
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
    background: #2c6b9e;
    color: #ffffff;
}

.card-form-pemeriksaan-ibu .card-header-custom h6 {
    font-weight: 600;
    margin: 0;
    font-size: 14px;
}

.card-form-pemeriksaan-ibu .card-header-custom h6 i {
    margin-right: 8px;
}

.card-form-pemeriksaan-ibu .card-body-custom {
    padding: 24px 28px;
}

.form-group label {
    font-weight: 600;
    color: #4a5568;
    font-size: 12px;
    margin-bottom: 4px;
}

.form-control, .custom-select {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 10px 14px;
    transition: all 0.2s ease;
    background: #fafbfc;
    height: 44px;
}

.form-control:focus, .custom-select:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

textarea.form-control {
    height: auto;
    min-height: 80px;
}

.btn {
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 24px;
    transition: all 0.2s ease;
}

.btn-success {
    background: #28a745;
    border: none;
    color: #ffffff;
}

.btn-success:hover {
    background: #1e7e34;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.25);
    color: #ffffff;
}

.btn-secondary {
    background: #f0f4f8;
    border: none;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #e2e8f0;
    color: #1a2634;
}

/* Info Ibu */
.info-ibu {
    background: #e8f0fe;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.info-ibu .nama {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
}

.info-ibu .detail {
    font-size: 13px;
    color: #4a5568;
}

.info-ibu .detail span {
    font-weight: 600;
    color: #2c6b9e;
}

.empty-state {
    text-align: center;
    padding: 30px 20px;
}

.empty-state i {
    font-size: 36px;
    color: #d1d5db;
    display: block;
    margin-bottom: 8px;
}

.empty-state p {
    color: #8a94a6;
    font-size: 13px;
}

@media (max-width: 768px) {
    .pemeriksaan-ibu-input-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .search-box {
        max-width: 100%;
    }
    .info-ibu {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
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
        <div>
            <a href="index.php?url=pemeriksaan_ibu&id_kegiatan=<?= $id_kegiatan ?>" class="btn-action-input secondary">
                <i class="fas fa-arrow-left"></i> Monitoring
            </a>
        </div>
    </div>

    <?php if (!$totalIbu): ?>
        <div class="alert-warning-custom">
            <i class="fas fa-exclamation-triangle"></i> 
            Belum ada ibu hamil yang tercatat hadir pada kegiatan ini.
        </div>
    <?php else: ?>

    <!-- DAFTAR IBU HAMIL HADIR -->
    <div class="card-daftar-ibu">
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px; width: 100%;">
                <h6>
                    <i class="fas fa-list"></i> Daftar Ibu Hamil Hadir
                    <span style="background: #e8f0fe; color: #2c6b9e; padding: 2px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-left: 8px;">
                        <?= $totalIbu ?>
                    </span>
                </h6>
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="searchIbu" placeholder="Cari nama ibu...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-ibu-hadir">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Nama Ibu</th>
                            <th>NIK</th>
                            <th>Usia</th>
                            <th>Trimester</th>
                            <th>Status</th>
                            <th width="140" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyIbu">
                        <?php $no = 1; foreach ($ibuHamil as $ib): 
                            $usia = $ib['usia_kehamilan_minggu'] ?? 0;
                            $trimester = 0;
                            if ($usia <= 13) $trimester = 1;
                            elseif ($usia <= 27) $trimester = 2;
                            elseif ($usia > 27) $trimester = 3;
                            $class = 't0';
                            if ($trimester == 1) $class = 't1';
                            elseif ($trimester == 2) $class = 't2';
                            elseif ($trimester == 3) $class = 't3';
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($ib['nama_ibu']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($ib['nik'] ?? '-') ?></td>
                            <td><?= $usia > 0 ? $usia . ' Minggu' : '-' ?></td>
                            <td>
                                <span class="badge-trimester <?= $class ?>">
                                    <?= $trimester > 0 ? 'T' . $trimester : '-' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-status <?= $ib['sudah_diperiksa'] ? 'sudah' : 'belum' ?>">
                                    <?= $ib['sudah_diperiksa'] ? 'Sudah Diperiksa' : 'Belum Diperiksa' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($id_ibu == $ib['id']): ?>
                                    <span class="btn-input-pemeriksaan-ibu active">
                                        <i class="fas fa-check-circle"></i> Sedang Diperiksa
                                    </span>
                                <?php else: ?>
                                    <a href="index.php?url=pemeriksaan_ibu-input&id_kegiatan=<?= $id_kegiatan ?>&id_ibu=<?= $ib['id'] ?>" 
                                       class="btn-input-pemeriksaan-ibu <?= $ib['sudah_diperiksa'] ? 'success' : 'primary' ?>">
                                        <i class="fas <?= $ib['sudah_diperiksa'] ? 'fa-edit' : 'fa-plus-circle' ?>"></i>
                                        <?= $ib['sudah_diperiksa'] ? 'Edit' : 'Input' ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($id_ibu > 0 && $dataIbu): ?>
    <!-- FORM PEMERIKSAAN -->
    <div class="card-form-pemeriksaan-ibu">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-edit"></i> Form Pemeriksaan Ibu Hamil
            </h6>
        </div>
        <div class="card-body-custom">
            
            <!-- Info Ibu -->
            <div class="info-ibu">
                <div>
                    <div class="nama">
                        <i class="fas fa-person-pregnant" style="color: #2c6b9e;"></i>
                        <?= htmlspecialchars($dataIbu['nama_ibu']) ?>
                    </div>
                    <div class="detail">
                        NIK: <span><?= htmlspecialchars($dataIbu['nik'] ?? '-') ?></span> &bull;
                        Usia Kehamilan: <span><?= $dataIbu['usia_kehamilan_minggu'] ?? 0 ?> minggu</span>
                        <?php if ($pemeriksaan): ?>
                            &bull; <span style="color: #28a745;"><i class="fas fa-check-circle"></i> Sudah diperiksa</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <?php 
                    $usia = $dataIbu['usia_kehamilan_minggu'] ?? 0;
                    $trimester = 0;
                    if ($usia <= 13) $trimester = 1;
                    elseif ($usia <= 27) $trimester = 2;
                    elseif ($usia > 27) $trimester = 3;
                    $class = 't0';
                    if ($trimester == 1) $class = 't1';
                    elseif ($trimester == 2) $class = 't2';
                    elseif ($trimester == 3) $class = 't3';
                    ?>
                    <span style="background: #e8f0fe; color: #2c6b9e; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        Trimester <?= $trimester > 0 ? $trimester : '-' ?>
                    </span>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="id_ibu" value="<?= $dataIbu['id'] ?>">
                <input type="hidden" name="usia_kehamilan" value="<?= $dataIbu['usia_kehamilan_minggu'] ?? 0 ?>">
                <input type="hidden" name="tanggal_periksa" value="<?= date('Y-m-d') ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Berat Badan (Kg)</label>
                            <input type="number" step="0.01" name="berat_badan" 
                                   value="<?= $pemeriksaan['berat_badan'] ?? '' ?>" 
                                   class="form-control" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tekanan Darah</label>
                            <input type="text" name="tekanan_darah" 
                                   value="<?= htmlspecialchars($pemeriksaan['tekanan_darah'] ?? '') ?>" 
                                   class="form-control" placeholder="120/80">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Lingkar Lengan (LILA) (cm)</label>
                            <input type="number" step="0.01" name="lingkar_lengan" 
                                   value="<?= $pemeriksaan['lingkar_lengan'] ?? '' ?>" 
                                   class="form-control" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tinggi Fundus (TFU) (cm)</label>
                            <input type="number" step="0.01" name="tinggi_fundus" 
                                   value="<?= $pemeriksaan['tinggi_fundus'] ?? '' ?>" 
                                   class="form-control" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keluhan</label>
                    <textarea name="keluhan" class="form-control" rows="3" placeholder="Keluhan ibu hamil..."><?= htmlspecialchars($pemeriksaan['keluhan'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Tindakan</label>
                    <textarea name="tindakan" class="form-control" rows="2" placeholder="Tindakan yang diberikan..."><?= htmlspecialchars($pemeriksaan['tindakan'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan..."><?= htmlspecialchars($pemeriksaan['keterangan'] ?? '') ?></textarea>
                </div>

                <hr style="margin: 20px 0;">

                <div class="d-flex" style="gap: 10px; flex-wrap: wrap;">
                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Pemeriksaan
                    </button>
                    <a href="index.php?url=pemeriksaan_ibu-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>

</div>

<script>
// Search / Pencarian
document.getElementById('searchIbu')?.addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#tableBodyIbu tr');
    rows.forEach(function(row) {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>