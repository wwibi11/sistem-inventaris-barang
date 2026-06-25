<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <!-- TOPBAR -->
        <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">

            <!-- Toggle Button (hanya untuk mobile) -->
            <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3" 
                    style="color: #4a5568; font-size: 20px; width: 40px; height: 40px;
                           display: flex; align-items: center; justify-content: center;
                           border: none; background: transparent;"
                    title="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Brand Title (untuk mobile) -->
            <span class="navbar-brand d-md-none" style="color: #1a2634; font-weight: 600; font-size: 16px;">
                <i class="fas fa-heartbeat" style="color: #2c6b9e;"></i>
                E-Posyandu
            </span>

            <!-- Right Menu -->
            <ul class="navbar-nav ml-auto">

                <!-- User Dropdown -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" 
                       href="#" data-toggle="dropdown" 
                       style="padding: 4px 12px; border-radius: 8px; transition: all 0.2s;">
                        
                        <!-- Avatar -->
                        <div class="img-profile rounded-circle" 
                             style="width: 40px; height: 40px; background: #e8f0fe; 
                                    display: flex; align-items: center; justify-content: center;
                                    color: #2c6b9e; font-weight: 700; font-size: 16px;">
                            <?= strtoupper(substr($_SESSION['user']['nama'] ?? 'U', 0, 1)) ?>
                        </div>
                        
                        <!-- Name -->
                        <span class="ml-2 d-none d-lg-inline text-white small" 
                              style="color: #1a2634 !important; font-weight: 500;">
                            <?= $_SESSION['user']['nama'] ?? 'User' ?>
                            <small style="display: block; font-weight: 400; color: #8a94a6; font-size: 11px;">
                                <?= ucfirst($_SESSION['user']['role'] ?? '') ?>
                            </small>
                        </span>
                    </a>

                    <!-- Dropdown -->
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <div class="dropdown-header" style="color: #8a94a6; font-size: 12px;">
                            <i class="fas fa-user-circle"></i> 
                            <?= $_SESSION['user']['nama'] ?? 'User' ?>
                        </div>
                        <div class="dropdown-divider"></div>
                        
                        <a class="dropdown-item" href="../auth/logout.php">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>

            </ul>
        </nav>