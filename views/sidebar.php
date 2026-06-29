<?php
// views/sidebar.php

$current_url = $_GET['url'] ?? 'dashboard';
$user_role = $_SESSION['user']['role'] ?? 'staff';

// Role labels
$role_labels = [
    'super_admin' => 'Super Admin',
    'admin' => 'Admin',
    'staff' => 'Staff'
];
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
                <i class="fas fa-boxes" style="font-size: 32px; color: #2c6b9e;"></i>
            </div>
            <div class="sidebar-brand-text" style="color: #1a2634; font-weight: 700; font-size: 18px; line-height: 1.2; white-space: nowrap;">
                Inventaris
                <small style="display: block; font-weight: 400; font-size: 11px; color: #8a94a6;">Sistem Manajemen Barang</small>
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

    <!-- Items - Semua Role -->
    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'items' ? 'active' : '' ?>" 
           href="index.php?url=items"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-box" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Data Barang</span>
            <?php if ($user_role == 'staff'): ?>
                <small style="font-size: 9px; color: #8a94a6; margin-left: auto;">(read)</small>
            <?php endif; ?>
        </a>
    </li>

    <!-- Categories - Admin & Super Admin -->
    <?php if (in_array($user_role, ['admin', 'super_admin'])): ?>
    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'categories' ? 'active' : '' ?>" 
           href="index.php?url=categories"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-tags" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Kategori</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Borrowers - Semua Role -->
    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'borrowers' ? 'active' : '' ?>" 
           href="index.php?url=borrowers"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-users" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Data Peminjam</span>
            <?php if ($user_role == 'staff'): ?>
                <small style="font-size: 9px; color: #8a94a6; margin-left: auto;">(read)</small>
            <?php endif; ?>
        </a>
    </li>

    <hr class="sidebar-divider" style="margin: 10px 20px;">

    <!-- ======================== -->
    <!-- TRANSAKSI -->
    <!-- ======================== -->
    <div class="sidebar-heading" style="padding: 12px 20px 6px; font-size: 11px; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700;">
        Transaksi
    </div>

    <!-- Loans - Semua Role -->
    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'loans' ? 'active' : '' ?>" 
           href="index.php?url=loans"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-hand-holding" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Peminjaman</span>
        </a>
    </li>

    <!-- Returns - Semua Role -->
    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'returns' ? 'active' : '' ?>" 
           href="index.php?url=returns"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-undo-alt" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Pengembalian</span>
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
        <a class="nav-link <?= $current_url == 'reports' ? 'active' : '' ?>" 
           href="index.php?url=reports"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-file-alt" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Laporan &amp; Export</span>
        </a>
    </li>

    <hr class="sidebar-divider" style="margin: 10px 20px;">

    <!-- ======================== -->
    <!-- MANAJEMEN (Khusus Admin & Super Admin) -->
    <!-- ======================== -->
    <?php if (in_array($user_role, ['admin', 'super_admin'])): ?>
    <div class="sidebar-heading" style="padding: 12px 20px 6px; font-size: 11px; color: #8a94a6; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700;">
        Manajemen
    </div>

    <!-- History - Admin & Super Admin -->
    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'history' ? 'active' : '' ?>" 
           href="index.php?url=history"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-history" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Riwayat Barang</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if ($user_role == 'super_admin'): ?>
    <!-- Users - Super Admin Only -->
    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'users' ? 'active' : '' ?>" 
           href="index.php?url=users"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-users-cog" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Manajemen User</span>
        </a>
    </li>

    <!-- Settings - Super Admin Only -->
    <li class="nav-item">
        <a class="nav-link <?= $current_url == 'settings' ? 'active' : '' ?>" 
           href="index.php?url=settings"
           style="padding: 13px 20px; margin: 4px 14px; border-radius: 10px;">
            <i class="fas fa-cog" style="width: 24px; font-size: 15px;"></i>
            <span style="font-size: 14px;">Pengaturan</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Spacer bottom -->
    <div style="flex: 1; min-height: 30px;"></div>

</ul>