<?php
include 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan cart ada meski kosong
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Hapus produk dari keranjang
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart_deleted'] = true;
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: keranjang.php");
    exit;
}

// Update jumlah produk + simpan pilihan checkbox
if (isset($_POST['update'])) {
    $pilih = isset($_POST['pilih']) ? $_POST['pilih'] : [];
    foreach ($_SESSION['cart'] as &$item) {
        $id = $item['id'];

        // update jumlah
        if (isset($_POST['jumlah'][$id])) {
            $jumlahBaru = (int) $_POST['jumlah'][$id];
            if ($jumlahBaru > 0) {
                $item['jumlah'] = $jumlahBaru;
            }
        }

        // simpan status checkbox
        $item['checked'] = in_array($id, $pilih);
    }
    unset($item);
    header("Location: keranjang.php");
    exit;
}

// Hitung total belanja berdasarkan checkbox
$total_belanja = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (!isset($item['checked']) || $item['checked']) { // default checked
            $total_belanja += $item['harga'] * $item['jumlah'];
        }
    }
}

// Hitung ongkos kirim (contoh: gratis jika total > 500000)
$ongkos_kirim = ($total_belanja > 500000) ? 0 : 30000;
$total_pembayaran = $total_belanja + $ongkos_kirim;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Toko Online</title>
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

        .cart-item {
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .notification {
            transform: translateX(100%);
            transition: transform 0.5s ease;
        }

        .notification.show {
            transform: translateX(0);
        }

        .quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .checkout-btn {
            background: linear-gradient(to right, #ff3d00, #ff6d39);
            transition: all 0.3s ease;
        }

        .checkout-btn:hover {
            background: linear-gradient(to right, #e53600, #ff5c29);
            box-shadow: 0 4px 12px rgba(255, 61, 0, 0.25);
        }

        .discount-badge {
            background: linear-gradient(to right, #ff3d00, #ff6d39);
        }

        .progress-bar {
            height: 6px;
            background-color: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(to right, #ff3d00, #ff6d39);
            border-radius: 3px;
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- Notification -->
    <?php if (isset($_SESSION['cart_deleted']) && $_SESSION['cart_deleted']): ?>
        <div class="fixed top-4 right-4 z-50 notification bg-white rounded-lg shadow-lg p-4 max-w-sm border-l-4 border-primary show"
            id="notification">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-primary text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-gray-800 font-medium">Produk berhasil dihapus dari keranjang</p>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['cart_deleted']); ?>
    <?php endif; ?>

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
    <br>
    <br>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Cart Items -->
            <div class="md:w-2/3">
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-dark">Keranjang Belanja</h1>
                        <span class="text-gray-500"><?php echo count($_SESSION['cart']); ?> barang</span>
                    </div>

                    <!-- Free Shipping Progress -->
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <div class=" rounded-lg p-4 mb-6">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-shipping-fast text-primary mr-2"></i>
                                <?php if ($total_belanja < 500000): ?>
                                    <span class="text-sm font-medium">Tambahkan Rp
                                        <?php echo number_format(500000 - $total_belanja, 0, ',', '.'); ?> lagi untuk gratis
                                        ongkir</span>
                                <?php else: ?>
                                    <span class="text-sm font-medium">Selamat! Anda mendapatkan gratis ongkos kirim</span>
                                <?php endif; ?>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill"
                                    style="width: <?php echo min(($total_belanja / 500000) * 100, 100); ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Cart Items -->
                    <form method="post">
                        <div class="space-y-4">
                            <?php
                            if (!empty($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $subtotal = $item['harga'] * $item['jumlah'];
                                    ?>
                                    <!-- Item -->
                                    <div class="cart-item bg-white border border-gray-200 rounded-xl p-4 flex items-center">
                                        <div class="flex items-center mr-4">
                                            <input type="checkbox" name="pilih[]" value="<?php echo $item['id']; ?>"
                                                class="h-5 w-5 text-primary rounded" <?php echo (!isset($item['checked']) || $item['checked']) ? 'checked' : ''; ?>>

                                        </div>

                                        <img src="images/<?php echo htmlspecialchars($item['gambar']); ?>"
                                            alt="<?php echo htmlspecialchars($item['nama']); ?>"
                                            class="w-20 h-20 object-contain rounded-lg">
                                        <div class="ml-4 flex-grow">
                                            <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($item['nama']); ?>
                                            </h3>
                                            <p class="text-primary font-semibold mt-1">Rp
                                                <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                                            </p>
                                            <?php if (isset($item['diskon']) && $item['diskon'] > 0): ?>
                                                <div class="flex items-center mt-1">
                                                    <span class="discount-badge text-xs text-white px-2 py-1 rounded-full">DISKON
                                                        <?php echo $item['diskon']; ?>%</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <div class="flex items-center border border-gray-300 rounded-lg">
                                                <button type="button"
                                                    class="quantity-btn w-8 h-8 flex items-center justify-center text-gray-600 <?php echo $item['jumlah'] <= 1 ? 'disabled' : ''; ?>"
                                                    onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['jumlah'] - 1; ?>)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" name="jumlah[<?php echo $item['id']; ?>]"
                                                    value="<?php echo $item['jumlah']; ?>" min="1"
                                                    class="w-12 h-8 text-center border-x border-gray-300"
                                                    onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                                                <button type="button"
                                                    class="quantity-btn w-8 h-8 flex items-center justify-center text-gray-600"
                                                    onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['jumlah'] + 1; ?>)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <a href="keranjang.php?hapus=<?php echo $item['id']; ?>"
                                                class="mt-4 text-red-500 text-sm flex items-center">
                                                <i class="fas fa-trash-alt mr-1"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <!-- Keranjang Kosong -->
                                <div class="text-center py-12">
                                    <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
                                    <h3 class="text-xl font-medium text-gray-500">Keranjang belanja Anda kosong</h3>
                                    <p class="text-gray-400 mt-2">Silakan tambahkan produk ke keranjang belanja Anda</p>
                                    <a href="toko.php"
                                        class="inline-block mt-6 px-6 py-2 bg-primary text-white rounded-lg hover:bg-orange-600">
                                        <i class="fas fa-arrow-left mr-2"></i>Lanjut Belanja
                                    </a>
                                </div>
                            <?php } ?>
                        </div>

                        <?php if (!empty($_SESSION['cart'])): ?>
                            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                                <div class="flex items-center">
                                    <input type="checkbox" class="h-5 w-5 text-primary rounded mr-2" id="select-all"
                                        checked>
                                    <label for="select-all" class="text-gray-700">Pilih Semua</label>
                                </div>
                                <button type="submit" name="update"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    <i class="fas fa-sync-alt mr-2"></i>Perbarui Keranjang
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <?php if (!empty($_SESSION['cart'])): ?>
                <div class="md:w-1/3 mt-6 md:mt-0">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                        <h2 class="text-xl font-bold text-dark mb-4">Ringkasan Belanja</h2>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Harga (<?php echo count($_SESSION['cart']); ?>
                                    barang)</span>
                                <span class="text-gray-800 font-medium">Rp
                                    <?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Biaya Pengiriman</span>
                                <span
                                    class="<?php echo $ongkos_kirim == 0 ? 'text-green-600' : 'text-gray-800'; ?> font-medium">
                                    <?php echo $ongkos_kirim == 0 ? 'Gratis' : 'Rp ' . number_format($ongkos_kirim, 0, ',', '.'); ?>
                                </span>
                            </div>

                            <div class="border-t border-gray-200 pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-bold text-dark">Total Pembayaran</span>
                                    <span class="text-lg font-bold text-primary">Rp
                                        <?php echo number_format($total_pembayaran, 0, ',', '.'); ?></span>
                                </div>
                            </div>
                        </div>

                        <a href="checkout.php"
                            class="checkout-btn w-full py-3 rounded-lg text-white font-semibold mt-6 shadow-md flex items-center justify-center">
                            <i class="fas fa-shopping-bag mr-2"></i> Beli Sekarang
                        </a>

                        <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-800 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i> Tips Pembayaran Aman
                            </h3>
                            <p class="text-sm text-blue-600 mt-2">Gunakan metode pembayaran yang tersedia di platform kami
                                untuk transaksi yang aman dan terjamin.</p>
                        </div>

                        <div class="mt-6 flex items-center justify-between text-sm text-gray-500">
                            <span>Diperbarui pada: <?php echo date('d M Y'); ?></span>
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt mr-2"></i>
                                <span>Aman & Terjamin</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="copyright">
        <p>&copy; 2025 W-Store website.</p>
    </footer>

    <script>
        // Notification animation
        document.addEventListener('DOMContentLoaded', function () {
            const notification = document.getElementById('notification');
            if (notification) {
                // Hide notification after 3 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 3000);
            }

            // Select all functionality
            const selectAll = document.getElementById('select-all');
            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#select-all)');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = selectAll.checked;
                    });
                });
            }
        });

        // Function to update quantity
        function updateQuantity(productId, newQuantity) {
            if (newQuantity < 1) newQuantity = 1;

            // Create a form and submit it to update the quantity
            const form = document.createElement('form');
            form.method = 'post';
            form.action = 'keranjang.php';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'jumlah[' + productId + ']';
            input.value = newQuantity;
            form.appendChild(input);

            const updateInput = document.createElement('input');
            updateInput.type = 'hidden';
            updateInput.name = 'update';
            updateInput.value = '1';
            form.appendChild(updateInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>

</html>