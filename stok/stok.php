<?php
require '../function.php';
require '../cek.php';

// Tangkap filter kategori (kalau ada)
$filter_kategori = isset($_GET['filter_kategori']) ? $_GET['filter_kategori'] : '';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Malilkids - Stock Barang</title>

  <!-- Bootstrap core css -->
  <link rel="stylesheet" href="../css/bootstrap.css" />
  <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet" />
  <!-- Font awesome -->
  <link href="../css/font-awesome.min.css" rel="stylesheet" />
  <!-- Custom styles -->
  <link href="../css/style.css" rel="stylesheet" />
  <link href="../css/responsive.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/home.css" />

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
  h2.mb-4 {
    font-weight: 700;
    color: #0077b6;
    text-align: center;
    margin-bottom: 30px;
  }

  .card {
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 119, 182, 0.08);
  }

  .card-header {
    background-color: #e9f5fc;
    border-bottom: 2px solid #0077b6;
    padding: 20px;
  }

  .card-header .btn-primary {
    background-color: #0077b6;
    border-color: #0077b6;
    font-weight: 600;
    margin-right: 10px;
  }

  .card-header .btn-primary:hover {
    background-color: #005f8a;
    border-color: #005f8a;
  }

  .card-header .btn-success {
    background-color: #ff8800;
    border-color: #ff8800;
    font-weight: 600;
  }

  .card-header .btn-success:hover {
    background-color: #ff6600;
    border-color: #ff6600;
  }

  .form-control, select {
    border-radius: 8px;
  }

  table th {
    background-color: #0077b6;
    color: #fff;
    text-align: center;
  }

  table td {
    vertical-align: middle;
    text-align: center;
  }

  .btn-warning {
    background-color: #ffca3a;
    border: none;
    font-weight: 600;
    color: #333;
  }

  .btn-warning:hover {
    background-color: #f5b700;
    color: #fff;
  }

  .btn-danger {
    background-color: #e63946;
    border: none;
    font-weight: 600;
  }

  .btn-danger:hover {
    background-color: #d62828;
  }

  .modal-header {
    background-color: #0077b6;
    color: white;
  }

  .modal-footer .btn-primary {
    background-color: #0077b6;
    border-color: #0077b6;
  }

  .modal-footer .btn-primary:hover {
    background-color: #005f8a;
    border-color: #005f8a;
  }

  .footer_section {
    background-color: #0077b6;
    color: white;
  }

  .footer_section a {
    color: #ffdd99;
  }

  .footer_section a:hover {
    color: #ffffff;
  }
</style>

</head>

