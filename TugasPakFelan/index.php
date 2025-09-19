<?php
include 'koneksi.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxTime - Toko Jam Tangan Premium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Animasi masuk dari kiri */
        .slide-in-left {
            opacity: 0;
            transform: translateX(-100px);
            transition: all 0.8s ease-out;
        }

        .slide-in-left.show {
            opacity: 1;
            transform: translateX(0);
        }

        /* Animasi masuk dari kanan */
        .slide-in-right {
            opacity: 0;
            transform: translateX(100px);
            transition: all 0.8s ease-out;
        }

        .slide-in-right.show {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <section id="beranda">
            <nav class="navbar navbar-expand-lg navbar-light bg-gray px-3">
                <p class="logo mb-0">W-<span>Store</span></p>
                <ul class="nav-links navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="produkDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i>Produk Kami</i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="produkDropdown">
                            <li><a class="dropdown-item" href="toko.php"><i>Lihat Semua</i></a></li>
                            <li><a class="dropdown-item" href="toko.php?kategori=sport"><i>Sport</i></a></li>
                            <li><a class="dropdown-item" href="toko.php?kategori=luxury"><i>Luxury</i></a></li>
                            <li><a class="dropdown-item" href="toko.php?kategori=classic"><i>Classic</i></a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="tentang_kami.php">About us</a></li>

                    <a href="keranjang.php" class="ml-4 text-gray-600 hover:text-primary relative">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <span
                                class="absolute -top-2 -right-2 bg-orange-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                <?= count($_SESSION['cart']); ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <li class="mr-4 nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle fa-lg"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile_edit.php"><i
                                        class="fas fa-user-edit me-2"></i>Edit Profil</a></li>
                            <li><a class="dropdown-item" href="riwayat.php"><i class="fas fa-history me-2"></i>Riwayat
                                    Pembelian</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i
                                        class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="menu-toggle d-lg-none"><i class="fas fa-bars"></i></div>
            </nav>
        </section>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="beranda">
        <div class="hero-content fade-in">
            <h1>Koleksi Jam Tangan Eksklusif</h1>
            <p>Temukan jam tangan berkualitas tinggi yang mencerminkan gaya dan kepribadian Anda</p>
            <a href="toko.php" class="btn">View Produk</a>
        </div>
    </section>

    <!-- Categories -->
    <section id="kategori">
        <h2 class="section-title">Produk Populer</h2>
        <div class="categories">

            <div class="category-card">
                <div class="category-img">
                    <img src="Saved Pictures/IMG-20250811-WA0023.jpg" alt="Koleksi jam tangan mewah" />
                </div>
                <div class="category-info">
                    <h3>Luxury</h3>
                    <p>Jam tangan eksklusif dari merek ternama</p>
                    <br>
                    <a href="toko.php?kategori=luxury" class="btn">Lihat Koleksi</a>
                </div>
            </div>
            <div class="category-card">
                <div class="category-img">
                    <img src="Saved Pictures/Gambar WhatsApp 2025-08-11 pukul 09.55.26_07df64c1.jpg"
                        alt="Jam tangan sport" />
                </div>
                <div class="category-info">
                    <h3>Sport</h3>
                    <p>Untuk gaya hidup aktif dan petualangan</p>
                    <br>
                    <a href="toko.php?kategori=sport" class="btn">Lihat Koleksi</a>
                </div>
            </div>
            <div class="category-card">
                <div class="category-img">
                    <img src="Saved Pictures/IMG-20250811-WA0025.jpg" alt="Jam tangan klasik" />
                </div>
                <div class="category-info">
                    <h3>Classic</h3>
                    <p>Gaya timeless untuk kesan yang elegan</p>
                    <br>
                    <a href="toko.php?kategori=classic" class="btn">Lihat Koleksi</a>
                </div>
            </div>

        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials" id="test">
        <h2 class="section-title">Apa Kata Pelanggan</h2>
        <div class="mb-2">
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                <div class="carousel-inner">

                    <!-- Testimoni 1 -->
                    <div class="carousel-item active">
                        <div class="testimonial-card mx-auto" style="max-width: 700px;">
                            <p class="testimonial-text">"Beli Emporio Armani AR1452 Cakepp pisan dan sangat elegan.
                                Pelayanan LuxTime sangat profesional dan ramah."</p>
                            <div class="testimonial-author">
                                <img src="Saved Pictures/gambar jelek.jpg" alt="Foto pelanggan" />
                                <div class="author-info">
                                    <h4>Hapis Zakiy</h4>
                                    <p>Pelajar, Metro</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimoni 2 -->
                    <div class="carousel-item">
                        <div class="testimonial-card mx-auto" style="max-width: 700px;">
                            <p class="testimonial-text">"Sebagai kolektor jam tangan, saya sangat menghargai keaslian
                                dan
                                kondisi produk dari LuxTime. Packaging-nya juga sangat premium dan aman."</p>
                            <div class="testimonial-author">
                                <img src="Saved Pictures/Gambar naufal.jpg" alt="Foto pelanggan" />
                                <div class="author-info">
                                    <h4>M.Naufal Maulana</h4>
                                    <p>Mahasiswa, Metro</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimoni 3 -->
                    <div class="carousel-item">
                        <div class="testimonial-card mx-auto" style="max-width: 700px;">
                            <p class="testimonial-text">"Pengalaman belanja pertama saya di LuxTime sangat menyenangkan.
                                Ditambah lagi dengan garansi resmi yang diberikan, membuat saya lebih percaya."</p>
                            <div class="testimonial-author">
                                <img src="Saved Pictures/Gambar sutan.jpg" alt="Foto pelanggan" />
                                <div class="author-info">
                                    <h4>Sutan Dafa Samudra</h4>
                                    <p>Mahasiswa, Lampung</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Navigasi Carousel -->
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>

                <!-- Indikator Bawah -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="0"
                        class="active"></button>
                    <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="2"></button>
                </div>
            </div>
        </div>
    </section>



    <!-- Footer -->
    <footer class="copyright">
        <p>&copy; 2025 W-Store website.</p>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile Menu Toggle
        document.querySelector('.menu-toggle').addEventListener('click', function () {
            document.querySelector('.nav-links').classList.toggle('active');
        });

        // Smooth Scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Animation on Scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2
        });

        document.querySelectorAll('.slide-in-left, .slide-in-right').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>

</html>