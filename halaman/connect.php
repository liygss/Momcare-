<?php
require_once 'connect.php';
$host     = "localhost";
$user     = "root";
$pass     = "";
$database = "momcare";

$conn = mysqli_connect($host, $user, $pass, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Gagal koneksi: " . $conn->connect_error);
} else 
?>