<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db_host = "localhost";
$db_user = "root";
$db_pass = ""; 
$db_name = "stokbarang";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

function hitungStokAktual($conn, $idbarang) {
    $queryStok = mysqli_query($conn, "SELECT stok FROM stok WHERE idbarang='$idbarang'");
    $dataStok = mysqli_fetch_array($queryStok);
    
    if (!$dataStok) {
        return 0;
    }
    
    return max(0, $dataStok['stok']);
}

// Fungsi untuk update stok berdasarkan transaksi
function updateStokBarang($conn, $idbarang) {
    // Ambil stok awal (stok yang diinput saat pertama kali menambah barang)
    $queryStokAwal = mysqli_query($conn, "SELECT stok FROM stok WHERE idbarang='$idbarang'");
    $dataStokAwal = mysqli_fetch_array($queryStokAwal);
    
    if (!$dataStokAwal) {
        return false;
    }
    
    $stokAwal = $dataStokAwal['stok'];
    
    // Hitung total masuk
    $queryMasuk = mysqli_query($conn, "SELECT COALESCE(SUM(jumlah), 0) as total FROM masuk WHERE idbarang='$idbarang'");
    $dataMasuk = mysqli_fetch_array($queryMasuk);
    $totalMasuk = $dataMasuk['total'];
    
    // Hitung total keluar
    $queryKeluar = mysqli_query($conn, "SELECT COALESCE(SUM(jumlah), 0) as total FROM keluar WHERE idbarang='$idbarang'");
    $dataKeluar = mysqli_fetch_array($queryKeluar);
    $totalKeluar = $dataKeluar['total'];
    
    // Stok akhir = stok_awal + total_masuk - total_keluar
    $stokAkhir = $stokAwal + $totalMasuk - $totalKeluar;
    
    // Update stok di tabel stok
    $updateQuery = mysqli_query($conn, "UPDATE stok SET stok = '$stokAkhir' WHERE idbarang = '$idbarang'");
    
    return $updateQuery;
}

// Menambah barang baru
if(isset($_POST['add'])) {
    $namabarang = htmlspecialchars($_POST['namabarang']);
    $kategori = htmlspecialchars($_POST['kategori']);
    $stok = intval($_POST['stok']); 

    // Foto upload
    $allowed_extension = array('png','jpg','jpeg');
    $nama = $_FILES['foto']['name'];
    $dot = explode('.', $nama);
    $ekstensi = strtolower(end($dot));
    $ukuran = $_FILES['foto']['size'];
    $file_tmp = $_FILES['foto']['tmp_name'];

    // Generate nama file baru
    $image = md5(uniqid($nama,true)).'.'.$ekstensi;

    // Validasi file
    if(in_array($ekstensi, $allowed_extension) === true) {
        if($ukuran <= 5000000){ // 5MB
            move_uploaded_file($file_tmp, '../images/foto_produk/'.$image);

            // Insert data barang dengan stok awal
            $addtotable = mysqli_query($conn, "INSERT INTO stok (namabarang, kategori, stok, foto) VALUES ('$namabarang', '$kategori', '$stok', '$image')");
            if($addtotable) {
                echo '<script>alert("Barang berhasil ditambahkan!"); window.location.href="stok.php";</script>';
            } else {
                echo '<script>alert("Gagal tambah stok");window.location.href="stok.php";</script>';
            }
        } else {
            echo '<script>alert("Ukuran file terlalu besar (max 5MB)");window.location.href="stok.php";</script>';
        }
    } else {
        echo '<script>alert("File harus JPG, JPEG, atau PNG");window.location.href="stok.php";</script>';
    }
}

