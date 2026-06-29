<?php
// detail_kehadiran_ibu.php
$id_kegiatan = $_GET['id'] ?? 0;

// DATA IBU HAMIL
$ibuHamil = $pdo->query("SELECT * FROM ibu_hamil WHERE status='Aktif' ORDER BY nama_ibu")->fetchAll(PDO::FETCH_ASSOC);

// KEHADIRAN IBU
$q = $pdo->prepare("SELECT * FROM kehadiran_ibu_hamil WHERE id_kegiatan=?");
$q->execute([$id_kegiatan]);
$kehadiranIbuData = [];
foreach($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $kehadiranIbuData[$row['ibu_hamil_id']] = $row;
}

function getTrimester($usia) {
    if ($usia <= 0) return 0;
    elseif ($usia <= 13) return 1;
    elseif ($usia <= 27) return 2;
    else return 3;
}

// SIMPAN KEHADIRAN IBU
if (isset($_POST['simpan_kehadiran_ibu'])) {
    foreach ($_POST['hadir_ibu'] as $id_ibu => $status) {
        $stmt = $pdo->prepare("
            INSERT INTO kehadiran_ibu_hamil (ibu_hamil_id, id_kegiatan, status_hadir, dicatat_oleh)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status_hadir = VALUES(status_hadir)
        ");
        $stmt->execute([$id_ibu, $id_kegiatan, $status, $_SESSION['user']['id']]);
    }
    echo "<script>window.location='index.php?url=kegiatan-detail&id=".$id_kegiatan."&tab=kehadiran_ibu';</script>";
    exit;
}
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
.btn-sm-kegiatan { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; }
.btn-sm-kegiatan.primary { background: #2c6b9e; color: #ffffff; }
.btn-sm-kegiatan.primary:hover { background: #1f507a; color: #ffffff; }
.badge-trimester { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-trimester.t1 { background: #dbeafe; color: #1d4ed8; }
.badge-trimester.t2 { background: #fef3c7; color: #92400e; }
.badge-trimester.t3 { background: #fce4ec; color: #c62828; }
.badge-trimester.t0 { background: #f3f4f6; color: #6b7280; }
</style>

<h6 class="mb-3" style="color: #1a2634; font-weight: 600;">
    <i class="fas fa-calendar-check" style="color: #2c6b9e;"></i> Kehadiran Ibu Hamil
</h6>

<form method="POST">
    <div class="table-responsive mb-4">
        <table class="table table-detail">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Ibu</th>
                    <th>NIK</th>
                    <th>Usia Kehamilan</th>
                    <th>Trimester</th>
                    <th width="200">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($ibuHamil) > 0): ?>
                    <?php $no = 1; foreach($ibuHamil as $ih): 
                        $status = $kehadiranIbuData[$ih['id']]['status_hadir'] ?? 'hadir';
                        $usia = $ih['usia_kehamilan'] ?? 0;
                        $trimester = getTrimester($usia);
                        $class = 't0';
                        if($trimester == 1) $class = 't1';
                        elseif($trimester == 2) $class = 't2';
                        elseif($trimester == 3) $class = 't3';
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($ih['nama_ibu']) ?></strong></td>
                        <td><?= htmlspecialchars($ih['nik'] ?? '-') ?></td>
                        <td><?= $usia > 0 ? $usia . ' Minggu' : '-' ?></td>
                        <td><span class="badge-trimester <?= $class ?>"><?= $trimester > 0 ? 'Trimester ' . $trimester : '-' ?></span></td>
                        <td>
                            <select name="hadir_ibu[<?= $ih['id'] ?>]" class="form-control form-control-sm" style="border-radius: 8px;">
                                <option value="hadir" <?= $status == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="tidak" <?= $status == 'tidak' ? 'selected' : '' ?>>Tidak Hadir</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data ibu hamil</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <button type="submit" name="simpan_kehadiran_ibu" class="btn btn-primary btn-sm-kegiatan">
        <i class="fas fa-save"></i> Simpan Kehadiran Ibu Hamil
    </button>
</form>