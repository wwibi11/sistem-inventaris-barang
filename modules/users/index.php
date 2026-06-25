<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// HAPUS
// ==========================
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$_GET['hapus']]);
    echo "
    <script>
        alert('User berhasil dihapus');
        location='index.php?url=users';
    </script>
    ";
    exit;
}

// ==========================
// DATA USER
// ==========================
$data = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Hitung total per role
$totalAdmin = 0;
$totalKader = 0;
$totalBidan = 0;
foreach($data as $d) {
    if($d['role'] == 'admin') $totalAdmin++;
    elseif($d['role'] == 'kader') $totalKader++;
    elseif($d['role'] == 'bidan') $totalBidan++;
}
?>

<style>
/* ============================================
   STYLE DASHBOARD USERS
   ============================================ */

.users-container { padding: 10px 0; }

/* Header */
.users-header {
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

.users-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.users-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.users-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Button Tambah */
.btn-tambah-user {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-tambah-user:hover {
    background: #1f507a;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(44, 107, 158, 0.25);
    text-decoration: none;
}

/* Stat Cards */
.stat-card-users {
    background: #ffffff;
    border-radius: 12px;
    padding: 14px 18px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 14px;
}

.stat-card-users:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-card-users .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #ffffff;
    flex-shrink: 0;
}

.stat-card-users .stat-icon.primary { background: #2c6b9e; }
.stat-card-users .stat-icon.success { background: #28a745; }
.stat-card-users .stat-icon.warning { background: #e8a317; }
.stat-card-users .stat-icon.purple { background: #6f42c1; }

.stat-card-users .stat-info .stat-number {
    font-size: 22px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

.stat-card-users .stat-info .stat-label {
    font-size: 11px;
    color: #8a94a6;
}

/* Card Utama */
.card-users {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-users .card-body {
    padding: 20px 22px;
}

/* Search Box */
.search-wrapper-users {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}

.search-box-users {
    position: relative;
    flex: 1;
    max-width: 340px;
}

.search-box-users .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 14px;
}

.search-box-users .form-control {
    padding: 10px 16px 10px 40px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    background: #fafbfc;
    transition: all 0.2s ease;
    height: 44px;
}

.search-box-users .form-control:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

/* Tabel */
.table-users {
    font-size: 13px;
    margin: 0;
    width: 100%;
}

.table-users thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 12px 14px;
    border-bottom: 2px solid #edf2f7;
    white-space: nowrap;
}

.table-users thead th i {
    margin-right: 4px;
    color: #8a94a6;
}

.table-users tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-users tbody tr:hover {
    background: #fafbfc;
}

.table-users tbody tr:last-child td {
    border-bottom: none;
}

/* Info User */
.info-user .nama {
    font-weight: 600;
    color: #1a2634;
    font-size: 14px;
}

/* Badge Role */
.badge-role-user {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-role-user.admin {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-role-user.kader {
    background: #dcfce7;
    color: #15803d;
}

.badge-role-user.bidan {
    background: #fef3c7;
    color: #b45309;
}

/* Aksi Button */
.btn-action-user {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    font-size: 13px;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-action-user.edit {
    background: #fef3c7;
    color: #92400e;
}

.btn-action-user.edit:hover {
    background: #92400e;
    color: #ffffff;
}

.btn-action-user.delete {
    background: #fee2e2;
    color: #b91c1c;
}

.btn-action-user.delete:hover {
    background: #b91c1c;
    color: #ffffff;
}

/* Empty State */
.empty-state-users {
    text-align: center;
    padding: 40px 20px;
}

.empty-state-users i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 12px;
    display: block;
}

.empty-state-users h6 {
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 4px;
}

.empty-state-users p {
    color: #8a94a6;
    font-size: 13px;
}

/* Responsive */
@media (max-width: 768px) {
    .users-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    .btn-tambah-user {
        width: 100%;
        justify-content: center;
    }
    .search-box-users {
        max-width: 100%;
    }
}
</style>

<div class="users-container">

    <!-- HEADER -->
    <div class="users-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-users-cog"></i>
                Data User
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Manajemen pengguna sistem Posyandu Bougenvil Belik
            </div>
        </div>
        <a href="index.php?url=users-create" class="btn-tambah-user">
            <i class="fas fa-plus-circle"></i> Tambah User
        </a>
    </div>

    <!-- STATISTIK -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-users">
                <div class="stat-icon primary"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <div class="stat-number"><?= count($data) ?></div>
                    <div class="stat-label">Total User</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-users">
                <div class="stat-icon purple"><i class="fas fa-user-cog"></i></div>
                <div class="stat-info">
                    <div class="stat-number"><?= $totalAdmin ?></div>
                    <div class="stat-label">Admin</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-users">
                <div class="stat-icon success"><i class="fas fa-user-nurse"></i></div>
                <div class="stat-info">
                    <div class="stat-number"><?= $totalKader ?></div>
                    <div class="stat-label">Kader</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-users">
                <div class="stat-icon warning"><i class="fas fa-user-md"></i></div>
                <div class="stat-info">
                    <div class="stat-number"><?= $totalBidan ?></div>
                    <div class="stat-label">Bidan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card-users">
        <div class="card-body">

            <!-- Search -->
            <div class="search-wrapper-users">
                <div class="search-box-users">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari user...">
                </div>
                <span style="font-size: 12px; color: #8a94a6;">
                    <i class="fas fa-database"></i> <?= count($data) ?> data
                </span>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-users">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th><i class="fas fa-user"></i> Nama</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th><i class="fas fa-user-tag"></i> Role</th>
                            <th><i class="fas fa-calendar-plus"></i> Dibuat</th>
                            <th width="100" class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if(count($data) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach($data as $d): ?>
                            <tr>
                                <td>
                                    <span style="font-weight: 600; color: #8a94a6; font-size: 12px;"><?= $no++ ?></span>
                                </td>
                                <td>
                                    <div class="info-user">
                                        <div class="nama"><?= htmlspecialchars($d['nama']) ?></div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($d['email']) ?></td>
                                <td>
                                    <?php
                                    $class = 'kader';
                                    if($d['role'] == 'admin') $class = 'admin';
                                    if($d['role'] == 'bidan') $class = 'bidan';
                                    ?>
                                    <span class="badge-role-user <?= $class ?>">
                                        <?= ucfirst($d['role']) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                                <td>
                                    <div class="d-flex justify-content-center" style="gap: 4px;">
                                        <a href="index.php?url=users-edit&id=<?= $d['id'] ?>" class="btn-action-user edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?url=users&hapus=<?= $d['id'] ?>" class="btn-action-user delete" 
                                           onclick="return confirm('Yakin ingin menghapus user <?= htmlspecialchars($d['nama']) ?>?')" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state-users">
                                        <i class="fas fa-users"></i>
                                        <h6>Belum Ada Data User</h6>
                                        <p>Klik tombol "Tambah User" untuk menambahkan pengguna baru</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#tableBody tr');
    rows.forEach(function(row) {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>