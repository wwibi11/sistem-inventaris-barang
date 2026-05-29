<?php
session_start();
require '../config/database.php'; // koneksi PDO kamu

$email = $_POST['email'];
$password = $_POST['password'];

// ambil user
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {

    // cek password
    if (password_verify($password, $user['password'])) {

        // simpan session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nama' => $user['nama'],
            'role' => $user['role']
        ];

        header("Location: /index.php");
        exit;

    } else {
        $_SESSION['error'] = "Password salah!";
    }

} else {
    $_SESSION['error'] = "Email tidak ditemukan!";
}

header("Location: auth/login.php");
exit;