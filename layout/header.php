
<?php
require 'function.php';
require 'cek.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Malilkids</title>

  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet" />
  <link href="css/responsive.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/home.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <div class="hero_area">
    <header class="header_section long_section px-0">
      <nav class="navbar navbar-expand-lg custom_nav-container">
        <a class="navbar-brand" href="home.php">
          <span class="brand-logo">
            <span class="text-blue">mal</span><span class="text-orange">il</span><span class="text-blue">kids</span>
          </span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
          <span class=""> </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <div class="d-flex mx-auto flex-column flex-lg-row align-items-center">
            <ul class="navbar-nav">
              <li class="nav-item active">
                <a class="nav-link" href="home.php">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="stok/stok.php">Stok Barang</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="barangmasuk/barangmasuk.php">Barang Masuk</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="barangkeluar/barangkeluar.php">Barang Keluar</a>
              </li>
            </ul>
          </div>
          <div class="quote_btn-container">
            <a href="auth/logout.php"><span>Logout</span></a>
          </div>
        </div>
      </nav>
    </header>
