<?php
// modules/items/delete.php

// ============================================
// LOAD FUNCTIONS
// ============================================
require_once __DIR__ . '/../../config/functions.php';

// ============================================
// PROSES DELETE
// ============================================

// Redirect jika bukan admin
if (!isAdmin()) {
    $_SESSION['error'] = 'Akses ditolak! Anda tidak memiliki izin.';
    redirect('index.php?url=items');
}

// Ambil ID dari URL
$id = $_GET['id'] ?? 0;
if ($id <= 0) {
    $_SESSION['error'] = 'ID barang tidak valid!';
    redirect('index.php?url=items');
}

// Ambil data barang
$item = fetchOne("SELECT * FROM items WHERE id = ?", [$id]);

if (!$item) {
    $_SESSION['error'] = 'Data barang tidak ditemukan!';
    redirect('index.php?url=items');
}

// Cek apakah barang sedang dipinjam
$isBorrowed = fetchColumn(
    "SELECT COUNT(*) FROM loan_details WHERE item_id = ? AND status = 'dipinjam'",
    [$id]
);

if ($isBorrowed > 0) {
    $_SESSION['error'] = 'Barang sedang dipinjam, tidak dapat dihapus!';
    redirect('index.php?url=items');
}

// Proses hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        // Hapus foto jika ada
        if ($item['photo']) {
            deleteFile($item['photo']);
        }
        
        // Hapus data dari database
        $deleted = delete('items', 'id = ?', [$id]);
        
        if ($deleted) {
            $_SESSION['success'] = 'Barang "' . $item['name'] . '" berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Gagal menghapus data. Silakan coba lagi.';
        }
        
        redirect('index.php?url=items');
        
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        redirect('index.php?url=items');
    }
}
?>

<style>
/* ============================================
   STYLE UNTUK HALAMAN DELETE
   ============================================ */
.card-delete {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.card-delete .card-body {
    padding: 30px 24px;
}

/* Icon */
.icon-delete {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #fee2e2;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.icon-delete i {
    font-size: 36px;
    color: #dc2626;
}

/* Info barang */
.info-barang {
    background: #f8fafc;
    border-radius: 8px;
    padding: 16px 20px;
    margin: 16px 0;
}

.info-barang .label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-barang .value {
    font-size: 15px;
    font-weight: 500;
    color: #1e293b;
}

/* Alert */
.alert-custom {
    border-radius: 10px;
    border: none;
    padding: 12px 18px;
    font-size: 13px;
}

.alert-custom.alert-success {
    background: #dcfce7;
    color: #166534;
}

.alert-custom.alert-danger {
    background: #fee2e2;
    color: #991b1b;
}

.alert-custom.alert-warning {
    background: #fef3c7;
    color: #92400e;
}

/* Button */
.btn-custom {
    border-radius: 8px;
    padding: 8px 24px;
    font-size: 13px;
    font-weight: 500;
}

.btn-custom-danger {
    background: #dc2626;
    color: #fff;
    border: none;
}

.btn-custom-danger:hover {
    background: #b91c1c;
    color: #fff;
}

.btn-custom-secondary {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.btn-custom-secondary:hover {
    background: #e2e8f0;
    color: #1e293b;
}

/* Responsive */
@media (max-width: 768px) {
    .card-delete .card-body {
        padding: 20px 16px;
    }
    .icon-delete {
        width: 64px;
        height: 64px;
    }
    .icon-delete i {
        font-size: 28px;
    }
}
</style>

<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <!-- ============================================
            KONFIRMASI HAPUS
            ============================================ -->
            <div class="card card-delete">
                <div class="card-body text-center">
                    
                    <!-- Icon -->
                    <div class="icon-delete">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    
                    <!-- Judul -->
                    <h4 class="fw-bold text-dark mb-2">Konfirmasi Hapus</h4>
                    <p class="text-muted mb-3">
                        Apakah Anda yakin ingin menghapus data barang ini?
                        <br>
                        <span class="text-danger fw-bold">Tindakan ini tidak dapat dibatalkan!</span>
                    </p>
                    
                    <!-- Info Barang -->
                    <div class="info-barang text-start">
                        <div class="row">
                            <div class="col-6">
                                <div class="label">Kode Barang</div>
                                <div class="value"><?= htmlspecialchars($item['code']) ?></div>
                            </div>
                            <div class="col-6">
                                <div class="label">Kategori</div>
                                <div class="value">
                                    <?php 
                                    $category = fetchOne("SELECT name FROM categories WHERE id = ?", [$item['category_id']]);
                                    echo htmlspecialchars($category['name'] ?? '-');
                                    ?>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="label">Nama Barang</div>
                                <div class="value"><?= htmlspecialchars($item['name']) ?></div>
                            </div>
                            <div class="col-6 mt-2">
                                <div class="label">Stok</div>
                                <div class="value"><?= $item['quantity'] ?></div>
                            </div>
                            <div class="col-6 mt-2">
                                <div class="label">Kondisi</div>
                                <div class="value"><?= ucfirst($item['condition']) ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Peringatan -->
                    <?php if ($item['photo']): ?>
                    <div class="alert alert-custom alert-warning text-start mb-3">
                        <i class="fas fa-image me-2"></i> 
                        Foto barang juga akan dihapus secara permanen.
                    </div>
                    <?php endif; ?>
                    
                    <!-- Form Konfirmasi -->
                    <form method="POST">
                        <input type="hidden" name="confirm" value="1">
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <button type="submit" class="btn btn-custom btn-custom-danger">
                                <i class="fas fa-trash me-1"></i> Ya, Hapus
                            </button>
                            <a href="index.php?url=items" class="btn btn-custom btn-custom-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                    
                </div>
            </div>
            
            <!-- Link Kembali -->
            <div class="text-center mt-3">
                <a href="index.php?url=items" class="text-muted text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Barang
                </a>
            </div>
            
        </div>
    </div>
</div>