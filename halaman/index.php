<?php
include 'connect.php';
session_start();
$ibuHamil = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='Ibu Hamil'")->fetch_assoc()['total'];
$risikoTinggi = $conn->query("SELECT COUNT(*) as total FROM pemeriksaan_ibu WHERE risiko_tinggi > 0")->fetch_assoc()['total'];
$pemeriksaanBulan = $conn->query("SELECT COUNT(*) as total FROM pemeriksaan_ibu WHERE MONTH(tanggal_pemeriksaan)=MONTH(CURRENT_DATE()) AND YEAR(tanggal_pemeriksaan)=YEAR(CURRENT_DATE())")->fetch_assoc()['total'];
$jadwalMinggu = $conn->query("SELECT COUNT(*) as total FROM jadwal WHERE WEEK(tanggal)=WEEK(CURRENT_DATE()) AND YEAR(tanggal)=YEAR(CURRENT_DATE())")->fetch_assoc()['total'];
$risikoData = $conn->query("SELECT nama_ibu, usia_kehamilan, risiko_tinggi FROM pemeriksaan_ibu WHERE risiko_tinggi > 0 ORDER BY tanggal_pemeriksaan DESC LIMIT 5");

// Data untuk tabel Jadwal Pemeriksaan Bulan Ini (DIREVISI: hanya nama_ibu, tanggal_pemeriksaan, usia_kehamilan)
$angka_bulan_sekarang = date('m');
$tahun_sekarang = date('Y');

