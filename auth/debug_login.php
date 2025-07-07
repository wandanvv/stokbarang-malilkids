<?php
// Debug login issues
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Koneksi database
$db_host = "localhost";
$db_user = "root";
$db_pass = ""; 
$db_name = "stokbarang";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Tampilkan semua data user di database
echo "<h2>Data User di Database:</h2>";
$result = mysqli_query($conn, "SELECT * FROM user");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Email</th><th>Password</th><th>Length Email</th><th>Length Password</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>'" . $row['email'] . "'</td>";
        echo "<td>'" . $row['password'] . "'</td>";
        echo "<td>" . strlen($row['email']) . "</td>";
        echo "<td>" . strlen($row['password']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . mysqli_error($conn);
}

echo "<hr>";

// Test login form
if(isset($_POST['test_login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    echo "<h2>Debug Login Process:</h2>";
    echo "Email yang diinput: '" . $email . "' (length: " . strlen($email) . ")<br>";
    echo "Password yang diinput: '" . $password . "' (length: " . strlen($password) . ")<br>";
    
    // Test query tanpa escape dulu
    $query1 = "SELECT * FROM user WHERE email='$email' AND password='$password'";
    echo "Query: " . $query1 . "<br>";
    
    $result1 = mysqli_query($conn, $query1);
    if($result1){
        $count = mysqli_num_rows($result1);
        echo "Jumlah row ditemukan: " . $count . "<br>";
        
        if($count > 0){
            echo "<span style='color: green;'>✓ LOGIN BERHASIL!</span><br>";
            $user_data = mysqli_fetch_assoc($result1);
            echo "Data user yang ditemukan: ";
            print_r($user_data);
        } else {
            echo "<span style='color: red;'>✗ LOGIN GAGAL - Data tidak ditemukan</span><br>";
            
            // Cek apakah email ada
            $check_email = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
            if(mysqli_num_rows($check_email) > 0){
                echo "Email ditemukan, kemungkinan password salah<br>";
                $user_data = mysqli_fetch_assoc($check_email);
                echo "Password di database: '" . $user_data['password'] . "'<br>";
                echo "Password yang diinput: '" . $password . "'<br>";
                echo "Apakah sama? " . ($user_data['password'] === $password ? "YA" : "TIDAK") . "<br>";
            } else {
                echo "Email tidak ditemukan di database<br>";
            }
        }
    } else {
        echo "Error query: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Login</title>
</head>
<body>
    <h2>Test Login Form</h2>
    <form method="post">
        <p>
            <label>Email:</label><br>
            <input type="email" name="email" required style="width: 300px; padding: 5px;">
        </p>
        <p>
            <label>Password:</label><br>
            <input type="text" name="password" required style="width: 300px; padding: 5px;">
            <small>(Saya sengaja pakai type='text' agar bisa melihat password)</small>
        </p>
        <p>
            <button type="submit" name="test_login">Test Login</button>
        </p>
    </form>
</body>
</html>