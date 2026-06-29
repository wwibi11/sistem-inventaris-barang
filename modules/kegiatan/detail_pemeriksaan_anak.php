<?php
// detail_pemeriksaan_anak.php
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

// DATA PEMERIKSAAN
$q = $pdo->prepare("
    SELECT 
        p.*, 
        a.nama,
        a.nik,
        a.tanggal_lahir,
        TIMESTAMPDIFF(MONTH, a.tanggal_lahir, CURDATE()) AS umur_bulan
    FROM pemeriksaan p
    JOIN anak a ON a.id = p.id_anak
    WHERE p.id_kegiatan = ?
    ORDER BY a.nama ASC
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
.badge-pemeriksaan { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-pemeriksaan.Baik { background: #d1fae5; color: #047857; }
.badge-pemeriksaan.Normal { background: #d1fae5; color: #047857; }
.badge-pemeriksaan.Kurang { background: #fef3c7; color: #92400e; }
.badge-pemeriksaan.Buruk { background: #fee2e2; color: #b91c1c; }
.badge-pemeriksaan.Lebih { background: #dbeafe; color: #1d4ed8; }
.empty-state { text-align: center; padding: 30px 20px; }
.empty-state i { font-size: 36px; color: #d1d5db; display: block; margin-bottom: 8px; }
.empty-state p { color: #8a94a6; font-size: 13px; }
</style>

<h6 class="mb-3" style="color: #1a2634; font-weight: 600;">
    <i class="fas fa-stethoscope" style="color: #2c6b9e;"></i> Pemeriksaan Anak
</h6>

<?php if($totalHadir > 0): ?>
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div class="alert-info-custom" style="margin: 0;">
            <i class="fas fa-info-circle"></i> 
            <?= $totalHadir ?> anak hadir. 
            <?php if($totalPemeriksaan < $totalHadir): ?>
                <span style="color: #e8a317;"><?= $totalHadir - $totalPemeriksaan ?> anak belum diperiksa</span>
            <?php else: ?>
                <span style="color: #28a745;">Semua anak sudah diperiksa ✓</span>
            <?php endif; ?>
        </div>
        <a href="index.php?url=pemeriksaan-input&id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-success btn-sm-kegiatan">
            <i class="fas fa-plus-circle"></i> Input Pemeriksaan Anak
        </a>
    </div>
    
    <?php if($totalPemeriksaan > 0): ?>
    <div class="table-responsive">
        <table class="table table-detail">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Anak</th>
                    <th>Umur</th>
                    <th>BB (Kg)</th>
                    <th>TB (Cm)</th>
                    <th>LK (Cm)</th>
                    <th>Status Gizi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($dataPemeriksaan as $p): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($p['nama']) ?></strong></td>
                    <td>
                        <?php 
                        if(!empty($p['umur_bulan'])) {
                            $tahun = floor($p['umur_bulan'] / 12);
                            $bulan = $p['umur_bulan'] % 12;
                            echo $tahun . ' Th ' . $bulan . ' Bl';
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td><?= $p['berat_badan'] ?? '-' ?></td>
                    <td><?= $p['tinggi_badan'] ?? '-' ?></td>
                    <td><?= $p['lingkar_kepala'] ?? '-' ?></td>
                    <td>
                        <?php 
                        $status = $p['status_gizi'] ?? '';
                        if(!empty($status)) {
                            $class = '';
                            switch(strtolower($status)) {
                                case 'normal': case 'baik': $class = 'Baik'; break;
                                case 'kurang': $class = 'Kurang'; break;
                                case 'buruk': $class = 'Buruk'; break;
                                case 'lebih': case 'gemuk': $class = 'Lebih'; break;
                                default: $class = '';
                            }
                            ?>
                            <span class="badge-pemeriksaan <?= $class ?>"><?= htmlspecialchars($status) ?></span>
                        <?php } else { ?>
                            <span class="text-muted">-</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data pemeriksaan anak</p></div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert-warning-custom">
        <i class="fas fa-exclamation-triangle"></i> 
        Belum ada anak yang hadir pada kegiatan ini. <strong>Input kehadiran anak terlebih dahulu.</strong>
    </div>
<?php endif; ?>