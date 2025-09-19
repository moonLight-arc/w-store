<?php
include 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Jika keranjang kosong
if (empty($_SESSION['cart'])) {
    echo "<script>alert('Keranjang masih kosong!'); window.location='toko.php';</script>";
    exit;
}

// Ambil ID user
$user_id = $_SESSION['user']['id_user'] ?? null;
if (!$user_id) {
    echo "<script>alert('ID user tidak ditemukan di session!'); window.location='login.php';</script>";
    exit;
}

// Hitung subtotal
$grand_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $grand_total += $item['harga'] * $item['jumlah'];
}

// Hitung ongkir
$ongkir = ($grand_total >= 600000) ? 0 : 30000;
$grandTotal = $grand_total + $ongkir;

// Proses checkout
if (isset($_POST['checkout'])) {
    // Ambil input form
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $telepon  = mysqli_real_escape_string($koneksi, $_POST['telepon']);
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kota     = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $provinsi = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
    $kode_pos = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $pembayaran = isset($_POST['pembayaran']) 
        ? mysqli_real_escape_string($koneksi, $_POST['pembayaran']) 
        : 'Belum Dipilih';

    // Simpan ke tabel orders
    $query = "INSERT INTO `orders` 
        (user_id, nama_penerima, telepon, alamat, kota, provinsi, kode_pos, metode_pembayaran, ongkir, total, created_at) 
        VALUES 
        ('$user_id', '$nama', '$telepon', '$alamat', '$kota', '$provinsi', '$kode_pos', '$pembayaran', '$ongkir', '$grandTotal', NOW())";
    mysqli_query($koneksi, $query);

    $order_id = mysqli_insert_id($koneksi);

    // Simpan detail ke order_items
    foreach ($_SESSION['cart'] as $item) {
        $id_produk = (int) $item['id'];
        $jumlah    = (int) $item['jumlah'];
        $harga     = (int) $item['harga'];

        $query_item = "INSERT INTO order_items (order_id, product_id, jumlah, harga) 
                       VALUES ('$order_id', '$id_produk', '$jumlah', '$harga')";
        mysqli_query($koneksi, $query_item);
    }

    // Kosongkan keranjang
    unset($_SESSION['cart']);

    echo "<script>alert('Pesanan berhasil dibuat!'); window.location='riwayat.php';</script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Toko Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ff3d00',
                        secondary: '#ffedcc',
                        dark: '#1e293b',
                        light: '#f8fafc'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .checkout-card {
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .checkout-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .checkout-btn {
            background: linear-gradient(to right, #ff3d00, #ff6d39);
            transition: all 0.3s ease;
        }

        .checkout-btn:hover {
            background: linear-gradient(to right, #e53600, #ff5c29);
            box-shadow: 0 4px 12px rgba(255, 61, 0, 0.25);
        }

        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ff3d00;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }

        .payment-method {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .payment-method:hover,
        .payment-method.selected {
            border-color: #ff3d00;
            background-color: #fff5f0;
        }

        .summary-item {
            border-bottom: 1px dashed #e2e8f0;
            padding: 10px 0;
        }

        .summary-item:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body class="bg-gray-50">
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
                                <?php echo count($_SESSION['cart']); ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Dropdown User -->
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
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i
                                        class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>

                <div class="menu-toggle d-lg-none">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </section>
    </header>

    <br><br><br>

    <main class="container mx-auto px-4 py-8">
        <h1 class="text-2xl md:text-3xl font-bold text-dark mb-2">Checkout</h1>
        <p class="text-gray-600 mb-8">Lengkapi informasi berikut untuk menyelesaikan pesanan Anda</p>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Form Section -->
            <div class="lg:w-2/3">
                <form method="post">
                    <!-- Informasi Pengiriman -->
                    <div class="checkout-card bg-white rounded-xl p-6 mb-6">
                        <h2 class="text-xl font-semibold text-dark mb-4 flex items-center">
                            <i class="fas fa-truck mr-3 text-primary"></i> Informasi Pengiriman
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="nama">Nama
                                    Penerima</label>
                                <input type="text" id="nama" name="nama"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    value="<?php echo isset($_SESSION['user']['nama']) ? $_SESSION['user']['nama'] : ''; ?>"
                                    required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="telepon">No.
                                    Telepon</label>
                                <input type="tel" id="telepon" name="telepon"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    value="<?php echo isset($_SESSION['user']['telepon']) ? $_SESSION['user']['telepon'] : ''; ?>"
                                    required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="alamat">Alamat
                                Lengkap</label>
                            <textarea id="alamat" name="alamat" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                required></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="kota">Kota</label>
                                <input type="text" id="kota" name="kota"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2"
                                    for="provinsi">Provinsi</label>
                                <input type="text" id="provinsi" name="provinsi"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="kode_pos">Kode
                                    Pos</label>
                                <input type="text" id="kode_pos" name="kode_pos"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="checkout-card bg-white rounded-xl p-6 mb-6">
                        <h2 class="text-xl font-semibold text-dark mb-4 flex items-center">
                            <i class="fas fa-credit-card mr-3 text-primary"></i> Metode Pembayaran
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="payment-method" onclick="selectPayment(this)">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-university text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-medium">Transfer Bank</h3>
                                        <p class="text-sm text-gray-500">BCA, BNI, Mandiri, BRI</p>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-method" onclick="selectPayment(this)">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-wallet text-green-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-medium">E-Wallet</h3>
                                        <p class="text-sm text-gray-500">Gopay, OVO, Dana, LinkAja</p>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-method" onclick="selectPayment(this)">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-credit-card text-red-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-medium">Kartu Kredit</h3>
                                        <p class="text-sm text-gray-500">Visa, Mastercard, JCB</p>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-method" onclick="selectPayment(this)">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-money-bill-wave text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-medium">COD</h3>
                                        <p class="text-sm text-gray-500">Bayar di tempat</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <?php
include 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Tambahkan fungsi update jumlah produk di keranjang
if (isset($_POST['id'], $_POST['action'])) {
    $id = $_POST['id'];

    if ($_POST['action'] === 'increase') {
        $_SESSION['cart'][$id]['jumlah']++;
    } elseif ($_POST['action'] === 'decrease') {
        $_SESSION['cart'][$id]['jumlah']--;
        if ($_SESSION['cart'][$id]['jumlah'] <= 0) {
            unset($_SESSION['cart'][$id]); // hapus produk jika jumlah 0
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ✅ Cek login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// ✅ Jika keranjang kosong
if (empty($_SESSION['cart'])) {
    echo "<script>alert('Keranjang masih kosong!'); window.location='toko.php';</script>";
    exit;
}

// ✅ Ambil ID user
$user_id = $_SESSION['user']['id_user'] ?? null;
if (!$user_id) {
    echo "<script>alert('ID user tidak ditemukan di session!'); window.location='login.php';</script>";
    exit;
}

// ✅ Hitung subtotal
$grand_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $grand_total += $item['harga'] * $item['jumlah'];
}

// ✅ Hitung ongkir
$ongkir = ($grand_total >= 600000) ? 0 : 30000;
$grandTotal = $grand_total + $ongkir;

// ✅ Proses checkout
if (isset($_POST['checkout'])) {
    $nama       = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $telepon    = mysqli_real_escape_string($koneksi, $_POST['telepon']);
    $alamat     = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kota       = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $provinsi   = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
    $kode_pos   = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $pembayaran = $_POST['pembayaran'] ?? 'Belum Dipilih';

    $query = "INSERT INTO `orders`
        (user_id, nama_penerima, telepon, alamat, kota, provinsi, kode_pos, metode_pembayaran, ongkir, total, created_at) 
        VALUES 
        ('$user_id', '$nama', '$telepon', '$alamat', '$kota', '$provinsi', '$kode_pos', '$pembayaran', '$ongkir', '$grandTotal', NOW())";
    mysqli_query($koneksi, $query);

    $order_id = mysqli_insert_id($koneksi);

    foreach ($_SESSION['cart'] as $item) {
        $id_produk = (int) $item['id'];
        $jumlah    = (int) $item['jumlah'];
        $harga     = (int) $item['harga'];

        $query_item = "INSERT INTO order_items (order_id, product_id, jumlah, harga) 
                       VALUES ('$order_id', '$id_produk', '$jumlah', '$harga')";
        mysqli_query($koneksi, $query_item);
    }

    unset($_SESSION['cart']);

    echo "<script>alert('Pesanan berhasil dibuat!'); window.location='riwayat.php';</script>";
    exit;
}
?>

                    <!-- Daftar Produk -->
                    <div class="checkout-card bg-white rounded-xl p-6 mb-6">
                        <h2 class="text-xl font-semibold text-dark mb-4 flex items-center">
                            <i class="fas fa-shopping-bag mr-3 text-primary"></i> Daftar Produk
                        </h2>

                        <div class="space-y-4">
                            <?php
                            foreach ($_SESSION['cart'] as $item) {
                                $total = $item['harga'] * $item['jumlah'];
                                echo '<div class="flex items-center border-b border-gray-100 pb-4">
        <img src="images/' . htmlspecialchars($item['gambar']) . '" 
             alt="' . htmlspecialchars($item['nama']) . '" 
             class="w-20 h-20 object-contain rounded-lg">
        <div class="ml-4 flex-grow">
            <h3 class="font-medium text-gray-800">' . htmlspecialchars($item['nama']) . '</h3>
            <p class="text-primary font-semibold mt-1">Rp ' . number_format($item['harga'], 0, ',', '.') . '</p>
        </div>
        <div class="text-right">
            <p class="text-gray-600">x' . $item['jumlah'] . '</p>
            <p class="text-primary font-semibold mt-1">Rp ' . number_format($total, 0, ',', '.') . '</p>
        </div>
    </div>
    ';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col md:flex-row justify-between gap-4 mt-8">
                        <a href="keranjang.php"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium text-center hover:bg-gray-300 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Keranjang
                        </a>
                        <button type="submit" name="checkout"
                            class="checkout-btn px-6 py-3 rounded-lg text-white font-semibold shadow-md flex items-center justify-center">
                            <i class="fas fa-lock mr-2"></i> Konfirmasi Pesanan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <h2 class="text-xl font-bold text-dark mb-6">Ringkasan Pesanan</h2>

                    <div class="space-y-3 mb-6">
                        <?php
                        include 'koneksi.php';
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }

                        // Cek login
                        if (!isset($_SESSION['user'])) {
                            header("Location: login.php");
                            exit;
                        }

                        // Jika keranjang kosong
                        if (empty($_SESSION['cart'])) {
                            echo "<script>alert('Keranjang masih kosong!'); window.location='toko.php';</script>";
                            exit;
                        }

                        $user_id = $_SESSION['user']['id_user'] ?? null;
                        if (!$user_id) {
                            echo "<script>alert('ID user tidak ditemukan di session!'); window.location='login.php';</script>";
                            exit;
                        }

                        // Hitung subtotal belanja
                        $grand_total = 0;
                        foreach ($_SESSION['cart'] as $item) {
                            $grand_total += $item['harga'] * $item['jumlah'];
                        }

                        // Hitung ongkir (gratis kalau subtotal >= 600.000)
                        $ongkir = ($grand_total >= 600000) ? 0 : 30000;

                        // Total bayar
                        $grandTotal = $grand_total + $ongkir;

                        // Proses checkout
                        if (isset($_POST['checkout'])) {
                            // Ambil data dari form
                            $nama       = mysqli_real_escape_string($koneksi, $_POST['nama']);
                            $telepon    = mysqli_real_escape_string($koneksi, $_POST['telepon']);
                            $alamat     = mysqli_real_escape_string($koneksi, $_POST['alamat']);
                            $kota       = mysqli_real_escape_string($koneksi, $_POST['kota']);
                            $provinsi   = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
                            $kode_pos   = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
                            $pembayaran = $_POST['metode_pembayaran'] ?? 'Transfer Bank';

                            // Simpan ke tabel orders
                            $query = "INSERT INTO `orders` 
              (user_id, nama_penerima, telepon, alamat, kota, provinsi, kode_pos, metode_pembayaran, ongkir, total, created_at) 
              VALUES 
              ('$user_id','$nama','$telepon','$alamat','$kota','$provinsi','$kode_pos','$pembayaran','$ongkir','$grandTotal',NOW())";
                            mysqli_query($koneksi, $query) or die(mysqli_error($koneksi));

                            $order_id = mysqli_insert_id($koneksi);

                            // Simpan item ke order_items
                            foreach ($_SESSION['cart'] as $item) {
                                $id_produk = $item['id'];
                                $jumlah    = (int) $item['jumlah'];
                                $harga     = (int) $item['harga'];

                                $query_item = "INSERT INTO order_items (order_id, product_id, jumlah, harga) 
                       VALUES ('$order_id', '$id_produk', '$jumlah', '$harga')";
                                mysqli_query($koneksi, $query_item) or die(mysqli_error($koneksi));
                            }

                            // Kosongkan keranjang
                            unset($_SESSION['cart']);

                            echo "<script>alert('Pesanan berhasil dibuat!'); window.location='riwayat.php';</script>";
                            exit;
                        }
                        ?>


                    </div>

                    <div class="space-y-3 border-t border-gray-200 pt-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-800 font-medium">Rp
                                <?php echo number_format($grand_total, 0, ',', '.'); ?>
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="text-gray-800 font-medium">
                                <?php echo $ongkir == 0 ? 'Gratis' : 'Rp ' . number_format($ongkir, 0, ',', '.'); ?>
                            </span>
                        </div>

                        <div class="flex justify-between text-lg font-bold mt-3 pt-3 border-t border-gray-200">
                            <span class="text-dark">Total Bayar</span>
                            <span class="text-primary">Rp <?php echo number_format($grandTotal, 0, ',', '.'); ?></span>
                        </div>
                    </div>


                    <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-800 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i> Informasi Penting
                        </h3>
                        <p class="text-sm text-blue-600 mt-2">Pesanan akan diproses setelah pembayaran diterima. Silakan
                            periksa kembali alamat pengiriman Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="copyright">
        <p>&copy; 2025 W-Store website.</p>
    </footer>

    <script>
        // Function to select payment method
        function selectPayment(element) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(method => {
                method.classList.remove('selected');
            });

            // Add selected class to clicked payment method
            element.classList.add('selected');
        }

        // Auto-fill form with user data if available
        document.addEventListener('DOMContentLoaded', function() {
            // This would typically come from your user database
            // For demo purposes, we're using placeholder values
            const userData = {
                telepon: '-',
                alamat: 'Jl. Contoh Alamat No. 123',
                kota: '-',
                provinsi: '-',
                kode_pos: '-'
            };

            // Fill form fields
            document.getElementById('telepon').value = userData.telepon;
            document.getElementById('alamat').value = userData.alamat;
            document.getElementById('kota').value = userData.kota;
            document.getElementById('provinsi').value = userData.provinsi;
            document.getElementById('kode_pos').value = userData.kode_pos;

            // Select first payment method by default
            document.querySelector('.payment-method').classList.add('selected');
        });
    </script>
</body>

</html>