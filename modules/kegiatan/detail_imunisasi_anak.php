<?php
// detail_imunisasi_anak.php
$id_kegiatan = $_GET['id'] ?? 0;

// ANAK HADIR
$q = $pdo->prepare("
    SELECT a.* FROM kehadiran h
    JOIN anak a ON a.id = h.id_anak
    WHERE h.id_kegiatan=? AND h.status_hadir='hadir'
    ORDER BY a.nama
");
$q->execute([$id_kegiatan]);
$anakHadir = $q->fetchAll(PDO::FETCH_ASSOC);
$totalHadir = count($anakHadir);

// DATA IMUNISASI
$q = $pdo->prepare("
    SELECT 
        i.*, 
        a.nama,
        a.nik,
        mi.nama_imunisasi AS master_nama
    FROM imunisasi i
    JOIN anak a ON a.id = i.id_anak
    LEFT JOIN master_imunisasi mi ON mi.id = i.id_master_imunisasi
    WHERE i.id_kegiatan = ?
    ORDER BY a.nama ASC
");
$q->execute([$id_kegiatan]);
$dataImunisasi = $q->fetchAll(PDO::FETCH_ASSOC);
$totalImunisasi = count($dataImunisasi);
?>
<style>
.table-detail { font-size: 13px; margin: 0; }
.table-detail thead th {
    background: #f8f9fc; color: #4a5568; font-size: 11px;
    font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;
    padding: 10px 14px; border-bottom: 2px solid #edf2f7;
}
.table-detail tbody td { padding: 10px 14px; border-bottom: 1px solid #f0f2f5; vertical-align: middle; }
.btn-sm-kegiatan { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-sm-kegiatan.primary { background: #2c6b9e; color: #ffffff; }
.btn-sm-kegiatan.primary:hover { background: #1f507a; color: #ffffff; text-decoration: none; }
.alert-info-custom { border-radius: 10px; border: none; background: #e8f0fe; color: #1a2634; padding: 12px 16px; }
.alert-info-custom i { color: #2c6b9e; margin-right: 8px; }
.alert-warning-custom { border-radius: 10px; border: none; background: #fef3c7; color: #92400e; padding: 12px 16px; }
.badge-imunisasi-detail { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-imunisasi-detail.primary { background: #dbeafe; color: #1d4ed8; }
.badge-imunisasi-detail.success { background: #d1fae5; color: #047857; }
.badge-imunisasi-detail.warning { background: #fef3c7; color: #92400e; }
.badge-imunisasi-detail.danger { background: #fee2e2; color: #b91c1c; }
.badge-imunisasi-detail.info { background: #e0f7fa; color: #00838f; }
.badge-imunisasi-detail.default { background: #f3f4f6; color: #6b7280; }
.empty-state { text-align: center; padding: 30px 20px; }
.empty-state i { font-size: 36px; color: #d1d5db; display: block; margin-bottom: 8px; }
.empty-state p { color: #8a94a6; font-size: 13px; }
</style>

<h6 class="mb-3" style="color: #1a2634; font-weight: 600;">
    <i class="fas fa-syringe" style="color: #2c6b9e;"></i> Imunisasi Anak
</h6>

<?php if($totalHadir > 0): ?>
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div class="alert-info-custom" style="margin: 0;">
            <i class="fas fa-info-circle"></i> 
            <?= $totalHadir ?> anak hadir. 
            <?php if($totalImunisasi < $totalHadir): ?>
                <span style="color: #e8a317;"><?= $totalHadir - $totalImunisasi ?> anak belum diimunisasi</span>
            <?php else: ?>
                <span style="color: #28a745;">Semua anak sudah diimunisasi ✓</span>
            <?php endif; ?>
        </div>
        <a href="index.php?url=imunisasi-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-primary btn-sm-kegiatan">
            <i class="fas fa-plus-circle"></i> Input Imunisasi Anak
        </a>
    </div>
    
    <?php if($totalImunisasi > 0): ?>
    <div class="table-responsive">
        <table class="table table-detail">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Anak</th>
                    <th>Jenis Imunisasi</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($dataImunisasi as $i): 
                    $badgeClass = 'default';
                    $namaImunisasi = $i['master_nama'] ?? $i['jenis_imunisasi'] ?? 'Imunisasi';
                    if(strpos(strtolower($namaImunisasi), 'bcg') !== false) $badgeClass = 'success';
                    elseif(strpos(strtolower($namaImunisasi), 'polio') !== false) $badgeClass = 'info';
                    elseif(strpos(strtolower($namaImunisasi), 'campak') !== false) $badgeClass = 'danger';
                    elseif(strpos(strtolower($namaImunisasi), 'mr') !== false) $badgeClass = 'warning';
                    elseif(strpos(strtolower($namaImunisasi), 'dpt') !== false) $badgeClass = 'primary';
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($i['nama']) ?></strong></td>
                    <td><span class="badge-imunisasi-detail <?= $badgeClass ?>"><?= htmlspecialchars($namaImunisasi) ?></span></td>
                    <td><?= $i['tanggal'] ? date('d M Y', strtotime($i['tanggal'])) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data imunisasi anak</p></div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert-warning-custom">
        <i class="fas fa-exclamation-triangle"></i> 
        Belum ada anak yang hadir pada kegiatan ini. <strong>Input kehadiran anak terlebih dahulu.</strong>
    </div>
<?php endif; ?>