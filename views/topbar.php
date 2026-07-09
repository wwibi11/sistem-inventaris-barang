<?php
// views/topbar.php

$user_role = $_SESSION['user']['role'] ?? 'staff';
$user_name = $_SESSION['user']['name'] ?? 'User';

$role_labels = [
    'super_admin' => 'Super Admin',
    'admin' => 'Admin',
    'staff' => 'Staff'
];
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <!-- TOPBAR -->
        <nav class="navbar navbar-expand navbar-light bg-navbar topbar static-top" 
             style="z-index: 999; position: sticky; top: 0;">

            <!-- ============================================
            TOMBOL HAMBURGER - PASTIKAN ID INI
            ============================================ -->
            <button id="sidebarToggleTop" class="btn btn-link rounded-circle" 
                    style="color: #4a5568; font-size: 22px; width: 40px; height: 40px;
                           display: flex !important; align-items: center; justify-content: center;
                           border: none; background: transparent; padding: 0; cursor: pointer;"
                    title="Toggle Sidebar"
                    aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Brand Title (mobile) -->
            <span class="navbar-brand d-md-none" style="color: #1a2634; font-weight: 600; font-size: 14px; padding: 0; margin: 0 10px;">
                <i class="fas fa-boxes" style="color: #2c6b9e;"></i>
                <?= function_exists('getAppName') ? getAppName() : 'Inventaris' ?>
            </span>

            <!-- Breadcrumb (Desktop) -->
            <div class="d-none d-md-block">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                        <li class="breadcrumb-item">
                            <a href="index.php?url=dashboard" style="color: #8a94a6; text-decoration: none;">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <?php if (isset($current_module) && $current_module != 'dashboard'): ?>
                            <li class="breadcrumb-item active" style="color: #1a2634; font-weight: 500;">
                                <?= ucfirst($current_module) ?>
                            </li>
                            <?php if (isset($current_action) && !in_array($current_action, ['index', 'dashboard'])): ?>
                                <li class="breadcrumb-item active" style="color: #1a2634;">
                                    <?= ucfirst($current_action) ?>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>

            <!-- Right Menu -->
            <ul class="navbar-nav ml-auto">

                <!-- User Dropdown -->
                <li class="nav-item dropdown no-arrow" style="position: relative;">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" 
                       href="#" id="userDropdown" role="button" 
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                       style="padding: 4px 8px; border-radius: 8px; transition: all 0.2s; cursor: pointer;">
                        
                        <!-- Avatar -->
                        <div class="img-profile rounded-circle" 
                             style="width: 36px; height: 36px; background: #e8f0fe; 
                                    display: flex; align-items: center; justify-content: center;
                                    color: #2c6b9e; font-weight: 700; font-size: 14px;">
                            <?= strtoupper(substr($user_name, 0, 1)) ?>
                        </div>
                        
                        <!-- Name & Role (Desktop) -->
                        <span class="ml-2 d-none d-lg-inline" 
                              style="color: #1a2634 !important; font-weight: 500; font-size: 13px;">
                            <?= htmlspecialchars($user_name) ?>
                            <small style="display: block; font-weight: 400; color: #8a94a6; font-size: 10px;">
                                <?= $role_labels[$user_role] ?? ucfirst($user_role) ?>
                            </small>
                        </span>
                    </a>

                    <!-- Dropdown -->
                    <div class="dropdown-menu dropdown-menu-right shadow" 
                         aria-labelledby="userDropdown"
                         style="z-index: 99999; min-width: 180px; border-radius: 10px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                        <div class="dropdown-header" style="color: #8a94a6; font-size: 12px; padding: 10px 16px;">
                            <i class="fas fa-user-circle"></i> 
                            <?= htmlspecialchars($user_name) ?>
                        </div>
                        <div class="dropdown-divider" style="margin: 0;"></div>
                        
                        <a class="dropdown-item" href="auth/logout.php" 
                           style="padding: 8px 16px; color: #dc2626; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2" style="color: #dc2626;"></i>
                            Logout
                        </a>
                    </div>
                </li>

            </ul>
        </nav>