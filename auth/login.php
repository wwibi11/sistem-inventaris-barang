<?php
session_start();
// Load functions untuk flash message jika diperlukan
require_once __DIR__ . '/../config/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris Barang</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e8f0fe 0%, #d4e4f7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container { max-width: 440px; width: 100%; }
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 50px 40px 40px;
            box-shadow: 0 20px 60px rgba(44,107,158,0.15);
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2c6b9e, #4a90d9, #2c6b9e);
            background-size: 200% 100%;
            animation: grad 3s ease infinite;
        }
        @keyframes grad { 0%,100%{background-position:0% 50%;} 50%{background-position:100% 50%;} }
        .login-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #2c6b9e, #4a90d9);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 25px rgba(44,107,158,0.3);
        }
        .login-icon i { font-size: 36px; color: #fff; }
        .login-header { text-align: center; margin-bottom: 32px; }
        .app-badge {
            display: inline-block;
            background: #e8f0fe;
            color: #2c6b9e;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 16px;
            border-radius: 20px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .login-header h3 {
            color: #1a2634;
            font-weight: 800;
            font-size: 24px;
            margin-bottom: 6px;
        }
        .login-header .sub-title { color: #6b7a8f; font-size: 14px; }
        .divider-line {
            width: 50px; height: 3px;
            background: linear-gradient(90deg, #2c6b9e, #4a90d9);
            margin: 14px auto 0;
            border-radius: 2px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { font-size: 13px; font-weight: 600; color: #4a5568; display: block; margin-bottom: 6px; }
        .form-control {
            height: 50px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0 16px;
            font-size: 14px;
            width: 100%;
            transition: all 0.3s;
            background: #fafbfc;
        }
        .form-control:focus {
            border-color: #2c6b9e;
            box-shadow: 0 0 0 4px rgba(44,107,158,0.1);
            outline: none;
            background: #fff;
        }
        .input-group-icon { position: relative; }
        .input-group-icon .form-control { padding-left: 48px; }
        .input-group-icon .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
        }
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .form-options .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #6b7280;
            cursor: pointer;
        }
        .form-options .remember-me input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: #2c6b9e;
            margin: 0;
        }
        .form-options .forgot-link {
            font-size: 13px;
            color: #2c6b9e;
            text-decoration: none;
        }
        .btn-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #2c6b9e, #4a90d9);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.3s;
            cursor: pointer;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(44,107,158,0.35);
        }
        .btn-login i { margin-right: 8px; }
        .alert {
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-danger { background: #fef2f2; color: #b91c1c; border-left: 4px solid #dc2626; }
        .alert-success { background: #f0fdf4; color: #166534; border-left: 4px solid #22c55e; }
        .demo-credentials {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px 20px;
            margin-top: 20px;
            border: 1px dashed #d1d5db;
        }
        .demo-credentials .demo-title {
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            color: #1a2634;
            margin-bottom: 8px;
        }
        .demo-credentials .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 12px;
            border-bottom: 1px solid #edf2f7;
        }
        .demo-credentials .cred-row:last-child { border-bottom: none; }
        .demo-credentials .cred-row .value {
            color: #1a2634;
            font-weight: 500;
            font-family: monospace;
            font-size: 11px;
        }
        .role-badge {
            display: inline-block;
            padding: 1px 10px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }
        .role-badge.super-admin { background: #fef3c7; color: #92400e; }
        .role-badge.admin { background: #dbeafe; color: #1d4ed8; }
        .role-badge.staff { background: #d1fae5; color: #047857; }
        .login-footer { text-align: center; margin-top: 28px; padding-top: 20px; border-top: 1px solid #edf2f7; }
        .login-footer small { color: #8a94a6; font-size: 12px; }
        .login-footer .footer-brand { font-weight: 600; color: #2c6b9e; }
        @media (max-width: 576px) {
            .login-card { padding: 32px 20px 28px; }
            .login-header h3 { font-size: 20px; }
            .login-icon { width: 64px; height: 64px; }
            .login-icon i { font-size: 28px; }
            .demo-credentials .cred-row { flex-direction: column; gap: 2px; }
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="login-icon"><i class="fas fa-boxes"></i></div>
        <div class="login-header">
            <span class="app-badge"><i class="fas fa-star"></i> Inventaris</span>
            <h3>Sistem Inventaris Barang</h3>
            <p class="sub-title"><i class="fas fa-building"></i> Manajemen Aset & Peminjaman</p>
            <div class="divider-line"></div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form method="POST" action="proses_login.php">
            <div class="form-group">
                <label>Email <span style="color:#dc2626;">*</span></label>
                <div class="input-group-icon">
                    <input type="email" name="email" class="form-control" placeholder="admin@inventaris.com" required autofocus>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>
            <div class="form-group">
                <label>Password <span style="color:#dc2626;">*</span></label>
                <div class="input-group-icon">
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>
            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
                <a href="#" class="forgot-link">Lupa password?</a>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard</button>
        </form>

        <div class="demo-credentials">
            <div class="demo-title"><i class="fas fa-key"></i> Akun Demo</div>
            <div class="cred-row"><span><span class="role-badge super-admin">Super Admin</span></span><span class="value">superadmin@inventaris.com / password123</span></div>
            <div class="cred-row"><span><span class="role-badge admin">Admin</span></span><span class="value">admin@inventaris.com / password123</span></div>
            <div class="cred-row"><span><span class="role-badge staff">Staff</span></span><span class="value">staff@inventaris.com / password123</span></div>
        </div>

        <div class="login-footer">
            <small><span class="footer-brand"><i class="fas fa-boxes"></i> Sistem Inventaris Barang</span><br>&copy; <?= date('Y'); ?> · Versi 2.0</small>
        </div>
    </div>
</div>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>