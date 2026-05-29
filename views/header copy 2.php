<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link href="img/logo/logo.png" rel="icon">
  <title>Posyandu</title>

  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

<style>

/* =========================
   BODY
========================= */

html,
body{
    height:100%;
}

body{
    margin:0;
    background:#f4f7fe !important;
    font-family:'Nunito',sans-serif;
    color:#4a4a4a;
    overflow:hidden;
}


/* =========================
   WRAPPER
========================= */

#wrapper{
    display:flex;
    height:100vh;
    overflow:hidden;
}


/* =========================
   SIDEBAR
========================= */

.sidebar{
    width:220px !important;
    min-width:220px !important;
    height:100vh;
    background:linear-gradient(180deg,#4e73df,#3558c8) !important;

    display:flex;
    flex-direction:column;

    overflow:hidden;
}

/* BRAND */

.sidebar-brand{
    height:72px !important;
    min-height:72px !important;
    display:flex !important;
    align-items:center;
    justify-content:center;
    gap:10px;
    padding:0 16px !important;

    background:rgba(255,255,255,.03);
    border-bottom:1px solid rgba(255,255,255,.08);
}

.sidebar-brand-icon{
    font-size:22px;
    color:white;
}

.sidebar-brand-text{
    font-size:18px !important;
    font-weight:800;
    color:white !important;
    margin:0 !important;
}

/* MENU AREA */

.sidebar-menu{
    flex:1;
    overflow-y:auto;
    overflow-x:hidden;
    padding-top:10px;
    padding-bottom:10px;

    scrollbar-width:thin;
    scrollbar-color:rgba(255,255,255,.18) transparent;
}

/* SCROLLBAR */

.sidebar-menu::-webkit-scrollbar{
    width:5px;
}

.sidebar-menu::-webkit-scrollbar-thumb{
    background:rgba(255,255,255,.18);
    border-radius:10px;
}

.sidebar-menu::-webkit-scrollbar-track{
    background:transparent;
}

/* DIVIDER */

.sidebar-divider{
    margin:10px 14px !important;
    border-color:rgba(255,255,255,.10);
}

/* HEADING */

.sidebar-heading{
    padding:14px 18px 6px !important;
    font-size:10px !important;
    color:rgba(255,255,255,.55) !important;
    letter-spacing:1px;
    font-weight:700;
}

/* ITEM */

.sidebar .nav-item{
    margin:4px 10px;
}

/* LINK */

.sidebar .nav-link{
    padding:11px 14px !important;
    border-radius:12px;
    font-size:13px;
    font-weight:600;
    color:rgba(255,255,255,.88) !important;
    display:flex;
    align-items:center;
    transition:.2s;
}

/* ICON */

.sidebar .nav-link i{
    width:18px;
    font-size:14px;
    margin-right:10px;
    color:rgba(255,255,255,.75);
    transition:.2s;
}

/* HOVER */

.sidebar .nav-link:hover{
    background:rgba(255,255,255,.12) !important;
    color:white !important;
    text-decoration:none;
}

.sidebar .nav-link:hover i{
    color:white !important;
}

/* ACTIVE */

.sidebar .nav-item.active .nav-link{
    background:rgba(255,255,255,.18) !important;
    color:white !important;
    font-weight:700;
}

.sidebar .nav-item.active .nav-link i{
    color:white !important;
}

/* LOGOUT */

.sidebar-logout{
    padding-bottom:10px;
}

.sidebar-logout .nav-link{
    background:rgba(255,255,255,.08);
}

.sidebar-logout .nav-link:hover{
    background:#e74a3b !important;
}


/* =========================
   CONTENT WRAPPER
========================= */

#content-wrapper{
    flex:1;
    overflow:hidden;
    display:flex;
    flex-direction:column;
}


/* =========================
   TOPBAR
========================= */

.topbar{
    height:60px !important;
    min-height:60px !important;
    padding:0 18px !important;

    background:linear-gradient(90deg,#4e73df,#657ff1) !important;

    box-shadow:0 2px 10px rgba(0,0,0,.05) !important;

    z-index:10;
}

.topbar .navbar-nav .nav-link{
    height:60px !important;
    display:flex;
    align-items:center;
}

#sidebarToggleTop{
    width:36px;
    height:36px;
    border-radius:10px;
    color:white !important;
    font-size:14px;
}

