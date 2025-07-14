<?php
require '../function.php';
require '../cek.php';
// Tangkap filter kategori (kalau ada)
$filter_kategori = isset($_GET['filter_kategori']) ? $_GET['filter_kategori'] : '';

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

if(isset($_POST['addkeluar'])){
    $namabarang = $_POST['namabarang'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    $tanggal = date('Y-m-d H:i:s'); // Auto set tanggal sekarang

    if(empty($namabarang) || empty($jumlah) || empty($keterangan)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        mysqli_begin_transaction($conn);
        
        try {
            $cek_stok = mysqli_query($conn, "SELECT qty FROM stok WHERE idbarang='$namabarang'");
            
            if(mysqli_num_rows($cek_stok) == 0) {
                throw new Exception('Barang tidak ditemukan dalam stok');
            }
            
            $row_stok = mysqli_fetch_assoc($cek_stok);
            $stok_tersedia = $row_stok['qty'];
            
            if($stok_tersedia < $jumlah) {
                throw new Exception('Stok tidak mencukupi! Stok tersedia: ' . $stok_tersedia);
            }
            
            // Tambahkan tanggal ke INSERT query
            $addkeluar = mysqli_query($conn, "INSERT INTO keluar (idbarang, jumlah, keterangan, tanggal) VALUES ('$namabarang', '$jumlah', '$keterangan', '$tanggal')");
            
            if($addkeluar){
                $updatestok = mysqli_query($conn, "UPDATE stok SET qty = qty - '$jumlah' WHERE idbarang='$namabarang'");
                
                if($updatestok){
                    mysqli_commit($conn);
                    echo "<script>
                        alert('Data berhasil ditambahkan!');
                        window.location.href='barangkeluar.php';
                    </script>";
                    exit; 
                } else {
                    throw new Exception('Gagal update stok');
                }
            } else {
                throw new Exception('Gagal menambah data keluar');
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
}
// Edit Barang Keluar
if(isset($_POST['updatebarangkeluar'])){
    $idkeluar = $_POST['idkeluar'];
    $jumlah_baru = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    
    mysqli_begin_transaction($conn);
    
    try {
        // Ambil data lama untuk menghitung selisih
        $data_lama = mysqli_query($conn, "SELECT idbarang, jumlah FROM keluar WHERE idkeluar='$idkeluar'");
        $row = mysqli_fetch_assoc($data_lama);
        $idbarang = $row['idbarang'];
        $jumlah_lama = $row['jumlah'];
        
        // Hitung selisih
        $selisih = $jumlah_baru - $jumlah_lama;
        
        $query = mysqli_query($conn, "UPDATE keluar SET jumlah='$jumlah_baru', keterangan='$keterangan' WHERE idkeluar='$idkeluar'");

        if($query){
            $updatestok = mysqli_query($conn, "UPDATE stok SET stok = stok - '$selisih' WHERE idbarang='$idbarang'");
            
            if($updatestok){
                mysqli_commit($conn);
                echo "<script>
                    alert('Data berhasil diupdate!');
                    window.location.href='barangkeluar.php';
                </script>";
                exit;
            } else {
                throw new Exception('Gagal update stok');
            }
        } else {
            throw new Exception('Gagal update data keluar');
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// Hapus Barang Keluar
if(isset($_GET['hapuskeluar'])){
    $idkeluar = $_GET['hapuskeluar'];
    
    mysqli_begin_transaction($conn);
    
    try {
        // Ambil data untuk mengembalikan stok
        $data = mysqli_query($conn, "SELECT idbarang, jumlah FROM keluar WHERE idkeluar='$idkeluar'");
        $row = mysqli_fetch_assoc($data);
        $idbarang = $row['idbarang'];
        $jumlah = $row['jumlah'];
        
        // Hapus dari tabel keluar
        $hapus = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar='$idkeluar'");

        if($hapus){
            // Kembalikan stok (tambahkan kembali)
            $updatestok = mysqli_query($conn, "UPDATE stok SET qty = qty + '$jumlah' WHERE idbarang='$idbarang'");
            
            if($updatestok){
                mysqli_commit($conn);
                echo "<script>
                    alert('Data berhasil dihapus!');
                    window.location.href='barangkeluar.php';
                </script>";
                exit;
            } else {
                throw new Exception('Gagal update stok');
            }
        } else {
            throw new Exception('Gagal hapus data');
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Malilkids - Barang Keluar</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/bootstrap.css" />
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet" />
    <link href="../css/font-awesome.min.css" rel="stylesheet" />
    <link href="../css/style.css" rel="stylesheet" />
    <link href="../css/responsive.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

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
        }

        .card-header .btn-primary:hover {
            background-color: #005f8a;
            border-color: #005f8a;
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
                            <li class="nav-item">
                                <a class="nav-link" href="../barangmasuk/barangmasuk.php">Barang Masuk</a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="barangkeluar.php">Barang Keluar</a>
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
            <h2 class="mb-4">Data Barang Keluar</h2>
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                        Tambah Data
                    </button>
                    <a href="cetaklaporan_keluar.php" target="_blank" class="btn btn-success" style="margin-left: 10px;">
                        <i class="fa fa-print"></i> Cetak Laporan
                    </a>
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
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Tambahkan field tanggal ke query dan urutkan berdasarkan tanggal terbaru
                                $ambildatastok = mysqli_query($conn, "SELECT k.idkeluar, k.tanggal, s.namabarang, k.jumlah, k.keterangan 
                                                                      FROM keluar k 
                                                                      JOIN stok s ON k.idbarang = s.idbarang 
                                                                      ORDER BY k.tanggal DESC");
                                $i = 1;
                                while($data=mysqli_fetch_array($ambildatastok)){
                                    $idk = $data['idkeluar'];
                                    $tanggal = $data['tanggal'];
                                    $namabarang = $data['namabarang'];
                                    $jumlah = $data['jumlah'];
                                    $keterangan = $data['keterangan'];
                                    
                                    // Format tanggal untuk tampilan
                                    $tanggal_formatted = date('d/m/Y H:i', strtotime($tanggal));
                                ?>
                                <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=$tanggal_formatted;?></td>
                                    <td><?=$namabarang;?></td>
                                    <td><?=$jumlah;?></td>
                                    <td><?=$keterangan;?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?=$idk;?>">Edit</button>
                                        <a href="?hapuskeluar=<?=$idk;?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data keluar ini?')">Hapus</a>
                                    </td>
                                </tr>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="editModal<?=$idk;?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Barang Keluar</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="idkeluar" value="<?=$idk;?>">
                                                    <div class="form-group">
                                                        <label>Tanggal</label>
                                                        <input type="text" class="form-control" value="<?=$tanggal_formatted;?>" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Nama Barang</label>
                                                        <input type="text" class="form-control" value="<?=$namabarang;?>" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Jumlah</label>
                                                        <input type="number" name="jumlah" value="<?=$jumlah;?>" class="form-control" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Keterangan</label>
                                                        <input type="text" name="keterangan" value="<?=$keterangan;?>" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary" name="updatebarangkeluar">Simpan</button>
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
                        <h4 class="modal-title">Tambah Data Barang Keluar</h4>
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
                                <label for="keterangan">Keterangan:</label>
                                <input type="text" name="keterangan" id="keterangan" placeholder="Keterangan" class="form-control" required>
                            </div>
                            
                            <small class="form-text text-muted">Tanggal akan diisi otomatis saat data disimpan</small>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" name="addkeluar">Submit</button>
                        </div>
                    </form>

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
    </div>

    <!-- jQuery, Bootstrap, and DataTables scripts -->
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
                "searching": true,
                "order": [[ 1, "desc" ]] // Urutkan berdasarkan tanggal terbaru
            });

            document.getElementById("displayYear").innerHTML = new Date().getFullYear();

            $("#namabarang_search").autocomplete({
                source: 'barangkeluar.php',
                minLength: 2,
                select: function(event, ui){
                    event.preventDefault();
                    $("#namabarang_search").val(ui.item.label);
                    $("#idbarang").val(ui.item.value);
                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append("<div>" + item.label + "</div>")
                    .appendTo(ul);
            };
        });
    </script>

</body>

</html>