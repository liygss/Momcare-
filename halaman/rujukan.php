<?php
session_start();
include 'connect.php';

$nama_ibu = '';
$tanggal_pemeriksaan_form = date('Y-m-d'); 
$alasan_rujukan = '';
$rumah_sakit_tujuan = '';
$catatan = '';
$id_pemeriksaan_ibu = null; 


if (isset($_GET['id_pemeriksaan'])) {
    $id_pemeriksaan_ibu = intval($_GET['id_pemeriksaan']);


    $query_pemeriksaan = "SELECT nama_ibu, tanggal_pemeriksaan FROM pemeriksaan_ibu WHERE id = '$id_pameriksaan_ibu'";
    $result_pemeriksaan = mysqli_query($conn, $query_pemeriksaan);

    if ($result_pemeriksaan && mysqli_num_rows($result_pemeriksaan) > 0) {
        $data_pemeriksaan = mysqli_fetch_assoc($result_pemeriksaan);
        $ibu_nama = $data_pemeriksaan['nama_ibu'];
        $tanggal_pemeriksaan_form = $data_pemeriksaan['tanggal_pemeriksaan']; 
        $alasan_rujukan = 'Perlu Penanganan Lebih Lanjut'; 
        $rumah_sakit_tujuan = 'RS Umum Daerah';
    } else {
        echo "<p style='color: red; text-align: center;'>Data pemeriksaan tidak ditemukan untuk ID ini.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Rujukan - MomCare</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-pink: #ffd9dd;
            --accent-teal: #00a8a8;
            --dark-gray: #333;
            --light-gray-bg: #f9f9f9;
            --white: #fff;
            --box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--light-gray-bg);
            color: var(--dark-gray);
            line-height: 1.6;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: var(--primary-pink);
            height: 100vh;
            padding-top: 30px;
            box-shadow: var(--box-shadow);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 20px 30px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .sidebar .logo-container img {
            height: 40px;
        }

        .sidebar .logo-container .logo-text {
            font-size: 1.5em;
            font-weight: 700;
            color: var(--accent-teal);
        }

        .sidebar nav {
            flex-grow: 1;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: var(--dark-gray);
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
            gap: 10px;
        }

        .sidebar a:hover {
            background-color: #ffc2c7;
            color: var(--accent-teal);
        }

        .main-content {
            margin-left: 250px;
            flex-grow: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .header-content {
            width: 100%;
            max-width: 800px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background-color: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
        }

        .header-content h2 {
            color: var(--accent-teal);
            font-size: 2.2em;
            margin: 0;
        }

        .form-container {
            background: var(--white);
            padding: 40px;
            border-radius: 12px;
            max-width: 800px;
            width: 100%;
            box-shadow: var(--box-shadow);
        }

        .form-container h3 {
            margin-bottom: 30px;
            text-align: center;
            color: var(--accent-teal);
            font-size: 2em;
        }

        label {
            margin-top: 20px;
            margin-bottom: 5px;
            display: block;
            font-weight: 600;
            color: var(--dark-gray);
        }

        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            color: var(--dark-gray);
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        textarea:focus {
            border-color: var(--accent-teal);
            outline: none;
        }

        .btn-submit {
            margin-top: 30px;
            padding: 15px 25px;
            width: 100%;
            background-color: var(--accent-teal);
            color: var(--white);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 700;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-submit:hover {
            background-color: #008f8f;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                box-shadow: none;
                padding-top: 20px;
            }
            .sidebar .logo-container {
                justify-content: center;
                padding-bottom: 15px;
                border-bottom: none;
            }
            .sidebar nav {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
                padding: 10px 0;
                border-top: 1px solid rgba(0,0,0,0.1);
            }
            .sidebar a {
                padding: 10px 15px;
                font-size: 0.9em;
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            .header-content {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }
            .header-content h2 {
                font-size: 2em;
                margin-bottom: 10px;
            }
            .form-container {
                padding: 30px;
            }
            .form-container h3 {
                font-size: 1.8em;
            }
            input[type="text"],
            input[type="date"],
            textarea,
            .btn-submit {
                font-size: 0.95em;
                padding: 10px 12px;
            }
        }

        @media (max-width: 500px) {
            .sidebar .logo-container .logo-text {
                font-size: 1.3em;
            }
            .sidebar a {
                font-size: 0.85em;
                padding: 8px 10px;
            }
            .header-content h2 {
                font-size: 1.8em;
            }
            .form-container h3 {
                font-size: 1.6em;
            }
            input[type="text"],
            input[type="date"],
            textarea,
            .btn-submit {
                font-size: 0.9em;
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="http://localhost/momcare/halaman/momcare.jpg" alt="MomCare Logo">
            <span class="logo-text">MomCare</span>
        </div>
        <nav>
            <a href="beranda.php">🏠 Beranda</a>
            <a href="index.php">📊 Dashboard</a>
            <a href="pemeriksaan.php">📋 Pemeriksaan</a>
            <a href="kantong.php">📁 Kantong</a>
            <a href="logout.php">🚪 Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header-content">
            <h2>Form Rujukan</h2>
        </div>

        <div class="form-container">
            <h3>Detail Rujukan Ibu Hamil</h3>
            <form action="process_rujukan.php" method="POST">
                <?php if ($id_pemeriksaan_ibu !== null): ?>
                    <input type="hidden" name="id_pemeriksaan_ibu" value="<?= $id_pemeriksaan_ibu ?>">
                <?php endif; 
                ?>

                <label for="ibu">Nama Ibu</label>
                <input type="text" id="ibu" name="ibu" value="<?= htmlspecialchars($ibu_nama) ?>">

                <label for="tanggal_pemeriksaan">Tanggal Pemeriksaan</label>
                <input type="date" id="tanggal_pemeriksaan" name="tanggal_pemeriksaan" value="<?= htmlspecialchars($tanggal_pemeriksaan_form) ?>">

                <label for="alasan_rujukan">Alasan Rujukan</label>
                <input type="text" id="alasan_rujukan" name="alasan_rujukan" value="<?= htmlspecialchars($alasan_rujukan) ?>" placeholder="Contoh: Tekanan darah tinggi, Riwayat komplikasi">

                <label for="rumah_sakit_tujuan">Rumah Sakit Tujuan</label>
                <input type="text" id="rumah_sakit_tujuan" name="rumah_sakit_tujuan" value="<?= htmlspecialchars($rumah_sakit_tujuan) ?>" placeholder="Contoh: RSUD Dr. Soetomo">

                <label for="catatan">Catatan Tambahan</label>
                <textarea id="catatan" name="catatan" rows="5" placeholder="Tambahkan informasi penting lainnya seperti hasil lab, kondisi terkini, dll."><?= htmlspecialchars($catatan) ?></textarea>

                <button type="submit" class="btn-submit">Simpan Rujukan</button>
            </form>
        </div>
    </div>
</body>
</html>