<?php
// auth/proses_login.php

session_start();

// Load database
require_once __DIR__ . '/../config/database.php';

// Debug: tampilkan error jika ada
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get input
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validate
if (empty($email) || empty($password)) {
    $_SESSION['error'] = 'Email dan password wajib diisi!';
    header("Location: login.php");
    exit;
}

try {
    // Get user
    $user = fetchOne(
        "SELECT id, name, username, email, password, role, is_active 
         FROM users 
         WHERE email = ?",
        [$email]
    );
    
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
    
    // Login success
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role' => $user['role']
    ];
    
    // Update last login
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    if ($remember) {
        setcookie('remember_email', $email, time() + (86400 * 30), '/');
    }
    
    $_SESSION['success'] = 'Selamat datang, ' . $user['name'] . '!';
    header("Location: ../index.php?url=dashboard");
    exit;
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
    header("Location: login.php");
    exit;
}
?>