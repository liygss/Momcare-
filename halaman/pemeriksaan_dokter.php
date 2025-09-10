<?php
session_start();
include 'connect.php'; // Pastikan file connect.php ada dan berisi koneksi database

// Memeriksa apakah pengguna sudah login, jika tidak, arahkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: halaman_login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Mengambil user_id dari sesi
$nama_user = $_SESSION['user_nama'] ?? "Pengguna"; // Mengambil nama user dari sesi

// Proses form jika ada data yang dikirimkan (POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal_checkup = $_POST['tanggal_checkup'] ?? null;
    $keluhan_awal = $_POST['keluhan_awal'] ?? null;

    // Validasi input
    if (empty($tanggal_checkup) || !strtotime($tanggal_checkup)) {
        die("Tanggal check-up tidak valid.");
    }

    // Mulai transaksi untuk memastikan kedua insert berhasil
    $conn->begin_transaction();
    $success = true;

    // 1. Masukkan data ke tabel pemeriksaan_ibu
    $sql_pemeriksaan_ibu = "INSERT INTO pemeriksaan_ibu (user_id, nama_ibu, tanggal_pemeriksaan, keluhan) VALUES (?, ?, ?, ?)";
    if ($stmt_ibu = $conn->prepare($sql_pemeriksaan_ibu)) {
        $stmt_ibu->bind_param("isss", $user_id, $nama_user, $tanggal_checkup, $keluhan_awal);
        if (!$stmt_ibu->execute()) {
            $success = false;
            echo "Error inserting into pemeriksaan_ibu: " . $stmt_ibu->error;
        }
        $stmt_ibu->close();
    } else {
        $success = false;
        echo "Error preparing statement for pemeriksaan_ibu: " . $conn->error;
    }

    // 2. Masukkan data ke tabel jadwal_periksa
    // Anda bisa menentukan waktu dan dokter default atau menambahkannya ke form
    $waktu_periksa_default = "09:00:00"; // Contoh waktu default
    $nama_dokter_default = "Dr.Yuliani Kusna"; // Contoh nama dokter default

    if ($success) {
        // Cek apakah sudah ada jadwal periksa untuk user_id ini pada tanggal yang sama
        $sql_check_jadwal = "SELECT COUNT(*) FROM jadwal_periksa WHERE user_id = ? AND tanggal_periksa = ?";
        if ($stmt_check = $conn->prepare($sql_check_jadwal)) {
            $stmt_check->bind_param("is", $user_id, $tanggal_checkup);
            $stmt_check->execute();
            $stmt_check->bind_result($count);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($count == 0) { // Hanya masukkan jika belum ada jadwal pada tanggal yang sama
                $sql_jadwal_periksa = "INSERT INTO jadwal_periksa (user_id, tanggal_periksa, waktu_periksa, nama_dokter) VALUES (?, ?, ?, ?)";
                if ($stmt_jadwal = $conn->prepare($sql_jadwal_periksa)) {
                    $stmt_jadwal->bind_param("isss", $user_id, $tanggal_checkup, $waktu_periksa_default, $nama_dokter_default);
                    if (!$stmt_jadwal->execute()) {
                        $success = false;
                        echo "Error inserting into jadwal_periksa: " . $stmt_jadwal->error;
                    }
                    $stmt_jadwal->close();
                } else {
                    $success = false;
                    echo "Error preparing statement for jadwal_periksa: " . $conn->error;
                }
            } else {
                // Opsional: berikan pesan jika jadwal sudah ada untuk tanggal tersebut
                // echo "Info: Jadwal periksa untuk tanggal ini sudah ada.";
            }
        } else {
            $success = false;
            echo "Error preparing statement for check jadwal: " . $conn->error;
        }
    }


    if ($success) {
        $conn->commit();
        header("Location: index_tamu.php?status=success_pemeriksaan");
        exit();
    } else {
        $conn->rollback();
        // Pesan error sudah ditampilkan di atas
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Pemeriksaan - MomCare</title>
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
            --button-hover: #00796B;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--primary-pink);
            display: flex;
            min-height: 100vh;
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container-wrapper {
            display: flex;
            width: 100%;
        }

        .sidebar {
            width: 280px;
            background-color: var(--bg-light);
            padding: 30px 20px;
            border-right: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            position: sticky;
            top: 0;
            height: 100vh;
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
            transition: background-color 0.3s ease, color 0.3s ease;
            font-weight: 500;
        }

        .nav-menu a i {
            margin-right: 15px;
            font-size: 1.2em;
            color: var(--text-light);
            transition: color 0.3s ease;
        }

        .nav-menu a:hover, .nav-menu li.active a {
            background-color: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .nav-menu li.active a i {
            color: var(--sidebar-active-text);
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            background-color: var(--bg-white);
            display: flex;
            justify-content: center;
            align-items: center; /* Center content vertically */
            min-height: 100vh;
        }

        .form-container {
            background: var(--bg-white);
            padding: 40px;
            border-radius: 12px;
            max-width: 500px; /* Adjust max-width for simpler form */
            width: 100%;
            box-shadow: 0 10px 30px var(--shadow-subtle);
        }

        .form-container h2 {
            margin-top: 0;
            margin-bottom: 30px;
            text-align: center;
            color: var(--accent-teal);
            font-size: 2.2em;
            font-weight: 700;
        }

        label {
            margin-top: 15px;
            margin-bottom: 5px;
            display: block;
            font-weight: 600;
            color: var(--text-dark);
        }

        input[type="date"],
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            font-size: 1em;
            color: var(--text-dark);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
            margin-bottom: 0;
        }

        input[type="date"]:focus,
        textarea:focus {
            border-color: var(--accent-teal);
            box-shadow: 0 0 0 3px rgba(0, 168, 168, 0.2);
            outline: none;
        }

        button {
            margin-top: 30px;
            padding: 15px 25px;
            width: 100%;
            background-color: var(--accent-teal);
            border: none;
            color: white; /* Changed from var(--white) as --white is not defined */
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: var(--button-hover);
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
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
            .main-content {
                padding: 30px;
            }
            .form-container {
                padding: 30px;
                max-width: 450px;
            }
            .form-container h2 {
                font-size: 2em;
            }
        }

        @media (max-width: 768px) {
            .container-wrapper {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                box-shadow: none;
                padding-top: 20px;
                border-right: none;
                border-bottom: 1px solid var(--border-light);
            }
            .sidebar .logo {
                justify-content: center;
                margin-bottom: 20px;
                border-bottom: none;
            }
            .sidebar nav {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                padding: 10px 0;
                border-top: 1px solid rgba(0,0,0,0.1);
            }
            .sidebar a {
                padding: 10px 15px;
                font-size: 0.9em;
            }
            .sidebar a i {
                margin-right: 0;
                margin-bottom: 5px;
                font-size: 1.3em;
            }
            .main-content {
                padding: 20px;
                align-items: flex-start;
            }
            .form-container {
                padding: 25px;
            }
            .form-container h2 {
                font-size: 1.8em;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 15px;
            }
            .form-container {
                padding: 20px;
            }
            .form-container h2 {
                font-size: 1.6em;
                margin-bottom: 20px;
            }
            input[type="date"],
            textarea,
            button {
                font-size: 0.95em;
                padding: 10px 12px;
            }
            label {
                font-size: 0.9em;
            }
            .sidebar .logo-text {
                font-size: 1.3em;
            }
            .sidebar a {
                font-size: 0.8em;
                padding: 8px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container-wrapper">
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

        <main class="main-content">
            <div class="form-container">
                <h2>Ajukan Jadwal Pemeriksaan</h2>
                <form method="post">
                    <p style="font-weight: 600; color: var(--text-dark);">Nama Ibu: <?php echo htmlspecialchars($nama_user); ?></p>

                    <label for="tanggal_checkup">Tanggal Check-up yang Diinginkan</label>
                    <input type="date" name="tanggal_checkup" id="tanggal_checkup" required>

                    <label for="keluhan_awal">Keluhan Awal (Opsional)</label>
                    <textarea name="keluhan_awal" id="keluhan_awal" rows="4" placeholder="Contoh: Mual, pusing, nyeri punggung..."></textarea>

                    <button type="submit">Ajukan Pemeriksaan</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>