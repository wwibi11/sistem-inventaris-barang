<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Posyandu Bougenvil Belik</title>

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
        .posyandu-badge {
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

        .login-header .posyandu-name {
            color: #8a94a6;
            font-size: 13px;
            font-weight: 400;
        }

        .login-header .posyandu-name i {
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
            color: #dc2626;
            margin: 0 3px;
        }

        .login-footer .footer-brand {
            font-weight: 600;
            color: #2c6b9e;
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
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        
        <!-- Icon -->
        <div class="login-icon">
            <i class="fas fa-heartbeat"></i>
        </div>

        <!-- Header -->
        <div class="login-header">
            <span class="posyandu-badge">
                <i class="fas fa-star"></i> E-Posyandu
            </span>
            <h3>Posyandu Bougenvil</h3>
            <p class="sub-title">
                <i class="fas fa-map-marker-alt" style="color: #2c6b9e; font-size: 12px;"></i> 
                Belik
            </p>
            <p class="posyandu-name">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i> 
                Sistem Informasi Pelayanan Kesehatan Ibu & Anak
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

        <!-- Success Alert (optional) -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form method="POST" action="proses_login.php">
            
            <!-- Email -->
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <div class="input-group-icon">
                    <input type="email" name="email" id="email" class="form-control" 
                           placeholder="contoh@email.com" required autofocus>
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

            <!-- Button -->
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard
            </button>

        </form>

        <!-- Footer -->
        <div class="login-footer">
            <small>
                <span class="footer-brand">
                    <i class="fas fa-heartbeat"></i> E-Posyandu Bougenvil Belik
                </span>
                <br>
                &copy; <?= date('Y'); ?> · Dinas Kesehatan Kabupaten
                <i class="fas fa-circle" style="font-size: 4px; color: #d1d5db; margin: 0 6px; vertical-align: middle;"></i>
                Versi 1.0
            </small>
        </div>

    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>