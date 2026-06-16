<?php
require_once __DIR__ . '/../../config/database.php';

if (isset($_POST['simpan'])) {

    $stmt = $pdo->prepare("
        INSERT INTO users
        (
            nama,
            email,
            password,
            role
        )
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['nama'],
        $_POST['email'],
        password_hash($_POST['password'], PASSWORD_DEFAULT),
        $_POST['role']
    ]);

    echo "
    <script>
        alert('User berhasil ditambahkan');
        location='index.php?url=user';
    </script>
    ";
}
?>

<div class="container-fluid">

    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Tambah User
            </h6>
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text"
                           name="nama"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Role</label>

                    <select name="role"
                            class="form-control"
                            required>

                        <option value="admin">Admin</option>
                        <option value="kader">Kader</option>
                        <option value="bidan">Bidan</option>

                    </select>

                </div>

                <button type="submit"
                        name="simpan"
                        class="btn btn-primary">

                    Simpan

                </button>

                <a href="index.php?url=users"
                   class="btn btn-secondary">

                    Kembali

                </a>

            </form>

        </div>

    </div>

</div>