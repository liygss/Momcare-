<?php
include 'connect.php';

if (isset($_GET['id'])) {
    
    $id_pemeriksaan = mysqli_real_escape_string($conn, $_GET['id']);

 
    $sql = "DELETE FROM pemeriksaan_ibu WHERE id = '$id_pemeriksaan'"; 

    if (mysqli_query($conn, $sql)) {
        header("Location: jadwal_pemeriksaan.php");
        exit(); 
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "ID pemeriksaan tidak ditentukan.";
    exit();
}
?>