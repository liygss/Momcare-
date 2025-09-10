<?php
require_once 'connect.php';
// Menghubungkan ke database

$host     = "localhost";
$user     = "root";
$pass     = "";
$database = "ibu hamil";

$conn = new mysqli($host, $user, $pass, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Gagal koneksi: " . $conn->connect_error);
} else {
    echo "Berhasil terkoneksi ke database.";
}
?>