// Menambah barang masuk
if(isset($_POST['addmasuk'])) {
    // Cek apakah sudah ada submit dalam 5 detik terakhir (prevent double submit)
    $timestamp = time();
    if(isset($_SESSION['last_submit_masuk']) && ($timestamp - $_SESSION['last_submit_masuk']) < 5) {
        echo '<script>alert("Terlalu cepat submit! Tunggu beberapa detik."); window.location.href="barangmasuk.php";</script>';
        exit();
    }
    
    $idbarang = $_POST['namabarang'];
    $jumlah = intval($_POST['jumlah']);
    $penerima = htmlspecialchars($_POST['penerima']);

    // Validasi input
    if(empty($idbarang)){
        echo '<script>alert("ID Barang tidak boleh kosong."); window.location.href="barangmasuk.php";</script>';
        exit();
    }
    
    if($jumlah <= 0){
        echo '<script>alert("Jumlah harus berupa angka positif."); window.location.href="barangmasuk.php";</script>';
        exit();
    }
    
    if(empty($penerima)){
        echo '<script>alert("Penerima tidak boleh kosong."); window.location.href="barangmasuk.php";</script>';
        exit();
    }

    // Cek apakah barang ada di tabel stok
    $cekbarang = mysqli_query($conn, "SELECT * FROM stok WHERE idbarang='$idbarang'");
    if(mysqli_num_rows($cekbarang) == 0) {
        echo '<script>alert("Barang tidak ditemukan!"); window.location.href="barangmasuk.php";</script>';
        exit();
    }

    // Cek duplikasi berdasarkan waktu dan data yang sama (dalam 10 detik terakhir)
    $check_duplicate = mysqli_query($conn, "SELECT * FROM masuk WHERE idbarang='$idbarang' AND jumlah='$jumlah' AND penerima='$penerima' AND tanggal >= DATE_SUB(NOW(), INTERVAL 10 SECOND)");
    if(mysqli_num_rows($check_duplicate) > 0) {
        echo '<script>alert("Data yang sama sudah diinput dalam 10 detik terakhir!"); window.location.href="barangmasuk.php";</script>';
        exit();
    }

    // Set session timestamp
    $_SESSION['last_submit_masuk'] = $timestamp;

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Tambah data barang masuk
        $addtomasuk = mysqli_query($conn, "INSERT INTO masuk (idbarang, jumlah, penerima) VALUES ('$idbarang', '$jumlah', '$penerima')");
        
        if(!$addtomasuk) {
            throw new Exception("Gagal menambahkan data barang masuk");
        }
        
        // Update stok langsung
$updateStok = mysqli_query($conn, "UPDATE stok SET stok = stok + $jumlah WHERE idbarang = '$idbarang'");

        // Commit transaksi
        mysqli_commit($conn);
        
        // Redirect dengan header untuk mencegah refresh duplicate
        header("Location: barangmasuk.php?success=1");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($conn);
        echo '<script>alert("' . $e->getMessage() . '"); window.location.href="barangmasuk.php";</script>';
    }
}

// Menambah barang keluar
if(isset($_POST['addkeluar'])) {
    $idbarang = $_POST['namabarang'];
    $jumlah = intval($_POST['jumlah']);
    $keterangan = htmlspecialchars($_POST['keterangan']);

    // Validasi input
    if(empty($idbarang)){
        echo '<script>alert("ID Barang tidak boleh kosong."); window.location.href="barangkeluar.php";</script>';
        exit();
    }
    
    if($jumlah <= 0){
        echo '<script>alert("Jumlah harus berupa angka positif."); window.location.href="barangkeluar.php";</script>';
        exit();
    }
    
    if(empty($keterangan)){
        echo '<script>alert("Keterangan tidak boleh kosong."); window.location.href="barangkeluar.php";</script>';
        exit();
    }

    // Cek apakah barang ada di tabel stok
    $cekbarang = mysqli_query($conn, "SELECT * FROM stok WHERE idbarang='$idbarang'");
    if(mysqli_num_rows($cekbarang) == 0) {
        echo '<script>alert("Barang tidak ditemukan!"); window.location.href="barangkeluar.php";</script>';
        exit();
    }

    // Hitung stok aktual saat ini
    $stokTersedia = hitungStokAktual($conn, $idbarang);
    
    // Validasi stok mencukupi
    if($stokTersedia < $jumlah){
        echo '<script>alert("Stok tidak mencukupi! Stok tersedia: '.$stokTersedia.' unit. Jumlah yang diminta: '.$jumlah.' unit."); window.location.href="barangkeluar.php";</script>';
        exit();
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Tambah data barang keluar
        $addtokeluar = mysqli_query($conn, "INSERT INTO keluar (idbarang, jumlah, keterangan) VALUES ('$idbarang', '$jumlah', '$keterangan')");
        
        if(!$addtokeluar) {
            throw new Exception("Gagal menambahkan data barang keluar");
        }

        // Update stok langsung
        $updateStok = mysqli_query($conn, "UPDATE stok SET stok = stok - $jumlah WHERE idbarang = '$idbarang'");

        // Commit transaksi
        mysqli_commit($conn);
        echo '<script>alert("Data barang keluar berhasil ditambahkan dan stok telah diperbarui!"); window.location.href="barangkeluar.php";</script>';
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($conn);
        echo '<script>alert("' . $e->getMessage() . '"); window.location.href="barangkeluar.php";</script>';
    }
}

