<?php
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

// Ambil data keranjang dari session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - W-Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <div class="container py-5">
    <div class="row">
      <!-- Form Checkout -->
      <div class="col-md-7">
        <div class="card shadow-lg border-0 rounded-4 mb-4">
          <div class="card-body">
            <h4 class="mb-4">Informasi Pembeli</h4>
            <form action="proses_co.php" method="POST">
              <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
              </div>
              <div class="mb-3">
                <label for="alamat" class="form-label">Alamat Lengkap</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
              </div>
              <div class="mb-3">
                <label for="telepon" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control" id="telepon" name="telepon" required>
              </div>
              <div class="mb-3">
                <label for="metode" class="form-label">Metode Pembayaran</label>
                <select class="form-select" id="metode" name="metode" required>
                  <option value="">-- Pilih Metode --</option>
                  <option value="transfer">Transfer Bank</option>
                  <option value="cod">Bayar di Tempat (COD)</option>
                  <option value="ewallet">E-Wallet (Dana, OVO, Gopay)</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
              </div>
              <button type="submit" class="btn btn-success w-100 py-2 rounded-pill">Konfirmasi & Bayar</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Ringkasan Pesanan -->
      <div class="col-md-5">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body">
            <h4 class="mb-4">Ringkasan Pesanan</h4>
            <?php
            $total = 0;
            if (!empty($cart)) {
              foreach ($cart as $id => $item) {
                $subtotal = $item['harga'] * $item['jumlah'];
                $total += $subtotal;
                echo "
                  <div class='d-flex justify-content-between align-items-center mb-2'>
                      <div>
                          <strong>{$item['nama']}</strong> <br>
                          <small>{$item['jumlah']} x Rp " . number_format($item['harga'], 0, ',', '.') . "</small>
                      </div>
                      <span>Rp " . number_format($subtotal, 0, ',', '.') . "</span>
                  </div>
                  <hr>";
              }
              echo "<h5 class='d-flex justify-content-between'>
                      <span>Total:</span> 
                      <span class='text-success fw-bold'>Rp " . number_format($total, 0, ',', '.') . "</span>
                    </h5>";
            } else {
              echo "<p>Keranjang belanja masih kosong.</p>";
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>