<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="assets/img/logo/logo.png" rel="icon">
    <title>Sistem Inventaris Barang</title>

    <!-- Font Awesome -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/ruang-admin.min.css" rel="stylesheet">
    
    <style>
        /* ============================================
           FIX SCROLL SIDEBAR
           ============================================ */
        body, html {
            overflow-x: hidden !important;
            width: 100% !important;
        }

        #wrapper {
            display: flex !important;
            min-height: 100vh !important;
            width: 100% !important;
            overflow-x: hidden !important;
            max-width: 100% !important;
        }

        /* ==========================================
           SIDEBAR - FIXED
           ========================================== */
        .sidebar {
            background: #ffffff !important;
            border-right: 1px solid #e8ecf1;
            box-shadow: 2px 0 8px rgba(0,0,0,0.04);
            width: 250px !important;
            min-width: 250px !important;
            max-width: 250px !important;
            padding-top: 0 !important;
            padding-bottom: 20px !important;
            display: flex !important;
            flex-direction: column !important;
            height: 100vh !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            z-index: 1000 !important;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #c1c7cd;
            border-radius: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #a0a6ad;
        }

        #content-wrapper {
            background: #f8f9fc;
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh !important;
            width: calc(100% - 250px) !important;
            margin-left: 250px !important;
            overflow-x: hidden !important;
            max-width: calc(100% - 250px) !important;
        }

        #content {
            flex: 1 !important;
            padding: 0 20px !important;
            overflow-x: hidden !important;
            max-width: 100% !important;
        }

        .container-fluid {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }

        /* SIDEBAR NAV ITEM */
        .sidebar .nav-item .nav-link {
            padding: 11px 16px !important;
            margin: 3px 12px !important;
            border-radius: 10px !important;
            font-size: 14px !important;
            transition: all 0.2s ease !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            color: #4a5568 !important;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-item .nav-link i {
            width: 22px !important;
            text-align: center !important;
            flex-shrink: 0 !important;
            color: #8a94a6;
            font-size: 15px;
        }

        .sidebar .nav-item .nav-link span {
            display: inline-block !important;
            max-width: 150px !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
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

        .sidebar .sidebar-heading {
            padding: 10px 20px 4px !important;
            font-size: 10px !important;
            color: #8a94a6 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .sidebar .sidebar-divider {
            margin: 4px 16px !important;
            border-color: #edf2f7 !important;
        }

        /* SIDEBAR BRAND */
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
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
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

        /* TOPBAR */
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

        /* FOOTER */
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

        /* SCROLL TO TOP */
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

        /* BADGE STOK */
        .badge-stok {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-stok.habis { background: #fee2e2; color: #b91c1c; }
        .badge-stok.menipis { background: #fef3c7; color: #92400e; }
        .badge-stok.cukup { background: #d1fae5; color: #047857; }

        /* BADGE STATUS */
        .badge-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-status.tersedia { background: #d1fae5; color: #047857; }
        .badge-status.dipinjam { background: #fef3c7; color: #92400e; }
        .badge-status.perbaikan { background: #dbeafe; color: #1d4ed8; }
        .badge-status.hilang { background: #fee2e2; color: #b91c1c; }

        /* BADGE KONDISI */
        .badge-condition {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-condition.baik { background: #d1fae5; color: #047857; }
        .badge-condition.rusak { background: #fee2e2; color: #b91c1c; }
        .badge-condition.perbaikan { background: #fef3c7; color: #92400e; }

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
            .sidebar.toggled {
                transform: translateX(-100%) !important;
            }
            #sidebarToggleTop {
                display: flex !important;
            }
            .bg-navbar {
                padding: 8px 12px;
            }
            #content-wrapper {
                width: 100% !important;
                margin-left: 0 !important;
                max-width: 100% !important;
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