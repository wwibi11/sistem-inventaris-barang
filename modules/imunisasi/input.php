<?php
require_once __DIR__ . '/../../config/database.php';

if(isset($_POST['simpan'])){

    $stmt = $pdo->prepare("
        INSERT INTO imunisasi
        (
            id_anak,
            id_kegiatan,
            jenis_imunisasi,
            tanggal,
            diberikan_oleh
        )
        VALUES
        (
            ?,?,?,?,?
        )
    ");

    $stmt->execute([
        $_POST['anak'],
        $_POST['kegiatan'],
        $_POST['jenis'],
        $_POST['tanggal'],
        $_SESSION['user']['id']
    ]);

    header("Location:index.php?url=imunisasi");
    exit;
}

$anak = $pdo->query("
SELECT *
FROM anak
ORDER BY nama
")->fetchAll(PDO::FETCH_ASSOC);

$kegiatan = $pdo->query("
SELECT *
FROM kegiatan
ORDER BY tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>