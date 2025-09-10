<?php
session_start();
include 'connect.php'; // Pastikan file connect.php ada dan berisi koneksi database

// Memeriksa apakah pengguna sudah login dan role-nya 'Ibu Hamil'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Ibu Hamil') {
    // Jika tidak sesuai, alihkan kembali ke halaman login atau beranda
    header("Location: halaman_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$nama_user = $_SESSION['user_nama'] ?? "Ibu Hamil";
$message = ''; // Untuk menampilkan pesan sukses atau error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usia_kehamilan_input = $_POST['usia_kehamilan'] ?? null;
    $estimasi_kelahiran_input = $_POST['estimasi_kelahiran'] ?? null;
    $kondisi_bayi_input = $_POST['kondisi_bayi'] ?? null;

    // Validasi input
    if (empty($usia_kehamilan_input) || !is_numeric($usia_kehamilan_input)) {
        $message = "Usia kehamilan tidak valid.";
    } elseif (empty($estimasi_kelahiran_input) || !strtotime($estimasi_kelahiran_input)) {
        $message = "Tanggal estimasi kelahiran tidak valid.";
    } else {
        // Update data di tabel users
        $sql = "UPDATE users SET usia_kehamilan = ?, estimasi_kelahiran = ?, kondisi_bayi = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssi", $usia_kehamilan_input, $estimasi_kelahiran_input, $kondisi_bayi_input, $user_id);

            if ($stmt->execute()) {
                // Berhasil update, alihkan ke halaman login agar bisa masuk ke dashboard
                header("Location: halaman_login.php?status=pendaftaran_ibu_success");
                exit();
            } else {
                $message = "Error saat menyimpan data: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ibu Hamil - MomCare</title>
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
            background-color: var(--primary-pink);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--dark-gray);
        }

        .container {
            background: var(--white);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 500px;
            text-align: center;
            margin: 20px;
        }

        .container h2 {
            color: var(--accent-teal);
            font-size: 2.2em;
            margin-bottom: 30px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .logo-container img {
            height: 60px;
        }

        .logo-container .logo-text {
            font-size: 2em;
            font-weight: 700;
            color: var(--accent-teal);
        }

        label {
            display: block;
            text-align: left;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--dark-gray);
        }

        input[type="number"],
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
            box-sizing: border-box;
        }

        input[type="number"]:focus,
        input[type="date"]:focus,
        textarea:focus {
            border-color: var(--accent-teal);
            outline: none;
        }

        .btn {
            width: 100%;
            background-color: var(--accent-teal);
            color: var(--white);
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #008f8f;
            transform: translateY(-2px);
        }

        .message {
            color: #dc3545; /* Merah untuk error */
            margin-bottom: 15px;
            font-weight: 600;
        }
        .message.success {
            color: var(--accent-teal); /* Teal untuk sukses */
        }

        /* Responsive */
        @media (max-width: 600px) {
            .container {
                padding: 30px;
                margin: 15px;
            }
            .container h2 {
                font-size: 2em;
                margin-bottom: 20px;
            }
            input[type="number"],
            input[type="date"],
            textarea {
                padding: 10px 12px;
                font-size: 0.95em;
                margin-bottom: 10px;
            }
            .btn {
                padding: 12px 20px;
                font-size: 1em;
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="http://localhost/momcare/halaman/momcare.jpg" alt="MomCare Logo">
            <span class="logo-text">MomCare</span>
        </div>
        <h2>Data Tambahan Ibu Hamil</h2>
        <p>Halo, Ibu **<?php echo htmlspecialchars($nama_user); ?>**! Mohon lengkapi data kehamilan Anda.</p>

        <?php if (!empty($message)) : ?>
            <p class="message <?= (strpos($message, 'berhasil') !== false) ? 'success' : '' ?>"><?= $message ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label for="usia_kehamilan">Usia Kehamilan (dalam minggu)</label>
            <input type="number" id="usia_kehamilan" name="usia_kehamilan" placeholder="Contoh: 8" min="0" required>

            <label for="estimasi_kelahiran">Estimasi Tanggal Kelahiran (HPL)</label>
            <input type="date" id="estimasi_kelahiran" name="estimasi_kelahiran" required>

            <label for="kondisi_bayi">Kondisi Bayi (Opsional)</label>
            <textarea id="kondisi_bayi" name="kondisi_bayi" rows="3" placeholder="Contoh: Sehat, aktif, tidak ada kelainan..."></textarea>

            <button type="submit" class="btn">Simpan Data Kehamilan</button>
        </form>
    </div>
</body>
</html>