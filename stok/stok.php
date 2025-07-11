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

  .alert-stok {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 20px;
  }

  .info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    transition: transform 0.3s ease;
  }

  .info-card:hover {
    transform: translateY(-5px);
  }

  .info-card .card-body {
    padding: 1.5rem;
  }

  .info-card .card-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    opacity: 0.9;
  }

  .info-card .display-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .info-card.total-stock {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  }

  .info-card.jenis-barang {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  }

  .info-card.stok-rendah {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    color: #333;
  }

  .info-card.stok-rendah .card-title {
    color: #333;
    opacity: 0.8;
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
        <?php if($filter_kategori) { ?>
          <a href="stok.php" class="btn btn-secondary ml-2">Reset</a>
        <?php } ?>
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
          $ambil = mysqli_query($conn, "SELECT * FROM stok WHERE kategori='$filter_kategori' ORDER BY namabarang ASC");
        } else {
          $ambil = mysqli_query($conn, "SELECT * FROM stok ORDER BY namabarang ASC");
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
            <img src="../images/foto_produk/<?= htmlspecialchars($foto); ?>" 
                 width="60" height="60" 
                 alt="<?= htmlspecialchars($namabarang); ?>"
                 style="object-fit: cover; border-radius: 5px;">
          </td>
          <td><?= htmlspecialchars($namabarang); ?></td>
          <td><?= htmlspecialchars($kategori); ?></td>
          <td>
            <span class="badge <?= ($stok < 10) ? 'badge-danger' : 'badge-success'; ?>">
              <?= $stok; ?>
            </span>
          </td>
       
          <td>
            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit<?= $idbarang; ?>">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalHapus<?= $idbarang; ?>">
              <i class="fa fa-trash"></i> Hapus
            </button>
          </td>
        </tr>

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
                  
                  <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="namabarangedit" value="<?= htmlspecialchars($namabarang); ?>" class="form-control" required>
                  </div>
                  
                   <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategoriedit" class="form-control" required>
                      <option value="">Pilih Kategori</option>
                      <?php
                      $ambilkategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY namakategori ASC");
                      while($datakategori = mysqli_fetch_array($ambilkategori)){
                        $namakategori = $datakategori['namakategori'];
                        $selected = ($kategori == $namakategori) ? 'selected' : '';
                        echo "<option value='$namakategori' $selected>$namakategori</option>";
                      }
                      ?>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label>Stok Saat Ini</label>
                    <input type="number" value="<?= $stok; ?>" class="form-control" readonly>
                    <small class="text-muted">
                      <i class="fa fa-info-circle"></i> 
                      Stok otomatis terhitung dari transaksi barang masuk dan keluar
                    </small>
                  </div>
                  
                  <div class="form-group">
                    <label>Foto (kosongkan jika tidak ingin mengganti)</label>
                    <input type="file" name="fotoedit" class="form-control" accept=".jpg,.jpeg,.png">
                    <small class="text-muted">Foto saat ini: <?= htmlspecialchars($foto); ?></small>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
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
                      <div class="alert alert-warning mt-2">
                        <small><i class="fa fa-exclamation-triangle"></i> Barang yang memiliki riwayat transaksi tidak dapat dihapus</small>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                      <button type="submit" name="hapusbarang" class="btn btn-danger">Hapus</button>
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

  <!-- footer start -->
  <footer class="footer_section bg-light text-center py-3 mt-auto">
    <div class="container">
      <p class="mb-0">
        &copy; <span id="displayYear"></span> All Rights Reserved
        <a href=""></a>
      </p>
    </div>
  </footer>
  <!-- footer end -->

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
        <form method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="form-group">
              <label>Nama Barang</label>
              <input type="text" name="namabarang" placeholder="Masukkan nama barang" class="form-control" required>
            </div>
            
            <div class="form-group">
              <label>Kategori</label>
              <select name="kategori" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php
                  $getkategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY namakategori ASC");
                  while($kategori = mysqli_fetch_array($getkategori)){
                    echo "<option value='".$kategori['namakategori']."'>".$kategori['namakategori']."</option>";
                  }
                ?>
              </select>
            </div>
            
            <div class="form-group">
              <label>Stok Awal</label>
              <input type="number" name="stok" placeholder="Masukkan jumlah stok" class="form-control" required min="0">
            </div>
            
            <div class="form-group">
              <label>Foto Produk</label>
              <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="form-control" required>
              <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 5MB</small>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" name="add">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- jQuery, Bootstrap, and DataTables scripts -->
  <script src="../js/jquery-3.4.1.min.js"></script>
  <script src="../js/bootstrap.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="../js/custom.js"></script>

  <script>
    $(document).ready(function() {
      $('#datatablesSimple').DataTable({
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "language": {
          "search": "Cari:",
          "lengthMenu": "Tampilkan _MENU_ data per halaman",
          "zeroRecords": "Data tidak ditemukan",
          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
          "infoFiltered": "(disaring dari _MAX_ total data)",
          "paginate": {
            "first": "Pertama",
            "last": "Terakhir",
            "next": "Selanjutnya",
            "previous": "Sebelumnya"
          }
        }
      });
    });

    // Untuk update tahun otomatis di footer
    document.getElementById("displayYear").innerHTML = new Date().getFullYear();
    
    // Validasi form sebelum submit
    document.querySelector('form[method="post"]').addEventListener('submit', function(e) {
      var stok = document.querySelector('input[name="stok"]').value;
      if (stok < 0) {
        e.preventDefault();
        alert('Stok tidak boleh bernilai negatif!');
      }
    });
  </script>

</body>
</html>