// Edit barang masuk
if(isset($_POST['editmasuk'])){
    $idmasuk = $_POST['idmasuk'];
    $idbarang = $_POST['idbarang'];
    $jumlah = intval($_POST['jumlah']);
    $penerima = htmlspecialchars($_POST['penerima']);

    // Validasi input
    if($jumlah <= 0){
        echo '<script>alert("Jumlah harus berupa angka positif."); window.location.href="barangmasuk.php";</script>';
        exit();
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Update data barang masuk
        $updatemasuk = mysqli_query($conn, "UPDATE masuk SET jumlah='$jumlah', penerima='$penerima' WHERE idmasuk='$idmasuk'");
        
        if(!$updatemasuk) {
            throw new Exception("Gagal mengupdate data barang masuk");
        }
        
        // Update stok berdasarkan transaksi
        if(!updateStokBarang($conn, $idbarang)) {
            throw new Exception("Gagal mengupdate stok barang");
        }

        // Commit transaksi
        mysqli_commit($conn);
        echo '<script>alert("Data barang masuk berhasil diubah dan stok telah diperbarui!"); window.location.href="barangmasuk.php";</script>';
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($conn);
        echo '<script>alert("' . $e->getMessage() . '"); window.location.href="barangmasuk.php";</script>';
    }
}

// Edit barang keluar
if(isset($_POST['editkeluar'])){
    $idkeluar = $_POST['idkeluar'];
    $idbarang = $_POST['idbarang'];
    $jumlah = intval($_POST['jumlah']);
    $keterangan = htmlspecialchars($_POST['keterangan']);

    // Validasi input
    if($jumlah <= 0){
        echo '<script>alert("Jumlah harus berupa angka positif."); window.location.href="barangkeluar.php";</script>';
        exit();
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Update data barang keluar
        $updatekeluar = mysqli_query($conn, "UPDATE keluar SET jumlah='$jumlah', keterangan='$keterangan' WHERE idkeluar='$idkeluar'");
        
        if(!$updatekeluar) {
            throw new Exception("Gagal mengupdate data barang keluar");
        }
        
        // Update stok berdasarkan transaksi
        if(!updateStokBarang($conn, $idbarang)) {
            throw new Exception("Gagal mengupdate stok barang");
        }

        // Commit transaksi
        mysqli_commit($conn);
        echo '<script>alert("Data barang keluar berhasil diubah dan stok telah diperbarui!"); window.location.href="barangkeluar.php";</script>';
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($conn);
        echo '<script>alert("' . $e->getMessage() . '"); window.location.href="barangkeluar.php";</script>';
    }
}

// Edit barang (tanpa edit stok)
if(isset($_POST['editbarang'])){
    $idb = $_POST['idbarang'];
    $namabarang = htmlspecialchars($_POST['namabarangedit']);
    $kategori = htmlspecialchars($_POST['kategoriedit']);

    // Cek apakah user upload gambar baru
    if($_FILES['fotoedit']['name'] != '') {
        $allowed_extension = array('png','jpg','jpeg');
        $nama = $_FILES['fotoedit']['name'];
        $dot = explode('.', $nama);
        $ekstensi = strtolower(end($dot));
        $ukuran = $_FILES['fotoedit']['size'];
        $file_tmp = $_FILES['fotoedit']['tmp_name'];

        // Validasi file
        if(in_array($ekstensi, $allowed_extension) === true) {
            if($ukuran <= 5000000){ // 5MB
                // Generate nama file baru
                $image = md5(uniqid($nama,true)).'.'.$ekstensi;
                
                // Hapus gambar lama
                $ambilgambarlama = mysqli_query($conn, "SELECT foto FROM stok WHERE idbarang='$idb'");
                $gambarlama = mysqli_fetch_array($ambilgambarlama);
                if(file_exists('../images/foto_produk/'.$gambarlama['foto'])) {
                    unlink('../images/foto_produk/'.$gambarlama['foto']);
                }
                
                // Upload gambar baru
                move_uploaded_file($file_tmp, '../images/foto_produk/'.$image);

                // Update dengan gambar baru
                $update = mysqli_query($conn, "UPDATE stok SET namabarang='$namabarang', kategori='$kategori', foto='$image' WHERE idbarang='$idb'");
            } else {
                echo '<script>alert("Ukuran file terlalu besar (max 5MB)");window.location.href="stok.php";</script>';
                exit();
            }
        } else {
            echo '<script>alert("File harus JPG, JPEG, atau PNG");window.location.href="stok.php";</script>';
            exit();
        }
    } else {
        // Update tanpa ganti gambar
        $update = mysqli_query($conn, "UPDATE stok SET namabarang='$namabarang', kategori='$kategori' WHERE idbarang='$idb'");
    }

    if($update){
        echo '<script>alert("Data berhasil diubah!");window.location.href="stok.php";</script>';
    } else {
        echo '<script>alert("Gagal mengubah data!");window.location.href="stok.php";</script>';
    }
}

