<?php
// modules/dashboard/index.php
// TANPA GUARD - Cukup routing di index.php

require_once __DIR__ . '/../../config/functions.php';

// Statistics
$total_items = fetchColumn("SELECT COUNT(*) FROM items");
$total_tersedia = fetchColumn("SELECT COUNT(*) FROM items WHERE status = 'tersedia'");
$total_dipinjam = fetchColumn("SELECT COUNT(*) FROM items WHERE status = 'dipinjam'");
$total_rusak = fetchColumn("SELECT COUNT(*) FROM items WHERE `condition` = 'rusak'");
$total_kategori = fetchColumn("SELECT COUNT(*) FROM categories");
$total_peminjam = fetchColumn("SELECT COUNT(*) FROM borrowers WHERE is_active = 1");
$total_loans_active = fetchColumn("SELECT COUNT(*) FROM loans WHERE status IN ('dipinjam', 'terlambat')");
$total_terlambat = fetchColumn("SELECT COUNT(*) FROM loans WHERE status = 'terlambat'");

// Chart Data
$chart_categories = fetchAll("SELECT c.name, COUNT(i.id) as total FROM categories c LEFT JOIN items i ON c.id = i.category_id GROUP BY c.id");
$chart_status = fetchAll("SELECT status, COUNT(*) as total FROM items GROUP BY status");
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard Inventaris</h1>
    
    <!-- Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Barang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_items) ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-boxes fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_tersedia) ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Dipinjam</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_dipinjam) ?></div>
                            <div class="small text-gray-500"><?= number_format($total_loans_active) ?> peminjaman aktif</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-hand-holding fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rusak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_rusak) ?></div>
                            <div class="small text-gray-500"><?= number_format($total_terlambat) ?> terlambat</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Info -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-2">
            <div class="card shadow h-100 py-2">
                <div class="card-body text-center">
                    <div class="h5 mb-0 font-weight-bold text-primary"><?= number_format($total_kategori) ?></div>
                    <div class="small text-gray-500"><i class="fas fa-tags"></i> Kategori</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="card shadow h-100 py-2">
                <div class="card-body text-center">
                    <div class="h5 mb-0 font-weight-bold text-info"><?= number_format($total_peminjam) ?></div>
                    <div class="small text-gray-500"><i class="fas fa-users"></i> Peminjam</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Barang per Kategori</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartCategory" style="height:250px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Barang</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartStatus" style="height:250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Peminjaman Terbaru</h6>
                </div>
                <div class="card-body" style="max-height:250px; overflow-y:auto;">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent = fetchAll("SELECT l.code, b.name, l.status FROM loans l LEFT JOIN borrowers b ON l.borrower_id = b.id ORDER BY l.created_at DESC LIMIT 5");
                            foreach ($recent as $row): ?>
                            <tr>
                                <td><?= $row['code'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= getStatusBadge($row['status']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recent)): ?>
                            <tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Stok Menipis</h6>
                </div>
                <div class="card-body" style="max-height:250px; overflow-y:auto;">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $low = fetchAll("SELECT code, name, quantity FROM items WHERE quantity <= min_quantity AND status = 'tersedia' ORDER BY quantity ASC LIMIT 5");
                            foreach ($low as $row): ?>
                            <tr>
                                <td><?= $row['code'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><span class="badge bg-warning"><?= $row['quantity'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($low)): ?>
                            <tr><td colspan="3" class="text-center text-muted">Semua stok aman</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Category Chart
const ctxCat = document.getElementById('chartCategory').getContext('2d');
new Chart(ctxCat, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($chart_categories, 'name')) ?>,
        datasets: [{
            label: 'Jumlah Barang',
            data: <?= json_encode(array_column($chart_categories, 'total')) ?>,
            backgroundColor: ['#2c6b9e', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1'],
            borderColor: '#fff',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// Status Chart
const ctxStatus = document.getElementById('chartStatus').getContext('2d');
new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($chart_status, 'status')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($chart_status, 'total')) ?>,
            backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545'],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>