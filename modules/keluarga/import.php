<?php
// modules/keluarga/import.php
// PROSES IMPORT - SUPPORT EXCEL (.xlsx, .xls) & CSV

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
if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
    echo "<script>
        alert('Format file tidak didukung! Gunakan .xlsx, .xls, atau .csv');
        window.location='index.php?url=keluarga';
    </script>";
    exit;
}

$rows = [];

// ============================================================
// JIKA FILE CSV
// ============================================================
if ($ext == 'csv') {
    if (($handle = fopen($file, "r")) !== false) {
        // Deteksi delimiter otomatis (koma atau titik koma)
        $firstLine = fgets($handle);
        rewind($handle);
        
        $delimiter = ',';
        if (strpos($firstLine, ';') !== false) {
            $delimiter = ';';
        } elseif (strpos($firstLine, "\t") !== false) {
            $delimiter = "\t";
        }
        
        // Baca header (skip)
        $header = fgetcsv($handle, 0, $delimiter, '"');
        
        // Baca data
        while (($data = fgetcsv($handle, 0, $delimiter, '"')) !== false) {
            // Skip baris kosong
            if (empty(trim($data[0] ?? '')) && empty(trim($data[1] ?? ''))) {
                continue;
            }
            // Skip baris yang mengandung kata PETUNJUK atau ===
            $firstCol = trim($data[0] ?? '');
            if (strpos($firstCol, 'PETUNJUK') !== false || 
                strpos($firstCol, '===') !== false ||
                strpos($firstCol, 'NO_KK') !== false) {
                continue;
            }
            $rows[] = $data;
        }
        fclose($handle);
    }
}
// ============================================================
// JIKA FILE EXCEL
// ============================================================
else {
    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        array_shift($rows); // Hapus header
        
        // Bersihkan data kosong di akhir
        $rows = array_filter($rows, function($row) {
            return !empty(trim($row[0] ?? '')) || !empty(trim($row[1] ?? ''));
        });
        
    } catch (Exception $e) {
        echo "<script>
            alert('Error membaca file: " . addslashes($e->getMessage()) . "');
            window.location='index.php?url=keluarga';
        </script>";
        exit;
    }
}

// ============================================================
// PROSES INSERT
// ============================================================
$sukses = 0;
$gagal = 0;
$errors = [];

foreach ($rows as $rowIndex => $row) {
    // Pastikan row adalah array
    if (!is_array($row)) {
        continue;
    }
    
    // Ambil data dengan index yang benar
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

    // Validasi
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

    // Cek duplikat
    $stmt = $pdo->prepare("SELECT id FROM keluarga WHERE no_kk = ?");
    $stmt->execute([$no_kk]);
    if ($stmt->fetch()) {
        $errors[] = "Baris " . ($rowIndex + 1) . ": NO_KK '$no_kk' sudah terdaftar!";
        $gagal++;
        continue;
    }

    // Insert
    $stmt = $pdo->prepare("
        INSERT INTO keluarga (
            no_kk, nama_kepala_keluarga, nik_ayah, nama_ayah,
            nik_ibu, nama_ibu, alamat, rt, rw, desa, kecamatan, no_hp
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    try {
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
    } catch (Exception $e) {
        $errors[] = "Baris " . ($rowIndex + 1) . ": " . $e->getMessage();
        $gagal++;
    }
}

// ============================================================
// TAMPILKAN HASIL
// ============================================================
$message = "✅ Import selesai!\n";
$message .= "✅ Berhasil: $sukses data\n";
$message .= "❌ Gagal: $gagal data";

if (!empty($errors)) {
    $message .= "\n\n📋 Detail Error:\n" . implode("\n", $errors);
}

echo "<script>
    alert('" . addslashes($message) . "');
    window.location='index.php?url=keluarga';
</script>";
exit;
?>