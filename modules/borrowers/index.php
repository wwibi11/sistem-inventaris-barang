<?php
// modules/borrowers/index.php

require_once __DIR__ . '/../../config/functions.php';

// Redirect jika bukan admin
if (!isAdmin()) {
    $_SESSION['error'] = 'Akses ditolak! Anda tidak memiliki izin.';
    redirect('index.php?url=dashboard');
}

// Pagination
$page = $_GET['page'] ?? 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Search
$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(name LIKE ? OR code LIKE ? OR institution LIKE ? OR phone LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($type) {
    $where[] = "`type` = ?";
    $params[] = $type;
}

$whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

// Get total
$total = fetchColumn("SELECT COUNT(*) FROM borrowers $whereClause", $params);

// Get borrowers
$borrowers = fetchAll(
    "SELECT * FROM borrowers 
     $whereClause 
     ORDER BY name ASC 
     LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $offset])
);

// Flash messages
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<style>
/* ============================================
   STYLE UNTUK HALAMAN BORROWERS
   ============================================ */
.card-borrowers {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.card-borrowers .card-body {
    padding: 16px 20px;
}

/* Table */
.table-borrowers {
    margin-bottom: 0;
    font-size: 13px;
}

.table-borrowers thead th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 14px;
    border-bottom: 2px solid #eef2f7;
    white-space: nowrap;
}

.table-borrowers tbody td {
    padding: 12px 14px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
}

.table-borrowers tbody tr:hover {
    background: #f8fafc;
}

.table-borrowers tbody tr:last-child td {
    border-bottom: none;
}

/* Badge */
.badge-code {
    background: #1e293b !important;
    color: #ffffff !important;
    font-size: 12px;
    padding: 4px 12px;
    border-radius: 6px;
    font-family: 'Courier New', monospace;
    font-weight: 600;
    letter-spacing: 0.5px;
    display: inline-block;
}

