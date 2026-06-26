<?php
// modules/keluarga/import.php
// FILE INI HANYA PROSES IMPORT

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Cek file upload
if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] != 0) {
    echo "<script>
        alert('Gagal upload file!');
        window.location='index.php?url=keluarga';
    </script>";
    exit;
}

$file = $_FILES['file_excel']['tmp_name'];
$fileName = $_FILES['file_excel']['name'];

// Validasi ekstensi
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
if (!in_array($ext, ['xlsx', 'xls'])) {
    echo "<script>
        alert('Format file tidak didukung! Gunakan .xlsx atau .xls');
        window.location='index.php?url=keluarga';
    </script>";
    exit;
}

try {
    // Baca file Excel
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // Hapus header (baris 1)
    array_shift($rows);

    $sukses = 0;
    $gagal = 0;
    $errors = [];

    foreach ($rows as $rowIndex => $row) {
        // Skip baris kosong
        if (empty(trim($row[0] ?? '')) && empty(trim($row[1] ?? ''))) {
            continue;
        }

        // Cek NO_KK harus diisi dan UNIK
        $no_kk = trim($row[0] ?? '');
        $nama_kepala_keluarga = trim($row[1] ?? '');
        $nik_ayah = trim($row[2] ?? '');
        $nama_ayah = trim($row[3] ?? '');
        $nik_ibu = trim($row[4] ?? '');
        $nama_ibu = trim($row[5] ?? '');
        $alamat = trim($row[6] ?? '');
        $rt = trim($row[7] ?? '');
        $rw = trim($row[8] ?? '');
        $desa = trim($row[9] ?? '');
        $kecamatan = trim($row[10] ?? '');
        $no_hp = trim($row[11] ?? '');

        // Validasi wajib
        if (empty($no_kk)) {
            $errors[] = "Baris " . ($rowIndex + 1) . ": NO_KK tidak boleh kosong";
            $gagal++;
            continue;
        }

        if (empty($nama_kepala_keluarga)) {
            $errors[] = "Baris " . ($rowIndex + 1) . ": NAMA_KEPALA_KELUARGA tidak boleh kosong";
            $gagal++;
            continue;
        }

        // Cek NO_KK sudah ada
        $stmt = $pdo->prepare("SELECT id FROM keluarga WHERE no_kk = ?");
        $stmt->execute([$no_kk]);
        if ($stmt->fetch()) {
            $errors[] = "Baris " . ($rowIndex + 1) . ": NO_KK '$no_kk' sudah terdaftar!";
            $gagal++;
            continue;
        }

        // Insert data
        $stmt = $pdo->prepare("
            INSERT INTO keluarga (
                no_kk, nama_kepala_keluarga, nik_ayah, nama_ayah,
                nik_ibu, nama_ibu, alamat, rt, rw, desa, kecamatan, no_hp
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $no_kk,
            $nama_kepala_keluarga,
            $nik_ayah,
            $nama_ayah,
            $nik_ibu,
            $nama_ibu,
            $alamat,
            $rt,
            $rw,
            $desa,
            $kecamatan,
            $no_hp
        ]);

        $sukses++;
    }

    // Tampilkan hasil
    $message = "Import selesai! Berhasil: $sukses data, Gagal: $gagal data.";
    if (!empty($errors)) {
        $message .= "\n\nDetail Error:\n" . implode("\n", $errors);
    }

    echo "<script>
        alert('" . addslashes($message) . "');
        window.location='index.php?url=keluarga';
    </script>";

} catch (Exception $e) {
    echo "<script>
        alert('Error: " . addslashes($e->getMessage()) . "');
        window.location='index.php?url=keluarga';
    </script>";
}
exit;