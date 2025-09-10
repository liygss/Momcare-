<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MomCare - Kesehatan Ibu Hamil</title>
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
            background-color: var(--white);
            color: var(--dark-gray);
            line-height: 1.6;
            overflow-x: hidden; /* Prevent horizontal scroll from animations */
        }

        header {
            background-color: var(--primary-pink);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--box-shadow);
            position: sticky; /* Navbar tetap di atas saat scroll */
            top: 0;
            z-index: 1000;
        }

        header .logo-container {
            display: flex;
            align-items: center;
            gap: 10px; 
            /* Animation for logo */
            animation: fadeInDown 0.8s ease-out;
        }

        header .logo-container img {
            height: 50px; 
        }
        
        header .logo-container .logo-text {
            font-size: 1.8em; 
            font-weight: 700;
            color: var(--accent-teal);
        }

        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: var(--dark-gray);
            font-weight: 600;
            transition: color 0.3s ease, transform 0.2s ease; /* Added transform */
        }

        nav a:hover {
            color: var(--accent-teal);
            transform: translateY(-3px); /* Subtle lift on hover */
        }

        .hero {
            background: linear-gradient(to right, var(--primary-pink), #ffe6ea);
            display: flex;
            justify-content: center; 
            align-items: center;
            padding: 60px 40px;
            min-height: 400px; 
            gap: 40px; 
            overflow: hidden; /* Ensure elements don't overflow during animation */
        }

        .hero-text {
            max-width: 500px; 
            text-align: left;
            /* Animation for hero text */
            animation: fadeInLeft 1s ease-out;
        }

        .hero-text h1 {
            color: var(--accent-teal);
            font-size: 3.2em; 
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .hero-text p {
            font-size: 1.1em;
            color: var(--dark-gray);
            margin-bottom: 25px;
        }

        .btn {
            padding: 12px 25px;
            background-color: var(--accent-teal);
            color: var(--white);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease; /* Added box-shadow transition */
        }

        .btn:hover {
            background-color: #008f8f; 
            transform: translateY(-5px); /* More pronounced lift */
            box-shadow: 0 8px 15px rgba(0, 168, 168, 0.3); /* Add shadow on hover */
        }

        .hero img {
            max-height: 400px; 
            border-radius: 15px; 
            box-shadow: var(--box-shadow);
            /* Animation for hero image */
            animation: fadeInRight 1s ease-out;
        }

        .section-container {
            display: flex;
            flex-direction: column; 
            padding: 60px 40px;
            background-color: var(--light-gray-bg);
            gap: 50px; 
        }

        .section-item {
            background-color: var(--white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Added transitions */
            opacity: 0; /* Hidden by default for JS animation */
            transform: translateY(20px); /* Start slightly below for JS animation */
        }

        .section-item.animate { /* Class added by JavaScript */
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .section-item:hover {
            transform: translateY(-8px); /* Lift higher on hover */
            box-shadow: 0 8px 16px rgba(0,0,0,0.2); /* Stronger shadow on hover */
        }

        .section-item h2 {
            color: var(--accent-teal);
            font-size: 2.2em;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .layanan-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            text-align: center;
            font-size: 1.1em;
        }
        .layanan-list p {
            background-color: var(--primary-pink);
            padding: 15px;
            border-radius: 8px;
            color: var(--dark-gray);
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: background-color 0.3s ease, transform 0.2s ease; /* Added transform */
        }
        .layanan-list p:hover {
            background-color: #ffc2c7;
            transform: scale(1.03); /* Slight scale up on hover */
        }

        .news ul {
            list-style: none; 
            padding: 0;
        }
        .news li {
            background-color: #e0f0f0; 
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: background-color 0.3s ease, transform 0.2s ease; /* Added transform */
        }
        .news li:hover {
            background-color: #d0e0e0;
            transform: translateX(5px); /* Slide right on hover */
        }

        .about-us p {
            font-size: 1.1em;
            line-height: 1.8;
            text-align: justify;
        }

        /* Keyframe Animations */
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

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
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

        /* Responsif */
        @media (max-width: 900px) {
            header, .hero, .section-container {
                padding: 15px 20px;
            }
            header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            header nav {
                width: 100%;
                display: flex;
                justify-content: space-around;
            }
            nav a {
                margin: 0 5px;
                font-size: 0.9em;
            }
            .hero {
                flex-direction: column;
                text-align: center;
                gap: 30px;
            }
            .hero-text {
                max-width: 100%;
                text-align: center;
            }
            .hero-text h1 {
                font-size: 2.5em;
            }
            .hero img {
                max-height: 250px;
            }
            .section-container {
                gap: 30px;
            }
            .layanan-list {
                grid-template-columns: 1fr; 
            }
        }
        
        @media (max-width: 500px) {
            header .logo-container .logo-text {
                font-size: 1.5em;
            }
            .hero-text h1 {
                font-size: 2em;
            }
            .btn {
                padding: 10px 20px;
                font-size: 1em;
            }
            .section-item h2 {
                font-size: 1.8em;
            }
            .about-us p, .layanan-list p, .news li {
                font-size: 0.95em;
            }
        }

    </style>
</head>
<body>

<header>
    <div class="logo-container">
        <img src="http://localhost/momcare/halaman/momcare.jpg" alt="MomCare Logo"> 
        <span class="logo-text">MomCare</span> </div>
    <nav>
        <a href="beranda.php">Beranda</a>
        <a href="#tentang-kami">Tentang</a> <a href="#layanan-kami">Layanan</a> <a href="#momcare-news">Berita</a> <a href="halaman_login.php">Login</a> </nav>
</header>

<section class="hero">
    <div class="hero-text">
        <h1>Kesehatan Ibu Hamil</h1>
        <p>Solusi kesehatan menyeluruh untuk ibu hamil. Kami hadir sebagai mitra terpercaya Anda dalam perjalanan kehamilan.</p>
        <a href="halaman_login.php" class="btn">Mulai Sekarang</a>
    </div>
    <img src="http://localhost/momcare/halaman/ibuhamil.png" alt="Ibu Hamil">
</section>

<div class="section-container">
    <section class="section-item about-us" id="tentang-kami"> <h2>Tentang Kami</h2>
        <p>Selamat datang di MomCare! Kami hadir sebagai mitra terpercaya bagi para ibu dan calon ibu, memberikan dukungan menyeluruh selama masa kehamilan. Di sini, Anda bisa mendapatkan informasi seputar kehamilan, pemeriksaan rutin, hingga tips menjaga kesehatan ibu dan bayi. Dengan tenaga kesehatan yang ramah dan berpengalaman, kami siap membantu Anda menjalani kehamilan yang sehat, aman, dan penuh kebahagiaan. Semua layanan kami disediakan dengan sepenuh hati, karena kami percaya bahwa ibu sehat, keluarga pun bahagia.</p>
    </section>

    <section class="section-item" id="layanan-kami"> <h2>Layanan Kami</h2>
        <div class="layanan-list">
            <p>👩‍🍼 Konsultasi Kehamilan</p>
            <p>📸 USG & Pemeriksaan</p>
            <p>➕ Perawatan Kesehatan</p>
            <p>📚 Edukasi Kehamilan</p>
            <p>💡 Tips Kesehatan</p>
            <p>🗓️ Jadwal Pemeriksaan</p>
        </div>
    </section>

    <section class="section-item news" id="momcare-news"> <h2>MomCare News</h2>
        <ul>
            <li>5 Tips Menjaga Kesehatan Selama Kehamilan</li>
            <li>Ciri-Ciri Kehamilan Sehat yang Perlu Diketahui Ibu Hamil</li>
            <li>Manfaat Senam Hamil untuk Kebugaran Ibu</li>
            <li>Nutrisi Penting untuk Perkembangan Janin</li>
        </ul>
    </section>
</div>

<script>
    // JavaScript for scroll animations
    document.addEventListener('DOMContentLoaded', function() {
        const sectionItems = document.querySelectorAll('.section-item');

        const observerOptions = {
            root: null, // relative to the viewport
            rootMargin: '0px',
            threshold: 0.1 // 10% of the item visible to trigger
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, observerOptions);

        sectionItems.forEach(item => {
            observer.observe(item);
        });
    });
</script>

</body>
</html>