<?php
// Pastikan session dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if(!isset($_SESSION['log']) || $_SESSION['log'] !== 'True'){
    header('location:../login/');
    exit();
}

// Pastikan username ada di session, jika tidak ada ambil dari database
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
    if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){
        // Ambil username dari database berdasarkan user_id
        $user_id = $_SESSION['user_id'];
        $query = mysqli_query($conn, "SELECT username FROM user WHERE iduser='$user_id'");
        
        if($query && mysqli_num_rows($query) > 0){
            $user_data = mysqli_fetch_assoc($query);
            $_SESSION['username'] = $user_data['username'];
        } else {
            // Jika tidak ditemukan, set default
            $_SESSION['username'] = 'User';
        }
    } else {
        $_SESSION['username'] = 'User';
    }
}
?>