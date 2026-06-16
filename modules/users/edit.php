<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
");

$stmt->execute([$id]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data tidak ditemukan");
}

if (isset($_POST['update'])) {

    if (!empty($_POST['password'])) {

        $sql = "
            UPDATE users
            SET
                nama=?,
                email=?,
                password=?,
                role=?
            WHERE id=?
        ";

        $pdo->prepare($sql)->execute([
            $_POST['nama'],
            $_POST['email'],
            password_hash($_POST['password'], PASSWORD_DEFAULT),
            $_POST['role'],
            $id
        ]);

    } else {

        $sql = "
            UPDATE users
            SET
                nama=?,
                email=?,
                role=?
            WHERE id=?
        ";

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
        location='index.php?url=user';
    </script>
    ";
}
?>

<div class="container-fluid">

    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Edit User
            </h6>
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text"
                           name="nama"
                           class="form-control"
                           value="<?= htmlspecialchars($data['nama']) ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="<?= htmlspecialchars($data['email']) ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password"
                           name="password"
                           class="form-control">

                    <small class="text-muted">
                        Kosongkan jika tidak ingin mengganti password
                    </small>
                </div>

                <div class="form-group">

                    <label>Role</label>

                    <select name="role"
                            class="form-control">

                        <option value="admin"
                            <?= $data['role']=='admin'?'selected':'' ?>>
                            Admin
                        </option>

                        <option value="kader"
                            <?= $data['role']=='kader'?'selected':'' ?>>
                            Kader
                        </option>

                        <option value="bidan"
                            <?= $data['role']=='bidan'?'selected':'' ?>>
                            Bidan
                        </option>

                    </select>

                </div>

                <button type="submit"
                        name="update"
                        class="btn btn-primary">

                    Update

                </button>

                <a href="index.php?url=users"
                   class="btn btn-secondary">

                    Kembali

                </a>

            </form>

        </div>

    </div>

</div>