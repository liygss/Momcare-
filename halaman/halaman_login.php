<?php
session_start();
include 'connect.php'; // Pastikan file connect.php ada dan berisi koneksi database

$error = '';
// Check for error messages from proses_login.php
if (isset($_GET['error'])) {
    $error = htmlspecialchars(urldecode($_GET['error']));
}
// Check for success message from pendaftaran_ibu.php
$success_message = '';
if (isset($_GET['status']) && $_GET['status'] == 'pendaftaran_ibu_success') {
    $success_message = 'Pendaftaran data kehamilan berhasil! Silakan login.';
}
// Check for register success from halaman_register.php
if (isset($_GET['register']) && $_GET['register'] == 'success') {
    $success_message = 'Registrasi berhasil! Silakan login.';
} elseif (isset($_GET['register']) && $_GET['register'] == 'success_unknown_role') {
    $success_message = 'Registrasi berhasil. Silakan login.'; // Atau pesan yang lebih spesifik
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Your login processing will now be handled by proses_login.php
    // You should *not* have the login logic here if you're using proses_login.php as a handler
    // This file (halaman_login.php) should only display the form and messages
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MomCare</title>
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

        .login-container {
            background: var(--white);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin: 20px;
        }

        .login-container .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .login-container .logo-container img {
            height: 60px;
        }

        .login-container .logo-container .logo-text {
            font-size: 2em;
            font-weight: 700;
            color: var(--accent-teal);
        }

        .login-container h2 {
            color: var(--accent-teal);
            font-size: 2.2em;
            margin-bottom: 30px;
        }

        .message-container {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: 600;
        }
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .success-message {
            color: #28a745;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }


        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            color: var(--dark-gray);
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
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
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #008f8f;
            transform: translateY(-2px);
        }

        .register-link {
            margin-top: 25px;
            font-size: 1em;
            color: var(--dark-gray);
        }

        .register-link a {
            color: var(--accent-teal);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #008f8f;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .login-container {
                padding: 30px;
                margin: 15px;
            }
            .login-container h2 {
                font-size: 2em;
                margin-bottom: 20px;
            }
            .login-container .logo-container img {
                height: 50px;
            }
            .login-container .logo-container .logo-text {
                font-size: 1.8em;
            }
            input[type="text"],
            input[type="password"] {
                padding: 10px 12px;
                font-size: 0.95em;
                margin-bottom: 15px;
            }
            .btn {
                padding: 12px 20px;
                font-size: 1em;
                margin-top: 10px;
            }
            .register-link {
                font-size: 0.9em;
                margin-top: 20px;
            }
        }

        @media (max-width: 400px) {
            .login-container {
                padding: 20px;
            }
            .login-container h2 {
                font-size: 1.8em;
            }
            .login-container .logo-container img {
                height: 40px;
            }
            .login-container .logo-container .logo-text {
                font-size: 1.5em;
            }
            input[type="text"],
            input[type="password"] {
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
    <div class="login-container">
        <div class="logo-container">
            <img src="http://localhost/momcare/halaman/momcare.jpg" alt="MomCare Logo">
            <span class="logo-text">MomCare</span>
        </div>
        <h2>Selamat Datang Kembali</h2>
        <?php if (!empty($error)) : ?>
            <p class="message-container error-message"><?= $error ?></p>
        <?php elseif (!empty($success_message)) : ?>
            <p class="message-container success-message"><?= $success_message ?></p>
        <?php endif; ?>
        <form action="proses_login.php" method="post"> <input type="text" name="email" placeholder="Email Anda" required>
            <input type="password" name="password" placeholder="Password Anda" required>
            <button type="submit" class="btn">Login</button>
        </form>
        <p class="register-link">Belum punya akun? <a href="halaman_register.php">Daftar sekarang</a></p>
    </div>
</body>
</html>