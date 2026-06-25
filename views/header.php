<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="img/logo/logo.png" rel="icon">
    <title>E-Posyandu Bougenvil Belik</title>

    <!-- Font Awesome -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    
    <style>
        /* ============================================
           CUSTOM STYLE - TANPA GRADASI, ELEGAN
           ============================================ */
        
        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* WRAPPER */
        #wrapper {
            display: flex !important;
            min-height: 100vh !important;
            width: 100% !important;
        }
        
        /* ==========================================
           SIDEBAR - FIXED TIDAK BISA MINIMIZE
           ========================================== */
        .sidebar {
            background: #ffffff !important;
            border-right: 1px solid #e8ecf1;
            box-shadow: 2px 0 8px rgba(0,0,0,0.04);
            width: 250px !important;
            min-width: 250px !important;
            padding-top: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            height: 100vh !important;
            position: sticky !important;
            top: 0 !important;
            overflow-y: auto !important;
        }
        
        /* ==========================================
           SIDEBAR BRAND
           ========================================== */
        .sidebar .sidebar-brand {
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f7;
            background: #ffffff !important;
            min-height: 70px;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            width: 100% !important;
        }
        
        .sidebar .sidebar-brand a {
            text-decoration: none;
            gap: 10px;
            display: flex;
            align-items: center;
        }
        
        .sidebar .sidebar-brand .sidebar-brand-text {
            color: #1a2634;
            font-weight: 700;
            font-size: 16px;
            line-height: 1.2;
            white-space: nowrap;
        }
        
        .sidebar .sidebar-brand .sidebar-brand-text small {
            display: block;
            font-weight: 400;
            font-size: 10px;
            color: #8a94a6;
        }
        
        .sidebar .sidebar-brand .sidebar-brand-icon i {
            font-size: 28px;
            color: #2c6b9e;
        }
        
        /* ==========================================
           NAV ITEM
           ========================================== */
        .sidebar .nav-item {
            flex-shrink: 0 !important;
        }
        
        .sidebar .nav-item .nav-link {
            color: #4a5568 !important;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
        }
        
        .sidebar .nav-item .nav-link i {
            color: #8a94a6;
            width: 20px;
            text-align: center;
            font-size: 15px;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        
        .sidebar .nav-item .nav-link:hover {
            background: #f0f4f8 !important;
            color: #2c6b9e !important;
        }
        
        .sidebar .nav-item .nav-link:hover i {
            color: #2c6b9e;
        }
        
        .sidebar .nav-item .nav-link.active {
            background: #e8f0fe !important;
            color: #2c6b9e !important;
            font-weight: 600;
        }
        
        .sidebar .nav-item .nav-link.active i {
            color: #2c6b9e;
        }
        
        /* ==========================================
           SIDEBAR HEADING
           ========================================== */
        .sidebar .sidebar-heading {
            color: #8a94a6 !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 20px 6px;
            white-space: nowrap;
            flex-shrink: 0 !important;
        }
        
        .sidebar .sidebar-divider {
            border-color: #edf2f7 !important;
            margin: 6px 20px;
            flex-shrink: 0 !important;
        }
        
        /* ==========================================
           CONTENT WRAPPER
           ========================================== */
        #content-wrapper {
            background: #f8f9fc;
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh !important;
            width: 100% !important;
        }
        
        #content {
            flex: 1 !important;
            padding: 0 20px !important;
        }
        
        /* ==========================================
           TOPBAR
           ========================================== */
        .bg-navbar {
            background: #ffffff !important;
            border-bottom: 1px solid #e8ecf1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 10px 20px;
        }
        
        .bg-navbar .navbar-nav .nav-link {
            color: #4a5568 !important;
        }
        
        .bg-navbar .navbar-nav .nav-link .text-white {
            color: #1a2634 !important;
        }
        
        .bg-navbar .dropdown-menu {
            border: 1px solid #e8ecf1;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            padding: 8px 0;
        }
        
        .bg-navbar .dropdown-menu .dropdown-item {
            color: #4a5568;
            font-size: 14px;
            padding: 8px 20px;
            transition: all 0.2s ease;
        }
        
        .bg-navbar .dropdown-menu .dropdown-item:hover {
            background: #f0f4f8;
            color: #2c6b9e;
        }
        
        .bg-navbar .dropdown-menu .dropdown-item i {
            color: #8a94a6;
        }
        
        .bg-navbar .dropdown-menu .dropdown-item:hover i {
            color: #2c6b9e;
        }
        
        /* ==========================================
           FOOTER
           ========================================== */
        .sticky-footer {
            background: #ffffff !important;
            border-top: 1px solid #e8ecf1;
            padding: 15px 0;
            flex-shrink: 0 !important;
            margin-top: auto !important;
        }
        
        .sticky-footer .copyright {
            color: #8a94a6;
            font-size: 13px;
        }
        
        .sticky-footer .copyright span {
            color: #1a2634;
            font-weight: 500;
        }
        
        /* ==========================================
           SCROLL TO TOP
           ========================================== */
        .scroll-to-top {
            background: #2c6b9e !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 15px rgba(44, 107, 158, 0.3);
        }
        
        .scroll-to-top:hover {
            background: #1f507a !important;
        }
        
        .scroll-to-top i {
            color: #ffffff !important;
        }
        
        /* ==========================================
           MOBILE RESPONSIVE
           ========================================== */
        @media (max-width: 768px) {
            .sidebar {
                width: 280px !important;
                min-width: 280px !important;
                position: fixed !important;
                z-index: 1040 !important;
                height: 100vh !important;
                left: 0 !important;
                top: 0 !important;
                transform: translateX(0) !important;
            }
            
            /* Sembunyikan sidebar di mobile dengan overlay */
            .sidebar.toggled {
                transform: translateX(-100%) !important;
            }
            
            /* Tampilkan tombol toggle di topbar untuk mobile */
            #sidebarToggleTop {
                display: flex !important;
            }
            
            .bg-navbar {
                padding: 8px 12px;
            }
        }
        
        @media (min-width: 769px) {
            #sidebarToggleTop {
                display: none !important;
            }
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">