<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - Posyandu</title>

  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/ruang-admin.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-login">

<div class="container-login">
  <div class="row justify-content-center">
    <div class="col-xl-5 col-lg-6 col-md-8">
      <div class="card shadow-lg my-5">
        <div class="card-body p-4">

          <div class="text-center mb-4">
            <h3 class="text-primary">Sistem Posyandu</h3>
            <p class="small text-gray-500">Login untuk melanjutkan</p>
          </div>

          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
              <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="proses_login.php">
            <div class="form-group">
              <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>

            <div class="form-group">
              <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
              <i class="fas fa-sign-in-alt"></i> Login
            </button>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="/posyandu/vendor/jquery/jquery.min.js"></script>
<script src="/posyandu/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>