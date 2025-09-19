<?php
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// // Jika keranjang kosong
// if (empty($_SESSION['cart'])) {
//     echo "<script>alert('Keranjang masih kosong!'); window.location='toko.php';</script>";
//     exit;
// }

// Ambil ID user dari session
$user_id = $_SESSION['user']['id_user'] ?? null;
if (!$user_id) {
    echo "<script>alert('ID user tidak ditemukan!'); window.location='login.php';</script>";
    exit;
}

// Proses checkout ketika tombol ditekan
if (isset($_POST['checkout'])) {
    $grand_total = 0;

    foreach ($_SESSION['cart'] as $item) {
        $grand_total += $item['harga'] * $item['jumlah'];
    }

    // Simpan order utama
    $query = "INSERT INTO `orders` (user_id, total, created_at) VALUES ('$user_id', '$grand_total', NOW())";
    mysqli_query($koneksi, $query);
    $order_id = mysqli_insert_id($koneksi);

    // Simpan detail order
    foreach ($_SESSION['cart'] as $item) {
        $id_produk = $item['id'];
        $jumlah = (int) $item['jumlah'];
        $harga = (int) $item['harga'];

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
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

    <h2 class="mb-4">Checkout</h2>
    <p>Silakan periksa kembali pesanan Anda:</p>dx

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $grand_total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total = $item['harga'] * $item['jumlah'];
                $grand_total += $total;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['nama']); ?></td>
                    <td>Rp <?= number_format($item['harga'], 0, ',', '.'); ?></td>
                    <td><?= $item['jumlah']; ?></td>
                    <td>Rp <?= number_format($total, 0, ',', '.'); ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                <td><strong>Rp <?= number_format($grand_total, 0, ',', '.'); ?></strong></td>
            </tr>
        </tbody>
    </table>

    <form method="post" class="mt-3">
        <button type="submit" name="checkout" class="btn btn-success">✅ Konfirmasi Pesanan</button>
        <a href="toko.php" class="btn btn-secondary">⬅ Kembali Belanja</a>
    </form>

</body>
</html>
