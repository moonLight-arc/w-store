<?php
include 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek login dan level admin
if (!isset($_SESSION['user']) || $_SESSION['user']['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

/* ==================== CRUD PRODUK ==================== */

// Tambah Data Produk
if (isset($_POST['tambah'])) {
    $filename = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    $folder = "images/" . $filename;

    move_uploaded_file($tmp_name, $folder);

    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);

    mysqli_query($koneksi, "INSERT INTO produk (gambar, deskripsi, harga, kategori) 
        VALUES ('$filename', '$deskripsi', '$harga', '$kategori')");
    header("Location: dashboard.php?kategori=$kategori");
    exit;
}

// Edit Data Produk
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);

    if (!empty($_FILES['gambar']['name'])) {
        $filename = $_FILES['gambar']['name'];
        $tmp_name = $_FILES['gambar']['tmp_name'];
        $folder = "images/" . $filename;
        move_uploaded_file($tmp_name, $folder);

        mysqli_query($koneksi, "UPDATE produk 
            SET gambar='$filename', deskripsi='$deskripsi', harga='$harga', kategori='$kategori' 
            WHERE id_produk='$id'");
    } else {
        mysqli_query($koneksi, "UPDATE produk 
            SET deskripsi='$deskripsi', harga='$harga', kategori='$kategori' 
            WHERE id_produk='$id'");
    }

    header("Location: dashboard.php?kategori=$kategori");
    exit;
}

// Hapus Data Produk
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk='$id'");
    header("Location: dashboard.php");
    exit;
}

/* ==================== CRUD PESANAN ==================== */

// Update status pesanan
if (isset($_POST['update_status'])) {
    $order_id = mysqli_real_escape_string($koneksi, $_POST['order_id']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    mysqli_query($koneksi, "UPDATE orders SET status='$status' WHERE id='$order_id'");
    header("Location: dashboard.php?menu=pesanan");
    exit;
}

// Hapus pesanan
if (isset($_GET['hapus_pesanan'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus_pesanan']);
    mysqli_query($koneksi, "DELETE FROM orders WHERE id='$id'");
    header("Location: dashboard.php?menu=pesanan");
    exit;
}

// Pilihan menu (produk/pesanan)
$menu = isset($_GET['menu']) ? $_GET['menu'] : 'produk';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'all';

// Hitung jumlah produk per kategori
$countLuxury = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk WHERE kategori='Luxury'"))['total'];
$countSport  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk WHERE kategori='Sport'"))['total'];
$countClassic = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk WHERE kategori='Classic'"))['total'];
$countAll    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; min-height: 100vh; }
        .sidebar { width: 220px; background: #343a40; color: #fff; flex-shrink: 0; }
        .sidebar a { color: #ddd; display: block; padding: 12px 20px; text-decoration: none; }
        .sidebar a.active, .sidebar a:hover { background: #495057; color: #fff; }
        .content { flex-grow: 1; padding: 20px; background: #f8f9fa; }
        /* small fix so modal content not influence table layout */
        .modal { z-index: 2000; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="p-3">Kategori</h4>
    <a href="dashboard.php?menu=produk&kategori=all" class="<?= ($menu=='produk' && $kategori=='all')?'active':'' ?>">Semua Produk</a>
    <hr class="bg-light">
    <a href="dashboard.php?menu=produk&kategori=Luxury" class="<?= ($menu=='produk' && $kategori=='Luxury')?'active':'' ?>">Luxury</a>
    <a href="dashboard.php?menu=produk&kategori=Sport" class="<?= ($menu=='produk' && $kategori=='Sport')?'active':'' ?>">Sport</a>
    <a href="dashboard.php?menu=produk&kategori=Classic" class="<?= ($menu=='produk' && $kategori=='Classic')?'active':'' ?>">Classic</a>
    <hr class="bg-light">
    <a href="dashboard.php?menu=pesanan" class="<?= $menu=='pesanan'?'active':'' ?>">Pesanan</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Content -->
<div class="content">
    <?php if ($menu == 'pesanan'): ?>
        <h2 class="mb-4">Daftar Pesanan Pelanggan</h2>

        <?php
        // ambil pesanan
        $pesanan = mysqli_query($koneksi, "SELECT * FROM orders ORDER BY created_at DESC");
        $modals = ''; // menyimpan html modal agar diletakkan di luar tabel
        ?>

        <?php if (mysqli_num_rows($pesanan) > 0): ?>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID Pesanan</th>
                        <th>User ID</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = mysqli_fetch_assoc($pesanan)): 
                        // gunakan null coalescing agar tidak memicu warning
                        $status_p = $p['status'] ?? 'Pending';
                        $order_id = (int)$p['id'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($order_id) ?></td>
                            <td><?= htmlspecialchars($p['user_id']) ?></td>
                            <td>Rp <?= number_format($p['total'],0,',','.') ?></td>
                            <td>
                                <form method="POST" class="d-flex">
                                    <input type="hidden" name="order_id" value="<?= $order_id ?>">
                                    <select name="status" class="form-select form-select-sm me-2">
                                        <option <?= $status_p=='Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option <?= $status_p=='Diproses' ? 'selected' : '' ?>>Diproses</option>
                                        <option <?= $status_p=='Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                        <option <?= $status_p=='Selesai' ? 'selected' : '' ?>>Selesai</option>
                                        <option <?= $status_p=='Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
                                </form>
                            </td>
                            <td><?= htmlspecialchars($p['created_at']) ?></td>
                            <td>
                                <a href="dashboard.php?menu=pesanan&hapus_pesanan=<?= $order_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus pesanan ini?')">Hapus</a>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $order_id ?>">Detail</button>
                            </td>
                        </tr>

                        <?php
                        // buat modal untuk order ini (disimpan terpisah agar diletakkan di luar tabel)
                        $items_q = mysqli_query($koneksi, "
                            SELECT oi.*, p.deskripsi 
                            FROM order_items oi 
                            LEFT JOIN produk p ON oi.product_id = p.id_produk 
                            WHERE oi.order_id = '{$order_id}'
                        ");

                        $modal_html = '<div class="modal fade" id="detailModal'.$order_id.'" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Pesanan #'.htmlspecialchars($order_id).'</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Produk</th>
                                                    <th>Jumlah</th>
                                                    <th>Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                        while ($item = mysqli_fetch_assoc($items_q)) {
                            $modal_html .= '<tr>
                                <td>'.htmlspecialchars($item['deskripsi'] ?? 'Produk dihapus').'</td>
                                <td>'.(int)$item['jumlah'].'</td>
                                <td>Rp '.number_format($item['harga'],0,',','.').'</td>
                            </tr>';
                        }
                        $modal_html .= '</tbody></table></div></div></div></div>';

                        $modals .= $modal_html;
                        ?>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Tampilkan semua modal di sini (setelah tabel) -->
            <?= $modals ?>

        <?php else: ?>
            <div class="alert alert-info">Belum ada pesanan.</div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Bagian produk (tampilan lama tetap) -->
        <h2 class="mb-4">Dashboard Admin - <?= $kategori=='all' ? 'Semua Produk' : htmlspecialchars($kategori) ?></h2>

        <?php if ($kategori == 'all') : ?>
            <!-- Statistik produk -->
            <div class="row">
                <div class="col-md-3"><div class="card text-center p-3 shadow-sm"><h5>Total Produk</h5><p class="fs-3 fw-bold text-dark"><?= $countAll ?></p></div></div>
                <div class="col-md-3"><div class="card text-center p-3 shadow-sm"><h5>Luxury</h5><p class="fs-3 fw-bold text-primary"><?= $countLuxury ?></p></div></div>
                <div class="col-md-3"><div class="card text-center p-3 shadow-sm"><h5>Sport</h5><p class="fs-3 fw-bold text-success"><?= $countSport ?></p></div></div>
                <div class="col-md-3"><div class="card text-center p-3 shadow-sm"><h5>Classic</h5><p class="fs-3 fw-bold text-danger"><?= $countClassic ?></p></div></div>
            </div>
        <?php else: ?>
            <!-- CRUD produk -->
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Produk</button>
            <table class="table table-bordered table-striped">
                <thead class="table-dark"><tr><th>Gambar</th><th>Deskripsi</th><th>Harga</th><th>Kategori</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php
                    $data = mysqli_query($koneksi, "SELECT * FROM produk WHERE kategori='".mysqli_real_escape_string($koneksi,$kategori)."'");
                    while ($row = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><img src="images/<?= htmlspecialchars($row['gambar']) ?>" width="70" height="80"></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td><?= htmlspecialchars($row['harga']) ?></td>
                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_produk'] ?>">Edit</button>
                                <a href="dashboard.php?hapus=<?= $row['id_produk'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                        <!-- Modal Edit Produk -->
                        <div class="modal fade" id="editModal<?= $row['id_produk'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-header"><h5 class="modal-title">Edit Produk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id_produk'] ?>">
                                            <div class="mb-3"><label class="form-label">Gambar</label><br><img src="images/<?= htmlspecialchars($row['gambar']) ?>" width="80" class="mb-2"><br><input type="file" class="form-control" name="gambar"></div>
                                            <div class="mb-3"><label class="form-label">Deskripsi</label><input type="text" class="form-control" name="deskripsi" value="<?= htmlspecialchars($row['deskripsi']) ?>" required></div>
                                            <div class="mb-3"><label class="form-label">Harga</label><input type="text" class="form-control" name="harga" value="<?= htmlspecialchars($row['harga']) ?>" required></div>
                                            <div class="mb-3"><label class="form-label">Kategori</label>
                                                <select class="form-control" name="kategori" required>
                                                    <option <?= $row['kategori']=='Luxury'?'selected':'' ?>>Luxury</option>
                                                    <option <?= $row['kategori']=='Sport'?'selected':'' ?>>Sport</option>
                                                    <option <?= $row['kategori']=='Classic'?'selected':'' ?>>Classic</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer"><button type="submit" name="edit" class="btn btn-primary">Simpan</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header"><h5 class="modal-title">Tambah produk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Gambar</label><input type="file" class="form-control" name="gambar" required></div>
                    <div class="mb-3"><label class="form-label">Deskripsi</label><input type="text" class="form-control" name="deskripsi" required></div>
                    <div class="mb-3"><label class="form-label">Harga</label><input type="text" class="form-control" name="harga" required></div>
                    <div class="mb-3"><label class="form-label">Kategori</label>
                        <select class="form-control" name="kategori" required>
                            <option>Luxury</option>
                            <option>Sport</option>
                            <option>Classic</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" name="tambah" class="btn btn-success">Tambah</button></div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
