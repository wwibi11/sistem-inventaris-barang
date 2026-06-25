<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data tidak ditemukan");
}

if (isset($_POST['update'])) {
    if (!empty($_POST['password'])) {
        $sql = "UPDATE users SET nama=?, email=?, password=?, role=? WHERE id=?";
        $pdo->prepare($sql)->execute([
            $_POST['nama'],
            $_POST['email'],
            password_hash($_POST['password'], PASSWORD_DEFAULT),
            $_POST['role'],
            $id
        ]);
    } else {
        $sql = "UPDATE users SET nama=?, email=?, role=? WHERE id=?";
        $pdo->prepare($sql)->execute([
            $_POST['nama'],
            $_POST['email'],
            $_POST['role'],
            $id
        ]);
    }
    echo "
    <script>
        alert('User berhasil diperbarui');
        location='index.php?url=users';
    </script>
    ";
    exit;
}
?>

<style>
.users-edit-container { padding: 10px 0; }

/* Header */
.users-edit-header {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.users-edit-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.users-edit-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.users-edit-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Card Form */
.card-form-users-edit {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-form-users-edit .card-header-custom {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
    background: #f8f9fc;
}

.card-form-users-edit .card-header-custom h6 {
    font-weight: 600;
    color: #1a2634;
    margin: 0;
    font-size: 14px;
}

.card-form-users-edit .card-header-custom h6 i {
    color: #2c6b9e;
    margin-right: 8px;
}

.card-form-users-edit .card-body-custom {
    padding: 22px 24px;
}

.form-group label {
    font-weight: 600;
    color: #4a5568;
    font-size: 12px;
    margin-bottom: 4px;
}

.form-control, .custom-select {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    padding: 10px 14px;
    transition: all 0.2s ease;
    background: #fafbfc;
    height: 44px;
}

.form-control:focus, .custom-select:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

.text-muted-custom {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 4px;
}

.btn {
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 24px;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #2c6b9e;
    border: none;
    color: #ffffff;
}

.btn-primary:hover {
    background: #1f507a;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(44, 107, 158, 0.25);
    color: #ffffff;
}

.btn-secondary {
    background: #f0f4f8;
    border: none;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #e2e8f0;
    color: #1a2634;
}

@media (max-width: 768px) {
    .users-edit-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .card-form-users-edit .card-body-custom {
        padding: 16px;
    }
}
</style>

<div class="users-edit-container">

    <!-- HEADER -->
    <div class="users-edit-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-user-edit"></i>
                Edit User
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Perbarui data pengguna sistem Posyandu Bougenvil Belik
            </div>
        </div>
        <a href="index.php?url=users" style="background: #f0f4f8; color: #4a5568; padding: 10px 20px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-weight: 500; transition: all 0.2s;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- FORM -->
    <div class="card-form-users-edit">
        <div class="card-header-custom">
            <h6>
                <i class="fas fa-user"></i> Form Edit User
            </h6>
        </div>
        <div class="card-body-custom">
            <form method="POST">
                <div class="form-group">
                    <label>Nama <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Email <span style="color: #dc2626;">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password">
                    <div class="text-muted-custom">
                        <i class="fas fa-info-circle"></i> Kosongkan jika tidak ingin mengganti password
                    </div>
                </div>

                <div class="form-group">
                    <label>Role <span style="color: #dc2626;">*</span></label>
                    <select name="role" class="custom-select" required>
                        <option value="admin" <?= $data['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="kader" <?= $data['role'] == 'kader' ? 'selected' : '' ?>>Kader</option>
                        <option value="bidan" <?= $data['role'] == 'bidan' ? 'selected' : '' ?>>Bidan</option>
                    </select>
                </div>

                <hr style="margin: 20px 0;">

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                    <a href="index.php?url=users" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>