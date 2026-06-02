<?php
require_once __DIR__ . '/../../config/database.php';

$data = $pdo->query("
  SELECT k.*, u.nama AS pembuat
  FROM kegiatan k
  LEFT JOIN users u ON u.id = k.created_by
  ORDER BY k.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">

  <div class="d-flex justify-content-between mb-3">
    <h4>Kegiatan</h4>

    <a href="index.php?url=kegiatan-create" class="btn btn-primary">
      + Tambah
    </a>
  </div>

  <table class="table table-bordered table-sm">
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Lokasi</th>
        <th>Pertemuan</th>
        <th>Status</th>
        <th>Created</th>
        <th>Aksi</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($data as $d): ?>
        <tr>
          <td><?= $d['tanggal'] ?></td>
          <td><?= $d['lokasi'] ?></td>
          <td><?= $d['pertemuan_ke'] ?></td>
          <td><?= $d['status'] ?></td>
          <td><?= $d['pembuat'] ?></td>
          <td>
            <a href="index.php?url=kegiatan-detail&id=<?= $d['id'] ?>" class="btn btn-info btn-sm">Detail</a>
            <a href="index.php?url=kegiatan-edit&id=<?= $d['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="index.php?url=kegiatan-delete&id=<?= $d['id'] ?>"
               onclick="return confirm('Hapus?')"
               class="btn btn-danger btn-sm">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>

  </table>

</div>