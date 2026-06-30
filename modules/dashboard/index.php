<?php
// ============================================
// DASHBOARD - SISTEM INVENTARIS BARANG
// ============================================

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/functions.php';

// ============================================
// DATA STATISTIK UTAMA
// ============================================

// Total Barang
$total_items = fetchColumn("SELECT COUNT(*) FROM items");

// Total Barang Tersedia
$total_tersedia = fetchColumn("SELECT COUNT(*) FROM items WHERE status = 'tersedia'");

// Total Barang Dipinjam
$total_dipinjam = fetchColumn("SELECT COUNT(*) FROM items WHERE status = 'dipinjam'");

// Total Barang Rusak
$total_rusak = fetchColumn("SELECT COUNT(*) FROM items WHERE `condition` = 'rusak'");

// Total Barang Perbaikan
$total_perbaikan = fetchColumn("SELECT COUNT(*) FROM items WHERE `condition` = 'perbaikan'");

// Total Barang Hilang
$total_hilang = fetchColumn("SELECT COUNT(*) FROM items WHERE status = 'hilang'");

// Total Kategori
$total_kategori = fetchColumn("SELECT COUNT(*) FROM categories");

// Total Peminjam
$total_peminjam = fetchColumn("SELECT COUNT(*) FROM borrowers WHERE is_active = 1");

// Total Peminjaman Aktif
$total_loans_active = fetchColumn("SELECT COUNT(*) FROM loans WHERE status IN ('dipinjam', 'terlambat')");

// Total Peminjaman Terlambat
$total_terlambat = fetchColumn("SELECT COUNT(*) FROM loans WHERE status = 'terlambat'");

// Total Peminjaman Selesai
$total_selesai = fetchColumn("SELECT COUNT(*) FROM loans WHERE status = 'dikembalikan'");

// Total Stok Menipis
$total_stok_menipis = fetchColumn("SELECT COUNT(*) FROM items WHERE quantity <= min_quantity AND status = 'tersedia'");

// Total Stok Habis
$total_stok_habis = fetchColumn("SELECT COUNT(*) FROM items WHERE quantity = 0");

