<?php 
include 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    echo "<script>alert('Keranjang kosong!'); window.location='toko.php';</script>";
    exit;
}

// Proses checkout
if (isset($_POST['checkout'])) {
    $user_id   = $_SESSION['user']['id_user'];
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $telepon   = mysqli_real_escape_string($koneksi, $_POST['telepon']);
    $alamat    = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kota      = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $provinsi  = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
    $kode_pos  = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $metode    = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran'] ?? 'Transfer Bank');

    // Hitung total
    $grand_total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $grand_total += $item['harga'] * $item['jumlah'];
    }
    $ongkir = ($grand_total >= 600000) ? 0 : 30000;
    $total_bayar = $grand_total + $ongkir;

    // Insert ke orders
    $query = "INSERT INTO orders (user_id, nama_penerima, telepon, alamat, kota, provinsi, kode_pos, metode_pembayaran, ongkir, total, created_at)
              VALUES ('$user_id','$nama','$telepon','$alamat','$kota','$provinsi','$kode_pos','$metode','$ongkir','$total_bayar',NOW())";
    mysqli_query($koneksi, $query) or die(mysqli_error($koneksi));
    $order_id = mysqli_insert_id($koneksi);

    // Insert ke order_items
    foreach ($_SESSION['cart'] as $item) {
        $id_produk = $item['id'];
        $jumlah    = (int)$item['jumlah'];
        $harga     = (int)$item['harga'];
        mysqli_query($koneksi, "INSERT INTO order_items (order_id, product_id, jumlah, harga)
                                VALUES ('$order_id','$id_produk','$jumlah','$harga')") or die(mysqli_error($koneksi));
    }

    // Kosongkan keranjang
    unset($_SESSION['cart']);

    echo "<script>alert('Pesanan berhasil dibuat!'); window.location='riwayat.php';</script>";
    exit;
}
?>
