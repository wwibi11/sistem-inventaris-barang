<?php
// modules/borrowers/view.php

require_once __DIR__ . '/../../config/functions.php';

// Redirect jika bukan admin
if (!isAdmin()) {
    $_SESSION['error'] = 'Akses ditolak! Anda tidak memiliki izin.';
    redirect('index.php?url=dashboard');
}

$id = $_GET['id'] ?? 0;
if ($id <= 0) {
    $_SESSION['error'] = 'ID peminjam tidak valid!';
    redirect('index.php?url=borrowers');
}

$borrower = fetchOne("SELECT * FROM borrowers WHERE id = ?", [$id]);

if (!$borrower) {
    $_SESSION['error'] = 'Data peminjam tidak ditemukan!';
    redirect('index.php?url=borrowers');
}

// Ambil riwayat peminjaman
$loans = fetchAll(
    "SELECT l.*, 
            (SELECT COUNT(*) FROM loan_details WHERE loan_id = l.id) as total_items
     FROM loans l 
     WHERE l.borrower_id = ? 
     ORDER BY l.created_at DESC 
     LIMIT 10",
    [$id]
);

$totalLoans = count($loans);
?>

<style>
.card-view {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.card-view .card-body {
    padding: 24px 20px;
}

.info-item .label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-item .value {
    font-size: 14px;
    font-weight: 500;
    color: #1e293b;
    margin-top: 2px;
}

.table-loans {
    font-size: 13px;
}

.table-loans thead th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 14px;
    border-bottom: 2px solid #eef2f7;
}

.table-loans tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.table-loans tbody tr:last-child td {
    border-bottom: none;
}

.btn-custom-secondary {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 500;
}

.btn-custom-secondary:hover {
    background: #e2e8f0;
    color: #1e293b;
}
</style>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark">
                <i class="fas fa-user text-primary me-2"></i>Detail Peminjam
            </h4>
            <p class="text-muted small mt-1">
                Kode: <span class="badge bg-secondary"><?= htmlspecialchars($borrower['code']) ?></span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?url=borrowers/edit&id=<?= $borrower['id'] ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="index.php?url=borrowers" class="btn btn-custom-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Info Peminjam -->
    <div class="card card-view mb-4">
        <div class="card-body">
            <div class="row g-3 info-item">
                <div class="col-md-3">
                    <div class="label">Nama Lengkap</div>
                    <div class="value"><?= htmlspecialchars($borrower['name']) ?></div>
                </div>
                <div class="col-md-3">
                    <div class="label">Tipe</div>
                    <div class="value">
                        <?php 
                        $typeLabels = [
                            'internal' => 'Internal',
                            'external' => 'External',
                            'student' => 'Student',
                            'employee' => 'Employee',
                            'other' => 'Other'
                        ];
                        $type = $borrower['type'] ?? 'other';
                        ?>
                        <span class="badge-type <?= $type ?>">
                            <?= $typeLabels[$type] ?? $type ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="label">Status</div>
                    <div class="value">
                        <?php if ($borrower['is_active'] == 1): ?>
                        <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                        <span class="badge bg-danger">Nonaktif</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="label">Total Peminjaman</div>
                    <div class="value"><span class="badge bg-primary"><?= $totalLoans ?></span></div>
                </div>
                <div class="col-md-6">
                    <div class="label">Instansi</div>
                    <div class="value"><?= htmlspecialchars($borrower['institution'] ?? '-') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="label">Telepon</div>
                    <div class="value"><?= htmlspecialchars($borrower['phone'] ?? '-') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="label">Email</div>
                    <div class="value"><?= htmlspecialchars($borrower['email'] ?? '-') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="label">NIK / NIM / NIP</div>
                    <div class="value"><?= htmlspecialchars($borrower['identity_number'] ?? '-') ?></div>
                </div>
                <div class="col-12">
                    <div class="label">Alamat</div>
                    <div class="value"><?= nl2br(htmlspecialchars($borrower['address'] ?? '-')) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Peminjaman -->
    <?php if (!empty($loans)): ?>
    <div class="card card-view">
        <div class="card-body">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-history text-primary me-2"></i>Riwayat Peminjaman (10 Terakhir)
            </h5>
            <div class="table-responsive">
                <table class="table table-loans">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Total Item</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td><?= htmlspecialchars($loan['code']) ?></td>
                            <td><?= formatDate($loan['loan_date']) ?></td>
                            <td><?= formatDate($loan['expected_return_date']) ?></td>
                            <td><?= $loan['total_items'] ?></td>
                            <td><?= getStatusBadge($loan['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="card card-view">
        <div class="card-body text-center py-4">
            <i class="fas fa-inbox fa-3x text-muted d-block mb-3"></i>
            <p class="text-muted">Belum ada riwayat peminjaman</p>
        </div>
    </div>
    <?php endif; ?>
</div>