<?php
// detail_pemeriksaan_ibu.php
$id_kegiatan = $_GET['id'] ?? 0;

// IBU HAMIL HADIR
$q = $pdo->prepare("
    SELECT ih.* FROM kehadiran_ibu_hamil h
    JOIN ibu_hamil ih ON ih.id = h.ibu_hamil_id
    WHERE h.id_kegiatan=? AND h.status_hadir='hadir'
    ORDER BY ih.nama_ibu
");
$q->execute([$id_kegiatan]);
$ibuHadir = $q->fetchAll(PDO::FETCH_ASSOC);
$totalHadir = count($ibuHadir);

// DATA PEMERIKSAAN IBU
$q = $pdo->prepare("
    SELECT 
        pih.*, 
        ih.nama_ibu,
        ih.nik,
        ih.usia_kehamilan
    FROM pemeriksaan_ibu_hamil pih
    JOIN ibu_hamil ih ON ih.id = pih.ibu_hamil_id
    WHERE pih.id_kegiatan = ?
    ORDER BY ih.nama_ibu ASC
");
$q->execute([$id_kegiatan]);
$dataPemeriksaan = $q->fetchAll(PDO::FETCH_ASSOC);
$totalPemeriksaan = count($dataPemeriksaan);
?>
<style>
.table-detail { font-size: 13px; margin: 0; }
.table-detail thead th {
    background: #f8f9fc; color: #4a5568; font-size: 11px;
    font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;
    padding: 10px 14px; border-bottom: 2px solid #edf2f7;
}
.table-detail tbody td { padding: 10px 14px; border-bottom: 1px solid #f0f2f5; vertical-align: middle; }
.table-detail tbody tr:last-child td { border-bottom: none; }
.btn-sm-kegiatan { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-sm-kegiatan.success { background: #28a745; color: #ffffff; }
.btn-sm-kegiatan.success:hover { background: #1e7e34; color: #ffffff; text-decoration: none; }
.alert-info-custom { border-radius: 10px; border: none; background: #e8f0fe; color: #1a2634; padding: 12px 16px; }
.alert-info-custom i { color: #2c6b9e; margin-right: 8px; }
.alert-warning-custom { border-radius: 10px; border: none; background: #fef3c7; color: #92400e; padding: 12px 16px; }
.empty-state { text-align: center; padding: 30px 20px; }
.empty-state i { font-size: 36px; color: #d1d5db; display: block; margin-bottom: 8px; }
.empty-state p { color: #8a94a6; font-size: 13px; }
</style>

<h6 class="mb-3" style="color: #1a2634; font-weight: 600;">
    <i class="fas fa-stethoscope" style="color: #2c6b9e;"></i> Pemeriksaan Ibu Hamil
</h6>

<?php if($totalHadir > 0): ?>
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div class="alert-info-custom" style="margin: 0;">
            <i class="fas fa-info-circle"></i> 
            <?= $totalHadir ?> ibu hadir. 
            <?php if($totalPemeriksaan < $totalHadir): ?>
                <span style="color: #e8a317;"><?= $totalHadir - $totalPemeriksaan ?> ibu belum diperiksa</span>
            <?php else: ?>
                <span style="color: #28a745;">Semua ibu sudah diperiksa ✓</span>
            <?php endif; ?>
        </div>
        <a href="index.php?url=pemeriksaan_ibu-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-success btn-sm-kegiatan">
            <i class="fas fa-plus-circle"></i> Input Pemeriksaan Ibu
        </a>
    </div>
    
    <?php if($totalPemeriksaan > 0): ?>
    <div class="table-responsive mb-4">
        <table class="table table-detail">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Ibu</th>
                    <th>Usia Kehamilan</th>
                    <th>BB (Kg)</th>
                    <th>Tekanan Darah</th>
                    <th>Tinggi Fundus</th>
                    <th>Keluhan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($dataPemeriksaan as $p): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($p['nama_ibu']) ?></strong></td>
                    <td><?= $p['usia_kehamilan'] ?? '-' ?> Minggu</td>
                    <td><?= $p['berat_badan'] ?? '-' ?></td>
                    <td><?= htmlspecialchars($p['tekanan_darah'] ?? '-') ?></td>
                    <td><?= $p['tinggi_fundus'] ?? '-' ?> cm</td>
                    <td><?= htmlspecialchars($p['keluhan'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data pemeriksaan ibu hamil</p></div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert-warning-custom">
        <i class="fas fa-exclamation-triangle"></i> 
        Belum ada ibu hamil yang hadir pada kegiatan ini. <strong>Input kehadiran ibu hamil terlebih dahulu.</strong>
    </div>
<?php endif; ?>