<body>
<div class="hero_area">
  <header class="header_section long_section px-0">
    <nav class="navbar navbar-expand-lg custom_nav-container ">
      <a class="navbar-brand" href="../home.php">
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
            <li class="nav-item">
              <a class="nav-link" href="../home.php">Home</a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="stok.php">Stok Barang</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../barangmasuk/barangmasuk.php">Barang Masuk</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../barangkeluar/barangkeluar.php">Barang Keluar</a>
            </li>
          </ul>
        </div>
        <div class="quote_btn-container">
          <a href="../auth/logout.php">
            <span>Logout</span>
          </a>
        </div>
      </div>
    </nav>
  </header>

  <div class="content container mt-4">
  <h2 class="mb-4">Stock Barang</h2>
  <div class="card">
    <div class="card-header">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
          Tambah Stok
      </button>
      <!-- Filter kategori -->
      <form method="get" class="form-inline mt-3">
        <select name="filter_kategori" class="form-control mr-2">
          <option value="">Semua Kategori</option>
          <?php
          $getkategori = mysqli_query($conn, "SELECT * FROM kategori");
          while($kategori = mysqli_fetch_array($getkategori)){
            $selected = ($filter_kategori == $kategori['namakategori']) ? 'selected' : '';
            echo "<option value='".$kategori['namakategori']."' $selected>".$kategori['namakategori']."</option>";
          }
          ?>
        </select>
        <button type="submit" class="btn btn-success">Filter</button>
      </form>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table id="datatablesSimple" class="table table-striped table-bordered">
          <thead class="thead-dark">
            <tr>
              <th>No</th>
              <th>Foto</th>
              <th>Nama Barang</th>
              <th>Kategori</th>
              <th>Stok</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if($filter_kategori){
              $ambil = mysqli_query($conn, "SELECT * FROM stok WHERE kategori='$filter_kategori'");
            } else {
              $ambil = mysqli_query($conn, "SELECT * FROM stok");
            }

            $i = 1;
            while($data = mysqli_fetch_array($ambil)){
              $foto = $data['foto']; 
              $namabarang = $data['namabarang'];
              $kategori = $data['kategori'];
              $stok = $data['stok'];
              $idbarang = $data['idbarang'];
            ?>
            <tr>
              <td><?= $i++; ?></td>
              <td>
              <img src="../images/foto_produk/<?= htmlspecialchars($foto); ?>" width="60" alt="<?= htmlspecialchars($namabarang); ?>">
              </td>
              <td><?= htmlspecialchars($namabarang); ?></td>
              <td><?= htmlspecialchars($kategori); ?></td>
              <td><?= htmlspecialchars($stok); ?></td>
              <td>
              <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit<?= $idbarang; ?>">Edit</button>
              <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalHapus<?= $idbarang; ?>">Hapus</button>
              </td>
            </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

  <!-- footer start -->
  <footer class="footer_section bg-light text-center py-3 mt-auto">
    <div class="container">
      <p class="mb-0">
        &copy; <span id="displayYear"></span> All Rights Reserved By
        <a href="https://html.design/">Free Html Templates</a>
      </p>
    </div>
  </footer>
  <!-- footer end -->

  <!-- jQuery, Bootstrap, and DataTables scripts -->
  <script src="../js/jquery-3.4.1.min.js"></script>
  <script src="../js/bootstrap.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="../js/custom.js"></script>

  <script>
   $('#datatablesSimple').DataTable({
    "lengthChange": false,
    "searching": false
});

    // Untuk update tahun otomatis di footer
    document.getElementById("displayYear").innerHTML = new Date().getFullYear();
  </script>

</body>
 <!-- Modal tambah stok -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
    
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Tambah Stok Barang</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      
      <!-- Modal Body -->
      <form method="post" enctype="multipart/form-data"> <!-- tambahkan enctype -->
        <div class="modal-body">
          <input type="text" name="namabarang" placeholder="Nama Barang" class="form-control" required> <br>
          
          <select name="kategori" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            <?php
              $getkategori = mysqli_query($conn, "SELECT * FROM kategori");
              while($kategori = mysqli_fetch_array($getkategori)){
                echo "<option value='".$kategori['namakategori']."'>".$kategori['namakategori']."</option>";
              }
            ?>
          </select>
          <br>
          
          <input type="number" name="stok" placeholder="Stok" class="form-control" required> <br>
          
          <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="form-control" required> <br> <!-- file input -->
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" name="add">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit untuk setiap barang -->
<div class="modal fade" id="modalEdit<?= $idbarang; ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h4 class="modal-title">Edit Barang</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="idbarang" value="<?= $idbarang; ?>">
          <input type="text" name="namabarangedit" value="<?= htmlspecialchars($namabarang); ?>" class="form-control" required><br>
          <input type="number" name="stokedit" value="<?= $stok; ?>" class="form-control" required><br>
          <input type="file" name="fotoedit" class="form-control"><br>
        </div>
        <div class="modal-footer">
          <button type="submit" name="editbarang" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus<?= $idbarang; ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          Yakin ingin menghapus <strong><?= htmlspecialchars($namabarang); ?></strong>?
          <input type="hidden" name="idbaranghapus" value="<?= $idbarang; ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" name="hapusbarang" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

</html>