#sidebarToggleTop:hover{
    background:rgba(255,255,255,.10);
}

/* PROFILE */

.img-profile{
    width:34px !important;
    height:34px !important;
    object-fit:cover;
    border:2px solid rgba(255,255,255,.25);
}

.topbar .text-white{
    font-size:13px !important;
    font-weight:600;
}


/* =========================
   CONTENT
========================= */

#content{
    flex:1;
    overflow-y:auto;
    background:#f4f7fe;
}

/* CONTENT SCROLLBAR */

#content::-webkit-scrollbar{
    width:6px;
}

#content::-webkit-scrollbar-thumb{
    background:#d8dff0;
    border-radius:10px;
}

/* CONTAINER */

.container-fluid{
    padding:18px !important;
}

/* TITLE */

.h3{
    font-size:22px !important;
    margin-bottom:3px !important;
    font-weight:700;
    color:#2e3a59;
}

.text-muted{
    font-size:13px;
    color:#7c859a !important;
}


/* =========================
   CARD
========================= */

.card{
    border:none !important;
    border-radius:16px !important;
    background:white;
    box-shadow:0 4px 18px rgba(0,0,0,.05) !important;
}

.card-header{
    background:white !important;
    border-bottom:1px solid #eef2f7 !important;
}

.card-body{
    padding:16px !important;
}


/* =========================
   TABLE
========================= */

.table{
    margin-bottom:0;
}

.table thead th{
    font-size:11px;
    padding:12px !important;
    background:#edf2ff;
    border:none !important;
    color:#4e73df;
    font-weight:700;
}

.table td{
    padding:12px !important;
    font-size:13px;
    vertical-align:middle !important;
    border-top:1px solid #f1f3f8 !important;
}

.table-hover tbody tr:hover{
    background:#f8faff;
}


/* =========================
   BUTTON
========================= */

.btn{
    border-radius:10px !important;
    font-size:12px !important;
    padding:7px 14px !important;
    font-weight:600;
}

.btn-primary{
    background:#4e73df !important;
    border:none !important;
}

.btn-success,
.btn-danger,
.btn-warning{
    border:none !important;
}

.btn-warning{
    color:white !important;
}

.btn-light{
    background:#eef2f7 !important;
    border:none !important;
}

.btn-icon{
    width:30px;
    height:30px;
    padding:0 !important;
    display:flex;
    align-items:center;
    justify-content:center;
}


/* =========================
   FORM
========================= */

.form-control{
    height:40px !important;
    border-radius:10px !important;
    border:1px solid #dfe5f1 !important;
    font-size:13px !important;
    box-shadow:none !important;
}

.form-control:focus{
    border-color:#4e73df !important;
    box-shadow:0 0 0 3px rgba(78,115,223,.08) !important;
}

textarea.form-control{
    height:auto !important;
}

label{
    font-size:12px;
    margin-bottom:5px;
    font-weight:700;
    color:#4f5b75;
}


/* =========================
   MODAL
========================= */

.modal-content{
    border-radius:16px !important;
    border:none !important;
    overflow:hidden;
}

.modal-header{
    padding:14px 18px !important;
    background:linear-gradient(90deg,#4e73df,#657ff1);
    color:white;
    border:none !important;
}

.modal-title{
    font-size:15px;
    font-weight:700;
}

.modal-body{
    padding:18px !important;
}

.modal-footer{
    padding:12px 18px !important;
    border-top:1px solid #edf2f7 !important;
}


/* =========================
   FOOTER
========================= */

footer.sticky-footer{
    background:white !important;
    padding:10px 0 !important;
    font-size:12px;
    color:#7c859a;
    border-top:1px solid #edf2f7;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width:768px){

    body{
        overflow:auto;
    }

    .sidebar{
        width:200px !important;
        min-width:200px !important;
    }

    .container-fluid{
        padding:12px !important;
    }

    .h3{
        font-size:18px !important;
    }

}

</style>

</head>
<script>
$("#sidebarToggleTop, #sidebarToggle").on('click', function(e) {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
});
</script>

<body id="page-top">

<div id="wrapper">