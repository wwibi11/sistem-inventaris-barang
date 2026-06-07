<?php
require_once __DIR__ . '/../../config/database.php';

$data = $pdo->query("
  SELECT *
  FROM kegiatan
  ORDER BY tanggal DESC
")->fetchAll();
?>

<div class="container-fluid">

  <h3>Kegiatan Posyandu</h3>

  <table class="table table-bordered">
    <tr>
      <th>Tanggal</th>
      <th>Lokasi</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>

    <?php foreach($data as $d): ?>
    <tr>
      <td><?= $d['tanggal'] ?></td>
      <td><?= $d['lokasi'] ?></td>
      <td><?= $d['status'] ?></td>
      <td>
        <a href="index.php?url=kegiatan-detail&id=<?= $d['id'] ?>"
           class="btn btn-primary btn-sm">
          Detail
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>

</div>