<?php
require_once __DIR__ . '/../../config/database.php';

$id_kegiatan = (int) ($_GET['id_kegiatan'] ?? 0);
$id_anak = (int) ($_GET['id_anak'] ?? 0);

if (!$id_kegiatan) {
    echo "<script>window.location='index.php?url=pemeriksaan';</script>";
    exit;
}

// DATA KEGIATAN
$stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE id = ?");
$stmt->execute([$id_kegiatan]);
$kegiatan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kegiatan) {
    die("<div style='padding:30px'><h3>Kegiatan tidak ditemukan</h3><a href='index.php?url=pemeriksaan'>Kembali</a></div>");
}

// ANAK HADIR
$stmt = $pdo->prepare("
    SELECT
        a.*,
        TIMESTAMPDIFF(YEAR, a.tanggal_lahir, CURDATE()) AS umur_tahun,
        TIMESTAMPDIFF(MONTH, a.tanggal_lahir, CURDATE()) % 12 AS umur_sisa_bulan,
        TIMESTAMPDIFF(MONTH, a.tanggal_lahir, CURDATE()) AS umur_bulan,
        CASE WHEN p.id IS NOT NULL THEN 1 ELSE 0 END AS sudah_diperiksa
    FROM kehadiran h
    JOIN anak a ON a.id = h.id_anak
    LEFT JOIN pemeriksaan p ON p.id_anak = a.id AND p.id_kegiatan = h.id_kegiatan
    WHERE h.id_kegiatan = ? AND h.status_hadir = 'hadir'
    GROUP BY a.id
    ORDER BY a.nama
");
$stmt->execute([$id_kegiatan]);
$anakHadir = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Data anak yang dipilih
$dataAnak = null;
if ($id_anak > 0) {
    foreach ($anakHadir as $a) {
        if ($a['id'] == $id_anak) {
            $dataAnak = $a;
            break;
        }
    }
    if (!$dataAnak) {
        echo "<script>
            alert('Anak tidak ditemukan atau tidak hadir!');
            window.location='index.php?url=pemeriksaan&id_kegiatan=".$id_kegiatan."';
        </script>";
        exit;
    }
}

// PEMERIKSAAN EXISTING
$pemeriksaan = [];
if ($id_anak > 0) {
    $stmt = $pdo->prepare("SELECT * FROM pemeriksaan WHERE id_kegiatan = ? AND id_anak = ?");
    $stmt->execute([$id_kegiatan, $id_anak]);
    $pemeriksaan = $stmt->fetch(PDO::FETCH_ASSOC);
}

// SIMPAN
if (isset($_POST['simpan'])) {
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
        $_POST['id_anak'],
        $id_kegiatan,
        $_POST['umur_bulan'] ?? 0,
        $_POST['berat_badan'] ?? null,
        $_POST['tinggi_badan'] ?? null,
        $_POST['lingkar_kepala'] ?? null,
        $_POST['status_gizi'] ?? '',
        $_POST['catatan'] ?? '',
        $_SESSION['user']['id']
    ]);
    
    echo "<script>
        alert('Data pemeriksaan berhasil disimpan!');
        window.location='index.php?url=pemeriksaan-input&id_kegiatan=".$id_kegiatan."';
    </script>";
    exit;
}

$totalAnak = count($anakHadir);
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

.alert-warning-custom {
    border-radius: 10px;
    border: none;
    background: #fef3c7;
    color: #92400e;
    padding: 14px 18px;
}

/* Card Daftar Anak */
.card-daftar-anak {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 20px;
}

.card-daftar-anak .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    background: #f8f9fc;
}

