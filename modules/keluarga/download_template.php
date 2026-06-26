<?php
// modules/keluarga/download_template.php
// VERSI CSV - NATIVE PHP, TANPA LIBRARY

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Template_Import_Keluarga_Posyandu.csv"');

// Buat file pointer
$output = fopen('php://output', 'w');

// Set BOM untuk UTF-8 (biar ga error di Excel)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// ============================================================
// HEADER
// ============================================================
fputcsv($output, [
    'NO_KK',
    'NAMA_KEPALA_KELUARGA',
    'NIK_AYAH',
    'NAMA_AYAH',
    'NIK_IBU',
    'NAMA_IBU',
    'ALAMAT',
    'RT',
    'RW',
    'DESA',
    'KECAMATAN',
    'NO_HP'
]);

// ============================================================
// CONTOH DATA (3 baris)
// ============================================================
$exampleData = [
    [
        '1234567890123456',
        'Bapak Ahmad Suharto',
        '3321010101010001',
        'Ahmad Suharto',
        '3321010101010002',
        'Siti Rahayu',
        'Jl. Merdeka No. 10',
        '01',
        '02',
        'Sumberejo',
        'Kecamatan 1',
        '081234567890'
    ],
    [
        '1234567890123457',
        'Bapak Budi Santoso',
        '3321010101010003',
        'Budi Santoso',
        '3321010101010004',
        'Dewi Lestari',
        'Jl. Kenanga No. 5',
        '02',
        '03',
        'Mulyorejo',
        'Kecamatan 2',
        '081234567891'
    ],
    [
        '1234567890123458',
        'Bapak Cipto Wibowo',
        '3321010101010005',
        'Cipto Wibowo',
        '3321010101010006',
        'Rina Kurniawati',
        'Jl. Melati No. 8',
        '03',
        '01',
        'Karangrejo',
        'Kecamatan 3',
        '081234567892'
    ]
];

foreach ($exampleData as $row) {
    fputcsv($output, $row);
}

// ============================================================
// BARIS KOSONG + INSTRUKSI
// ============================================================
fputcsv($output, []); // baris kosong
fputcsv($output, ['========== PETUNJUK ==========']);
fputcsv($output, ['1. NO_KK wajib diisi dan harus UNIK']);
fputcsv($output, ['2. NAMA_KEPALA_KELUARGA wajib diisi']);
fputcsv($output, ['3. Kolom lain boleh dikosongkan jika tidak ada']);
fputcsv($output, ['4. Hapus data contoh sebelum mengisi data']);
fputcsv($output, ['5. Simpan sebagai .csv atau .xlsx']);

fclose($output);
exit;
?>