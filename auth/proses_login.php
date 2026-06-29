<?php
// ============================================
// PROSES LOGIN
// ============================================

session_start();

// Load database connection
require_once '../config/database.php';

// Get input
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validate input
if (empty($email) || empty($password)) {
    $_SESSION['error'] = 'Email dan password wajib diisi!';
    header("Location: login.php");
    exit;
}

try {
    // Get user by email
    $user = fetchOne(
        "SELECT id, name, username, email, password, role, is_active 
         FROM users 
         WHERE email = ?",
        [$email]
    );
    
    // Check if user exists
    if (!$user) {
        $_SESSION['error'] = 'Email tidak ditemukan!';
        header("Location: login.php");
        exit;
    }
    
    // Check if user is active
    if ($user['is_active'] != 1) {
        $_SESSION['error'] = 'Akun Anda tidak aktif. Hubungi administrator!';
        header("Location: login.php");
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Password salah!';
        header("Location: login.php");
        exit;
    }
    
    // ============================================
    // LOGIN SUCCESS
    // ============================================
    
    // Update last login
    update(
        'users',
        ['last_login' => date('Y-m-d H:i:s')],
        'id = ?',
        [$user['id']]
    );
    
    // Set session
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role' => $user['role']
    ];
    
    // Set user ID for triggers
    $_SESSION['current_user_id'] = $user['id'];
    
    // Remember me (cookie)
    if ($remember) {
        setcookie('remember_email', $email, time() + (86400 * 30), '/'); // 30 days
    } else {
        setcookie('remember_email', '', time() - 3600, '/');
    }
    
    // Log activity
    // logActivity('login', 'User login: ' . $user['email']);
    
    // Redirect to dashboard
    $_SESSION['success'] = 'Selamat datang, ' . $user['name'] . '!';
    header("Location: ../index.php?url=dashboard");
    exit;
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    header("Location: login.php");
    exit;
}
?>