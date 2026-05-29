<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">

  <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
    <i class="fa fa-bars"></i>
  </button>

  <ul class="navbar-nav ml-auto">

    <li class="nav-item dropdown no-arrow">
      <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
        <img class="img-profile rounded-circle" src="img/boy.png" style="max-width: 60px">
        <span class="ml-2 d-none d-lg-inline text-white small">
          <?= $_SESSION['user']['nama'] ?? 'User'; ?>
        </span>
      </a>

      <div class="dropdown-menu dropdown-menu-right shadow">
        <a class="dropdown-item" href="../auth/logout.php">
          <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
          Logout
        </a>
      </div>
    </li>

  </ul>
</nav>