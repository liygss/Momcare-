<?php
session_start();
include 'connect.php';

$email = $_POST['email'];
$password = $_POST['password'];

// Siapkan statement ambil berdasarkan email
$stmt = mysqli_prepare($conn , "SELECT * FROM users WHERE email=?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Ambil user
if ($row = mysqli_fetch_assoc($result)) {
    // Cocokkan password dengan password_verify
    if (password_verify($password, $row['password'])) {
        $_SESSION['email'] = $row['nama'];
        $_SESSION['role'] = $row['role'];
        header("Location: index.php");
        exit;
    } else {
        echo "Password salah! <a href='halaman_login.php'>Coba lagi</a>";
    }
} else {
    echo "Email tidak ditemukan! <a href='halaman_login.php'>Coba lagi</a>";
}
?>
