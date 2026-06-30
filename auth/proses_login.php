<?php
session_start();
require_once __DIR__ . '/../config/functions.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if (empty($email) || empty($password)) {
    $_SESSION['error'] = 'Email dan password wajib diisi!';
    header("Location: login.php");
    exit;
}

$user = fetchOne("SELECT id, name, username, email, password, role, is_active FROM users WHERE email = ?", [$email]);

if (!$user) {
    $_SESSION['error'] = 'Email tidak ditemukan!';
    header("Location: login.php");
    exit;
}

if ($user['is_active'] != 1) {
    $_SESSION['error'] = 'Akun Anda tidak aktif!';
    header("Location: login.php");
    exit;
}

if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Password salah!';
    header("Location: login.php");
    exit;
}

$_SESSION['user'] = [
    'id' => $user['id'],
    'name' => $user['name'],
    'username' => $user['username'],
    'email' => $user['email'],
    'role' => $user['role']
];

setCurrentUserId($user['id']);
update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

if ($remember) {
    setcookie('remember_email', $email, time() + (86400 * 30), '/');
}

$_SESSION['success'] = 'Selamat datang, ' . $user['name'] . '!';
header("Location: ../index.php?url=dashboard");
exit;