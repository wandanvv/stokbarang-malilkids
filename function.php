<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db_host = "localhost";
$db_user = "root";
$db_pass = ""; 
$db_name = "stokbarang";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);



//menambah barang baru
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
        // cek ukuran file
        if($ukuran <= 5000000){ // 5MB
            move_uploaded_file($file_tmp, '../images/foto_produk/'.$image);

            $addtotable = mysqli_query($conn, "INSERT INTO stok (namabarang, kategori, stok, foto) VALUES ('$namabarang', '$kategori', '$stok', '$image')");
            if($addtotable) {
                header('location:stok.php');
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
    $idbarang = $_POST['namabarang'];
    $jumlah = $_POST['jumlah'];
    $penerima = $_POST['penerima'];

    // Validasi input
    if(empty($idbarang)){
        echo '<script>alert("ID Barang tidak boleh kosong."); window.location.href="barangmasuk.php";</script>';
        exit();
    }
    
    if(!is_numeric($jumlah) || $jumlah <= 0){
        echo '<script>alert("Jumlah harus berupa angka positif."); window.location.href="barangmasuk.php";</script>';
        exit();
    }
    
    if(empty($penerima)){
        echo '<script>alert("Penerima tidak boleh kosong."); window.location.href="barangmasuk.php";</script>';
        exit();
    }

    // Menambah data barang masuk
    $addtomasuk = mysqli_query($conn, "INSERT INTO masuk (idbarang, jumlah, penerima) VALUES ('$idbarang', '$jumlah', '$penerima')");
    
    // Update stok barang
    if($addtomasuk){
        // Ambil stok saat ini
        $cekstok = mysqli_query($conn, "SELECT * FROM stok WHERE idbarang='$idbarang'");
        $ambildata = mysqli_fetch_array($cekstok);
        
        $stoksekarang = $ambildata['stok'];
        $tambahkanstoksekarangdenganquantitybaru = $stoksekarang + $jumlah;
        
        // Update stok
        $updatestokmasuk = mysqli_query($conn, "UPDATE stok SET stok='$tambahkanstoksekarangdenganquantitybaru' WHERE idbarang='$idbarang'");
        
        if($updatestokmasuk){
            echo '<script>alert("Data berhasil ditambahkan!"); window.location.href="barangmasuk.php";</script>';
        } else {
            echo '<script>alert("Gagal mengupdate stok barang!"); window.location.href="barangmasuk.php";</script>';
        }
    } else {
        echo '<script>alert("Gagal menambahkan data barang masuk!"); window.location.href="barangmasuk.php";</script>';
    }
}

// Menambah barang keluar
if(isset($_POST['addkeluar'])) {
    $idbarang = $_POST['namabarang'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    // Validasi input
    if(empty($idbarang)){
        echo '<script>alert("ID Barang tidak boleh kosong."); window.location.href="barangkeluar.php";</script>';
        exit();
    }
    
    if(!is_numeric($jumlah) || $jumlah <= 0){
        echo '<script>alert("Jumlah harus berupa angka positif."); window.location.href="barangkeluar.php";</script>';
        exit();
    }
    
    if(empty($keterangan)){
        echo '<script>alert("Keterangan tidak boleh kosong."); window.location.href="barangkeluar.php";</script>';
        exit();
    }

    // Cek stok tersedia
    $cekstoktersedia = mysqli_query($conn, "SELECT * FROM stok WHERE idbarang='$idbarang'");
    $ambildatastok = mysqli_fetch_array($cekstoktersedia);
    
    // Sesuaikan dengan nama kolom di database (yaitu 'stok' bukan 'stock')
    $stoktersedia = $ambildatastok['stok'];
    
    // Tambahkan debug untuk memeriksa nilai
    // echo "Stok tersedia: " . $stoktersedia . " | Jumlah diminta: " . $jumlah;
    // exit();
    
    // Cek apakah stok cukup - konversi ke integer untuk memastikan perbandingan numerik
    if((int)$stoktersedia < (int)$jumlah){
        echo '<script>alert("Stok tidak mencukupi! Stok tersedia: '.$stoktersedia.'"); window.location.href="barangkeluar.php";</script>';
        exit();
    }

    if(isset($_POST['addkeluar'])){
        $idbarang = $_POST['namabarang'];
        $jumlah = $_POST['jumlah'];
        $keterangan = $_POST['keterangan'];
    
        // Tambahkan data ke tabel keluar
        $addtokeluar = mysqli_query($conn, "INSERT INTO keluar (idbarang, jumlah, keterangan) VALUES ('$idbarang', '$jumlah', '$keterangan')");
    
        if($addtokeluar){
            // Kurangi stok di tabel stok
            $updatestok = mysqli_query($conn, "UPDATE stok SET stok = stok - $jumlah WHERE idbarang = '$idbarang'");
    
            if($updatestok){
                echo "<script>window.location.href='barangkeluar.php';</script>";
            } else {
                echo "<script>alert('Gagal update stok');</script>";
            }
        } else {
            echo "<script>alert('Gagal tambah data');</script>";
        }
    }
}

if(isset($_POST['editbarang'])){
    $idb = $_POST['idbarang'];
    $namabarang = $_POST['namabarangedit'];
    $stok = $_POST['stokedit'];

    // Cek apakah user upload gambar baru
    if($_FILES['fotoedit']['name'] != '') {
        $image = $_FILES['fotoedit']['name'];
        $file_tmp = $_FILES['fotoedit']['tmp_name'];

        // Simpan gambar ke folder yang benar
        move_uploaded_file($file_tmp, '../images/foto_produk/'.$image);

        // Update dengan gambar baru
        $update = mysqli_query($conn, "UPDATE stok SET namabarang='$namabarang', stok='$stok', foto='$image' WHERE idbarang='$idb'");
    } else {
        // Update tanpa ganti gambar
        $update = mysqli_query($conn, "UPDATE stok SET namabarang='$namabarang', stok='$stok' WHERE idbarang='$idb'");
    }

    if($update){
        echo '<script>alert("Data berhasil diubah!");window.location.href="stok.php";</script>';
    } else {
        echo '<script>alert("Gagal mengubah data!");window.location.href="stok.php";</script>';
    }
}

if(isset($_POST['hapusbarang'])) {
    $idbarang = $_POST['idbaranghapus'];
    $hapus = mysqli_query($conn, "DELETE FROM stok WHERE idbarang='$idbarang'");
    if($hapus){
        echo '<script>alert("Berhasil dihapus!");window.location.href="stok.php";</script>';
    } else {
        echo '<script>alert("Gagal hapus data!");window.location.href="stok.php";</script>';
    }
}


    ?>