.card-daftar-anak .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-daftar-anak .card-header-custom h6 i {
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
.table-anak-hadir {
    font-size: 13px;
    margin: 0;
}

.table-anak-hadir thead th {
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

.table-anak-hadir tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-anak-hadir tbody tr:hover {
    background: #fafbfc;
}

.table-anak-hadir tbody tr:last-child td {
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

/* Tombol Input */
.btn-input-pemeriksaan {
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

.btn-input-pemeriksaan.primary {
    background: #2c6b9e;
    color: #ffffff;
}

.btn-input-pemeriksaan.primary:hover {
    background: #1f507a;
    color: #ffffff;
    text-decoration: none;
}

.btn-input-pemeriksaan.success {
    background: #28a745;
    color: #ffffff;
}

.btn-input-pemeriksaan.success:hover {
    background: #1e7e34;
    color: #ffffff;
    text-decoration: none;
}

.btn-input-pemeriksaan.active {
    background: #e8f0fe;
    color: #2c6b9e;
    cursor: default;
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
    background: #2c6b9e;
    color: #ffffff;
}

.card-form-pemeriksaan .card-header-custom h6 {
    font-weight: 600;
    margin: 0;
    font-size: 14px;
}

.card-form-pemeriksaan .card-header-custom h6 i {
    margin-right: 8px;
}

.card-form-pemeriksaan .card-body-custom {
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

/* Info Anak */
.info-anak {
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

.info-anak .nama {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
}

.info-anak .detail {
    font-size: 13px;
    color: #4a5568;
}

.info-anak .detail span {
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
    .pemeriksaan-input-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .search-box {
        max-width: 100%;
    }
    .info-anak {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
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
        <div>
            <a href="index.php?url=pemeriksaan&id_kegiatan=<?= $id_kegiatan ?>" class="btn-action-input secondary">
                <i class="fas fa-arrow-left"></i> Monitoring
            </a>
        </div>
    </div>

    <?php if (!$totalAnak): ?>
        <div class="alert-warning-custom">
            <i class="fas fa-exclamation-triangle"></i> 
            Belum ada anak yang tercatat hadir pada kegiatan ini.
        </div>
    <?php else: ?>

    <!-- DAFTAR ANAK HADIR -->
    <div class="card-daftar-anak">
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px; width: 100%;">
                <h6>
                    <i class="fas fa-list"></i> Daftar Anak Hadir
                    <span style="background: #e8f0fe; color: #2c6b9e; padding: 2px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-left: 8px;">
                        <?= $totalAnak ?>
                    </span>
                </h6>
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="searchAnak" placeholder="Cari nama anak...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-anak-hadir">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Nama Anak</th>
                            <th>NIK</th>
                            <th>Umur</th>
                            <th>JK</th>
                            <th>Status</th>
                            <th width="140" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyAnak">
                        <?php $no = 1; foreach ($anakHadir as $a): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($a['nama']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($a['nik'] ?? '-') ?></td>
                            <td><?= $a['umur_bulan'] ?> bulan</td>
                            <td><?= $a['jenis_kelamin'] == 'L' ? 'L' : 'P' ?></td>
                            <td>
                                <span class="badge-status <?= $a['sudah_diperiksa'] ? 'sudah' : 'belum' ?>">
                                    <?= $a['sudah_diperiksa'] ? 'Sudah Diperiksa' : 'Belum Diperiksa' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($id_anak == $a['id']): ?>
                                    <span class="btn-input-pemeriksaan active">
                                        <i class="fas fa-check-circle"></i> Sedang Diperiksa
                                    </span>
                                <?php else: ?>
                                    <a href="index.php?url=pemeriksaan-input&id_kegiatan=<?= $id_kegiatan ?>&id_anak=<?= $a['id'] ?>" 
                                       class="btn-input-pemeriksaan <?= $a['sudah_diperiksa'] ? 'success' : 'primary' ?>">
                                        <i class="fas <?= $a['sudah_diperiksa'] ? 'fa-edit' : 'fa-plus-circle' ?>"></i>
                                        <?= $a['sudah_diperiksa'] ? 'Edit' : 'Input' ?>
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

    <?php if ($id_anak > 0 && $dataAnak): ?>
    <!-- FORM PEMERIKSAAN -->
    <div class="card-form-pemeriksaan">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-edit"></i> Form Pemeriksaan
            </h6>
        </div>
        <div class="card-body-custom">
            
            <!-- Info Anak -->
            <div class="info-anak">
                <div>
                    <div class="nama">
                        <i class="fas fa-child" style="color: #2c6b9e;"></i>
                        <?= htmlspecialchars($dataAnak['nama']) ?>
                    </div>
                    <div class="detail">
                        NIK: <span><?= htmlspecialchars($dataAnak['nik'] ?? '-') ?></span> &bull;
                        Lahir: <span><?= date('d M Y', strtotime($dataAnak['tanggal_lahir'])) ?></span> &bull;
                        Umur: <span><?= $dataAnak['umur_bulan'] ?> bulan</span>
                        <?php if ($pemeriksaan): ?>
                            &bull; <span style="color: #28a745;"><i class="fas fa-check-circle"></i> Sudah diperiksa</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <span style="background: #e8f0fe; color: #2c6b9e; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        <?= $dataAnak['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                    </span>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="id_anak" value="<?= $dataAnak['id'] ?>">
                <input type="hidden" name="umur_bulan" value="<?= $dataAnak['umur_bulan'] ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Berat Badan (Kg) <span style="color: #dc2626;">*</span></label>
                            <input type="number" step="0.01" name="berat_badan" 
                                   value="<?= $pemeriksaan['berat_badan'] ?? '' ?>" 
                                   class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tinggi Badan (Cm) <span style="color: #dc2626;">*</span></label>
                            <input type="number" step="0.01" name="tinggi_badan" 
                                   value="<?= $pemeriksaan['tinggi_badan'] ?? '' ?>" 
                                   class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Lingkar Kepala (Cm)</label>
                            <input type="number" step="0.01" name="lingkar_kepala" 
                                   value="<?= $pemeriksaan['lingkar_kepala'] ?? '' ?>" 
                                   class="form-control" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Gizi <span style="color: #dc2626;">*</span></label>
                            <select name="status_gizi" class="custom-select" required>
                                <option value="Baik" <?= (($pemeriksaan['status_gizi'] ?? '') == 'Baik') ? 'selected' : '' ?>>Baik</option>
                                <option value="Normal" <?= (($pemeriksaan['status_gizi'] ?? '') == 'Normal') ? 'selected' : '' ?>>Normal</option>
                                <option value="Kurang" <?= (($pemeriksaan['status_gizi'] ?? '') == 'Kurang') ? 'selected' : '' ?>>Kurang</option>
                                <option value="Buruk" <?= (($pemeriksaan['status_gizi'] ?? '') == 'Buruk') ? 'selected' : '' ?>>Buruk</option>
                                <option value="Lebih" <?= (($pemeriksaan['status_gizi'] ?? '') == 'Lebih') ? 'selected' : '' ?>>Lebih</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."><?= htmlspecialchars($pemeriksaan['catatan'] ?? '') ?></textarea>
                </div>

                <hr style="margin: 20px 0;">

                <div class="d-flex" style="gap: 10px; flex-wrap: wrap;">
                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Pemeriksaan
                    </button>
                    <a href="index.php?url=pemeriksaan-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-secondary">
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
document.getElementById('searchAnak')?.addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#tableBodyAnak tr');
    rows.forEach(function(row) {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>