<?php

require '../function.php';
require '../cek.php';

// Handle AJAX request for autocomplete
if(isset($_GET['term'])) {
    $searchTerm = $_GET['term'];
    $query = mysqli_query($conn, "SELECT idbarang, namabarang FROM stok WHERE namabarang LIKE '%$searchTerm%' ORDER BY namabarang ASC");
    
    $result = array();
    while ($row = mysqli_fetch_assoc($query)) {
        $data = array(
            'label' => $row['namabarang'],
            'value' => $row['idbarang']
        );
        array_push($result, $data);
    }
    
    // Return JSON and exit
    echo json_encode($result);
    exit();
}

// Tambah Barang Masuk
if(isset($_POST['addmasuk'])){
    $idbarang = $_POST['namabarang'];
    $jumlah = $_POST['jumlah'];
    $penerima = $_POST['penerima'];

    // Insert ke tabel masuk
    $addmasuk = mysqli_query($conn, "INSERT INTO masuk (idbarang, jumlah, penerima) VALUES ('$idbarang', '$jumlah', '$penerima')");
    
    if($addmasuk){
        // Update stok - tambah jumlah
        $updatestok = mysqli_query($conn, "UPDATE stok SET jumlah = jumlah + '$jumlah' WHERE idbarang='$idbarang'");
        
        if($updatestok){
            echo "<script>window.location.href='barangmasuk.php';</script>";
        } else {
            echo "<script>alert('Data masuk berhasil ditambah, tapi gagal update stok');</script>";
        }
    } else {
        echo "<script>alert('Gagal menambah data masuk');</script>";
    }
}

// Edit Barang Masuk
if(isset($_POST['updatebarangmasuk'])) {
    $idmasuk = $_POST['idmasuk'];
    $jumlah_baru = $_POST['jumlah'];
    $penerima = $_POST['penerima'];
    
    // Ambil data lama untuk menghitung selisih
    $data_lama = mysqli_query($conn, "SELECT idbarang, jumlah FROM masuk WHERE idmasuk='$idmasuk'");
    $row = mysqli_fetch_assoc($data_lama);
    $idbarang = $row['idbarang'];
    $jumlah_lama = $row['jumlah'];
    
    // Hitung selisih
    $selisih = $jumlah_baru - $jumlah_lama;
    
    // Update tabel masuk
    $query = mysqli_query($conn, "UPDATE masuk SET jumlah='$jumlah_baru', penerima='$penerima' WHERE idmasuk='$idmasuk'");

    if($query){
        // Update stok berdasarkan selisih
        $updatestok = mysqli_query($conn, "UPDATE stok SET jumlah = jumlah + '$selisih' WHERE idbarang='$idbarang'");
        
        if($updatestok){
            echo "<script>window.location.href='barangmasuk.php';</script>";
        } else {
            echo "<script>alert('Data masuk berhasil diupdate, tapi gagal update stok');</script>";
        }
    } else {
        echo "<script>alert('Gagal update data');</script>";
    }
}

