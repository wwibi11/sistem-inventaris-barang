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
        /* Reset & Body */
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            padding: 45px 40px 40px;
            border: 1px solid #e8ecf1;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
        }

        /* Logo / Icon */
        .login-icon {
            width: 80px;
            height: 80px;
            background: #2c6b9e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
        }

        .login-icon i {
            font-size: 36px;
            color: #ffffff;
        }

        /* Decorative badge */
        .app-badge {
            display: inline-block;
            background: #e8f0fe;
            color: #2c6b9e;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 14px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header h3 {
            color: #1a2634;
            font-weight: 700;
            font-size: 22px;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .login-header .sub-title {
            color: #6b7a8f;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .login-header .app-name {
            color: #8a94a6;
            font-size: 13px;
            font-weight: 400;
        }

        .login-header .app-name i {
            color: #2c6b9e;
            margin: 0 4px;
        }

        .divider-line {
            width: 40px;
            height: 3px;
            background: #2c6b9e;
            margin: 12px auto 0;
            border-radius: 2px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 6px;
            display: block;
        }

        .form-control {
            height: 48px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 16px;
            font-size: 14px;
            color: #2d3748;
            background: #fafbfc;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #2c6b9e;
            box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.12);
            background: #ffffff;
        }

        .form-control::placeholder {
            color: #a0aec0;
            font-size: 13px;
        }

        /* Input with icon */
        .input-group-icon {
            position: relative;
        }

        .input-group-icon .form-control {
            padding-left: 44px;
        }

        .input-group-icon .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
            transition: color 0.2s;
        }

        .input-group-icon .form-control:focus ~ .input-icon {
            color: #2c6b9e;
        }

        /* Button */
        .btn-login {
            width: 100%;
            height: 48px;
            background: #2c6b9e;
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 6px;
        }

        .btn-login:hover {
            background: #1f507a;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(44, 107, 158, 0.25);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login i {
            font-size: 15px;
        }

        /* Alert */
        .alert {
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #fef2f2;
            color: #b91c1c;
            border-left: 3px solid #dc2626;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left: 3px solid #22c55e;
        }

        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            border-left: 3px solid #3b82f6;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #edf2f7;
        }

        .login-footer small {
            color: #8a94a6;
            font-size: 12px;
            display: block;
            line-height: 1.6;
        }

        .login-footer small i {
            color: #2c6b9e;
            margin: 0 3px;
        }

        .login-footer .footer-brand {
            font-weight: 600;
            color: #2c6b9e;
        }

        /* Demo credentials info */
        .demo-credentials {
            background: #f8f9fc;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 16px;
            border: 1px dashed #d1d5db;
        }

        .demo-credentials small {
            display: block;
            font-size: 12px;
            color: #6b7280;
        }

        .demo-credentials .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 12px;
        }

        .demo-credentials .cred-row .label {
            color: #6b7280;
        }

        .demo-credentials .cred-row .value {
            color: #1a2634;
            font-weight: 500;
            font-family: monospace;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                padding: 30px 20px 28px;
            }

            .login-header h3 {
                font-size: 20px;
            }

            .login-icon {
                width: 64px;
                height: 64px;
            }

            .login-icon i {
                font-size: 28px;
            }

            .btn-login {
                height: 44px;
                font-size: 14px;
            }

            .form-control {
                height: 44px;
                font-size: 13px;
            }

            .login-header .sub-title {
                font-size: 13px;
            }

            .demo-credentials .cred-row {
                flex-direction: column;
                gap: 2px;
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
                <i class="fas fa-building" style="color: #2c6b9e; font-size: 12px;"></i> 
                Manajemen Aset & Peminjaman
            </p>
            <p class="app-name">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i> 
                Kelola barang, peminjaman, dan laporan dengan mudah
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
        <form method="POST" action="proses_login.php">
            
            <!-- Email -->
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <div class="input-group-icon">
                    <input type="email" name="email" id="email" class="form-control" 
                           placeholder="admin@inventaris.com" required autofocus
                           value="<?= isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : '' ?>">
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <div class="input-group-icon">
                    <input type="password" name="password" id="password" class="form-control" 
                           placeholder="Masukkan kata sandi" required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="form-group" style="margin-bottom: 16px;">
                <div class="d-flex align-items-center">
                    <input type="checkbox" name="remember" id="remember" style="margin-right: 8px;">
                    <label for="remember" style="margin-bottom: 0; font-size: 13px; color: #6b7280; cursor: pointer;">
                        Ingat saya
                    </label>
                </div>
            </div>

            <!-- Button -->
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard
            </button>

        </form>

        <!-- Demo Credentials -->
        <div class="demo-credentials">
            <small style="font-weight: 600; color: #1a2634; margin-bottom: 6px;">
                <i class="fas fa-key"></i> Akun Demo
            </small>
            <div class="cred-row">
                <span class="label">Super Admin</span>
                <span class="value">superadmin@inventaris.com / password123</span>
            </div>
            <div class="cred-row">
                <span class="label">Admin</span>
                <span class="value">admin@inventaris.com / password123</span>
            </div>
            <div class="cred-row">
                <span class="label">Staff</span>
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
                Dibuat dengan <i class="fas fa-heart" style="color: #dc2626;"></i>
            </small>
        </div>

    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    // Auto focus ke email jika ada
    $(document).ready(function() {
        var email = $('#email').val();
        if (email) {
            $('#password').focus();
        } else {
            $('#email').focus();
        }
    });
</script>

</body>
</html>