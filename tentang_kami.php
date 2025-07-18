<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - JalanJalan Kuy!</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </head>
<body>
    <header class="main-header">
        <div class="container header-content">
            <div class="logo">
                <a href="index.php">JalanJalan Kuy!</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="paket_tur.php">Paket Tour</a></li>
                    <li><a href="tentang_kami.php" class="active">Tentang Kami</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container content">

        <section class="about-hero-section">
            <div class="about-hero-overlay"></div>
            <div class="container about-hero-content">
                <h1>Tentang Kami</h1>
                <p>JalanJalan Kuy! adalah mitra perjalanan tepercaya Anda, mewujudkan impian petualangan tak terlupakan ke seluruh penjuru dunia dengan kenyamanan dan keamanan terdepan.</p>
            </div>
        </section>

        <section class="vision-mission-section">
            <div class="container">
                <div class="section-title-wrapper">
                    <h2 class="section-title">Visi & Misi Kami</h2>
                    <p class="section-subtitle">Fondasi yang membimbing setiap langkah perjalanan kami.</p>
                </div>

                <div class="vm-grid">
                    <div class="vm-card">
                        <h3>Visi</h3>
                        <p>Menjadi platform perjalanan terkemuka yang menginspirasi dan memfasilitasi petualangan tak terlupakan bagi setiap individu, membuka wawasan baru dan pengalaman budaya yang mendalam.</p>
                    </div>
                    <div class="vm-card">
                        <h3>Misi</h3>
                        <ul>
                            <li>Menyediakan beragam pilihan tour berkualitas tinggi dengan harga kompetitif.</li>
                            <li>Mengutamakan kepuasan dan keamanan pelanggan dalam setiap aspek perjalanan.</li>
                            <li>Membangun tim profesional yang berdedikasi dan berpengetahuan luas.</li>
                            <li>Berinovasi dalam layanan untuk memenuhi kebutuhan perjalanan yang terus berkembang.</li>
                            <li>Berkontribusi pada pariwisata berkelanjutan dan pelestarian budaya lokal.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="why-choose-us">
            <div class="container">
                <div class="section-title-wrapper">
                    <h2 class="section-title">Mengapa Memilih Kami?</h2>
                    <p class="section-description">Kami berkomitmen untuk memberikan pengalaman perjalanan terbaik yang aman, nyaman, dan penuh kegembiraan.</p>
                </div>

                <div class="benefits-grid">
                    <div class="benefit-card">
                        <div class="icon-circle time-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3>Pelayanan Prima</h3>
                        <p>Tim kami siap membantu Anda dari perencanaan hingga akhir perjalanan, memastikan setiap detail terpenuhi dengan sempurna.</p>
                    </div>
                    <div class="benefit-card">
                        <div class="icon-circle check-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Keamanan Terjamin</h3>
                        <p>Prioritas utama kami adalah keamanan dan kenyamanan Anda. Kami bekerja sama dengan mitra terpercaya dan berpengalaman.</p>
                    </div>
                    <div class="benefit-card">
                        <div class="icon-circle package-icon">
                            <i class="fas fa-plane-departure"></i>
                        </div>
                        <h3>Paket Fleksibel</h3>
                        <p>Pilih dari berbagai paket tour yang dapat disesuaikan dengan minat dan anggaran Anda. Petualangan impian Anda, kami wujudkan!</p>
                    </div>
                </div>
            </div>
        </section>

        </div> <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy! All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Karnaufal</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Skrip untuk highlight navigasi aktif (Ini akan berfungsi untuk halaman ini saja)
        $(document).ready(function() {
            const currentPath = window.location.pathname.split('/').pop();
            $('nav.main-nav ul li a').each(function() {
                const linkPath = $(this).attr('href');
                if (linkPath === currentPath) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
    </body>
</html>