// Hapus barang masuk
if(isset($_POST['hapusmasuk'])) {
    $idmasuk = $_POST['idmasuk'];
    $idbarang = $_POST['idbarang'];
    
    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Hapus data barang masuk
        $hapus = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk='$idmasuk'");
        
        if(!$hapus) {
            throw new Exception("Gagal menghapus data barang masuk");
        }
        
        // Update stok berdasarkan transaksi
        if(!updateStokBarang($conn, $idbarang)) {
            throw new Exception("Gagal mengupdate stok barang");
        }

        // Commit transaksi
        mysqli_commit($conn);
        echo '<script>alert("Data barang masuk berhasil dihapus dan stok telah diperbarui!"); window.location.href="barangmasuk.php";</script>';
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($conn);
        echo '<script>alert("' . $e->getMessage() . '"); window.location.href="barangmasuk.php";</script>';
    }
}

// Hapus barang keluar
if(isset($_POST['hapuskeluar'])) {
    $idkeluar = $_POST['idkeluar'];
    $idbarang = $_POST['idbarang'];
    
    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Hapus data barang keluar
        $hapus = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar='$idkeluar'");
        
        if(!$hapus) {
            throw new Exception("Gagal menghapus data barang keluar");
        }
        
        // Update stok berdasarkan transaksi
        if(!updateStokBarang($conn, $idbarang)) {
            throw new Exception("Gagal mengupdate stok barang");
        }

        // Commit transaksi
        mysqli_commit($conn);
        echo '<script>alert("Data barang keluar berhasil dihapus dan stok telah diperbarui!"); window.location.href="barangkeluar.php";</script>';
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($conn);
        echo '<script>alert("' . $e->getMessage() . '"); window.location.href="barangkeluar.php";</script>';
    }
}

// Hapus barang
if(isset($_POST['hapusbarang'])) {
    $idbarang = $_POST['idbaranghapus'];
    
    // Cek apakah masih ada transaksi masuk/keluar untuk barang ini
    $cekmasuk = mysqli_query($conn, "SELECT COUNT(*) as total FROM masuk WHERE idbarang='$idbarang'");
    $cekkeluar = mysqli_query($conn, "SELECT COUNT(*) as total FROM keluar WHERE idbarang='$idbarang'");
    
    $datamasuk = mysqli_fetch_array($cekmasuk);
    $datakeluar = mysqli_fetch_array($cekkeluar);
    
    if($datamasuk['total'] > 0 || $datakeluar['total'] > 0) {
        echo '<script>alert("Tidak dapat menghapus barang yang memiliki riwayat transaksi masuk/keluar!");window.location.href="stok.php";</script>';
        exit();
    }
    
    // Ambil data gambar untuk dihapus
    $ambilgambar = mysqli_query($conn, "SELECT foto FROM stok WHERE idbarang='$idbarang'");
    $datagambar = mysqli_fetch_array($ambilgambar);
    
    // Hapus data dari database
    $hapus = mysqli_query($conn, "DELETE FROM stok WHERE idbarang='$idbarang'");
    
    if($hapus){
        // Hapus file gambar
        if(file_exists('../images/foto_produk/'.$datagambar['foto'])) {
            unlink('../images/foto_produk/'.$datagambar['foto']);
        }
        echo '<script>alert("Berhasil dihapus!");window.location.href="stok.php";</script>';
    } else {
        echo '<script>alert("Gagal hapus data!");window.location.href="stok.php";</script>';
    }
}

// Fungsi untuk mendapatkan total stok
function getTotalStok($conn) {
    $query = mysqli_query($conn, "SELECT SUM(stok) as total FROM stok");
    $data = mysqli_fetch_array($query);
    return $data['total'] ? $data['total'] : 0;
}

// Fungsi untuk mendapatkan jumlah jenis barang
function getJenisBarang($conn) {
    $query = mysqli_query($conn, "SELECT COUNT(*) as total FROM stok");
    $data = mysqli_fetch_array($query);
    return $data['total'];
}

// Fungsi untuk mendapatkan barang dengan stok rendah (kurang dari 10)
function getBarangStokRendah($conn) {
    $query = mysqli_query($conn, "SELECT * FROM stok WHERE stok < 10 ORDER BY stok ASC");
    return $query;
}

// Fungsi untuk mendapatkan stok real-time suatu barang
function getStokRealtime($conn, $idbarang) {
    return hitungStokAktual($conn, $idbarang);
}

// Fungsi untuk validasi stok sebelum pengeluaran
function validasiStokKeluar($conn, $idbarang, $jumlahKeluar) {
    $stokTersedia = hitungStokAktual($conn, $idbarang);
    return $stokTersedia >= $jumlahKeluar;
}

// Fungsi untuk sync semua stok (jika diperlukan)
function syncSemuaStok($conn) {
    $query = mysqli_query($conn, "SELECT idbarang FROM stok");
    $berhasil = 0;
    $total = 0;
    
    while($data = mysqli_fetch_array($query)) {
        $total++;
        if(updateStokBarang($conn, $data['idbarang'])) {
            $berhasil++;
        }
    }
    
    return array('berhasil' => $berhasil, 'total' => $total);
}

?>