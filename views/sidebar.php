<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">

  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?url=dashboard">
    <div class="sidebar-brand-icon">
      <!-- <img src="img/logo/logo2.png"> -->
    </div>
    <div class="sidebar-brand-text mx-3">Posyandu</div>
  </a>

  <hr class="sidebar-divider my-0">

  <!-- DASHBOARD -->
  <li class="nav-item">
    <a class="nav-link" href="index.php?url=dashboard">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
    </a>
  </li>

  <hr class="sidebar-divider">

  <!-- DATA MASTER -->
  <div class="sidebar-heading">Data Master</div>

  <li class="nav-item">
    <a class="nav-link" href="index.php?url=keluarga">
      <i class="fas fa-home"></i>
      <span>Data Keluarga</span>
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link" href="index.php?url=anak">
      <i class="fas fa-child"></i>
      <span>Data Anak</span>
    </a>
  </li>

  <hr class="sidebar-divider">

  <!-- KEGIATAN -->
  <div class="sidebar-heading">Kegiatan</div>

  <li class="nav-item">
    <a class="nav-link" href="index.php?url=kegiatan">
      <i class="fas fa-calendar"></i>
      <span>Jadwal Posyandu</span>
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link" href="index.php?url=kehadiran">
      <i class="fas fa-check"></i>
      <span>Kehadiran</span>
    </a>
  </li>

  <hr class="sidebar-divider">

  <!-- PEMERIKSAAN -->
  <div class="sidebar-heading">Pemeriksaan</div>

  <li class="nav-item">
    <a class="nav-link" href="index.php?url=pemeriksaan">
      <i class="fas fa-stethoscope"></i>
      <span>Pemeriksaan</span>
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link" href="index.php?url=imunisasi">
      <i class="fas fa-syringe"></i>
      <span>Imunisasi</span>
    </a>
  </li>

  <hr class="sidebar-divider">

  <!-- ADMIN -->
  <?php if ($_SESSION['user']['role'] == 'admin'): ?>
  <div class="sidebar-heading">Manajemen</div>

  <li class="nav-item">
    <a class="nav-link" href="index.php?url=users">
      <i class="fas fa-users"></i>
      <span>Manajemen User</span>
    </a>
  </li>
  <?php endif; ?>

</ul>