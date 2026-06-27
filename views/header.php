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

        #content-wrapper {
            background: #f8f9fc;
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh !important;
            width: 100% !important;
            overflow-x: hidden !important;
            max-width: 100% !important;
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

        /* ==========================================
           SIDEBAR
           ========================================== */
        .sidebar {
            background: #ffffff !important;
            border-right: 1px solid #e8ecf1;
            box-shadow: 2px 0 8px rgba(0,0,0,0.04);
            width: 250px !important;
            min-width: 250px !important;
            max-width: 250px !important;
            padding-top: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            height: 100vh !important;
            position: sticky !important;
            top: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        /* SIDEBAR NAV ITEM SPACING */
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
           CSS UNTUK DETAIL KEGIATAN
           ========================================== */
        .detail-kegiatan-container { padding: 10px 0; }

        .card-header-kegiatan {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 24px;
            border: 1px solid #e8ecf1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .card-header-kegiatan .title {
            font-size: 18px;
            font-weight: 700;
            color: #1a2634;
            margin: 0;
        }
        .card-header-kegiatan .date {
            font-size: 13px;
            color: #8a94a6;
        }

        .badge-status-kegiatan {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-status-kegiatan.selesai { background: #d1fae5; color: #047857; }
        .badge-status-kegiatan.scheduled { background: #fef3c7; color: #92400e; }

        .stat-card-detail-kegiatan {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px 20px;
            border: 1px solid #e8ecf1;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            height: 100%;
        }
        .stat-card-detail-kegiatan .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #1a2634;
        }
        .stat-card-detail-kegiatan .stat-label {
            font-size: 12px;
            color: #8a94a6;
            margin-top: 2px;
        }
        .stat-card-detail-kegiatan.primary .stat-number { color: #2c6b9e; }
        .stat-card-detail-kegiatan.success .stat-number { color: #28a745; }
        .stat-card-detail-kegiatan.info .stat-number { color: #17a2b8; }
        .stat-card-detail-kegiatan.warning .stat-number { color: #e8a317; }

        .progress-kegiatan-detail {
            height: 8px;
            border-radius: 4px;
            background: #edf2f7;
        }
        .progress-kegiatan-detail .progress-bar {
            height: 100%;
            border-radius: 4px;
            background: #28a745;
            transition: width 0.6s ease;
        }

        /* NAV TABS */
        .nav-tabs-custom-kegiatan {
            border-bottom: 1px solid #edf2f7;
            margin-bottom: 0;
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            padding-left: 0;
        }
        .nav-tabs-custom-kegiatan .nav-item {
            margin-bottom: -1px;
        }
        .nav-tabs-custom-kegiatan .nav-link {
            border: none;
            color: #8a94a6;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px 8px 0 0;
            transition: all 0.2s ease;
            cursor: pointer;
            display: block;
            text-decoration: none;
        }
        .nav-tabs-custom-kegiatan .nav-link:hover {
            background: #f0f4f8;
            color: #2c6b9e;
            text-decoration: none;
        }
        .nav-tabs-custom-kegiatan .nav-link.active {
            background: #e8f0fe;
            color: #2c6b9e;
            font-weight: 600;
            border-bottom: 3px solid #2c6b9e;
        }

        .tab-content-kegiatan {
            background: #ffffff;
            border: 1px solid #e8ecf1;
            border-top: none;
            border-radius: 0 0 12px 12px;
            padding: 20px;
            min-height: 200px;
        }

        .tab-pane {
            display: none;
        }
        .tab-pane.active,
        .tab-pane.show {
            display: block;
        }

        .table-kegiatan-detail {
            font-size: 13px;
            margin: 0;
        }
        .table-kegiatan-detail thead th {
            background: #f8f9fc;
            color: #4a5568;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 10px 14px;
            border-bottom: 2px solid #edf2f7;
        }
        .table-kegiatan-detail tbody td {
            padding: 10px 14px;
            border-bottom: 1px solid #f0f2f5;
            vertical-align: middle;
        }
        .table-kegiatan-detail tbody tr:last-child td { border-bottom: none; }

        .alert-info-custom {
            border-radius: 10px;
            border: none;
            background: #e8f0fe;
            color: #1a2634;
            padding: 12px 16px;
        }
        .alert-info-custom i { color: #2c6b9e; margin-right: 8px; }

        .btn-sm-kegiatan {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
        }

        .badge-pemeriksaan {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-pemeriksaan.Baik { background: #d1fae5; color: #047857; }
        .badge-pemeriksaan.Normal { background: #d1fae5; color: #047857; }
        .badge-pemeriksaan.Kurang { background: #fef3c7; color: #92400e; }
        .badge-pemeriksaan.Buruk { background: #fee2e2; color: #b91c1c; }
        .badge-pemeriksaan.Lebih { background: #dbeafe; color: #1d4ed8; }

        .badge-imunisasi-detail {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-imunisasi-detail.primary { background: #dbeafe; color: #1d4ed8; }
        .badge-imunisasi-detail.success { background: #d1fae5; color: #047857; }
        .badge-imunisasi-detail.warning { background: #fef3c7; color: #92400e; }
        .badge-imunisasi-detail.danger { background: #fee2e2; color: #b91c1c; }
        .badge-imunisasi-detail.info { background: #e0f7fa; color: #00838f; }
        .badge-imunisasi-detail.default { background: #f3f4f6; color: #6b7280; }

        .badge-trimester {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-trimester.t1 { background: #dbeafe; color: #1d4ed8; }
        .badge-trimester.t2 { background: #fef3c7; color: #92400e; }
        .badge-trimester.t3 { background: #fce4ec; color: #c62828; }
        .badge-trimester.t0 { background: #f3f4f6; color: #6b7280; }

        @media (max-width: 768px) {
            .card-header-kegiatan .d-flex {
                flex-direction: column;
                align-items: stretch !important;
                gap: 10px;
            }
            .card-header-kegiatan .text-right { text-align: left !important; }
            .nav-tabs-custom-kegiatan .nav-link { padding: 8px 12px; font-size: 12px; }
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