<?php
// modules/borrowers/create.php

ob_start();

require_once __DIR__ . '/../../config/functions.php';

// Redirect jika bukan admin
if (!isAdmin()) {
    $_SESSION['error'] = 'Akses ditolak! Anda tidak memiliki izin.';
    redirect('index.php?url=dashboard');
}

$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
$old = $_SESSION['old'] ?? [];
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['old']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $identity_number = trim($_POST['identity_number'] ?? '');
    $institution = trim($_POST['institution'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $type = $_POST['type'] ?? 'internal';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validasi
    $errors = [];
    if (empty($name)) $errors[] = 'Nama peminjam wajib diisi';
    if (!empty($email) && !isValidEmail($email)) $errors[] = 'Format email tidak valid';
    if (!empty($phone) && !isValidPhone($phone)) $errors[] = 'Format nomor telepon tidak valid';
    
    if (empty($errors)) {
        $code = generateBorrowerCode();
        
        $id = insert('borrowers', [
            'code' => $code,
            'name' => $name,
            'identity_number' => $identity_number,
            'institution' => $institution,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'type' => $type,
            'is_active' => $is_active
        ]);
        
        if ($id) {
            $_SESSION['success'] = 'Peminjam "' . $name . '" berhasil ditambahkan! Kode: ' . $code;
            redirect('index.php?url=borrowers');
        } else {
            $_SESSION['error'] = 'Gagal menyimpan data. Silakan coba lagi.';
            redirect('index.php?url=borrowers/create');
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
        $_SESSION['old'] = $_POST;
        redirect('index.php?url=borrowers/create');
    }
}

ob_end_flush();
?>

<style>
.card-form {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.card-form .card-body {
    padding: 24px 20px;
}

.form-label-custom {
    font-weight: 600;
    font-size: 12px;
    color: #475569;
    margin-bottom: 4px;
    display: block;
}

.form-label-custom .required {
    color: #dc2626;
}

.form-control-custom,
.form-select-custom {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 8px 12px;
    font-size: 13px;
    background: #fafbfc;
    transition: all 0.2s;
    width: 100%;
}

.form-control-custom:focus,
.form-select-custom:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    background: #ffffff;
    outline: none;
}

.form-text-custom {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 4px;
}

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

.btn-custom {
    border-radius: 8px;
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 500;
}

.btn-custom-primary {
    background: #2563eb;
    color: #fff;
    border: none;
}

.btn-custom-primary:hover {
    background: #1d4ed8;
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
</style>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark">
                <i class="fas fa-user-plus text-success me-2"></i>Tambah Peminjam
            </h4>
            <p class="text-muted small mt-1">Tambahkan data peminjam baru</p>
        </div>
        <a href="index.php?url=borrowers" class="btn btn-custom btn-custom-secondary btn-sm">
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
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label-custom">
                                Nama Lengkap <span class="required">*</span>
                            </label>
                            <input type="text" name="name" class="form-control-custom" 
                                   value="<?= htmlspecialchars($old['name'] ?? '') ?>" 
                                   placeholder="Masukkan nama lengkap" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label-custom">NIK / NIM / NIP</label>
                            <input type="text" name="identity_number" class="form-control-custom" 
                                   value="<?= htmlspecialchars($old['identity_number'] ?? '') ?>" 
                                   placeholder="Masukkan nomor identitas">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label-custom">Instansi</label>
                            <input type="text" name="institution" class="form-control-custom" 
                                   value="<?= htmlspecialchars($old['institution'] ?? '') ?>" 
                                   placeholder="Contoh: SMA Negeri 1, PT Maju Jaya">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label-custom">Telepon</label>
                            <input type="text" name="phone" class="form-control-custom" 
                                   value="<?= htmlspecialchars($old['phone'] ?? '') ?>" 
                                   placeholder="Contoh: 081234567890">
                            <div class="form-text-custom">Format: 08xxxxxxxxxx</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label-custom">Email</label>
                            <input type="email" name="email" class="form-control-custom" 
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                                   placeholder="contoh@email.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label-custom">Tipe <span class="required">*</span></label>
                            <select name="type" class="form-select-custom" required>
                                <option value="internal" <?= ($old['type'] ?? '') == 'internal' ? 'selected' : '' ?>>Internal</option>
                                <option value="external" <?= ($old['type'] ?? '') == 'external' ? 'selected' : '' ?>>External</option>
                                <option value="student" <?= ($old['type'] ?? '') == 'student' ? 'selected' : '' ?>>Student</option>
                                <option value="employee" <?= ($old['type'] ?? '') == 'employee' ? 'selected' : '' ?>>Employee</option>
                                <option value="other" <?= ($old['type'] ?? '') == 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label-custom">Alamat</label>
                            <textarea name="address" class="form-control-custom" rows="2" 
                                      placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                   <?= isset($old['is_active']) ? 'checked' : 'checked' ?>>
                            <label class="form-check-label" for="is_active">Aktif</label>
                            <div class="form-text-custom">Nonaktifkan jika peminjam tidak boleh meminjam</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <hr>
                        <button type="submit" class="btn btn-custom btn-custom-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                        <a href="index.php?url=borrowers" class="btn btn-custom btn-custom-secondary">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>