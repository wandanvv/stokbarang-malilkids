<?php
// Pastikan session dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include function.php dari parent directory
require '../function.php';

// Jika sudah login, redirect ke home
if(isset($_SESSION['log']) && $_SESSION['log'] === 'True'){
    header('location:../home.php');
    exit();
}

// Proses login
if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi input tidak kosong
    if(empty($email) || empty($password)){
        $error = "Email dan password harus diisi!";
    } else {
        // Escape string untuk keamanan
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);
        
        // Debug: tampilkan input (hapus setelah selesai debug)
        // echo "Email: '$email', Password: '$password'<br>";
        
        // Query ke database - coba beberapa variasi
        $queries = [
            // Query normal
            "SELECT * FROM user WHERE email='$email' AND password='$password'",
            // Query dengan TRIM (menghilangkan spasi)
            "SELECT * FROM user WHERE TRIM(email)='$email' AND TRIM(password)='$password'",
            // Query case insensitive
            "SELECT * FROM user WHERE LOWER(email)=LOWER('$email') AND password='$password'",
            // Query dengan MD5 (jika password di-hash dengan MD5)
            "SELECT * FROM user WHERE email='$email' AND password=MD5('$password')",
            // Query dengan SHA1 (jika password di-hash dengan SHA1)
            "SELECT * FROM user WHERE email='$email' AND password=SHA1('$password')"
        ];
        
        $login_success = false;
        $user_data = null;
        
        foreach($queries as $index => $query){
            $cekdb = mysqli_query($conn, $query);
            
            if($cekdb){
                $hitung = mysqli_num_rows($cekdb);
                
                if($hitung > 0){
                    $login_success = true;
                    $user_data = mysqli_fetch_assoc($cekdb);
                    // Debug: tampilkan query yang berhasil
                    // echo "Query berhasil #" . ($index + 1) . ": " . $query . "<br>";
                    break;
                }
            }
        }
        
        if($login_success){
            // Login berhasil
            $_SESSION['log'] = 'True';
            $_SESSION['user_id'] = $user_data['id'] ?? '';
            $_SESSION['user_email'] = $user_data['email'] ?? '';
            
            // Redirect ke home
            header('location:../home.php');
            exit();
        } else {
            // Cek apakah email ada di database
            $check_email = mysqli_query($conn, "SELECT * FROM user WHERE email='$email' OR TRIM(email)='$email' OR LOWER(email)=LOWER('$email')");
            
            if(mysqli_num_rows($check_email) > 0){
                $error = "Password salah!";
            } else {
                $error = "Email tidak ditemukan!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MALIKIDS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <!-- Header with logo -->
    <div class="header">
        <a href="#" class="logo">
            <span class="text-blue">mal</span><span class="text-pink">il</span><span class="text-blue">kids</span>
        </a>
    </div>

    <!-- Floating decorations -->
    <div class="decoration"></div>
    <div class="decoration"></div>
    <div class="decoration"></div>

    <!-- Main login container -->
    <div class="login-container">
        <form method="post" class="login">
            <fieldset>
                <legend class="legend">Welcome Back</legend>
                <p class="subtitle">Please sign in to your account</p>
                
                <?php if(isset($error)): ?>
                    <div class="error-message" style="color: red; text-align: center; margin-bottom: 15px; background: #ffe6e6; padding: 10px; border-radius: 5px;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div class="input">
                    <input type="email" name="email" placeholder="Enter your email" required />
                    <span><i class="fa fa-envelope"></i></span>
                </div>
                
                <div class="input">
                    <input type="password" name="password" placeholder="Enter your password" required />
                    <span><i class="fa fa-lock"></i></span>
                </div>
                
                <button type="submit" name="login" class="submit">Sign In</button>
            </fieldset>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        $(".input input").focusin(function() {
            $(this).siblings("span").animate({"opacity":"0"}, 200);
        });

        $(".input input").focusout(function() {
            $(this).siblings("span").animate({"opacity":"1"}, 300);
        });

        // Add loading state on form submit
        $("form.login").submit(function() {
            $(".submit").addClass("loading");
        });
    });
    </script>
</body>
</html>