// Hapus Barang Masuk
if(isset($_GET['hapusbarangmasuk'])){
    $idmasuk = $_GET['hapusbarangmasuk'];
    
    // Ambil data untuk mengembalikan stok
    $data = mysqli_query($conn, "SELECT idbarang, jumlah FROM masuk WHERE idmasuk='$idmasuk'");
    $row = mysqli_fetch_assoc($data);
    $idbarang = $row['idbarang'];
    $jumlah = $row['jumlah'];
    
    // Hapus dari tabel masuk
    $hapus = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk='$idmasuk'");

    if($hapus){
        // Kurangi stok
        $updatestok = mysqli_query($conn, "UPDATE stok SET jumlah = jumlah - '$jumlah' WHERE idbarang='$idbarang'");
        
        if($updatestok){
            echo "<script>window.location.href='barangmasuk.php';</script>";
        } else {
            echo "<script>alert('Data masuk berhasil dihapus, tapi gagal update stok');</script>";
        }
    } else {
        echo "<script>alert('Gagal hapus data');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Malilkids</title>

  <!-- CSS Bootstrap -->
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/responsive.css">
  <link rel="stylesheet" href="../css/font-awesome.min.css">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <!-- jQuery UI CSS for Autocomplete -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
     <link rel="stylesheet" href="css/home.css" />
     <style>
/* CSS untuk memastikan navbar konsisten */
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

  /* Autocomplete styling */
  .ui-autocomplete {
    max-height: 200px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 9999 !important;
  }
</style>

</head>

<body>
<div class="hero_area">
  <header class="header_section long_section px-0">
    <!-- Navbar yang benar untuk halaman Barang Masuk -->
<nav class="navbar navbar-expand-lg custom_nav-container">
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
        <li class="nav-item">
          <a class="nav-link" href="../stok/stok.php">Stok Barang</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="barangmasuk.php">Barang Masuk</a>
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

  <!-- Content -->
  <div class="content container mt-4">
    <h2 class="mb-4">Data Barang Masuk</h2>
    <div class="card">
      <div class="card-header">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
          Tambah Data
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="datatablesSimple" class="table table-striped table-bordered">
            <thead class="thead-dark">
              <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Penerima</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $ambilsemuadatastock = mysqli_query($conn, "SELECT m.idmasuk, s.namabarang, m.jumlah, m.penerima 
                                                          FROM masuk m 
                                                          JOIN stok s ON m.idbarang = s.idbarang");
              $i = 1;
              while($data=mysqli_fetch_array($ambilsemuadatastock)){
                  $idm = $data['idmasuk'];
                  $namabarang = $data['namabarang'];
                  $jumlah = $data['jumlah'];
                  $penerima = $data['penerima'];
              ?>
              <tr>
                <td><?=$i++;?></td>
                <td><?=$namabarang;?></td>
                <td><?=$jumlah;?></td>
                <td><?=$penerima;?></td>
                <td>
                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?=$idm;?>">Edit</button>
                <a href="?hapusbarangmasuk=<?=$idm;?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
              </tr>

              <!-- Modal Edit -->
              <div class="modal fade" id="editModal<?=$idm;?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="post">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Barang Masuk</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="idmasuk" value="<?=$idm;?>">
                        <div class="form-group">
                          <label>Nama Barang</label>
                          <input type="text" class="form-control" value="<?=$namabarang;?>" readonly>
                        </div>
                        <div class="form-group">
                          <label>Jumlah</label>
                          <input type="number" name="jumlah" value="<?=$jumlah;?>" class="form-control" required>
                        </div>
                        <div class="form-group">
                          <label>Penerima</label>
                          <input type="text" name="penerima" value="<?=$penerima;?>" class="form-control" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" name="updatebarangmasuk">Simpan</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- End Content -->

  <!-- Modal Tambah Data -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
        
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Tambah Data Barang Masuk</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <form method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <!-- Hidden ID Barang -->
            <input type="hidden" id="idbarang" name="namabarang">
            
            <div class="form-group">
              <label for="namabarang_search">Nama Barang:</label>
              <input type="text" id="namabarang_search" placeholder="Ketik Nama Barang..." class="form-control" required>
              <small class="form-text text-muted">Ketik minimal 2 huruf untuk mencari barang</small>
            </div>
            
            <div class="form-group">
              <label for="jumlah">Jumlah:</label>
              <input type="number" name="jumlah" id="jumlah" placeholder="Jumlah" class="form-control" required>
            </div>
            
            <div class="form-group">
              <label for="penerima">Penerima:</label>
              <input type="text" name="penerima" id="penerima" placeholder="Penerima" class="form-control" required>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" name="addmasuk">Submit</button>
          </div>
        </form>

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
</div>

  <!-- Scripts -->
  <script src="../js/jquery-3.4.1.min.js"></script>
  <script src="../js/bootstrap.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <script src="../js/custom.js"></script>

  <script>
    $(document).ready(function() {
      // Initialize DataTable
      $('#datatablesSimple').DataTable({
        "lengthChange": false,
        "searching": true
      });

      // Update Tahun Otomatis
      document.getElementById("displayYear").innerHTML = new Date().getFullYear();

      // Autocomplete Nama Barang
      $("#namabarang_search").autocomplete({
        source: 'barangmasuk.php', // Using the same file
        minLength: 2,
        select: function(event, ui){
          event.preventDefault();
          $("#namabarang_search").val(ui.item.label);
          $("#idbarang").val(ui.item.value);
          return false;
        }
      }).autocomplete("instance")._renderItem = function(ul, item) {
        // Custom rendering for dropdown items
        return $("<li>")
          .append("<div>" + item.label + "</div>")
          .appendTo(ul);
      };
    });
  </script>

</body>

</html>