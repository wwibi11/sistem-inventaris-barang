<?php
require_once __DIR__ . '/../../config/database.php';

// ==========================
// HAPUS
// ==========================
if (isset($_GET['hapus'])) {

    $stmt = $pdo->prepare("
        DELETE FROM users
        WHERE id=?
    ");

    $stmt->execute([
        $_GET['hapus']
    ]);

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
$data = $pdo->query("
    SELECT *
    FROM users
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<style>

.card-modern{
    border:none;
    border-radius:16px;
    overflow:hidden;
}

.shadow-soft{
    box-shadow:0 4px 18px rgba(0,0,0,.05);
}

.table-modern{
    margin-bottom:0;
}

.table-modern thead{
    background:#4e73df;
    color:white;
}

.table-modern th{
    border:none !important;
    padding:12px 10px !important;
    font-size:11px;
}

.table-modern td{
    padding:12px 10px !important;
    vertical-align:middle !important;
    font-size:12px;
}

.table-modern tbody tr:hover{
    background:#f8faff;
}

.btn{
    border-radius:10px !important;
    font-size:12px;
}

.btn-icon{
    width:32px;
    height:32px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.badge-role{
    padding:6px 10px;
    border-radius:20px;
    font-size:10px;
    font-weight:700;
}

.role-admin{
    background:#dbeafe;
    color:#1d4ed8;
}

.role-kader{
    background:#dcfce7;
    color:#15803d;
}

.role-bidan{
    background:#fef3c7;
    color:#b45309;
}

.info-name{
    font-weight:700;
    color:#2e3a59;
}

.small-text{
    font-size:11px;
}

</style>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h1 class="h5 mb-1 text-gray-800">
                <i class="fas fa-users text-primary"></i>
                Data User
            </h1>

            <div class="text-muted small-text">
                Manajemen pengguna sistem
            </div>

        </div>

        <a href="index.php?url=users-create"
           class="btn btn-primary shadow-sm">

            <i class="fas fa-plus mr-1"></i>
            Tambah User

        </a>

    </div>

    <div class="card card-modern shadow-soft">

        <div class="card-body">

            <!-- SEARCH -->
            <div class="mb-3" style="max-width:300px;">

                <div class="input-group">

                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>

                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Cari user...">

                </div>

            </div>

            <!-- TABLE -->
            <div class="table-responsive">

                <table class="table table-hover table-modern">

                    <thead>

                        <tr>
                            <th width="60">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th width="120">Aksi</th>
                        </tr>

                    </thead>

                    <tbody id="tableBody">

                        <?php
                        $no = 1;
                        foreach($data as $d):
                        ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td>

                                <div class="info-name">
                                    <?= htmlspecialchars($d['nama']) ?>
                                </div>

                            </td>

                            <td>

                                <?= htmlspecialchars($d['email']) ?>

                            </td>

                            <td>

                                <?php

                                $class = 'role-kader';

                                if($d['role'] == 'admin'){
                                    $class = 'role-admin';
                                }

                                if($d['role'] == 'bidan'){
                                    $class = 'role-bidan';
                                }

                                ?>

                                <span class="badge-role <?= $class ?>">
                                    <?= ucfirst($d['role']) ?>
                                </span>

                            </td>

                            <td>

                                <?= date(
                                    'd-m-Y',
                                    strtotime($d['created_at'])
                                ) ?>

                            </td>

                            <td>

                                <div class="d-flex">

                                    <a href="index.php?url=users-edit&id=<?= $d['id'] ?>"
                                       class="btn btn-warning btn-sm btn-icon mr-1">

                                        <i class="fas fa-edit"></i>

                                    </a>

                                    <a href="index.php?url=users&hapus=<?= $d['id'] ?>"
                                       class="btn btn-danger btn-sm btn-icon"
                                       onclick="return confirm('Yakin hapus user ini?')">

                                        <i class="fas fa-trash"></i>

                                    </a>

                                </div>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<script>

document.getElementById('searchInput')
.addEventListener('keyup', function(){

    let filter = this.value.toLowerCase();

    let rows =
        document.querySelectorAll('#tableBody tr');

    rows.forEach(row => {

        let text =
            row.innerText.toLowerCase();

        row.style.display =
            text.includes(filter)
            ? ''
            : 'none';

    });

});

</script>