$jadwalPemeriksaanDetailData = mysqli_query($conn, "
    SELECT
        nama_ibu,
        tanggal_pemeriksaan,
        usia_kehamilan
    FROM pemeriksaan_ibu
    WHERE MONTH(tanggal_pemeriksaan) = '$angka_bulan_sekarang'
    AND YEAR(tanggal_pemeriksaan) = '$tahun_sekarang'
    ORDER BY tanggal_pemeriksaan DESC
    LIMIT 5
");

$pemeriksaanTerbaruData = $conn->query("SELECT nama_ibu, tanggal_pemeriksaan, usia_kehamilan, risiko_tinggi FROM pemeriksaan_ibu ORDER BY tanggal_pemeriksaan DESC LIMIT 5");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard MomCare</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-pink: #ffd9dd;
            --accent-teal: #00a8a8;
            --dark-gray: #333;
            --light-gray-bg: #f9f9f9;
            --white: #fff;
            --box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            --card-pink: #FF6666;
            --normal-color: #28a745;
            --tinggi-color: #ffc107;
            --sangat-tinggi-color: #dc3545;
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

        .main {
            margin-left: 250px;
            padding: 40px;
            flex-grow: 1;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background-color: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
        }

        .top-bar h2 {
            color: var(--accent-teal);
            font-size: 2.2em;
            margin: 0;
        }

        .top-bar p {
            font-size: 1.1em;
            font-weight: 600;
            color: var(--dark-gray);
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card {
            background: linear-gradient(to right, var(--card-pink), #ff8c8c);
            color: var(--white);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            font-size: 1.4em;
            font-weight: 600;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card br {
            margin-bottom: 5px;
        }

        .section-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .box {
            background-color: var(--white);
            color: var(--dark-gray);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            min-height: 250px;
            display: flex;
            flex-direction: column;
        }

        .box h3 {
            color: var(--accent-teal);
            font-size: 1.8em;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            color: var(--dark-gray);
            border-collapse: collapse;
            margin-top: 15px;
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            flex-grow: 1;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
            white-space: normal;
            font-size: 1.1em;
        }

        th {
            background-color: #f2f2f2;
            font-weight: 700;
            color: var(--accent-teal);
            white-space: nowrap;
            font-size: 1.2em;
        }

        tr:last-child td {
            border-bottom: none;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        table tbody tr:hover {
            background-color: #ffeaea;
            cursor: pointer;
        }

        .no-data-message {
            text-align: center;
            padding: 20px;
            font-size: 1.2em;
            color: #777;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .risk-status-0 {
            font-weight: bold;
            color: var(--normal-color);
        }
        .risk-status-1 {
            font-weight: bold;
            color: var(--tinggi-color);
        }
        .risk-status-2 {
            font-weight: bold;
            color: var(--sangat-tinggi-color);
        }

        @media (max-width: 1200px) {
            .section-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                float: none;
                padding-top: 20px;
                box-shadow: none;
            }
            .sidebar .logo-container {
                justify-content: center;
                padding-bottom: 15px;
            }
            .sidebar nav {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            .sidebar a {
                padding: 10px 15px;
                font-size: 0.9em;
            }
            .main {
                margin-left: 0;
                padding: 20px;
            }
            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }
            .top-bar h2 {
                font-size: 1.8em;
                margin-bottom: 10px;
            }
            .cards {
                grid-template-columns: 1fr;
            }
            .card {
                font-size: 1.2em;
                padding: 25px;
            }
            .box {
                padding: 20px;
                min-height: 200px;
            }
            .box h3 {
                font-size: 1.6em;
            }
            table {
                width: auto;
                min-width: 100%;
            }
        }

        @media (max-width: 500px) {
            .sidebar .logo-container .logo-text {
                font-size: 1.3em;
            }
            .sidebar a {
                font-size: 0.8em;
                padding: 8px 8px;
            }
            .top-bar h2 {
                font-size: 1.5em;
            }
            .top-bar p {
                font-size: 1em;
            }
            .card {
                font-size: 1.1em;
                padding: 20px;
            }
            .box h3 {
                font-size: 1.4em;
            }
            th, td {
                padding: 10px 12px;
                font-size: 0.95em;
            }
            th {
                font-size: 1em;
            }
            .no-data-message {
                font-size: 1em;
            }
            /* Specific column widths for readability on small screens */
            /* These might need further adjustment based on actual content */
            td:nth-child(1) { /* Nama Ibu */
                min-width: 90px;
            }
            td:nth-child(2) { /* Tanggal Periksa */
                min-width: 90px;
            }
            td:nth-child(3) { /* Usia Kehamilan */
                min-width: 80px;
            }
            td:nth-child(4) { /* Risiko (for Pemeriksaan Terbaru) */
                min-width: 80px;
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
        <a href="jadwal_pemeriksaan.php">📁 Jadwal</a>
        <a href="logout.php">🚪 Logout</a>
    </nav>
</div>

<div class="main">
    <div class="top-bar">
        <h2>Dashboard</h2>
        <p>👨🏻‍💼<strong>Halo, Admin!</strong></p>
    </div>

    <div class="cards">
        <div class="card">
            <?= $ibuHamil ?> <br> Ibu Hamil<br>
            <small><?= $risikoTinggi ?> Risiko Tinggi</small>
        </div>
        <div class="card">
            <?= $pemeriksaanBulan ?> <br> Pemeriksaan <br> Bulan Ini
        </div>
        <div class="card">
            <?= $jadwalMinggu ?> <br> Jadwal <br> Minggu Ini
        </div>
    </div>

    <div class="section-container">
        <div class="box">
            <h3>Monitoring Risiko Tinggi (Pemeriksaan)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Ibu</th>
                        <th>Usia Kehamilan</th>
                        <th>Status Risiko</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($risikoData) > 0) {
                        while($row = $risikoData->fetch_assoc()) {
                            $status_text = '';
                            $status_class = '';
                            if ($row['risiko_tinggi'] == 0) {
                                $status_text = 'Normal';
                                $status_class = 'risk-status-0';
                            } elseif ($row['risiko_tinggi'] == 1) {
                                $status_text = 'Tinggi';
                                        $status_class = 'risk-status-1';
                            } elseif ($row['risiko_tinggi'] == 2) {
                                $status_text = 'Sangat Tinggi';
                                $status_class = 'risk-status-2';
                            }
                    ?>
                        <tr>
                            <td><?= $row['nama_ibu'] ?></td>
                            <td><?= $row['usia_kehamilan'] ?> Minggu</td>
                            <td class="<?= $status_class ?>"><?= $status_text ?></td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='3' class='no-data-message'>Tidak ada pemeriksaan risiko tinggi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="box">
            <h3>Jadwal Pemeriksaan Bulan Ini</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Ibu</th>
                        <th>Tanggal Periksa</th>
                        <th>Usia Kehamilan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Use the revised $jadwalPemeriksaanDetailData
                    if (mysqli_num_rows($jadwalPemeriksaanDetailData) > 0) {
                        while ($row = mysqli_fetch_assoc($jadwalPemeriksaanDetailData)) {
                            $tanggal_pemeriksaan_formatted = date('d M Y', strtotime($row['tanggal_pemeriksaan']));
                    ?>
                        <tr>
                            <td><?= $row['nama_ibu'] ?></td>
                            <td><?= $tanggal_pemeriksaan_formatted ?></td>
                            <td><?= $row['usia_kehamilan'] ?> minggu</td>
                        </tr>
                    <?php
                        }
                    } else {
                        // Adjusted colspan for 3 columns
                        echo "<tr><td colspan='3' class='no-data-message'>Tidak ada data pemeriksaan untuk bulan ini.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="box">
            <h3>Pemeriksaan Terbaru</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Ibu</th>
                        <th>Tanggal</th>
                        <th>Usia Kehamilan</th>
                        <th>Risiko</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($pemeriksaanTerbaruData) > 0) {
                        while($row = $pemeriksaanTerbaruData->fetch_assoc()) {
                            $status_text = '';
                            $status_class = '';
                            if ($row['risiko_tinggi'] == 0) {
                                $status_text = 'Normal';
                                $status_class = 'risk-status-0';
                            } elseif ($row['risiko_tinggi'] == 1) {
                                $status_text = 'Tinggi';
                                $status_class = 'risk-status-1';
                            } elseif ($row['risiko_tinggi'] == 2) {
                                $status_text = 'Sangat Tinggi';
                                $status_class = 'risk-status-2';
                            }
                    ?>
                        <tr>
                            <td><?= $row['nama_ibu'] ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])) ?></td>
                            <td><?= $row['usia_kehamilan'] ?> minggu</td>
                            <td class="<?= $status_class ?>"><?= $status_text ?></td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' class='no-data-message'>Tidak ada data pemeriksaan terbaru.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>