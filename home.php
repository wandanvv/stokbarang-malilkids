<?php
require 'function.php';
require 'cek.php';

// Ajax: Suggestion list
if(isset($_POST['keyword'])){
  $keyword = mysqli_real_escape_string($conn, $_POST['keyword']);
  $query = mysqli_query($conn, "SELECT idbarang, namabarang FROM stok WHERE namabarang LIKE '%$keyword%' LIMIT 5");
  while($row = mysqli_fetch_assoc($query)){
    echo '<a href="#" class="list-group-item list-group-item-action search-item" data-id="'.$row['idbarang'].'">'.$row['namabarang'].'</a>';
  }
  exit;
}

// Ajax: Detail produk
if(isset($_POST['idbarang'])){
  $id = mysqli_real_escape_string($conn, $_POST['idbarang']);
  $query = mysqli_query($conn, "SELECT * FROM stok WHERE idbarang = '$id'");
  if($row = mysqli_fetch_assoc($query)){
    $foto = !empty($row['foto']) ? "images/foto_produk/".$row['foto'] : "images/default.jpg";
    echo '
      <div class="card mx-auto" style="max-width: 400px;">
        <img src="'.$foto.'" class="card-img-top" alt="Foto Produk" style="object-fit:cover;height:300px;">
        <div class="card-body text-center">
          <h5 class="card-title">'.$row['namabarang'].'</h5>
          <p class="card-text">Stok saat ini: <strong>'.$row['stok'].'</strong></p>
        </div>
      </div>
    ';
  } else {
    echo '<div class="alert alert-warning">Data tidak ditemukan.</div>';
  }
  exit;
}

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
  <style>
     body {
    font-family: 'Poppins', sans-serif;
    background: #f9f9f9;
    color: #333;
  }

  .text-blue {
    color: #0077b6 !important;
    font-weight: 700;
    font-size: 28px;
  }

  .text-orange {
    color: #ff8800 !important;
    font-weight: 700;
    font-size: 28px;
  }

  .brand-logo {
    font-size: 32px;
  }

  .navbar {
    background-color: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 119, 182, 0.1);
  }

  .nav-link {
    color: #0077b6 !important;
    margin: 0 10px;
    transition: color 0.3s ease;
  }

  .nav-link:hover {
    color: #ff8800 !important;
  }

  .quote_btn-container a {
    background-color: #ff8800;
    color: #fff;
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
  }

  .quote_btn-container a:hover {
    background-color: #ff6600;
    color: #fff;
  }

  .search-container {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
  }

  .search-box .form-control {
    border-radius: 25px 0 0 25px;
    border: 2px solid #0077b6;
  }

  .search-box .btn-primary {
    background-color: #0077b6;
    border-color: #0077b6;
    border-radius: 0 25px 25px 0;
    font-weight: 600;
  }

  .search-box .btn-primary:hover {
    background-color: #005f8a;
    border-color: #005f8a;
  }

  .card {
    border: 1px solid #ddd;
    box-shadow: 0 4px 12px rgba(0, 119, 182, 0.15);
    border-radius: 15px;
  }

  .card-title {
    color: #0077b6;
    font-weight: bold;
  }

  .text-primary {
    color: #ff8800 !important;
  }

  .footer_section {
    background: #0077b6;
    color: #fff;
    text-align: center;
    padding: 15px 0;
    margin-top: 30px;
  }

  .footer_section a {
    color: #ffdd99;
  }

  .alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
    border-radius: 10px;
    padding: 15px;
    font-weight: 500;
  }

  /* Optional efek hover hasil pencarian */
  .search-item:hover {
    background-color: #ffdd99;
    color: #333;
  }
  </style>
</head>

<body>
  <div class="hero_area">
    <header class="header_section long_section px-0">
      <nav class="navbar navbar-expand-lg custom_nav-container ">
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
            <a href="auth/logout.php">
              <span>Logout</span>
            </a>
          </div>
        </div>
      </nav>
    </header>

    <section class="search_section">
      <div class="container search-container">
        <h1 class="mb-4">Cari Stok Produk</h1>
        <form method="POST" class="search-box">
          <div class="input-group">
            <input type="text" class="form-control" name="searchtext" id="searchtext" placeholder="Masukkan nama produk..." required>
            <div class="input-group-append">
              <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Cari</button>
            </div>
          </div>
        </form>

        <div id="result-box">
          <?php
          if(isset($_POST['searchtext'])) {
            $cari = mysqli_real_escape_string($conn, $_POST['searchtext']);
            $query = "SELECT * FROM stok WHERE namabarang LIKE '%$cari%' LIMIT 1";
            $result = mysqli_query($conn, $query);
            
            if(mysqli_num_rows($result) > 0) {
              $row = mysqli_fetch_assoc($result);
              $foto = !empty($row['foto']) ? 'images/foto_produk/'.$row['foto'] : 'images/noimage.png';
              echo '
              <div class="card mx-auto p-3" style="max-width: 600px;">
                <div class="row g-0 align-items-center">
                  <div class="col-md-8">
                    <div class="card-body text-start">
                      <h5 class="card-title">'.$row['namabarang'].'</h5>
                      <p class="card-text mb-1">Stok saat ini:</p>
                      <h4 class="text-primary">'.$row['stok'].'</h4>
                    </div>
                  </div>
                  <div class="col-md-4 text-end">
                    <img src="'.$foto.'" alt="Foto Produk" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                  </div>
                </div>
              </div>';
            } else {
              echo '<div class="alert alert-warning mt-4">Produk tidak ditemukan.</div>';
            }
          }
          ?>
        </div>
      </div>
    </section>
  </div>

  <footer class="footer_section">
    <div class="container">
      <p>
        &copy; <span id="displayYear"></span> All Rights Reserved
        <a href="">2025</a>
      </p>
    </div>
  </footer>

  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/custom.js"></script>
  <script>
    $(document).ready(function(){
  $('#search-input').on("keyup", function(){
    let keyword = $(this).val();
    if(keyword.length > 1){
      $.ajax({
        url: '', // home.php itu sendiri
        type: 'POST',
        data: {keyword: keyword},
        success: function(data){
          $('#suggestion-box').html(data).show();
        }
      });
    } else {
      $('#suggestion-box').hide();
      $('#result-box').html('');
    }
  });

  $(document).on('click', '.search-item', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    var nama = $(this).text();
    $('#search-input').val(nama);
    $('#suggestion-box').hide();

    $.ajax({
      url: '',
      type: 'POST',
      data: {idbarang: id},
      success: function(data){
        $('#result-box').html(data);
      }
    });
  });
});
  </script>
</body>

</html>
