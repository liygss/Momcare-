<?php
include 'connect.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama_ibu'];
    $tanggal = $_POST['tanggal_pemeriksaan'] ?? null;
    if (!$tanggal || !strtotime($tanggal)) {
        die("Tanggal pemeriksaan tidak valid.");
    }
    $tekanan = $_POST['tekanan_darah'];
    $berat = $_POST['berat_badan'];
    $tb = $_POST['tinggi_badan'] ?? null;
    $usia = $_POST['usia_kehamilan'];
    $fundus = $_POST['tinggi_fundus'] ?? null;
    $letak_janin = $_POST['letak_janin'] ?? null;
    $djj = $_POST['denyut_jantung_janin'] ?? null;
    $tbj = $_POST['tbj'] ?? null;
    $keluhan = $_POST['keluhan'];

    $skor_poedji_rochjati = $_POST['skor_poedji_rochjati'] ?? 0;

    
    $risiko_tinggi_status = 0; 
    if ($skor_poedji_rochjati >= 6 && $skor_poedji_rochjati <= 10) {
        $risiko_tinggi_status = 1; 
    } elseif ($skor_poedji_rochjati >= 12) {
        $risiko_tinggi_status = 2; 
    }


    $hpl = date('Y-m-d', strtotime('+40 weeks', strtotime($tanggal)));
    $hpht = date('Y-m-d', strtotime('-280 days', strtotime($hpl)));
    $glukosa = "Normal";
    $rujukan = ($glukosa == "Tinggi") ? "Rujuk" : "-";

    $sql = "INSERT INTO pemeriksaan_ibu
    (nama_ibu, tanggal_pemeriksaan, tekanan_darah, berat_badan, tinggi_badan, usia_kehamilan, tinggi_fundus, letak_janin, denyut_jantung_janin, tbj, keluhan, hpl, hpht, glukosa_darah, rujukan, risiko_tinggi)
    VALUES
    ('$nama', '$tanggal', '$tekanan', '$berat', '$tb', '$usia', '$fundus', '$letak_janin', '$djj', '$tbj', '$keluhan', '$hpl', '$hpht', '$glukosa', '$rujukan', '$risiko_tinggi_status')";

    if ($conn->query($sql) === TRUE) {
        header("Location: jadwal_pemeriksaan.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pemeriksaan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-container {
            background: var(--white);
            padding: 40px;
            border-radius: 12px;
            max-width: 600px;
            width: 100%;
            box-shadow: var(--box-shadow);
        }

        .form-container h2 {
            margin-bottom: 30px;
            text-align: center;
            color: var(--accent-teal);
            font-size: 2.2em;
        }

        label {
            margin-top: 15px;
            margin-bottom: 5px;
            display: block;
            font-weight: 600;
            color: var(--dark-gray);
        }
        
        input[type="text"],
        input[type="date"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            color: var(--dark-gray);
            transition: border-color 0.3s ease;
            box-sizing: border-box;
            margin-bottom: 0;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--accent-teal);
            outline: none;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row > div {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .form-row > div label {
            margin-top: 0;
            margin-bottom: 5px;
        }

        .full-width-input {
            width: 100%;
            box-sizing: border-box;
        }

        button {
            margin-top: 30px;
            padding: 15px 25px;
            width: 100%;
            background-color: var(--accent-teal);
            border: none;
            color: var(--white);
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #008f8f;
            transform: translateY(-2px);
        }

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
            .form-container {
                padding: 30px;
            }
            .form-container h2 {
                font-size: 2em;
            }
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            .form-row > div {
                width: 100%;
                margin-bottom: 15px;
            }
            .form-row > div:last-child {
                margin-bottom: 0;
            }
            .form-row > div label {
                margin-top: 0;
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
            .form-container {
                padding: 20px;
            }
            .form-container h2 {
                font-size: 1.8em;
                margin-bottom: 20px;
            }
            input[type="text"],
            input[type="date"],
            input[type="number"],
            textarea,
            button,
            select {
                font-size: 0.95em;
                padding: 10px 12px;
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
        <a href="index_tamu.php">📊 Dashboard</a>
        <a href="pemeriksaan2.php">📋 Pemeriksaan</a>
        <a href="pemeriksaan_dokter.php">🩺 Pemeriksaan dokter</a>
        <a href="logout.php">🚪 Logout</a>
     </nav>
    </div>

    <div class="main-content">
        <div class="form-container">
            <h2>Tambah Pemeriksaan Ibu Hamil</h2>
            <form method="post">
                <label for="nama_ibu">Nama Ibu</label>
                <input type="text" id="nama_ibu" name="nama_ibu" placeholder="Masukkan nama ibu" required>

                <label for="tanggal">Tanggal Pemeriksaan</label>
                <input type="date" name="tanggal_pemeriksaan" id="tanggal" required>

                <label>Pemeriksaan Fisik</label>
                <div class="form-row">
                    <div>
                        <label for="tekanan_darah">Tekanan Darah</label>
                        <input type="text" name="tekanan_darah" id="tekanan_darah" placeholder="Ex: 120/80" required>
                    </div>
                    <div>
                        <label for="berat_badan">Berat Badan </label>
                        <input type="number" step="0.1" name="berat_badan" id="berat_badan" placeholder="Ex: 60.5" required>
                    </div>
                    <div>
                        <label for="tinggi_badan">Tinggi Badan</label>
                        <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan" placeholder="Ex: 155" required>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="hpht">HPHT</label>
                        <input type="date" name="hpht" id="hpht" required>
                    </div>
                    <div>
                        <label for="hpl">HPL</label>
                        <input type="date" name="hpl" id="hpl" required>
                    </div>
                    <div>
                        <label for="usia_kehamilan">Usia Kehamilan</label>
                        <input type="number" step="0.1" name="usia_kehamilan" id="usia_kehamilan" placeholder="Ex: 28.5" required>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="tinggi_fundus">Tinggi Fundus </label>
                        <input type="number" step="0.1" name="tinggi_fundus" id="tinggi_fundus" placeholder="Ex: 25" required>
                    </div>
                    <div>
                        <label for="letak_janin">Letak Janin</label>
                        <select name="letak_janin" id="letak_janin" required>
                            <option value="">Pilih Letak Janin</option>
                            <option value="Kepala">Kepala</option>
                            <option value="Sungsang">Sungsang</option>
                            <option value="Melintang">Melintang</option>
                        </select>
                    </div>
                    <div>
                        <label for="denyut_jantung_janin">Denyut Jantung Janin </label>
                        <input type="number" name="denyut_jantung_janin" id="denyut_jantung_janin" placeholder="Ex: 140" required>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="tbj">Taksiran Berat Janin</label>
                        <input type="number" step="0.1" name="tbj" id="tbj" placeholder="Ex: 2500" required>
                    </div>
                    <div style="flex: 1;"></div>
                    <div style="flex: 1;"></div>
                </div>

                <label for="skor_poedji_rochjati">Skor Poedji Rochjati</label>
                <input type="number" id="skor_poedji_rochjati" name="skor_poedji_rochjati" class="full-width-input" placeholder="Masukkan skor (Ex: 2, 8, 15)" required>

                <label for="keluhan">Keluhan</label>
                <textarea name="keluhan" id="keluhan" rows="4" placeholder="Masukkan keluhan ibu hamil..."></textarea>

                <button type="submit">Simpan Pemeriksaan</button>
            </form>
        </div>
    </div>

</body>
</html>