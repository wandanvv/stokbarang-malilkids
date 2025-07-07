<?php 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if(!isset($_SESSION['log']) || $_SESSION['log'] !== 'True') {
    // Redirect ke login.php di folder auth
    header('location:auth/login.php');
    exit();
}
?>