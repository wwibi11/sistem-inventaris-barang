<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris Barang</title>

    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* ============================================
           RESET & BODY
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #e8f0fe 0%, #d4e4f7 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Background pattern */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(44, 107, 158, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(44, 107, 158, 0.08) 0%, transparent 50%);
            z-index: 0;
        }

        /* ============================================
           LOGIN CONTAINER
           ============================================ */
        .login-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(44, 107, 158, 0.15);
            padding: 50px 40px 40px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2c6b9e, #4a90d9, #2c6b9e);
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }

        @keyframes gradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .login-card:hover {
            box-shadow: 0 25px 70px rgba(44, 107, 158, 0.2);
            transform: translateY(-2px);
        }

        /* ============================================
           LOGO / ICON
           ============================================ */
        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2c6b9e, #4a90d9);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 25px rgba(44, 107, 158, 0.3);
            transition: all 0.3s ease;
        }

        .login-icon:hover {
            transform: scale(1.05) rotate(-5deg);
        }

        .login-icon i {
            font-size: 36px;
            color: #ffffff;
        }

        /* ============================================
           HEADER
           ============================================ */
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .app-badge {
            display: inline-block;
            background: #e8f0fe;
            color: #2c6b9e;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 16px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .login-header h3 {
            color: #1a2634;
            font-weight: 800;
            font-size: 24px;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .login-header .sub-title {
            color: #6b7a8f;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .login-header .sub-title i {
            color: #2c6b9e;
        }

        .login-header .app-name {
            color: #8a94a6;
            font-size: 13px;
        }

        .divider-line {
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #2c6b9e, #4a90d9);
            margin: 14px auto 0;
            border-radius: 2px;
        }

        /* ============================================
           FORM
           ============================================ */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 6px;
            display: block;
        }

        .form-group label .required {
            color: #dc2626;
            margin-left: 2px;
        }

        .form-control {
            height: 50px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0 16px;
            font-size: 14px;
            color: #2d3748;
            background: #fafbfc;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-control:focus {
            border-color: #2c6b9e;
            box-shadow: 0 0 0 4px rgba(44, 107, 158, 0.1);
            background: #ffffff;
            outline: none;
        }

        .form-control::placeholder {
            color: #a0aec0;
            font-size: 13px;
        }

        .form-control.error {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
        }

        /* Input with icon */
        .input-group-icon {
            position: relative;
        }

        .input-group-icon .form-control {
            padding-left: 48px;
        }

        .input-group-icon .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .input-group-icon .form-control:focus ~ .input-icon {
            color: #2c6b9e;
        }

        /* Toggle password */
        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            cursor: pointer;
            transition: all 0.3s ease;
            background: none;
            border: none;
            padding: 0;
            font-size: 16px;
        }

        .toggle-password:hover {
            color: #2c6b9e;
        }

        /* ============================================
           REMEMBER ME
           ============================================ */
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
            cursor: pointer;
        }

        .form-options .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #2c6b9e;
            cursor: pointer;
            margin: 0;
        }

        .form-options .remember-me label {
            font-size: 13px;
            color: #6b7280;
            cursor: pointer;
            margin: 0;
            font-weight: 400;
        }

        .form-options .forgot-link {
            font-size: 13px;
            color: #2c6b9e;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-options .forgot-link:hover {
            color: #1f507a;
            text-decoration: underline;
        }

        /* ============================================
           BUTTON
           ============================================ */
        .btn-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #2c6b9e, #4a90d9);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::after {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(44, 107, 158, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login i {
            font-size: 16px;
        }

        /* Loading state */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .btn-login.loading .spinner {
            display: inline-block;
        }

        .btn-login.loading .btn-text {
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ============================================
           ALERT
           ============================================ */
        .alert {
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 13px;
            border: none;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert i {
            font-size: 18px;
            flex-shrink: 0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #b91c1c;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e;
        }

        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        /* ============================================
           DEMO CREDENTIALS
           ============================================ */
        .demo-credentials {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px 20px;
            margin-top: 20px;
            border: 1px dashed #d1d5db;
        }

        .demo-credentials .demo-title {
            font-weight: 700;
            color: #1a2634;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .demo-credentials .demo-title i {
            color: #2c6b9e;
            margin-right: 6px;
        }

        .demo-credentials .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 12px;
            border-bottom: 1px solid #edf2f7;
        }

        .demo-credentials .cred-row:last-child {
            border-bottom: none;
        }

        .demo-credentials .cred-row .label {
            color: #6b7280;
        }

        .demo-credentials .cred-row .role-badge {
            display: inline-block;
            padding: 1px 10px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
            margin-right: 8px;
        }

        .role-badge.super-admin {
            background: #fef3c7;
            color: #92400e;
        }
        .role-badge.admin {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .role-badge.staff {
            background: #d1fae5;
            color: #047857;
        }

        .demo-credentials .cred-row .value {
            color: #1a2634;
            font-weight: 500;
            font-family: 'Courier New', monospace;
            font-size: 11px;
        }

        /* ============================================
           FOOTER
           ============================================ */
        .login-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #edf2f7;
        }

        .login-footer small {
            color: #8a94a6;
            font-size: 12px;
            display: block;
            line-height: 1.8;
        }

        .login-footer .footer-brand {
            font-weight: 600;
            color: #2c6b9e;
        }

        .login-footer .footer-brand i {
            margin-right: 4px;
        }

        .login-footer .heart {
            color: #dc2626;
            display: inline-block;
            animation: heartbeat 1.5s ease infinite;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 576px) {
            .login-card {
                padding: 32px 20px 28px;
                border-radius: 16px;
            }

            .login-header h3 {
                font-size: 20px;
            }

            .login-icon {
                width: 64px;
                height: 64px;
                border-radius: 16px;
            }

            .login-icon i {
                font-size: 28px;
            }

            .btn-login {
                height: 46px;
                font-size: 14px;
            }

            .form-control {
                height: 46px;
                font-size: 13px;
                padding: 0 14px;
            }

            .input-group-icon .form-control {
                padding-left: 42px;
            }

            .login-header .sub-title {
                font-size: 13px;
            }

            .form-options {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .demo-credentials .cred-row {
                flex-direction: column;
                gap: 2px;
                padding: 6px 0;
            }

            .demo-credentials .cred-row .value {
                font-size: 11px;
                word-break: break-all;
            }
        }

        @media (max-width: 400px) {
            .login-card {
                padding: 24px 16px 20px;
            }

            .login-header h3 {
                font-size: 18px;
            }

            .login-icon {
                width: 56px;
                height: 56px;
            }

            .login-icon i {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        
        <!-- Icon -->
        <div class="login-icon">
            <i class="fas fa-boxes"></i>
        </div>

        <!-- Header -->
        <div class="login-header">
            <span class="app-badge">
                <i class="fas fa-star"></i> Inventaris
            </span>
            <h3>Sistem Inventaris Barang</h3>
            <p class="sub-title">
                <i class="fas fa-building"></i> Manajemen Aset & Peminjaman
            </p>
            <p class="app-name">
                <i class="fas fa-chevron-right"></i> Kelola barang, peminjaman, dan laporan dengan mudah
            </p>
            <div class="divider-line"></div>
        </div>

        <!-- Error Alert -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> 
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Success Alert -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Info Alert -->
        <?php if (isset($_SESSION['info'])): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <?= $_SESSION['info']; unset($_SESSION['info']); ?>
            </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form method="POST" action="proses_login.php" id="loginForm">
            
            <!-- Email -->
            <div class="form-group">
                <label for="email">
                    Alamat Email <span class="required">*</span>
                </label>
                <div class="input-group-icon">
                    <input type="email" name="email" id="email" class="form-control" 
                           placeholder="admin@inventaris.com" required autofocus
                           value="<?= isset($_COOKIE['remember_email']) ? htmlspecialchars($_COOKIE['remember_email']) : '' ?>">
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">
                    Kata Sandi <span class="required">*</span>
                </label>
                <div class="input-group-icon">
                    <input type="password" name="password" id="password" class="form-control" 
                           placeholder="Masukkan kata sandi" required>
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="toggle-password" id="togglePassword" tabindex="-1">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Options -->
            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="remember" 
                           <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>>
                    <label for="remember">Ingat saya</label>
                </label>
                <a href="#" class="forgot-link">Lupa password?</a>
            </div>

            <!-- Button -->
            <button type="submit" class="btn-login" id="loginBtn">
                <span class="spinner"></span>
                <span class="btn-text">
                    <i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard
                </span>
            </button>

        </form>

        <!-- Demo Credentials -->
        <div class="demo-credentials">
            <div class="demo-title">
                <i class="fas fa-key"></i> Akun Demo
            </div>
            <div class="cred-row">
                <span class="label">
                    <span class="role-badge super-admin">Super Admin</span>
                </span>
                <span class="value">superadmin@inventaris.com / password123</span>
            </div>
            <div class="cred-row">
                <span class="label">
                    <span class="role-badge admin">Admin</span>
                </span>
                <span class="value">admin@inventaris.com / password123</span>
            </div>
            <div class="cred-row">
                <span class="label">
                    <span class="role-badge staff">Staff</span>
                </span>
                <span class="value">staff@inventaris.com / password123</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <small>
                <span class="footer-brand">
                    <i class="fas fa-boxes"></i> Sistem Inventaris Barang
                </span>
                <br>
                &copy; <?= date('Y'); ?> · Versi 2.0
                <i class="fas fa-circle" style="font-size: 4px; color: #d1d5db; margin: 0 6px; vertical-align: middle;"></i>
                Dibuat dengan <span class="heart">❤</span>
            </small>
        </div>

    </div>
</div>

<!-- ============================================
   SCRIPTS
   ============================================ -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    
    // ============================================
    // TOGGLE PASSWORD VISIBILITY
    // ============================================
    $('#togglePassword').on('click', function() {
        const password = $('#password');
        const icon = $(this).find('i');
        
        if (password.attr('type') === 'password') {
            password.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            password.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // ============================================
    // AUTO FOCUS
    // ============================================
    const email = $('#email').val();
    if (email) {
        $('#password').focus();
    } else {
        $('#email').focus();
    }

    // ============================================
    // FORM SUBMIT - LOADING STATE
    // ============================================
    $('#loginForm').on('submit', function() {
        const btn = $('#loginBtn');
        btn.addClass('loading');
        btn.prop('disabled', true);
    });

    // ============================================
    // ENTER KEY SUPPORT
    // ============================================
    $('#password').on('keypress', function(e) {
        if (e.which === 13) {
            $('#loginForm').submit();
        }
    });

    // ============================================
    // REMOVE ERROR STATE ON FOCUS
    // ============================================
    $('.form-control').on('focus', function() {
        $(this).removeClass('error');
    });

});
</script>

</body>
</html>