.badge-type {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-type.internal { background: #dbeafe; color: #1e40af; }
.badge-type.external { background: #fef3c7; color: #92400e; }
.badge-type.student { background: #dcfce7; color: #166534; }
.badge-type.employee { background: #e0e7ff; color: #3730a3; }
.badge-type.other { background: #f1f5f9; color: #475569; }

/* Alert */
.alert-custom {
    border-radius: 10px;
    border: none;
    padding: 12px 18px;
    font-size: 13px;
    border-left: 4px solid transparent;
}

.alert-custom.alert-success {
    background: #dcfce7 !important;
    color: #166534 !important;
    border-left-color: #22c55e !important;
}

.alert-custom.alert-danger {
    background: #fee2e2 !important;
    color: #991b1b !important;
    border-left-color: #dc2626 !important;
}

/* Tombol aksi */
.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    transition: all 0.2s;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
}

.btn-action:hover {
    transform: translateY(-1px);
    text-decoration: none;
}

.btn-action.view { background: #dbeafe; color: #1e40af; }
.btn-action.view:hover { background: #bfdbfe; color: #1e40af; }

.btn-action.edit { background: #fef3c7; color: #92400e; }
.btn-action.edit:hover { background: #fde68a; color: #92400e; }

.btn-action.delete { background: #fee2e2; color: #991b1b; }
.btn-action.delete:hover { background: #fecaca; color: #991b1b; }

/* Pagination */
.pagination-custom .page-item .page-link {
    color: #475569;
    border: none;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 13px;
    margin: 0 2px;
}

.pagination-custom .page-item.active .page-link {
    background: #2563eb;
    color: #fff;
}

.pagination-custom .page-item .page-link:hover {
    background: #f1f5f9;
    color: #2563eb;
}

.pagination-custom .page-item.active .page-link:hover {
    background: #2563eb;
    color: #fff;
}

/* Form filter */
.form-filter .form-control,
.form-filter .form-select {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 8px 12px;
    font-size: 13px;
    background: #fafbfc;
}

.form-filter .form-control:focus,
.form-filter .form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    background: #ffffff;
}

.btn-filter {
    border-radius: 8px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .table-borrowers thead th {
        font-size: 10px;
        padding: 8px 10px;
    }
    .table-borrowers tbody td {
        padding: 8px 10px;
        font-size: 12px;
    }
    .btn-action {
        width: 28px;
        height: 28px;
        font-size: 11px;
    }
}
</style>

<div class="container-fluid px-4">
    <!-- ============================================
    HEADER
    ============================================ -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark">
                <i class="fas fa-users text-primary me-2"></i>Manajemen Peminjam
            </h4>
            <p class="text-muted small mt-1">Kelola semua data peminjam barang</p>
        </div>
        <a href="index.php?url=borrowers/create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah Peminjam
        </a>
    </div>

    <!-- ============================================
    ALERT
    ============================================ -->
    <?php if ($success): ?>
    <div class="alert alert-custom alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-custom alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- ============================================
    SEARCH & FILTER
    ============================================ -->
    <div class="card card-borrowers mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end form-filter">
                <input type="hidden" name="url" value="borrowers">
                
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Cari</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari nama/kode/instansi..." value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Tipe</label>
                    <select name="type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="internal" <?= $type == 'internal' ? 'selected' : '' ?>>Internal</option>
                        <option value="external" <?= $type == 'external' ? 'selected' : '' ?>>External</option>
                        <option value="student" <?= $type == 'student' ? 'selected' : '' ?>>Student</option>
                        <option value="employee" <?= $type == 'employee' ? 'selected' : '' ?>>Employee</option>
                        <option value="other" <?= $type == 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-filter">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    <a href="index.php?url=borrowers" class="btn btn-outline-secondary btn-filter">
                        <i class="fas fa-sync me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================
    TABLE
    ============================================ -->
    <div class="card card-borrowers">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borrowers">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Instansi</th>
                            <th>Telepon</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($borrowers)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted d-block mb-3"></i>
                                <p class="text-muted mb-2">Belum ada data peminjam</p>
                                <a href="index.php?url=borrowers/create" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Tambah Peminjam
                                </a>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($borrowers as $borrower): ?>
                        <tr>
                            <td>
                                <span class="badge-code"><?= htmlspecialchars($borrower['code']) ?></span>
                            </td>
                            <td class="fw-medium"><?= htmlspecialchars($borrower['name']) ?></td>
                            <td><?= htmlspecialchars($borrower['institution'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($borrower['phone'] ?? '-') ?></td>
                            <td>
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
                            </td>
                            <td>
                                <?php if ($borrower['is_active'] == 1): ?>
                                <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="index.php?url=borrowers/view&id=<?= $borrower['id'] ?>" 
                                       class="btn-action view" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?url=borrowers/edit&id=<?= $borrower['id'] ?>" 
                                       class="btn-action edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?url=borrowers/delete&id=<?= $borrower['id'] ?>" 
                                       class="btn-action delete" 
                                       title="Hapus"
                                       onclick="return confirm('Yakin ingin menghapus peminjam <?= htmlspecialchars($borrower['name']) ?>?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ============================================
            PAGINATION
            ============================================ -->
            <?php if ($total > $perPage): ?>
            <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top">
                <span class="text-muted small">
                    Menampilkan <?= $offset + 1 ?> - <?= min($offset + $perPage, $total) ?> 
                    dari <?= $total ?> data
                </span>
                <nav>
                    <ul class="pagination pagination-custom mb-0">
                        <?php
                        $totalPages = ceil($total / $perPage);
                        $queryParams = http_build_query(['search' => $search, 'type' => $type]);
                        ?>
                        
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="index.php?url=borrowers&page=<?= $page - 1 ?>&<?= $queryParams ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php 
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        ?>
                        
                        <?php if ($startPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="index.php?url=borrowers&page=1&<?= $queryParams ?>">1</a>
                        </li>
                        <?php if ($startPage > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?url=borrowers&page=<?= $i ?>&<?= $queryParams ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="index.php?url=borrowers&page=<?= $totalPages ?>&<?= $queryParams ?>">
                                <?= $totalPages ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="index.php?url=borrowers&page=<?= $page + 1 ?>&<?= $queryParams ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>