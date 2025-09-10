<?php
error_reporting(E_ALL); // Pastikan ini diaktifkan untuk debugging
ini_set('display_errors', 1); // Pastikan ini diaktifkan untuk debugging

session_start();
include 'connect.php'; // Pastikan file connect.php ada dan berisi koneksi database

// Memeriksa apakah pengguna sudah login, jika tidak, arahkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: halaman_login.php");
    exit();
}

// Mengambil user_id dan nama pengguna dari sesi
// Variabel PHP ini tetap user_id karena mengambil dari $_SESSION['user_id']
$user_id = $_SESSION['user_id'];
$nama_user = $_SESSION['user_nama'] ?? "Pengguna"; // Jika user_nama belum diset, gunakan "Pengguna"

// --- Debugging User ID Sesi ---
// echo "Debugging: User ID dari sesi = " . $user_id . "<br>";

// Inisialisasi array untuk informasi janin
$info_janin = [
    'usia_kehamilan' => 'N/A',
    'estimasi_kelahiran' => 'N/A',
    'kondisi_bayi' => 'N/A'
];

// Mengambil informasi janin dari database berdasarkan user_id
// Pastikan kolom usia_kehamilan, estimasi_kelahiran, dan kondisi_bayi ada di tabel `users` Anda
$sql_user = "SELECT usia_kehamilan, estimasi_kelahiran, kondisi_bayi FROM users WHERE id = ?";
if ($stmt = $conn->prepare($sql_user)) {
    $stmt->bind_param("i", $user_id); // Menggunakan id dari users table
    $stmt->execute();
    $result_user = $stmt->get_result();

    if ($result_user->num_rows > 0) {
        $row_user = $result_user->fetch_assoc();

        // Menggunakan empty() untuk cek nilai null, string kosong, atau 0
        $info_janin['usia_kehamilan'] = !empty($row_user['usia_kehamilan']) ? htmlspecialchars($row_user['usia_kehamilan']) . ' minggu' : 'N/A';

        // Memformat tanggal estimasi kelahiran
        if (!empty($row_user['estimasi_kelahiran']) && $row_user['estimasi_kelahiran'] !== '0000-00-00') {
            $date_obj = new DateTime($row_user['estimasi_kelahiran']);
            // Array untuk konversi nama bulan ke Bahasa Indonesia
            $bulan_indonesia = [
                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
            ];
            $estimasi_kelahiran_formatted = $date_obj->format('d F Y');
            $info_janin['estimasi_kelahiran'] = strtr($estimasi_kelahiran_formatted, $bulan_indonesia);
        } else {
            $info_janin['estimasi_kelahiran'] = 'N/A';
        }

        $info_janin['kondisi_bayi'] = !empty($row_user['kondisi_bayi']) ? htmlspecialchars($row_user['kondisi_bayi']) : 'N/A';
    }
    $stmt->close();
}


// Inisialisasi array untuk jadwal periksa
$jadwal_periksa = [
    'tanggal' => 'N/A',
    'waktu' => 'N/A',
    'dokter' => 'N/A'
];
$current_date = date('Y-m-d'); // Tanggal saat ini

$sql_jadwal = "SELECT tanggal_periksa, waktu_periksa, nama_dokter FROM jadwal_periksa WHERE id = ? AND tanggal_periksa >=
? ORDER BY tanggal_periksa ASC, waktu_periksa ASC LIMIT 1";
if ($stmt = $conn->prepare($sql_jadwal)) {
    $stmt->bind_param("is", $user_id, $current_date);
    $stmt->execute();
    $result_jadwal = $stmt->get_result();
    if ($result_jadwal->num_rows > 0) {
        $row_jadwal = $result_jadwal->fetch_assoc();

        if (!empty($row_jadwal['tanggal_periksa']) && $row_jadwal['tanggal_periksa'] !== '0000-00-00') {
            $date_obj_jadwal = new DateTime($row_jadwal['tanggal_periksa']);
            // Pastikan $bulan_indonesia didefinisikan sebelum strtr
            if (!isset($bulan_indonesia)) { // Re-check if it's already defined
                $bulan_indonesia = [
                    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                ];
            }
            $tanggal_periksa_formatted = $date_obj_jadwal->format('j F Y');
            $jadwal_periksa['tanggal'] = strtr($tanggal_periksa_formatted, $bulan_indonesia);
        }

        $jadwal_periksa['waktu'] = !empty($row_jadwal['waktu_periksa']) ? substr(htmlspecialchars($row_jadwal['waktu_periksa']), 0, 5) . ' WIB' : 'N/A';
        $jadwal_periksa['dokter'] = !empty($row_jadwal['nama_dokter']) ? htmlspecialchars($row_jadwal['nama_dokter']) : 'N/A';
    }
    $stmt->close();
}