// ============================================
// GRAFIK BARANG PER KATEGORI
// ============================================
$chart_categories = fetchAll("
    SELECT 
        c.name AS category_name,
        c.icon,
        COUNT(i.id) AS total_items,
        SUM(i.quantity) AS total_stock,
        SUM(CASE WHEN i.`condition` = 'baik' THEN 1 ELSE 0 END) AS good_items,
        SUM(CASE WHEN i.`condition` = 'rusak' THEN 1 ELSE 0 END) AS damaged_items
    FROM categories c
    LEFT JOIN items i ON c.id = i.category_id
    GROUP BY c.id
    ORDER BY total_items DESC
");

// ============================================
// GRAFIK STATUS BARANG
// ============================================
$chart_status = fetchAll("
    SELECT 
        status,
        COUNT(*) AS total
    FROM items
    GROUP BY status
");

// ============================================
// GRAFIK PEMINJAMAN BULANAN
// ============================================
$chart_loans = fetchAll("
    SELECT 
        DATE_FORMAT(loan_date, '%b %Y') AS bulan,
        COUNT(*) AS total_loans,
        SUM(CASE WHEN status = 'dikembalikan' THEN 1 ELSE 0 END) AS returned,
        SUM(CASE WHEN status IN ('dipinjam', 'terlambat') THEN 1 ELSE 0 END) AS active
    FROM loans
    WHERE loan_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY YEAR(loan_date), MONTH(loan_date)
    ORDER BY YEAR(loan_date), MONTH(loan_date)
");

// ============================================
// PEMINJAMAN TERBARU
// ============================================
$recent_loans = fetchAll("
    SELECT 
        l.id,
        l.code,
        l.loan_date,
        l.expected_return_date,
        l.status,
        l.total_items,
        b.name AS borrower_name,
        b.institution,
        u.name AS staff_name
    FROM loans l
    LEFT JOIN borrowers b ON l.borrower_id = b.id
    LEFT JOIN users u ON l.created_by = u.id
    ORDER BY l.created_at DESC
    LIMIT 5
");

// ============================================
// STOK MENIPIS
// ============================================
$low_stock_items = fetchAll("
    SELECT 
        id,
        code,
        name,
        quantity,
        min_quantity,
        category_id,
        (SELECT name FROM categories WHERE id = items.category_id) AS category_name
    FROM items
    WHERE quantity <= min_quantity AND status = 'tersedia'
    ORDER BY quantity ASC
    LIMIT 5
");

// ============================================
// BARANG TERBARU
// ============================================
$recent_items = fetchAll("
    SELECT 
        id,
        code,
        name,
        quantity,
        `condition`,
        `status`,
        category_id,
        (SELECT name FROM categories WHERE id = items.category_id) AS category_name,
        created_at
    FROM items
    ORDER BY created_at DESC
    LIMIT 5
");

// ============================================
// DATA UNTUK CHART
// ============================================
$categoryLabels = [];
$categoryData = [];
$categoryColors = ['#2c6b9e', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997'];

foreach ($chart_categories as $cat) {
    $categoryLabels[] = $cat['category_name'];
    $categoryData[] = $cat['total_items'];
}

$statusLabels = [];
$statusData = [];
$statusColors = ['#28a745', '#ffc107', '#dc3545', '#6c757d'];
$statusMap = [
    'tersedia' => 'Tersedia',
    'dipinjam' => 'Dipinjam',
    'perbaikan' => 'Perbaikan',
    'hilang' => 'Hilang'
];

foreach ($chart_status as $st) {
    $statusLabels[] = $statusMap[$st['status']] ?? $st['status'];
    $statusData[] = $st['total'];
}

$loanLabels = [];
$loanActive = [];
$loanReturned = [];

foreach ($chart_loans as $loan) {
    $loanLabels[] = $loan['bulan'];
    $loanActive[] = $loan['active'];
    $loanReturned[] = $loan['returned'];
}
?>

<style>
/* ============================================
   DASHBOARD STYLE
   ============================================ */
.dashboard-container {
    padding: 10px 0;
}

/* HEADER */
.dashboard-header {
    background: #ffffff;
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 28px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.dashboard-header .header-title {
    font-size: 22px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.dashboard-header .header-title small {
    font-size: 14px;
    font-weight: 400;
    color: #8a94a6;
    margin-left: 12px;
}

.dashboard-header .header-sub {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 4px;
}

.dashboard-header .header-sub i {
    color: #2c6b9e;
}

.dashboard-header .header-badge {
    background: #e8f0fe;
    color: #2c6b9e;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

/* STAT CARD */
.stat-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px 22px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    align-items: center;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-card .stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #ffffff;
    flex-shrink: 0;
    margin-right: 16px;
}

.stat-card .stat-icon.primary { background: #2c6b9e; }
.stat-card .stat-icon.success { background: #28a745; }
.stat-card .stat-icon.warning { background: #ffc107; }
.stat-card .stat-icon.danger { background: #dc3545; }
.stat-card .stat-icon.info { background: #17a2b8; }
.stat-card .stat-icon.purple { background: #6f42c1; }

.stat-card .stat-content { flex: 1; min-width: 0; }
.stat-card .stat-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #8a94a6;
    margin-bottom: 2px;
}
.stat-card .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}
.stat-card .stat-sub {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

/* CARD MODERN */
.card-modern {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    height: 100%;
}

.card-modern .card-header-custom {
    padding: 14px 18px;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: transparent;
}

.card-modern .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-modern .card-header-custom .badge-count {
    background: #edf2f7;
    color: #4a5568;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
}

.card-modern .card-body-custom {
    padding: 16px 18px;
}

/* GRAFIK */
.chart-wrapper {
    position: relative;
    height: 250px;
}

.chart-wrapper-sm {
    position: relative;
    height: 200px;
}

/* TABLE ELEGANT */
.table-elegant {
    margin: 0;
    font-size: 13px;
}

.table-elegant thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 8px 14px;
    border-bottom: 2px solid #edf2f7;
}

.table-elegant tbody td {
    padding: 8px 14px;
    color: #2d3748;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-elegant tbody tr:last-child td {
    border-bottom: none;
}

.table-elegant tbody tr:hover {
    background: #fafbfc;
}

.table-elegant .empty-state {
    text-align: center;
    color: #a0aec0;
    padding: 20px 0;
}

.table-elegant .empty-state i {
    font-size: 20px;
    display: block;
    margin-bottom: 4px;
}

/* BADGE */
.badge-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-status.tersedia { background: #d1fae5; color: #047857; }
.badge-status.dipinjam { background: #fef3c7; color: #92400e; }
.badge-status.perbaikan { background: #dbeafe; color: #1d4ed8; }
.badge-status.hilang { background: #fee2e2; color: #b91c1c; }
.badge-status.terlambat { background: #fee2e2; color: #b91c1c; }
.badge-status.dikembalikan { background: #d1fae5; color: #047857; }

.badge-condition {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-condition.baik { background: #d1fae5; color: #047857; }
.badge-condition.rusak { background: #fee2e2; color: #b91c1c; }
.badge-condition.perbaikan { background: #fef3c7; color: #92400e; }

.badge-stock {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-stock.habis { background: #fee2e2; color: #b91c1c; }
.badge-stock.menipis { background: #fef3c7; color: #92400e; }
.badge-stock.cukup { background: #d1fae5; color: #047857; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .dashboard-header {
        padding: 16px 20px;
    }
    .dashboard-header .header-title {
        font-size: 18px;
    }
    .dashboard-header .header-title small {
        display: block;
        margin-left: 0;
        margin-top: 4px;
    }
    .stat-card .stat-value {
        font-size: 22px;
    }
    .stat-card {
        padding: 16px 18px;
    }
    .stat-card .stat-icon {
        width: 44px;
        height: 44px;
        font-size: 18px;
        margin-right: 12px;
    }
    .chart-wrapper {
        height: 200px;
    }
    .chart-wrapper-sm {
        height: 160px;
    }
    .table-elegant thead th,
    .table-elegant tbody td {
        padding: 6px 10px;
        font-size: 12px;
    }
}
</style>

<div class="dashboard-container">

    <!-- ============================================
    HEADER
    ============================================ -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="header-title">
                    <i class="fas fa-chart-pie" style="color: #2c6b9e; margin-right: 10px;"></i>
                    Dashboard Inventaris
                    <small><i class="fas fa-calendar-alt"></i> <?= date('d M Y') ?></small>
                </div>
                <div class="header-sub">
                    <i class="fas fa-boxes"></i> Sistem Manajemen Inventaris Barang
                </div>
            </div>
            <div class="col-md-4 text-md-right mt-2 mt-md-0">
                <span class="header-badge">
                    <i class="fas fa-check-circle"></i> Sistem Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- ============================================
    STATISTIK UTAMA
    ============================================ -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon primary"><i class="fas fa-boxes"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Total Barang</div>
                    <div class="stat-value"><?= number_format($total_items) ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Tersedia</div>
                    <div class="stat-value"><?= number_format($total_tersedia) ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon warning"><i class="fas fa-hand-holding"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Dipinjam</div>
                    <div class="stat-value"><?= number_format($total_dipinjam) ?></div>
                    <div class="stat-sub">
                        <i class="fas fa-clock"></i> <?= number_format($total_loans_active) ?> peminjaman aktif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon danger"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Rusak / Hilang</div>
                    <div class="stat-value"><?= number_format($total_rusak + $total_hilang) ?></div>
                    <div class="stat-sub">
                        <?= number_format($total_rusak) ?> rusak · <?= number_format($total_hilang) ?> hilang
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
    QUICK INFO
    ============================================ -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-2">
            <div class="stat-card" style="padding: 12px 16px;">
                <div class="stat-icon purple" style="width: 40px; height: 40px; font-size: 16px; margin-right: 12px;">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Kategori</div>
                    <div class="stat-value" style="font-size: 20px;"><?= number_format($total_kategori) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="stat-card" style="padding: 12px 16px;">
                <div class="stat-icon info" style="width: 40px; height: 40px; font-size: 16px; margin-right: 12px;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Peminjam</div>
                    <div class="stat-value" style="font-size: 20px;"><?= number_format($total_peminjam) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="stat-card" style="padding: 12px 16px;">
                <div class="stat-icon warning" style="width: 40px; height: 40px; font-size: 16px; margin-right: 12px;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Terlambat</div>
                    <div class="stat-value" style="font-size: 20px; color: #dc3545;"><?= number_format($total_terlambat) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="stat-card" style="padding: 12px 16px;">
                <div class="stat-icon danger" style="width: 40px; height: 40px; font-size: 16px; margin-right: 12px;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Stok Menipis</div>
                    <div class="stat-value" style="font-size: 20px; color: #dc3545;"><?= number_format($total_stok_menipis + $total_stok_habis) ?></div>
                    <div class="stat-sub"><?= number_format($total_stok_habis) ?> habis</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
    GRAFIK 2 KOLOM
    ============================================ -->
    <div class="row">
        <!-- Grafik 1: Barang per Kategori -->
        <div class="col-lg-6 mb-4">
            <div class="card-modern">
                <div class="card-header-custom">
                    <h6>
                        <i class="fas fa-chart-bar" style="color: #2c6b9e; margin-right: 8px;"></i>
                        Barang per Kategori
                    </h6>
                    <span class="badge-count"><?= count($categoryLabels) ?> kategori</span>
                </div>
                <div class="card-body-custom">
                    <div class="chart-wrapper">
                        <canvas id="chartCategory"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Status Barang -->
        <div class="col-lg-6 mb-4">
            <div class="card-modern">
                <div class="card-header-custom">
                    <h6>
                        <i class="fas fa-chart-pie" style="color: #28a745; margin-right: 8px;"></i>
                        Status Barang
                    </h6>
                    <span class="badge-count">Total <?= number_format($total_items) ?></span>
                </div>
                <div class="card-body-custom">
                    <div class="chart-wrapper">
                        <canvas id="chartStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
    GRAFIK 3: Peminjaman Bulanan
    ============================================ -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card-modern">
                <div class="card-header-custom">
                    <h6>
                        <i class="fas fa-chart-line" style="color: #6f42c1; margin-right: 8px;"></i>
                        Tren Peminjaman (6 Bulan Terakhir)
                    </h6>
                    <span class="badge-count">Bulanan</span>
                </div>
                <div class="card-body-custom">
                    <div class="chart-wrapper" style="height: 220px;">
                        <canvas id="chartLoans"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
    TABEL: PEMINJAMAN TERBARU & STOK MENIPIS
    ============================================ -->
    <div class="row">
        <!-- Peminjaman Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card-modern">
                <div class="card-header-custom">
                    <h6>
                        <i class="fas fa-hand-holding" style="color: #2c6b9e; margin-right: 8px;"></i>
                        Peminjaman Terbaru
                    </h6>
                    <span class="badge-count"><?= count($recent_loans) ?></span>
                </div>
                <div class="card-body-custom" style="padding: 0;">
                    <table class="table table-elegant">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_loans as $loan): ?>
                            <tr>
                                <td><small><?= $loan['code'] ?></small></td>
                                <td><?= htmlspecialchars($loan['borrower_name']) ?></td>
                                <td><?= getStatusBadge($loan['status']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recent_loans)): ?>
                            <tr><td colspan="3" class="empty-state"><i class="fas fa-inbox"></i> Tidak ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="col-lg-6 mb-4">
            <div class="card-modern">
                <div class="card-header-custom">
                    <h6>
                        <i class="fas fa-exclamation-triangle" style="color: #dc3545; margin-right: 8px;"></i>
                        Stok Menipis
                    </h6>
                    <span class="badge-count"><?= count($low_stock_items) ?></span>
                </div>
                <div class="card-body-custom" style="padding: 0;">
                    <table class="table table-elegant">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Stok</th>
                                <th>Min</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($low_stock_items as $item): ?>
                            <tr>
                                <td><small><?= $item['code'] ?></small></td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><span class="badge-stock <?= $item['quantity'] <= 0 ? 'habis' : 'menipis' ?>"><?= $item['quantity'] ?></span></td>
                                <td><?= $item['min_quantity'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($low_stock_items)): ?>
                            <tr><td colspan="4" class="empty-state"><i class="fas fa-check-circle" style="color: #28a745;"></i> Semua stok aman</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
    BARANG TERBARU
    ============================================ -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card-modern">
                <div class="card-header-custom">
                    <h6>
                        <i class="fas fa-box" style="color: #17a2b8; margin-right: 8px;"></i>
                        Barang Terbaru
                    </h6>
                    <span class="badge-count"><?= count($recent_items) ?></span>
                </div>
                <div class="card-body-custom" style="padding: 0;">
                    <table class="table table-elegant">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_items as $item): ?>
                            <tr>
                                <td><small><?= $item['code'] ?></small></td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['category_name']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= getConditionBadge($item['condition']) ?></td>
                                <td><?= getStatusBadge($item['status']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recent_items)): ?>
                            <tr><td colspan="6" class="empty-state"><i class="fas fa-inbox"></i> Tidak ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================
CHART.JS
============================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {

    // ============================================
    // CHART 1: BARANG PER KATEGORI (Bar Chart)
    // ============================================
    const ctxCategory = document.getElementById('chartCategory').getContext('2d');
    const categoryColors = ['#2c6b9e', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997'];

    new Chart(ctxCategory, {
        type: 'bar',
        data: {
            labels: <?= json_encode($categoryLabels) ?>,
            datasets: [{
                label: 'Jumlah Barang',
                data: <?= json_encode($categoryData) ?>,
                backgroundColor: categoryColors.slice(0, <?= count($categoryLabels) ?>),
                borderColor: '#ffffff',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 },
                        color: '#8a94a6'
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 10 },
                        color: '#8a94a6'
                    }
                }
            }
        }
    });

    // ============================================
    // CHART 2: STATUS BARANG (Doughnut)
    // ============================================
    const ctxStatus = document.getElementById('chartStatus').getContext('2d');

    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($statusLabels) ?>,
            datasets: [{
                data: <?= json_encode($statusData) ?>,
                backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545'],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        font: { size: 11 },
                        color: '#4a5568'
                    }
                }
            },
            cutout: '65%'
        }
    });

    // ============================================
    // CHART 3: TREN PEMINJAMAN (Line Chart)
    // ============================================
    const ctxLoans = document.getElementById('chartLoans').getContext('2d');

    new Chart(ctxLoans, {
        type: 'line',
        data: {
            labels: <?= json_encode($loanLabels) ?>,
            datasets: [{
                label: 'Aktif',
                data: <?= json_encode($loanActive) ?>,
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#ffc107',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }, {
                label: 'Selesai',
                data: <?= json_encode($loanReturned) ?>,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#28a745',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: { size: 11 },
                        color: '#4a5568',
                        padding: 16
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 },
                        color: '#8a94a6'
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 10 },
                        color: '#8a94a6'
                    }
                }
            }
        }
    });

});
</script>