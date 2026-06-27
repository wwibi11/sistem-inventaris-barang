<?php
// views/sidebar.php

$current_url = $_GET['url'] ?? 'dashboard';
$user_role = $_SESSION['user']['role'] ?? '';
?>

<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">

    <!-- ======================== -->
    <!-- BRAND -->
    <!-- ======================== -->
    <div class="sidebar-brand d-flex align-items-center justify-content-center" 
         style="padding: 20px 16px; border-bottom: 1px solid #edf2f7; min-height: 80px; width: 100%;">
        <a class="d-flex align-items-center" href="index.php?url=dashboard" 
           style="text-decoration: none; gap: 14px;">
            <div class="sidebar-brand-icon" style="flex-shrink: 0;">
                <i class="fas fa-heartbeat" style="font-size: 32px; color: #2c6b9e;"></i>
            </div>
            <div class="sidebar-brand-text" style="color: #1a2634; font-weight: 700; font-size: 18px; line-height: 1.2; white-space: nowrap;">
                E-Posyandu
                <small style="display: block; font-weight: 400; font-size: 11px; color: #8a94a6;">Bougenvil Belik</small>
            </div>
        </a>
    </div>

    <hr class="sidebar-divider my-0" style="margin: 0;">

    <!-- ======================== -->
    <!-- DASHBOARD -->
    <!-- ======================== -->
    <li class="nav-item" style="margin-top: 12px;">
        <a class="nav-link <?= $current_url == 'dashboard' ? 'active' : '' ?>" 
           href="index.php?url=dashboard"
           style="padding: 14px 20px; margin: 6px 14px; border-radius: 10px;">
            <i class="fas fa-fw fa-tachometer-alt" style="width: 24px; font-size: 16px;"></i>
            <span style="font-size: 14px;">Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider" style="margin: 10px 20px;">

    <!-- ======================== -->
    <!-- DATA MASTER -->
    <!-- ======================== -->
    <div class="sidebar-heading" style="padding: 12px 20px 6px; font-size: 11px; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700;">
        Data Master
    </div>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'keluarga' ? 'active' : '' ?>" 
           href="index.php?url=keluarga"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-home" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Data Keluarga</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'anak' ? 'active' : '' ?>" 
           href="index.php?url=anak"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-child" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Data Anak</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'ibu_hamil' ? 'active' : '' ?>" 
           href="index.php?url=ibu_hamil"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fa-solid fa-fw fa-person-pregnant" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Ibu Hamil</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'master_imunisasi' ? 'active' : '' ?>" 
           href="index.php?url=master_imunisasi"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-fw fa-syringe" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Master Imunisasi</span>
        </a>
    </li>

    <hr class="sidebar-divider" style="margin: 10px 20px;">

    <!-- ======================== -->
    <!-- KEGIATAN -->
    <!-- ======================== -->
    <div class="sidebar-heading" style="padding: 12px 20px 6px; font-size: 11px; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700;">
        Kegiatan
    </div>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'kegiatan' ? 'active' : '' ?>" 
           href="index.php?url=kegiatan"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-calendar" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Jadwal Posyandu</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'kehadiran' ? 'active' : '' ?>" 
           href="index.php?url=kehadiran"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-check" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Kehadiran</span>
        </a>
    </li>

    <hr class="sidebar-divider" style="margin: 10px 20px;">

    <!-- ======================== -->
    <!-- PEMERIKSAAN -->
    <!-- ======================== -->
    <div class="sidebar-heading" style="padding: 12px 20px 6px; font-size: 11px; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700;">
        Pemeriksaan
    </div>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'pemeriksaan' ? 'active' : '' ?>" 
           href="index.php?url=pemeriksaan"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-stethoscope" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Pemeriksaan Anak</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'imunisasi' ? 'active' : '' ?>" 
           href="index.php?url=imunisasi"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-syringe" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Imunisasi Anak</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'imunisasi_ibu' ? 'active' : '' ?>"
           href="index.php?url=imunisasi_ibu"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-fw fa-syringe" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Imunisasi Ibu Hamil</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'pemeriksaan_ibu' ? 'active' : '' ?>" 
           href="index.php?url=pemeriksaan_ibu"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-fw fa-stethoscope" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Pemeriksaan Ibu Hamil</span>
        </a>
    </li>

    <hr class="sidebar-divider" style="margin: 10px 20px;">

    <!-- ======================== -->
    <!-- LAPORAN -->
    <!-- ======================== -->
    <div class="sidebar-heading" style="padding: 12px 20px 6px; font-size: 11px; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700;">
        Laporan
    </div>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'laporan-kehadiran' ? 'active' : '' ?>"
           href="index.php?url=laporan-kehadiran"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-clipboard-check" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Laporan Kehadiran</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'laporan-pemeriksaan' ? 'active' : '' ?>"
           href="index.php?url=laporan-pemeriksaan"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-stethoscope" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Laporan Pemeriksaan</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'laporan-imunisasi' ? 'active' : '' ?>"
           href="index.php?url=laporan-imunisasi"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-syringe" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Laporan Imunisasi</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'statistik' ? 'active' : '' ?>"
           href="index.php?url=statistik"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-chart-bar" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Statistik Posyandu</span>
        </a>
    </li>

    <hr class="sidebar-divider" style="margin: 10px 20px;">

    <!-- ======================== -->
    <!-- MANAJEMEN (HANYA ADMIN) -->
    <!-- ======================== -->
    <?php if ($user_role == 'admin'): ?>
    <div class="sidebar-heading" style="padding: 12px 20px 6px; font-size: 11px; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700;">
        Manajemen
    </div>

    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'users' ? 'active' : '' ?>" 
           href="index.php?url=users"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-users-cog" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Manajemen User</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Spacer bottom -->
    <div style="flex: 1; min-height: 30px;"></div>

</ul>