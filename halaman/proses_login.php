<?php
session_start();
include 'connect.php'; // Pastikan file connect.php ada dan berisi koneksi database

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = mysqli_prepare($conn, "SELECT id, nama, role, password FROM users WHERE email=?"); // Ambil 'id' juga untuk user_id
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    if (password_verify($password, $row['password'])) {
        // Setel semua variabel sesi yang diperlukan
        $_SESSION['user_id'] = $row['id']; // Penting untuk digunakan di halaman lain seperti index_tamu.php
        $_SESSION['email'] = $row['email']; // Asumsi ada kolom email di tabel users, kalau tidak, bisa pakai $email
        $_SESSION['nama'] = $row['nama']; // Untuk penggunaan umum
        $_SESSION['user_nama'] = $row['nama']; // Khusus untuk index_tamu.php
        $_SESSION['role'] = $row['role']; // Role dari database

        // Logika pengalihan berdasarkan role
        if ($row['role'] == 'Ibu Hamil') {
            header("Location: index_tamu.php"); // Arahkan ke halaman beranda Ibu Hamil
            exit();
        } elseif ($row['role'] == 'Petugas') {
            header("Location: index.php"); // Arahkan ke halaman beranda Petugas
            exit();
        } else {
            // Jika role tidak dikenal (ini seharusnya tidak terjadi jika ENUM sudah benar),
            // arahkan ke halaman login dengan pesan error
            header("Location: halaman_login.php?error=unknown_role");
            exit();
        }
    } else {
        // Alihkan kembali ke halaman login dengan pesan error
        header("Location: halaman_login.php?error=password_salah");
        exit();
    }
} else {
    // Alihkan kembali ke halaman login dengan pesan error
    header("Location: halaman_login.php?error=email_tidak_ditemukan");
    exit();
}
?>