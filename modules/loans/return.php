<?php
// modules/loans/return.php

ob_start();

require_once __DIR__ . '/../../config/functions.php';

$id = $_GET['id'] ?? 0;
if ($id <= 0) {
    $_SESSION['error'] = 'ID peminjaman tidak valid!';
    redirect('index.php?url=loans');
}

$loan = fetchOne("SELECT * FROM loans WHERE id = ?", [$id]);

if (!$loan) {
    $_SESSION['error'] = 'Data peminjaman tidak ditemukan!';
    redirect('index.php?url=loans');
}

if ($loan['status'] == 'dikembalikan') {
    $_SESSION['error'] = 'Peminjaman sudah dikembalikan!';
    redirect('index.php?url=loans');
}

$details = fetchAll(
    "SELECT ld.*, i.name as item_name, i.code as item_code, i.quantity as current_stock
     FROM loan_details ld
     LEFT JOIN items i ON ld.item_id = i.id
     WHERE ld.loan_id = ? AND ld.status = 'dipinjam'",
    [$id]
);

if (empty($details)) {
    $_SESSION['error'] = 'Tidak ada barang yang perlu dikembalikan!';
    redirect('index.php?url=loans');
}

$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $return_date = $_POST['return_date'] ?? date('Y-m-d');
    $notes = trim($_POST['notes'] ?? '');
    $conditions = $_POST['condition'] ?? [];
    
    try {
        beginTransaction();
        
        $return_code = generateReturnCode();
        
        // Insert ke tabel returns
        $return_id = insert('returns', [
            'code' => $return_code,
            'loan_id' => $id,
            'return_date' => $return_date,
            'total_items' => count($details),
            'received_by' => currentUserId(),
            'notes' => $notes
        ]);
        
        if (!$return_id) {
            throw new Exception('Gagal menyimpan data pengembalian!');
        }
        
        // Insert ke tabel return_details
        foreach ($details as $d) {
            $condition = $conditions[$d['id']] ?? 'baik';
            
            $detail_id = insert('return_details', [
                'return_id' => $return_id,
                'loan_detail_id' => $d['id'],
                'item_id' => $d['item_id'],
                'quantity' => $d['quantity'],
                'condition' => $condition
            ]);
            
            if (!$detail_id) {
                throw new Exception('Gagal menyimpan detail pengembalian!');
            }
            
            // ============================================
            // PERBAIKAN: UPDATE STOK BARANG
            // ============================================
            // Ambil stok saat ini
            $current_stock = fetchColumn("SELECT quantity FROM items WHERE id = ?", [$d['item_id']]);
            $new_stock = $current_stock + $d['quantity'];
            
            // Update dengan nilai integer
            $updated = updateData('items', [
                'quantity' => $new_stock,
                'status' => 'tersedia'
            ], 'id', $d['item_id']);
            
            // Update loan detail
            updateData('loan_details', [
                'status' => 'dikembalikan',
                'returned_quantity' => $d['quantity'],
                'condition_after' => $condition
            ], 'id', $d['id']);
        }
        
        // Update loan status
        updateData('loans', [
            'status' => 'dikembalikan',
            'actual_return_date' => $return_date
        ], 'id', $id);
        
        commit();
        
        $_SESSION['success'] = 'Pengembalian berhasil! Kode: ' . $return_code;
        redirect('index.php?url=loans/detail&id=' . $id);
        
    } catch (Exception $e) {
        try {
            rollback();
        } catch (Exception $e2) {
            // Silent fail
        }
        
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        redirect('index.php?url=loans/return&id=' . $id);
    }
}

ob_end_flush();
?>

<!-- ============================================
STYLE (SAMA SEPERTI SEBELUMNYA)
============================================ -->
<style>
.card-form { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.card-form .card-body { padding: 24px 20px; }
.card-form .card-header { background: #fff; border-bottom: 1px solid #eef2f7; padding: 16px 20px; border-radius: 12px 12px 0 0 !important; }

.form-label-custom { font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 4px; display: block; }
.form-control-custom, .form-select-custom {
    border-radius: 8px; border: 1px solid #e2e8f0; padding: 8px 12px; font-size: 13px;
    background: #fafbfc; transition: all 0.2s; width: 100%;
}
.form-control-custom:focus, .form-select-custom:focus {
    border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fff; outline: none;
}

.btn-custom { border-radius: 8px; padding: 8px 20px; font-size: 13px; font-weight: 500; }
.btn-custom-success { background: #22c55e; color: #fff; border: none; }
.btn-custom-success:hover { background: #16a34a; color: #fff; }
.btn-custom-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.btn-custom-secondary:hover { background: #e2e8f0; color: #1e293b; }

.alert-custom { border-radius: 10px; border: none; padding: 12px 18px; font-size: 13px; border-left: 4px solid transparent; }
.alert-custom.alert-success { background: #dcfce7 !important; color: #166534 !important; border-left-color: #22c55e !important; }
.alert-custom.alert-danger { background: #fee2e2 !important; color: #991b1b !important; border-left-color: #dc2626 !important; }

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

.table-return { font-size: 13px; }
.table-return thead th { background: #f8fafc; color: #475569; font-size: 11px; text-transform: uppercase; padding: 10px 14px; border-bottom: 2px solid #eef2f7; }
.table-return tbody td { padding: 10px 14px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
</style>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark">
                <i class="fas fa-undo-alt text-success me-2"></i>Proses Pengembalian
            </h4>
            <p class="text-muted small mt-1">
                Peminjaman: <span class="badge-code"><?= htmlspecialchars($loan['code']) ?></span>
            </p>
        </div>
        <a href="index.php?url=loans/detail&id=<?= $id ?>" class="btn btn-custom btn-custom-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Alert -->
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

    <!-- Form -->
    <div class="card card-form">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label-custom">Tanggal Kembali</label>
                            <input type="date" name="return_date" class="form-control-custom" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label-custom">Catatan</label>
                            <input type="text" name="notes" class="form-control-custom" placeholder="Catatan pengembalian...">
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mt-3 mb-2"><i class="fas fa-list text-primary me-2"></i>Daftar Barang yang Dikembalikan</h6>
                <div class="table-responsive">
                    <table class="table table-return">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Kondisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['item_code']) ?></td>
                                <td><?= htmlspecialchars($d['item_name']) ?></td>
                                <td><?= $d['quantity'] ?></td>
                                <td>
                                    <select name="condition[<?= $d['id'] ?>]" class="form-select-custom" style="width:150px;">
                                        <option value="baik">Baik</option>
                                        <option value="rusak">Rusak</option>
                                        <option value="perbaikan">Perbaikan</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-custom btn-custom-success">
                    <i class="fas fa-check me-1"></i> Konfirmasi Pengembalian
                </button>
                <a href="index.php?url=loans/detail&id=<?= $id ?>" class="btn btn-custom btn-custom-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>