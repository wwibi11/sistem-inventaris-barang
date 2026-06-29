<?php
// detail_kehadiran_anak.php
$id_kegiatan = $_GET['id'] ?? 0;

// DATA ANAK
$anak = $pdo->query("SELECT * FROM anak WHERE status='aktif' ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// KEHADIRAN ANAK
$q = $pdo->prepare("SELECT * FROM kehadiran WHERE id_kegiatan=?");
$q->execute([$id_kegiatan]);
$kehadiranData = [];
foreach($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $kehadiranData[$row['id_anak']] = $row;
}

// SIMPAN KEHADIRAN
if (isset($_POST['simpan_kehadiran_anak'])) {
    foreach ($_POST['hadir_anak'] as $id_anak => $status) {
        $stmt = $pdo->prepare("
            INSERT INTO kehadiran (id_anak, id_kegiatan, status_hadir, dicatat_oleh)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status_hadir = VALUES(status_hadir)
        ");
        $stmt->execute([$id_anak, $id_kegiatan, $status, $_SESSION['user']['id']]);
    }
    echo "<script>window.location='index.php?url=kegiatan-detail&id=".$id_kegiatan."&tab=kehadiran_anak';</script>";
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
</style>

<h6 class="mb-3" style="color: #1a2634; font-weight: 600;">
    <i class="fas fa-child" style="color: #2c6b9e;"></i> Kehadiran Anak
</h6>

<form method="POST">
    <div class="table-responsive">
        <table class="table table-detail">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Anak</th>
                    <th>NIK</th>
                    <th width="200">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($anak) > 0): ?>
                    <?php $no = 1; foreach($anak as $a): 
                        $status = $kehadiranData[$a['id']]['status_hadir'] ?? 'hadir';
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($a['nama']) ?></strong></td>
                        <td><?= htmlspecialchars($a['nik'] ?? '-') ?></td>
                        <td>
                            <select name="hadir_anak[<?= $a['id'] ?>]" class="form-control form-control-sm" style="border-radius: 8px;">
                                <option value="hadir" <?= $status == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="tidak" <?= $status == 'tidak' ? 'selected' : '' ?>>Tidak Hadir</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data anak</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <button type="submit" name="simpan_kehadiran_anak" class="btn btn-primary btn-sm-kegiatan">
        <i class="fas fa-save"></i> Simpan Kehadiran Anak
    </button>
</form>