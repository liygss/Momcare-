<?php
session_start();
include 'connect.php'; // Pastikan file connect.php berisi koneksi database ($conn)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data dari POST request
    $nama_ibu = htmlspecialchars($_POST['nama_ibu']); // Mengganti $ibu menjadi $nama_ibu
    $tanggal_pemeriksaan = htmlspecialchars($_POST['tanggal_pemeriksaan']);
    $alasan_rujukan = htmlspecialchars($_POST['alasan_rujukan']);
    $rumah_sakit_tujuan = htmlspecialchars($_POST['rumah_sakit_tujuan']);
    $catatan = htmlspecialchars($_POST['catatan']);
    // $id_pemeriksaan_ibu = isset($_POST['id_pemeriksaan_ibu']) ? intval($_POST['id_pemeriksaan_ibu']) : null; // Ini mungkin tidak lagi diperlukan dengan kolom nama_ibu

    // 1. Definisikan query SQL untuk INSERT data
    // Sesuaikan nama tabel (misal: 'rujukan') dan kolom-kolomnya dengan struktur database Anda.
    // Ganti 'ibu' di sini dengan 'nama_ibu'
    $sql = "INSERT INTO rujukan (nama_ibu, tanggal_pemeriksaan, alasan_rujukan, rumah_sakit_tujuan, catatan) VALUES (?, ?, ?, ?, ?)";
    // Kolom id_pemeriksaan_ibu dihapus dari query karena tabel baru tidak memilikinya

    // 2. Buat prepared statement
    if ($stmt = $conn->prepare($sql)) {
        // 3. Bind parameter ke prepared statement
        // 'sssss' menunjukkan tipe data string untuk semua parameter (nama_ibu, tanggal_pemeriksaan, alasan_rujukan, rumah_sakit_tujuan, catatan)
        $stmt->bind_param("sssss", $nama_ibu, $tanggal_pemeriksaan, $alasan_rujukan, $rumah_sakit_tujuan, $catatan); // Sesuaikan dengan parameter baru

        // 4. Eksekusi statement
        if ($stmt->execute()) {
            echo "<script>alert('Data rujukan berhasil disimpan!'); window.location.href='jadwal_pemeriksaan.php';</script>";
            exit(); // Penting untuk menghentikan eksekusi setelah redirect atau alert
        } else {
            // Tangani error jika eksekusi gagal
            echo "Error saat menyimpan data: " . $stmt->error;
        }

        // 5. Tutup statement
        $stmt->close();
    } else {
        // Tangani error jika prepare statement gagal
        echo "Error saat menyiapkan statement: " . $conn->error;
    }

    // Pastikan koneksi database ditutup setelah semua operasi selesai
    $conn->close();

} else {
    // Jika bukan POST request, arahkan kembali ke rujukan.php
    header("Location: rujukan.php");
    exit(); // Penting untuk menghentikan eksekusi setelah redirect
}
?>