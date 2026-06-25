<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">

    <!-- ======================== -->
    <!-- BRAND -->
    <!-- ======================== -->
    <div class="sidebar-brand d-flex align-items-center justify-content-center" 
         style="padding: 14px 16px; border-bottom: 1px solid #edf2f7; min-height: 70px; width: 100%;">
        
        <!-- Brand -->
        <a class="d-flex align-items-center" href="index.php?url=dashboard" 
           style="text-decoration: none; gap: 10px;">
            <div class="sidebar-brand-icon" style="flex-shrink: 0;">
                <i class="fas fa-heartbeat" style="font-size: 28px; color: #2c6b9e;"></i>
            </div>
            <div class="sidebar-brand-text" style="color: #1a2634; font-weight: 700; font-size: 16px; line-height: 1.2; white-space: nowrap;">
                E-Posyandu
                <small style="display: block; font-weight: 400; font-size: 10px; color: #8a94a6;">Bougenvil Belik</small>
            </div>
        </a>
        
    </div>

    <hr class="sidebar-divider my-0">

    <!-- ======================== -->
    <!-- DASHBOARD -->
    <!-- ======================== -->
    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? 'dashboard') == 'dashboard' ? 'active' : '' ?>" 
           href="index.php?url=dashboard">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- ======================== -->
    <!-- DATA MASTER -->
    <!-- ======================== -->
    <div class="sidebar-heading">Data Master</div>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'keluarga' ? 'active' : '' ?>" 
           href="index.php?url=keluarga">
            <i class="fas fa-home"></i>
            <span>Data Keluarga</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'anak' ? 'active' : '' ?>" 
           href="index.php?url=anak">
            <i class="fas fa-child"></i>
            <span>Data Anak</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- ======================== -->
    <!-- KEGIATAN -->
    <!-- ======================== -->
    <div class="sidebar-heading">Kegiatan</div>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'kegiatan' ? 'active' : '' ?>" 
           href="index.php?url=kegiatan">
            <i class="fas fa-calendar"></i>
            <span>Jadwal Posyandu</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'kehadiran' ? 'active' : '' ?>" 
           href="index.php?url=kehadiran">
            <i class="fas fa-check"></i>
            <span>Kehadiran</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- ======================== -->
    <!-- PEMERIKSAAN -->
    <!-- ======================== -->
    <div class="sidebar-heading">Pemeriksaan</div>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'pemeriksaan' ? 'active' : '' ?>" 
           href="index.php?url=pemeriksaan">
            <i class="fas fa-stethoscope"></i>
            <span>Pemeriksaan</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'imunisasi' ? 'active' : '' ?>" 
           href="index.php?url=imunisasi">
            <i class="fas fa-syringe"></i>
            <span>Imunisasi</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- ======================== -->
    <!-- LAPORAN -->
    <!-- ======================== -->
    <div class="sidebar-heading">Laporan</div>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'laporan-kehadiran' ? 'active' : '' ?>" 
           href="index.php?url=laporan-kehadiran">
            <i class="fas fa-clipboard-check"></i>
            <span>Kehadiran</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'laporan-pemeriksaan' ? 'active' : '' ?>" 
           href="index.php?url=laporan-pemeriksaan">
            <i class="fas fa-file-medical"></i>
            <span>Pemeriksaan</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'laporan-imunisasi' ? 'active' : '' ?>" 
           href="index.php?url=laporan-imunisasi">
            <i class="fas fa-syringe"></i>
            <span>Imunisasi</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'laporan-statistik' ? 'active' : '' ?>" 
           href="index.php?url=laporan-statistik">
            <i class="fas fa-chart-bar"></i>
            <span>Statistik Posyandu</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- ======================== -->
    <!-- MANAJEMEN (ADMIN ONLY) -->
    <!-- ======================== -->
    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'admin'): ?>
    <div class="sidebar-heading">Manajemen</div>

    <li class="nav-item">
        <a class="nav-link <?= ($_GET['url'] ?? '') == 'users' ? 'active' : '' ?>" 
           href="index.php?url=users">
            <i class="fas fa-users-cog"></i>
            <span>Manajemen User</span>
        </a>
    </li>
    <?php endif; ?>

</ul>