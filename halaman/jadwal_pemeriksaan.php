<?php
include 'connect.php';
session_start();

$daftar_bulan = [];
$tahun_mulai = 2025; // Tahun awal yang diinginkan
$tahun_sekarang = date('Y'); // Tahun saat ini

// Loop dari tahun mulai hingga tahun saat ini + beberapa tahun ke depan (misal 2 tahun)
// Ini akan memastikan tahun saat ini dan beberapa tahun ke depan juga terdaftar
for ($tahun = $tahun_mulai; $tahun <= $tahun_sekarang + 2; $tahun++) {
    for ($i = 1; $i <= 12; $i++) {
        $daftar_bulan[] = date('F Y', mktime(0, 0, 0, $i, 1, $tahun));
    }
}

$bulan_aktif = isset($_GET['bulan']) ? $_GET['bulan'] : date('F Y');
if (!in_array($bulan_aktif, $daftar_bulan) && !isset($_GET['bulan'])) {
    $bulan_aktif = "Januari 2025";
}


$timestamp = strtotime("1 " . $bulan_aktif);
$angka_bulan = date('m', $timestamp);
$tahun = date('Y', $timestamp);

$data = mysqli_query($conn, "
    SELECT 
        id, 
        nama_ibu, 
        tanggal_pemeriksaan, 
        tekanan_darah, -- Ditambahkan
        berat_badan, 
        tinggi_badan, -- Ditambahkan
        usia_kehamilan, 
        tinggi_fundus, -- Ditambahkan
        letak_janin, -- Ditambahkan
        denyut_jantung_janin, -- Ditambahkan
        tbj, -- Ditambahkan
        hpl, 
        glukosa_darah, 
        rujukan,
        keluhan -- Ditambahkan
    FROM pemeriksaan_ibu
    WHERE MONTH(tanggal_pemeriksaan) = '$angka_bulan'
    AND YEAR(tanggal_pemeriksaan) = '$tahun'
    ORDER BY tanggal_pemeriksaan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kantong Persalinan MomCare</title>
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
            background-color: var(--light-gray-bg);
        }

        .header-content {
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

        .tabs-container {
            margin-bottom: 30px;
            background-color: var(--white);
            padding: 15px;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }
        .tabs-container::-webkit-scrollbar {
            height: 8px;
        }
        .tabs-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .tabs-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .tabs-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }


        .tab-button {
            background-color: var(--primary-pink);
            color: var(--dark-gray);
            border: none;
            padding: 10px 20px;
            margin-right: 10px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .tab-button:hover {
            background-color: #ffc2c7;
            color: var(--accent-teal);
        }

        .tab-button.active {
            background-color: var(--accent-teal);
            color: var(--white);
        }

        .data-table-container {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95em;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            white-space: nowrap;
        }

        th {
            background-color: #f2f2f2;
            font-weight: 700;
            color: var(--accent-teal);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .action-buttons a {
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block;
            font-size: 0.9em;
        }

        .action-buttons .edit-btn {
            background-color: #28a745;
            color: white;
        }

        .action-buttons .edit-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .action-buttons .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .action-buttons .delete-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .btn-global-action {
            margin-top: 30px;
            padding: 12px 25px;
            background-color: var(--accent-teal);
            color: var(--white);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-global-action:hover {
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
            .header-content {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }
            .header-content h2 {
                font-size: 2em;
                margin-bottom: 10px;
            }
            .tabs-container {
                padding: 10px;
            }
            .tab-button {
                padding: 8px 15px;
                font-size: 0.9em;
                margin-bottom: 5px;
            }
            th, td {
                padding: 10px;
                font-size: 0.85em;
            }
            .action-buttons a {
                padding: 6px 10px;
                font-size: 0.8em;
            }
            .btn-global-action {
                padding: 10px 20px;
                font-size: 1em;
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
            .header-content h2 {
                font-size: 1.8em;
            }
            .tab-button {
                font-size: 0.8em;
                padding: 6px 10px;
            }
            th, td {
                padding: 8px;
                font-size: 0.75em;
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

    <div class="main-content">
        <div class="header-content">
            <h2>Jadwal pemeriksaan</h2>
        </div>

        <div class="tabs-container">
            <?php foreach ($daftar_bulan as $b) : ?>
                <a href="?bulan=<?= urlencode($b) ?>" class="tab-button <?= $b === $bulan_aktif ? 'active' : '' ?>">
                    <?= $b ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="data-table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Ibu</th>
                        <th>Tanggal Periksa</th>
                        <th>TD</th>
                        <th>BB</th>
                        <th>TB</th>
                        <th>Usia Kehamilan</th>
                        <th>Tinggi Fundus</th>
                        <th>Letak Janin</th>
                        <th>DJJ</th>
                        <th>TBJ</th>
                        <th>HPL</th>
                        <th>Glukosa Darah</th>
                        <th>Rujukan</th>
                        <th>Keluhan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($data) > 0) {
                        while ($row = mysqli_fetch_assoc($data)) {
                            $tanggal_pemeriksaan_formatted = date('d M Y', strtotime($row['tanggal_pemeriksaan']));
                            $hpl_formatted = date('d M Y', strtotime($row['hpl']));

                            $gizi = ($row['berat_badan'] < 45) ? 'Kurang' : 'Normal';
                            
                            $rujuk_status = isset($row['rujukan']) && $row['rujukan'] !== null ? $row['rujukan'] : 'Tidak';

                            echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['nama_ibu']}</td>
                                <td>{$tanggal_pemeriksaan_formatted}</td>
                                <td>{$row['tekanan_darah']}</td>
                                <td>{$row['berat_badan']} kg</td>
                                <td>{$row['tinggi_badan']} cm</td>
                                <td>{$row['usia_kehamilan']} minggu</td>
                                <td>{$row['tinggi_fundus']} cm</td>
                                <td>{$row['letak_janin']}</td>
                                <td>{$row['denyut_jantung_janin']} dpm</td>
                                <td>{$row['tbj']} gr</td>
                                <td>{$hpl_formatted}</td>
                                <td>{$row['glukosa_darah']}</td>
                                <td>{$rujuk_status}</td>
                                <td>{$row['keluhan']}</td>
                                <td class='action-buttons'>
                                    <a href='hapus.php?id={$row['id']}' class='delete-btn' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?');\">Hapus</a>
                                </td>
                            </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='16'>Tidak ada data pemeriksaan untuk bulan ini.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <a href="rujukan.php" class="btn-global-action">Lihat Daftar Rujukan</a>

    </div>
</body>
</html>