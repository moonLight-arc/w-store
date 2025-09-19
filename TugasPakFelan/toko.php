<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W-Store - Toko Jam Tangan Premium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tambahkan SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style.css">
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
    <br>
    <!-- Section Produk -->
    <section id="produk">
        <?php
        include 'koneksi.php';

        // Fungsi beli sekarang
        if (isset($_POST['beli_sekarang'])) {
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $harga = $_POST['harga'];
            $jumlah = $_POST['jumlah'];
            $gambar = $_POST['gambar']; // simpan gambar

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $id) {
                    $item['jumlah'] += $jumlah;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $_SESSION['cart'][] = [
                    "id" => $id,
                    "nama" => $nama,
                    "harga" => $harga,
                    "jumlah" => $jumlah,
                    "gambar" => $gambar
                ];
            }

            header("Location: checkout.php");
            exit;
        }

        // Tambah ke keranjang
        if (isset($_POST['add_to_cart'])) {
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $harga = $_POST['harga'];
            $jumlah = $_POST['jumlah'];
            $gambar = $_POST['gambar']; // simpan gambar

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $id) {
                    $item['jumlah'] += $jumlah;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $_SESSION['cart'][] = [
                    "id" => $id,
                    "nama" => $nama,
                    "harga" => $harga,
                    "jumlah" => $jumlah,
                    "gambar" => $gambar
                ];
            }

            // Notifikasi modern pakai SweetAlert2
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Produk berhasil ditambahkan ke keranjang!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
            </script>";
        }
        ?>

        <section id="populer">
            <?php
            // ambil kategori dari URL
            $kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

            if ($kategori) {
                $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE kategori='$kategori'");
                echo "<h2 class='section-title text-capitalize'>Kategori: $kategori</h2>";
            } else {
                $query = mysqli_query($koneksi, "SELECT * FROM produk");
                echo "<h2 class='section-title'>Semua Produk</h2>";
            }
            ?>
            <div class="products">
                <?php while ($data = mysqli_fetch_assoc($query)) { ?>
                    <div class="product-card">
                        <div class="product-img">
                            <img src="images/<?= $data['gambar']; ?>" alt="Jam tangan premium" />
                            <span class="product-badge"><?= ucfirst($data['kategori']); ?></span>
                        </div>
                        <div class="product-info">
                            <h3><?= $data['deskripsi']; ?></h3>
                            <div class="price">Rp <?= number_format($data['harga'], 0, ',', '.'); ?></div>
                            
                            <!-- Form Beli Sekarang -->
                            <form method="post" action="toko.php<?= $kategori ? '?kategori=' . $kategori : '' ?>" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $data['id_produk']; ?>">
                                <input type="hidden" name="nama" value="<?= $data['deskripsi']; ?>">
                                <input type="hidden" name="harga" value="<?= $data['harga']; ?>">
                                <input type="hidden" name="gambar" value="<?= $data['gambar']; ?>"><!-- simpan gambar -->
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" name="beli_sekarang" class="btn btn-success">Beli Sekarang</button>
                            </form>

                            <!-- Form Tambah ke Keranjang -->
                            <form method="post" action="toko.php<?= $kategori ? '?kategori=' . $kategori : '' ?>" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $data['id_produk']; ?>">
                                <input type="hidden" name="nama" value="<?= $data['deskripsi']; ?>">
                                <input type="hidden" name="harga" value="<?= $data['harga']; ?>">
                                <input type="hidden" name="gambar" value="<?= $data['gambar']; ?>"><!-- simpan gambar -->
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" name="add_to_cart" class="btn btn-success">Keranjang</button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>

        <footer class="copyright">
            <p>&copy; 2025 W-Store website.</p>
        </footer>

        <script>
            // Mobile Menu Toggle
            document.querySelector('.menu-toggle').addEventListener('click', function () {
                document.querySelector('.nav-links').classList.toggle('active');
            });
        </script>

</body>
</html>
