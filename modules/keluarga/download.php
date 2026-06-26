<?php
// modules/keluarga/download.php
// DOWNLOAD TEMPLATE EXCEL (.xlsx) - PAKAI PHPSPREADSHEET

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Buat Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ============================================================
// HEADER (BARIS 1) - Dengan Warna Biru
// ============================================================
$headers = [
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
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// Style Header
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 11
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '2C6B9E']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
];

$sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

// ============================================================
// CONTOH DATA (BARIS 2-4)
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

$rowNum = 2;
foreach ($exampleData as $row) {
    $col = 'A';
    foreach ($row as $value) {
        $sheet->setCellValue($col . $rowNum, $value);
        $col++;
    }
    $rowNum++;
}

// Style Data Contoh
$dataStyle = [
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'CCCCCC']
        ]
    ]
];
$sheet->getStyle('A2:L4')->applyFromArray($dataStyle);

// ============================================================
// BARIS KOSONG + PETUNJUK
// ============================================================
$rowNum = 5;
$sheet->setCellValue('A' . $rowNum, '');
$rowNum++;

$instructions = [
    '========== PETUNJUK ==========',
    '1. NO_KK wajib diisi dan harus UNIK',
    '2. NAMA_KEPALA_KELUARGA wajib diisi',
    '3. Kolom lain boleh dikosongkan jika tidak ada',
    '4. Hapus data contoh (baris 2-4) sebelum mengisi data',
    '5. Simpan sebagai .xlsx atau .csv'
];

foreach ($instructions as $inst) {
    $sheet->setCellValue('A' . $rowNum, $inst);
    $sheet->mergeCells('A' . $rowNum . ':L' . $rowNum);
    $sheet->getStyle('A' . $rowNum)->getFont()->setSize(10);
    $rowNum++;
}

// ============================================================
// AUTO SIZE KOLOM
// ============================================================
foreach (range('A', 'L') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// ============================================================
// SET SHEET PROTECTION (biar ga diedit strukturnya)
// ============================================================
// $sheet->getProtection()->setSheet(true);
// $sheet->getProtection()->setSelectLockedCells(true);
// $sheet->getStyle('A1:L4')->getProtection()->setLocked(true);

// ============================================================
// OUTPUT FILE
// ============================================================
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Template_Import_Keluarga_Posyandu.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>