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

$user_id = $_SESSION['user']['id_user'];

// Jika ada request batal pesanan
if (isset($_GET['batal'])) {
    $id = (int) $_GET['batal'];
    // hanya update status kalau pesanan milik user dan masih pending/diproses
    $cek = mysqli_query($koneksi, "SELECT * FROM orders WHERE id='$id' AND user_id='$user_id' AND status IN ('Pending','Diproses')");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($koneksi, "UPDATE orders SET status='Dibatalkan' WHERE id='$id'");
        echo "<script>alert('Pesanan berhasil dibatalkan!'); window.location='riwayat.php';</script>";
        exit;
    } else {
        echo "<script>alert('Pesanan tidak bisa dibatalkan!'); window.location='riwayat.php';</script>";
        exit;
    }
}

// Ambil data pesanan user
$query = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

<h2 class="mb-4">Riwayat Pesanan</h2>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID Pesanan</th>
            <th>Total</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <?php if ($row['status'] == 'Pending' || $row['status'] == 'Diproses') : ?>
                        <a href="riwayat.php?batal=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin batalkan pesanan ini?')">Batalkan</a>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="index.php" class="btn btn-primary">Kembali Dashboard</a>
<a href="toko.php" class="btn btn-primary">Belanja Lagi</a>

</body>
</html>
