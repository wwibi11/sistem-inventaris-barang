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

body{
    background: #f4f7fe !important;
    font-family: 'Nunito', sans-serif;
    color: #4a4a4a;
}


/* =========================
   SIDEBAR
========================= */

.sidebar{
    width: 210px !important;
    min-height: 100vh;
    background: linear-gradient(180deg,#4e73df,#3558c8) !important;
}

/* ITEM */

.sidebar .nav-item{
    margin: 3px 10px;
}

/* LINK */

.sidebar .nav-link{
    padding: 10px 14px !important;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: rgba(255,255,255,.88) !important;
    transition: .2s;
}

/* ICON */

.sidebar .nav-link i{
    width: 18px;
    font-size: 13px;
    margin-right: 8px;
    color: rgba(255,255,255,.75);
}

/* HOVER */

.sidebar .nav-link:hover{
    background: rgba(255,255,255,.10) !important;
    color: white !important;
}

.sidebar .nav-link:hover i{
    color: white !important;
}

/* ACTIVE */

.sidebar .nav-item.active .nav-link{
    background: rgba(255,255,255,.18) !important;
    color: white !important;
    font-weight: 700;
    box-shadow: none !important;
}

.sidebar .nav-item.active .nav-link i{
    color: white !important;
}

/* BRAND */

.sidebar-brand{
    height: 58px !important;
    padding: 0 !important;
    margin-bottom: 4px;
}

.sidebar-brand-text{
    font-size: 22px !important;
    font-weight: 800;
    color: white !important;
}

/* HEADING */

.sidebar-heading{
    padding: 14px 18px 6px !important;
    font-size: 10px !important;
    color: rgba(255,255,255,.55) !important;
    letter-spacing: 1px;
    font-weight: 700;
}

/* DIVIDER */

.sidebar-divider{
    margin: 8px 14px !important;
    border-color: rgba(255,255,255,.08);
}

/* =========================
   TOPBAR
========================= */

.topbar{
    height: 60px !important;
    min-height: 60px !important;
    padding: 0 18px !important;
    background: linear-gradient(90deg,#4e73df,#657ff1) !important;
    box-shadow: 0 2px 10px rgba(0,0,0,.06) !important;
}

.topbar .navbar-nav .nav-link{
    height: 60px !important;
    display: flex;
    align-items: center;
}

#sidebarToggleTop{
    height: 36px;
    width: 36px;
    font-size: 14px;
    color: white !important;
    border-radius: 8px;
}

#sidebarToggleTop:hover{
    background: rgba(255,255,255,.10);
}

/* FOTO USER */

.img-profile{
    width: 34px !important;
    height: 34px !important;
    object-fit: cover;
    border: 2px solid rgba(255,255,255,.25);
}

/* NAMA USER */

.topbar .text-white{
    font-size: 13px !important;
    font-weight: 600;
}


/* =========================
   CONTENT
========================= */

#content{
    background: #f4f7fe;
}

.container-fluid{
    padding: 18px !important;
}

/* JUDUL HALAMAN */

.h3{
    font-size: 22px !important;
    margin-bottom: 3px !important;
    font-weight: 700;
    color: #2e3a59;
}

.text-muted{
    font-size: 13px;
    color: #7c859a !important;
}


/* =========================
   CARD
========================= */

.card{
    border: none !important;
    border-radius: 16px !important;
    background: white;
    box-shadow: 0 4px 18px rgba(0,0,0,.05) !important;
}

.card-header{
    background: white !important;
    border-bottom: 1px solid #eef2f7 !important;
}

.card-body{
    padding: 16px !important;
}


/* =========================
   TABLE
========================= */

.table{
    margin-bottom: 0;
}

.table thead th{
    font-size: 11px;
    padding: 12px !important;
    background: #edf2ff;
    border: none !important;
    color: #4e73df;
    font-weight: 700;
}

.table td{
    padding: 12px !important;
    font-size: 13px;
    vertical-align: middle !important;
    border-top: 1px solid #f1f3f8 !important;
}

.table-hover tbody tr:hover{
    background: #f8faff;
}


/* =========================
   BUTTON
========================= */

.btn{
    border-radius: 10px !important;
    font-size: 12px !important;
    padding: 7px 14px !important;
    font-weight: 600;
}

.btn-primary{
    background: #4e73df !important;
    border: none !important;
}

.btn-success{
    border: none !important;
}

.btn-danger{
    border: none !important;
}

.btn-warning{
    border: none !important;
    color: white !important;
}

.btn-light{
    background: #eef2f7 !important;
    border: none !important;
}

.btn-icon{
    width: 30px;
    height: 30px;
    padding: 0 !important;
    display: flex;
    align-items: center;
    justify-content: center;
}


/* =========================
   FORM
========================= */

.form-control{
    height: 40px !important;
    border-radius: 10px !important;
    border: 1px solid #dfe5f1 !important;
    font-size: 13px !important;
    box-shadow: none !important;
}

.form-control:focus{
    border-color: #4e73df !important;
    box-shadow: 0 0 0 3px rgba(78,115,223,.08) !important;
}

textarea.form-control{
    height: auto !important;
}

label{
    font-size: 12px;
    margin-bottom: 5px;
    font-weight: 700;
    color: #4f5b75;
}


/* =========================
   MODAL
========================= */

.modal-content{
    border-radius: 16px !important;
    border: none !important;
    overflow: hidden;
}

.modal-header{
    padding: 14px 18px !important;
    background: linear-gradient(90deg,#4e73df,#657ff1);
    color: white;
    border: none !important;
}

.modal-title{
    font-size: 15px;
    font-weight: 700;
}

.modal-body{
    padding: 18px !important;
}

.modal-footer{
    padding: 12px 18px !important;
    border-top: 1px solid #edf2f7 !important;
}


/* =========================
   FOOTER
========================= */

footer.sticky-footer{
    background: white !important;
    padding: 10px 0 !important;
    font-size: 12px;
    color: #7c859a;
    border-top: 1px solid #edf2f7;
}

footer.sticky-footer .copyright{
    line-height: 1.2;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width:768px){

    .sidebar{
        width: 200px !important;
    }

    .container-fluid{
        padding: 12px !important;
    }

    .h3{
        font-size: 18px !important;
    }

}

</style>

</head>

<body id="page-top">
<div id="wrapper">