// Fetch user's examination history from pemeriksaan_ibu table
$pemeriksaanHistoryData = null;
// PERUBAHAN: user_id diganti dengan id di WHERE clause
$sql_history = "SELECT
                    tanggal_pemeriksaan,
                    tekanan_darah,
                    berat_badan,
                    tinggi_badan,
                    usia_kehamilan,
                    tinggi_fundus,
                    letak_janin,
                    denyut_jantung_janin,
                    tbj,
                    hpl,
                    hpht,
                    glukosa_darah,
                    rujukan,
                    keluhan,
                    risiko_tinggi
                FROM
                    pemeriksaan_ibu
                WHERE
                    id = ?  -- PERUBAHAN DI SINI
                ORDER BY
                    tanggal_pemeriksaan DESC
                LIMIT 10"; // Limit to last 10 examinations, adjust as needed

if ($stmt = $conn->prepare($sql_history)) {
    $stmt->bind_param("i", $user_id); // $user_id (dari sesi) akan di-bind ke kolom 'id'
    $stmt->execute();
    $pemeriksaanHistoryData = $stmt->get_result();
    $stmt->close();
}

$conn->close(); // Menutup koneksi database
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MomCare - Beranda</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-pink: #FFD9DD;
            --accent-teal: #00A8A8;
            --text-dark: #333333;
            --text-medium: #555555;
            --text-light: #777777;
            --bg-light: #F9F9F9;
            --bg-white: #FFFFFF;
            --border-light: #EEEEEE;
            --shadow-subtle: rgba(0, 0, 0, 0.08);
            --sidebar-active-bg: #e0f2f1;
            --sidebar-active-text: #00796B;
            --article-red: #E53935;
            --normal-color: #28a745;       /* Green for Normal */
            --tinggi-color: #ffc107;       /* Yellow for Risiko Tinggi */
            --sangat-tinggi: #dc3545; /* Red for Sangat Tinggi */
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--primary-pink);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden; /* Prevent horizontal scroll from animations */
        }

        .container {
            display: flex;
            width: 95%;
            max-width: 1400px;
            background-color: var(--bg-white);
            border-radius: 16px;
            box-shadow: 0 10px 30px var(--shadow-subtle);
            overflow: hidden;
            min-height: 85vh;
            margin: 25px auto;
            /* Initial animation for the main container */
            animation: fadeInScale 0.6s ease-out;
        }

        .sidebar {
            width: 280px;
            background-color: var(--bg-light);
            padding: 30px 20px;
            border-right: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            /* Initial animation for sidebar */
            animation: slideInLeft 0.5s ease-out;
        }

        .sidebar .logo {
            display: flex;
            align-items: center;
            font-size: 26px;
            font-weight: 700;
            color: var(--accent-teal);
            margin-bottom: 40px;
            justify-content: center;
            padding-bottom: 15px;
            border-bottom: 1px dashed var(--border-light);
        }

        .sidebar .logo img {
            margin-right: 12px;
            border-radius: 50%;
            height: 48px;
            width: 48px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .nav-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-menu li {
            margin-bottom: 8px;
        }

        .nav-menu a {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            text-decoration: none;
            color: var(--text-dark);
            border-radius: 10px;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
            font-weight: 500;
            position: relative; /* For icon animation */
            overflow: hidden; /* For pseudo-element background effect */
            z-index: 1; /* For pseudo-element stacking */
        }

        .nav-menu a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--sidebar-active-bg);
            transform: translateX(-100%);
            transition: transform 0.3s ease-out;
            z-index: -1;
        }

        .nav-menu a:hover::before {
            transform: translateX(0);
        }

        .nav-menu a i {
            margin-right: 15px;
            font-size: 1.2em;
            color: var(--text-light);
            transition: color 0.3s ease, transform 0.2s ease; /* For icon scale */
        }

        .nav-menu a:hover, .nav-menu li.active a {
            color: var(--sidebar-active-text);
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .nav-menu a:hover i, .nav-menu li.active a i {
            color: var(--sidebar-active-text);
            transform: scale(1.1); /* Scale icon on hover/active */
        }

        .content {
            flex-grow: 1;
            padding: 40px;
            background-color: var(--bg-white);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--border-light);
            /* Initial animation for header */
            animation: fadeInDown 0.6s ease-out;
        }

        .header h2 {
            margin: 0;
            color: var(--text-dark);
            font-size: 2.2em;
            font-weight: 700;
        }

        .profile-pic {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 3px solid var(--accent-teal);
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .profile-pic:hover {
            transform: scale(1.08); /* More pronounced scale */
            box-shadow: 0 6px 20px rgba(0,0,0,0.2); /* Enhanced shadow */
        }

        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .info-sections {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .card.wide-card {
            grid-column: span 1;
        }
        .card.narrow-card {
            grid-column: span 1;
        }

        @media (min-width: 1025px) {
            .card.wide-card {
                grid-column: 1 / 2;
            }
            .card.narrow-card {
                grid-column: 2 / 3;
            }
        }

        .card {
            background-color: var(--bg-white);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px var(--shadow-subtle);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            min-height: 200px;
            opacity: 0; /* Hidden by default for JS animation */
            transform: translateY(20px); /* Start slightly below for JS animation */
            will-change: transform, opacity, box-shadow; /* Optimize for animation */
        }

        .card.animate { /* Class added by JavaScript */
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .card:hover {
            transform: translateY(-8px); /* More lift */
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); /* Stronger shadow */
        }

        .card h3 {
            margin-top: 0;
            color: var(--accent-teal);
            font-size: 1.4em;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: left;
            position: relative;
            z-index: 1;
        }

        .card-content {
            display: flex;
            flex-direction: column;
            gap: 12px;
            position: relative;
            flex-grow: 1;
            padding-right: 150px;
            z-index: 1;
            padding-bottom: 40px;
        }

        /* Parallax effect for baby image */
        .baby-image {
            position: absolute;
            bottom: -20px;
            right: -40px;
            opacity: 0.7;
            z-index: 0;
            pointer-events: none;
            width: 200px;
            height: auto;
            display: block;
            will-change: transform; /* Hint for browser optimization */
        }
        .baby-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .jadwal-detail {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px;
            font-size: 1.15em;
            color: var(--text-dark);
            font-weight: 600;
            margin-top: 10px;
        }

        .jadwal-detail i {
            font-size: 1.8em;
            color: var(--accent-teal);
            flex-shrink: 0;
            animation: pulse 2s infinite ease-in-out; /* Subtle pulse animation */
        }

        .jadwal-detail span {
            flex-shrink: 0;
        }

        .jadwal-detail .dokter-name {
            margin-left: auto;
            color: var(--text-medium);
            font-weight: 500;
            font-size: 0.9em;
            text-align: right;
        }

        /* --- Artikel Carousel Styles --- */
        .article-card .card-content {
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 10px;
            padding-right: 0;
            padding-bottom: 0;
            min-height: 220px; /* Increased height for full article text */
            position: relative;
            overflow: hidden;
            text-align: left;
            will-change: height; /* Optimize height transition */
            transition: min-height 0.5s ease-out; /* Smooth height adjustment */
        }

        .article-carousel-item {
            display: none;
            width: 100%;
            opacity: 0;
            transition: opacity 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94); /* Smoother cubic-bezier transition */
            position: absolute;
            top: 0;
            left: 0;
            transform: none;
            padding: 25px; /* Add padding to text container */
            box-sizing: border-box;
            padding-top: 0; /* Adjust if needed */
            will-change: opacity; /* Optimize opacity transition */
        }

        .article-carousel-item.active {
            display: block;
            opacity: 1;
            animation: fadeIn 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94); /* Match fade-in animation to transition */
        }

        .article-carousel-item .article-title {
            font-size: 1.4em;
            color: var(--accent-teal);
            text-decoration: none;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 10px;
            display: block;
            transition: color 0.3s ease;
        }

        .article-carousel-item .article-title:hover {
            color: var(--sidebar-active-text);
        }

        .article-carousel-item .article-text {
            font-size: 1em;
            color: var(--text-dark);
            line-height: 1.7; /* Enhanced readability */
            margin-bottom: 10px;
            text-align: justify; /* Justify text for professional look */
        }

        /* Hide the thumbnail container if still present in HTML */
        .article-thumbnail {
            display: none;
        }

        /* Styles for Examination History Table */
        .history-section {
            background-color: var(--bg-white);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px var(--shadow-subtle);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            opacity: 0; /* Hidden by default for JS animation */
            transform: translateY(20px); /* Start slightly below for JS animation */
            will-change: transform, opacity, box-shadow; /* Optimize for animation */
        }

        .history-section.animate { /* Class added by JavaScript */
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .history-section h3 {
            margin-top: 0;
            color: var(--accent-teal);
            font-size: 1.4em;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: left;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .history-table th, .history-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            white-space: nowrap;
            font-size: 0.95em;
        }

        .history-table th {
            background-color: var(--bg-light);
            font-weight: 700;
            color: var(--accent-teal);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .history-table tbody tr {
            opacity: 0; /* Hidden by default for staggered animation */
            transform: translateY(10px); /* Start slightly below */
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease, opacity 0.4s ease forwards;
            will-change: transform, opacity; /* Optimize row animation */
        }

        .history-table tbody tr.animate-row {
            animation: rowFadeInUp 0.5s ease-out forwards;
        }

        .history-table tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        .history-table tbody tr:hover {
            background-color: #ffeaea;
            transform: translateY(-2px) scale(1.005); /* Subtle lift and scale on hover */
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); /* More pronounced shadow */
        }

        .history-table .normal-status {
            color: var(--normal-color);
            font-weight: bold;
        }
        .history-table .tinggi-status {
            color: var(--tinggi-color);
            font-weight: bold;
        }
        .history-table .sangat-tinggi-status {
            color: var(--sangat-tinggi);
            font-weight: bold;
        }

        .history-table button {
            padding: 8px 12px;
            font-size: 0.9em;
            background-color: var(--accent-teal);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .history-table button:hover {
            background-color: #00796B;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 168, 168, 0.2);
        }


        /* Keyframe Animations */
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.98);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Staggered row animation */
        @keyframes rowFadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /* Responsive Design */

        @media (max-width: 1024px) {
            .info-sections {
                grid-template-columns: 1fr;
                gap: 25px;
            }
            .card.wide-card, .card.narrow-card {
                grid-column: span 1;
            }
            .sidebar {
                width: 220px;
                padding: 25px 15px;
            }
            .sidebar .logo {
                font-size: 22px;
                margin-bottom: 30px;
            }
            .sidebar .logo img {
                height: 40px;
                width: 40px;
            }
            .nav-menu a {
                padding: 12px 15px;
                font-size: 0.95em;
            }
            .nav-menu a i {
                font-size: 1.1em;
                margin-right: 12px;
            }
            .content {
                padding: 30px;
            }
            .header h2 {
                font-size: 2em;
            }
            .profile-pic {
                width: 60px;
                height: 60px;
            }
            .card {
                padding: 20px;
                min-height: 180px;
            }
            .card-content {
                padding-bottom: 30px;
                padding-right: 120px;
            }
            .baby-image {
                width: 180px;
                bottom: -15px;
                right: -30px;
            }
            .baby-image img {
                width: 100%;
            }
            .jadwal-detail {
                font-size: 1.05em;
            }
            .jadwal-detail i {
                font-size: 1.6em;
            }
            .article-card .card-content {
                padding-right: 0;
                min-height: 180px; /* Maintain height on smaller screens */
            }
            .article-carousel-item {
                padding: 20px;
            }
            .article-title {
                font-size: 1.2em;
            }
            .article-text {
                font-size: 0.95em;
            }
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                width: 100%;
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                min-height: 100vh;
            }
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--border-light);
                padding: 20px;
                height: auto;
                animation: none; /* Disable initial animation on small screens for better flow */
            }
            .sidebar .logo {
                justify-content: center;
                margin-bottom: 20px;
                border-bottom: none;
            }
            .nav-menu ul {
                display: flex;
                justify-content: space-around;
                flex-wrap: wrap;
                padding: 0;
            }
            .nav-menu li {
                margin: 0 5px 10px 5px;
            }
            .nav-menu a {
                flex-direction: column;
                text-align: center;
                padding: 10px 12px;
                font-size: 0.85em;
            }
            .nav-menu a i {
                margin-right: 0;
                margin-bottom: 5px;
                font-size: 1.3em;
            }
            .content {
                padding: 20px;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 30px;
                padding-bottom: 15px;
            }
            .header h2 {
                font-size: 1.8em;
                margin-bottom: 15px;
            }
            .profile-pic {
                margin-top: 15px;
                align-self: flex-end;
            }
            .info-sections {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .card:first-child .card-content {
                flex-direction: column;
                align-items: flex-start;
                padding-bottom: 0;
                padding-right: 0;
            }
            .card:first-child .baby-image {
                position: static; /* Remove absolute positioning */
                width: 100%;
                text-align: right;
                margin-top: 15px;
                opacity: 1;
                order: -1;
            }
            .card:first-child .baby-image img {
                width: 120px;
                height: auto;
                display: inline-block;
            }

            .jadwal-detail {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .jadwal-detail .dokter-name {
                margin-left: 0;
                width: 100%;
                text-align: left;
                font-size: 1em;
            }
            .article-card .card-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
                min-height: 160px; /* Adjust height for smaller text */
            }
            .article-carousel-item {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .content {
                padding: 15px;
            }
            .header h2 {
                font-size: 1.6em;
            }
            .profile-pic {
                width: 50px;
                height: 50px;
            }
            .card {
                padding: 15px;
                min-height: 160px;
            }
            .card-content {
                padding-bottom: 20px;
            }
            .card h3 {
                font-size: 1.1em;
            }
            .detail-row span {
                font-size: 0.9em;
            }
            .jadwal-detail {
                font-size: 0.95em;
            }
            .jadwal-detail i {
                font-size: 1.4em;
            }
            .article-title {
                font-size: 1em;
            }
            .article-text {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="http://localhost/momcared/halaman/momcare.jpg" alt="MomCare Logo">
                MomCare
            </div>
            <nav class="nav-menu">
                <ul>
                    <li class="active"><a href="beranda.php"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="pemeriksaan2.php"><i class="fas fa-notes-medical"></i> Pemeriksaan</a></li>
                    <li><a href="pemeriksaan_dokter.php"><i class="fas fa-notes-medical"></i> Daftar pemeriksaan</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <header class="header">
                <h2>Selamat datang, <?php echo htmlspecialchars($nama_user); ?></h2>
                <div class="profile-pic">
                    <img src="http://localhost/momcared/halaman/profil.png" alt="Profile Picture">
                </div>
            </header>

            <section class="info-sections">
                <div class="card wide-card">
                    <h3>Informasi Janin</h3>
                    <div class="card-content">
                        <div class="detail-row">
                            <span>Usia Kehamilan</span>
                            <span>: <?php echo $info_janin['usia_kehamilan']; ?></span>
                        </div>
                        <div class="detail-row">
                            <span>Estimasi Kelahiran</span>
                            <span>: <?php echo $info_janin['estimasi_kelahiran']; ?></span>
                        </div>
                        <div class="detail-row">
                            <span>Kondisi Bayi</span>
                            <span>: <?php echo $info_janin['kondisi_bayi']; ?></span>
                        </div>
                        <div class="baby-image">
                            <img src="http://localhost/momcared/halaman/ibuhamil2.png" alt="Pregnant Woman">
                        </div>
                    </div>
                </div>

                <div class="card narrow-card">
                    <h3>Jadwal Periksa Selanjutnya</h3>
                    <div class="card-content">
                        <div class="jadwal-detail">
                            <i class="far fa-calendar-alt"></i>
                            <span><?php echo $jadwal_periksa['tanggal']; ?></span>
                            <span><?php echo $jadwal_periksa['waktu']; ?></span>
                            <span class="dokter-name"><?php echo $jadwal_periksa['dokter']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="card article-card wide-card" style="grid-column: 1 / -1;">
                    <h3>Artikel Kesehatan</h3>
                    <div class="card-content article-carousel-content">
                        <div class="article-carousel-item active">
                            <a href="#" class="article-title">1. Nutrisi Esensial untuk Ibu Hamil: Panduan Lengkap</a>
                            <p class="article-text">Selama kehamilan, asupan nutrisi yang tepat sangat krusial untuk kesehatan ibu dan perkembangan janin. Fokus pada makanan kaya folat seperti sayuran hijau gelap, buah-buahan, dan biji-bijian utuh. Zat besi penting untuk mencegah anemia, ditemukan dalam daging merah tanpa lemak, ayam, ikan, serta kacang-kacangan. Kalsium mendukung pembentukan tulang dan gigi bayi, tersedia di produk susu, brokoli, dan tahu. Jangan lupakan protein dari sumber seperti telur, daging, dan polong-polongan. Penting juga untuk minum cukup air dan membatasi kafein serta makanan olahan. Selalu konsultasikan dengan dokter atau ahli gizi untuk rencana diet yang personal.</p>
                        </div>
                        <div class="article-carousel-item">
                            <a href="#" class="article-title">2. Manajemen Stres Selama Kehamilan: Kunci Kesejahteraan</a>
                            <p class="article-text">Kehamilan dapat membawa perubahan emosional yang signifikan, dan mengelola stres menjadi sangat penting. Cobalah teknik relaksasi seperti meditasi ringan, yoga prenatal, atau latihan pernapasan dalam. Berbicara dengan pasangan, teman, atau kelompok dukungan bisa sangat membantu. Pastikan Anda mendapatkan tidur yang cukup dan menyisihkan waktu untuk hobi yang Anda nikmati. Hindari membebani diri dengan terlalu banyak tugas dan belajarlah untuk mengatakan 'tidak' jika diperlukan. Jika stres terasa berlebihan atau berkepanjangan, jangan ragu untuk mencari bantuan profesional dari psikolog atau konselor.</p>
                        </div>
                         <div class="article-carousel-item">
                            <a href="#" class="article-title">3. Pentingnya Aktivitas Fisik Ringan Bagi Ibu Hamil</a>
                            <p class="article-text">Berolahraga secara teratur selama kehamilan dapat memberikan banyak manfaat, termasuk mengurangi sakit punggung, meningkatkan energi, meningkatkan mood, dan membantu persiapan persalinan. Pilihlah aktivitas berintensitas rendah hingga sedang yang aman, seperti berjalan kaki, berenang, atau yoga prenatal. Hindari olahraga kontak, aktivitas yang berisiko jatuh, atau yang melibatkan melompat dan gerakan tiba-tiba. Selalu dengarkan tubuh Anda, hindari overheating, dan pastikan Anda terhidrasi dengan baik. Yang terpenting, konsultasikan jenis dan intensitas olahraga dengan dokter Anda sebelum memulai atau melanjutkan program latihan apapun.</p>
                        </div>
                        <div class="article-carousel-item">
                            <a href="#" class="article-title">4. Tanda Bahaya Kehamilan yang Perlu Diketahui</a>
                            <p class="article-text">Meskipun sebagian besar kehamilan berjalan normal, penting bagi setiap ibu hamil untuk mengetahui tanda-tanda bahaya yang memerlukan perhatian medis segera. Ini termasuk perdarahan vagina, nyeri perut parah atau kram yang tidak biasa, keluarnya cairan ketuban sebelum waktunya, sakit kepala parah yang tidak mereda, penglihatan kabur, pembengkakan tiba-tiba di wajah atau tangan, demam tinggi, atau penurunan gerakan janin. Jangan pernah mengabaikan gejala-gejala ini. Segera hubungi dokter atau pergi ke fasilitas kesehatan terdekat jika Anda mengalami salah satu tanda ini.</p>
                        </div>
                        <div class="article-carousel-item">
                            <a href="#" class="article-title">5. Perawatan Diri Setelah Melahirkan (Postpartum Care)</a>
                            <p class="article-text">Periode postpartum adalah masa pemulihan penting bagi ibu. Fokus pada istirahat yang cukup, nutrisi yang baik, dan hidrasi. Jangan ragu meminta bantuan dari pasangan, keluarga, atau teman untuk tugas-tugas rumah tangga dan perawatan bayi. Perhatikan tanda-tanda depresi postpartum, seperti perasaan sedih yang berkepanjangan, kehilangan minat pada bayi, atau perubahan nafsu makan/tidur. Lakukan pemeriksaan postpartum dengan dokter Anda untuk memastikan pemulihan fisik berjalan lancar. Ingatlah, merawat diri sendiri adalah bagian penting dari merawat bayi Anda.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="history-section">
                <h3>Riwayat Pemeriksaan Anda</h3>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Usia Kehamilan</th>
                            <th>TD</th>
                            <th>BB</th>
                            <th>Keluhan</th>
                            <th>Risiko</th>
                            <th>Rujukan</th>
                            <th>Detail Lainnya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($pemeriksaanHistoryData && $pemeriksaanHistoryData->num_rows > 0) {
                            $row_index = 0; // Tambahkan index untuk staggered animation
                            while ($row = $pemeriksaanHistoryData->fetch_assoc()) {
                                $tanggal_formatted = date('d M Y', strtotime($row['tanggal_pemeriksaan']));
                                $risiko_text = 'N/A';
                                $risiko_class = '';
                                if ($row['risiko_tinggi'] == 0) {
                                    $risiko_text = 'Normal';
                                    $risiko_class = 'normal-status';
                                } elseif ($row['risiko_tinggi'] == 1) {
                                    $risiko_text = 'Tinggi';
                                    $risiko_class = 'tinggi-status';
                                } elseif ($row['risiko_tinggi'] == 2) {
                                    $risiko_text = 'Sangat Tinggi';
                                    $risiko_class = 'sangat-tinggi-status';
                                }
                        ?>
                            <tr style="animation-delay: <?= $row_index * 0.1 ?>s;"> <td><?= htmlspecialchars($tanggal_formatted) ?></td>
                                <td><?= htmlspecialchars($row['usia_kehamilan'] ?? 'N/A') . (empty($row['usia_kehamilan']) ? '' : ' minggu') ?></td>
                                <td><?= htmlspecialchars($row['tekanan_darah'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['berat_badan'] ?? 'N/A') . (empty($row['berat_badan']) ? '' : ' kg') ?></td>
                                <td><?= htmlspecialchars($row['keluhan'] ?? 'N/A') ?></td>
                                <td class="<?= $risiko_class ?>"><?= htmlspecialchars($risiko_text) ?></td>
                                <td><?= htmlspecialchars($row['rujukan'] ?? 'Tidak') ?></td>
                                <td>
                                    <button onclick="alert('Detail lebih lanjut:\nTB: <?= htmlspecialchars($row['tinggi_badan'] ?? 'N/A') ?> cm\nTF: <?= htmlspecialchars($row['tinggi_fundus'] ?? 'N/A') ?> cm\nLetak Janin: <?= htmlspecialchars($row['letak_janin'] ?? 'N/A') ?>\nDJJ: <?= htmlspecialchars($row['denyut_jantung_janin'] ?? 'N/A') ?> dpm\nTBJ: <?= htmlspecialchars($row['tbj'] ?? 'N/A') ?> gr\nHPL: <?= htmlspecialchars($row['hpl'] && $row['hpl'] !== '0000-00-00' ? date('d M Y', strtotime($row['hpl'])) : 'N/A') ?>\nHPHT: <?= htmlspecialchars($row['hpht'] && $row['hpht'] !== '0000-00-00' ? date('d M Y', strtotime($row['hpht'])) : 'N/A') ?>\nGlukosa: <?= htmlspecialchars($row['glukosa_darah'] ?? 'N/A') ?>');">
                                        Lihat
                                    </button>
                                </td>
                            </tr>
                        <?php
                                $row_index++;
                            }
                        } else {
                        ?>
                            <tr><td colspan="8" class="no-data-message">Belum ada riwayat pemeriksaan. Silakan ajukan pemeriksaan pertama Anda!</td></tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Animasi Scroll untuk Card dan Section ---
            const animatedElements = document.querySelectorAll('.card, .history-section');

            const observerOptions = {
                root: null, // relative to the viewport
                rootMargin: '0px',
                threshold: 0.1 // 10% of the element visible to trigger
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                        // Handle staggered animation for history table rows
                        if (entry.target.classList.contains('history-section')) {
                            const rows = entry.target.querySelectorAll('.history-table tbody tr');
                            rows.forEach((row, index) => {
                                // Add a delay to each row based on its index
                                row.style.animationDelay = `${index * 0.1}s`;
                                row.classList.add('animate-row');
                            });
                        }
                        observer.unobserve(entry.target); // Stop observing once animated
                    }
                });
            }, observerOptions);

            animatedElements.forEach(item => {
                observer.observe(item);
            });

            // --- Efek Parallax pada Gambar Ibu Hamil ---
            const babyImage = document.querySelector('.baby-image');
            if (babyImage) {
                const card = babyImage.closest('.card'); // Get the parent card

                window.addEventListener('scroll', function() {
                    // Check if the card is in view before applying parallax
                    const rect = card.getBoundingClientRect();
                    if (rect.top < window.innerHeight && rect.bottom > 0) {
                        const scrollPos = window.scrollY;
                        const cardTop = rect.top + scrollPos; // Absolute top position of the card

                        // Calculate offset based on scroll, making it move slightly slower
                        // The effect is more noticeable when scrolling within the card's viewport
                        // Adjust the 0.05 multiplier for more or less parallax effect
                        const offset = (scrollPos - cardTop) * 0.05;
                        babyImage.style.transform = `translateY(${offset}px)`;
                    } else {
                        // Reset transform when out of view to prevent weird positions
                        babyImage.style.transform = 'translateY(0px)';
                    }
                });
            }


            // --- Carousel Artikel Kesehatan ---
            let currentArticleIndex = 0;
            const articles = document.querySelectorAll('.article-carousel-item');
            const totalArticles = articles.length;
            const carouselContent = document.querySelector('.article-carousel-content');

            function showArticle(index) {
                // Dimulai dengan menghilangkan semua artikel secara langsung (display: none)
                articles.forEach(article => {
                    article.style.transition = 'none'; // Matikan transisi sementara
                    article.classList.remove('active');
                    article.style.display = 'none';
                    article.style.opacity = '0';
                });

                // Set ulang transisi untuk artikel aktif
                setTimeout(() => {
                    articles[index].style.transition = 'opacity 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    articles[index].style.display = 'block';
                    // Memaksa reflow agar transisi diterapkan dengan benar
                    articles[index].offsetWidth; // Trigger reflow
                    articles[index].classList.add('active'); // Memicu animasi fade-in
                    articles[index].style.opacity = '1';
                }, 10); // Sedikit delay untuk memastikan display: none diterapkan

                // Sesuaikan tinggi carousel content agar sesuai dengan artikel aktif
                // Ini penting jika artikel memiliki tinggi yang bervariasi
                const activeArticleHeight = articles[index].scrollHeight;
                carouselContent.style.minHeight = `${activeArticleHeight + 50}px`; // + padding
            }

            function nextArticle() {
                currentArticleIndex = (currentArticleIndex + 1) % totalArticles;
                showArticle(currentArticleIndex);
            }

            // Tampilkan artikel pertama saat halaman dimuat
            showArticle(currentArticleIndex);

            // Atur interval untuk mengganti artikel setiap 8 detik (disesuaikan untuk teks lebih panjang)
            setInterval(nextArticle, 8000); // Ganti artikel setiap 8000 ms (8 detik)
        });
    </script>
</body>
</html>