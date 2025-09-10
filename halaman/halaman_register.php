<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

 
    $stmt = $conn->prepare("INSERT INTO users (nik, nama, no_hp, tempat_lahir, tanggal_lahir, alamat, role, email, password)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $nik, $nama, $no_hp, $tempat_lahir, $tanggal_lahir, $alamat, $role, $email, $password);

    if ($stmt->execute()) {
        
        $new_user_id = $conn->insert_id;
        $_SESSION['user_id'] = $new_user_id; 
        $_SESSION['email'] = $email;
        $_SESSION['nama'] = $nama;
        $_SESSION['user_nama'] = $nama; 
        $_SESSION['role'] = $role; 

        // Pengalihan berdasarkan role setelah registrasi
        if ($role == 'Ibu Hamil') {
            header("Location: pendaftaran_ibu.php"); // Arahkan Ibu Hamil ke form pendaftaran_ibu
            exit();
        } elseif ($role == 'Petugas') {
            header("Location: halaman_login.php?register=success"); // Petugas langsung ke halaman login
            exit();
        } else {
            // Fallback jika role tidak dikenal
            header("Location: halaman_login.php?register=success_unknown_role");
            exit();
        }
    } else {
        echo "Registrasi gagal: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun - MomCare</title>
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
            background-color: var(--primary-pink);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--dark-gray);
        }

        .register-container {
            background: var(--white);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 500px;
            text-align: center;
            margin: 20px;
        }

        .register-container h2 {
            text-align: center;
            color: var(--accent-teal);
            font-size: 2.2em;
            margin-bottom: 30px;
        }

        .register-container .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .register-container .logo-container img {
            height: 60px;
        }

        .register-container .logo-container .logo-text {
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

        input[type="text"],
        input[type="date"],
        input[type="email"],
        input[type="password"],
        select {
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

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            border-color: var(--accent-teal);
            outline: none;
        }

        small {
            color: #666;
            font-size: 0.9em;
            margin-left: 5px;
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

        .login-link {
            margin-top: 25px;
            font-size: 1em;
            color: var(--dark-gray);
        }

        .login-link a {
            color: var(--accent-teal);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #008f8f;
            text-decoration: underline;
        }

        /* Responsif */
        @media (max-width: 600px) {
            .register-container {
                padding: 30px;
                margin: 15px;
            }
            .register-container h2 {
                font-size: 2em;
                margin-bottom: 20px;
            }
            .register-container .logo-container img {
                height: 50px;
            }
            .register-container .logo-container .logo-text {
                font-size: 1.8em;
            }
            label {
                font-size: 0.95em;
            }
            input[type="text"],
            input[type="date"],
            input[type="email"],
            input[type="password"],
            select {
                padding: 10px 12px;
                font-size: 0.95em;
                margin-bottom: 10px;
            }
            .btn {
                padding: 12px 20px;
                font-size: 1em;
                margin-top: 15px;
            }
            .login-link {
                font-size: 0.9em;
                margin-top: 20px;
            }
        }

        @media (max-width: 400px) {
            .register-container {
                padding: 20px;
            }
            .register-container h2 {
                font-size: 1.8em;
            }
            .register-container .logo-container img {
                height: 40px;
            }
            .register-container .logo-container .logo-text {
                font-size: 1.5em;
            }
            label {
                font-size: 0.9em;
            }
            input[type="text"],
            input[type="date"],
            input[type="email"],
            input[type="password"],
            select {
                padding: 8px 10px;
                font-size: 0.9em;
            }
            .btn {
                padding: 10px 15px;
                font-size: 0.95em;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-container">
            <img src="http://localhost/momcare/halaman/momcare.jpg" alt="MomCare Logo">
            <span class="logo-text">MomCare</span>
        </div>
        <h2>Daftar Akun Baru</h2>
        <form method="post" action="">
            <label for="nik">NIK</label>
            <input type="text" id="nik" name="nik" placeholder="Masukkan NIK Anda" required>

            <label for="nama">Nama <small>(Sesuai dengan NIK)</small></label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap Anda" required>

            <label for="no_hp">No. Handphone</label>
            <input type="text" id="no_hp" name="no_hp" placeholder="Masukkan nomor handphone" required>

            <label for="tempat_lahir">Tempat Lahir</label>
            <input type="text" id="tempat_lahir" name="tempat_lahir" placeholder="Masukkan tempat lahir" required>

            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>

            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" placeholder="Masukkan alamat lengkap" required>

            <label for="role">Daftar Sebagai</label>
            <select id="role" name="role" required>
                <option value="Petugas">Petugas Kesehatan</option>
                <option value="Ibu Hamil">Ibu Hamil</option>
            </select>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Masukkan email Anda">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Buat password">

            <button type="submit" class="btn">Daftar Akun</button>
        </form>
        <p class="login-link">Sudah punya akun? <a href="halaman_login.php">Login di sini</a></p>
    </div>
</body>
</html>