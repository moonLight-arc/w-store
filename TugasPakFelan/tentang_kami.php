<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - W-Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
    }

    .about-section {
      padding: 50px 20px;
    }

    .about-img {
      max-width: 100%;
      border-radius: 10px;
    }

    .about-text h2 {
      font-weight: bold;
      margin-bottom: 20px;
    }

    .about-text p {
      color: #555;
      line-height: 1.7;
    }

    .btn-home {
      background: linear-gradient(45deg, #000000, #b0adb4);
      color: white;
      border-radius: 50px;
      padding: 10px 25px;
      font-weight: bold;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease;
    }

    .btn-home:hover {
      background: linear-gradient(45deg, hsl(0, 1%, 67%), #000000);
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
      color: white;
    }

    .contact-card {
      background: #e7e7e7;
      border-radius: 9px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 30px;
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
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="produkDropdown" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
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
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-user-circle fa-lg"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="profile_edit.php"><i class="fas fa-user-edit me-2"></i>Edit Profil</a></li>
              <li><a class="dropdown-item" href="riwayat.php"><i class="fas fa-history me-2"></i>Riwayat Pembelian</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
          </li>
        </ul>
        <div class="menu-toggle d-lg-none"><i class="fas fa-bars"></i></div>
      </nav>
    </section>
  </header>
<br><br>
  <!-- About Us Section -->
  <section class="about-section container">
    <div class="row align-items-center">
      <div class="col-md-6">
        <img src="Saved Pictures/Gambar tentang kami.jpg" alt="Watches" class="about-img shadow">
      </div>
      <div class="col-md-6 about-text">
        <h2>About us</h2>
        <p>
          Selamat datang di <strong>W-Store</strong> – destinasi terbaik untuk para pecinta jam tangan.
          Sejak berdiri, kami berkomitmen menghadirkan koleksi jam tangan premium dengan kualitas terbaik
          dan desain yang elegan.
        </p>
        <p>
          Di W-Store, kami percaya bahwa jam tangan bukan hanya penunjuk waktu,
          tapi juga simbol gaya dan kepribadian.
          Setiap produk kami dipilih dengan cermat untuk memastikan kepuasan pelanggan
          dan ketahanan yang luar biasa.
        </p>
        <p>
          Bergabunglah dengan ribuan pelanggan kami yang sudah mempercayakan W-Store
          sebagai partner fashion mereka.
          Temukan jam tangan impian Anda hari ini!
        </p>
      </div>
    </div>
  </section>
  <hr>

  <!-- Kontak -->
  <section id="test">
    <div class="contact-card">
      <div class="container">
        <div class="row mb-2">
          <div class="col-md-8 text-center mx-auto">
            <h1 class="fw-bold text-dark"><i class="bi bi-envelope-fill"></i> Kontak Kami</h1>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 mt-4 mt-md-0">
            <h5 class="fw-bold"><i class="bi bi-geo-alt-fill"></i> Alamat</h5>
            <p>Jl. Mawar No. 123, Jakarta</p>
            <h5 class="fw-bold"><i class="bi bi-telephone-fill"></i> Telepon</h5>
            <p>+62 812 3456 7890</p>
            <h5 class="fw-bold"><i class="bi bi-envelope-at-fill"></i> Email</h5>
            <p>info@example.com</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="copyright">
    <p>&copy; 2025 W-Store website.</p>
  </footer>

  <!-- Bootstrap JS hanya sekali -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
