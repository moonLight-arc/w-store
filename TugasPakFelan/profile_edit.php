<?php

include 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Sesuaikan dengan nama kolom primary key
$user_id = $_SESSION['user']['id_user'];

$query = $koneksi->query("SELECT * FROM user WHERE id_user = '$user_id'");
$user = $query->fetch_assoc();

// Update profil
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    $update = $koneksi->query("UPDATE user SET nama='$nama', email='$email', password='$password' WHERE id_user='$user_id'");
    if ($update) {
        $_SESSION['user']['nama'] = $nama;
        $_SESSION['user']['email'] = $email;
        $success = "Profil berhasil diperbarui!";
    } else {
        $error = "Gagal memperbarui profil.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profil - W-Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: color(white );
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card-custom {
      border: none;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    }
    .card-header-custom {
      background: linear-gradient(90deg, #ff6600, #ffffffff);
      color: #fff;
      padding: 20px;
    }
    .card-header-custom h4 {
      margin: 0;
      font-weight: 700;
      letter-spacing: 1px;
    }
    .btn-orange {
      background-color: #ff6600;
      color: #fff;
      border-radius: 50px;
      transition: 0.3s ease;
      padding: 10px 25px;
    }
    .btn-orange:hover {
      background-color: #e65c00;
      transform: scale(1.05);
      color: #fff;
    }
    .btn-dark {
      border-radius: 50px;
      padding: 10px 25px;
      transition: 0.3s ease;
    }
    .btn-dark:hover {
      transform: scale(1.05);
    }
    .form-floating > label {
      color: #6c757d;
    }
    .form-control:focus {
      border-color: #ff6600;
      box-shadow: 0 0 0 0.25rem rgba(255,102,0,.25);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="col-md-6 mx-auto">
      <div class="card card-custom">
        <div class="card-header-custom d-flex align-items-center">
          <i class="fas fa-user-edit me-2"></i>
          <h4>Edit Profil</h4>
        </div>
        <div class="card-body p-4 bg-white">
          <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= $success; ?></div>
          <?php endif; ?>
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
          <?php endif; ?>

          <form method="post">
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" required>
              <label for="nama">Nama Lengkap</label>
            </div>
            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
              <label for="email">Email</label>
            </div>
            <div class="form-floating mb-4">
              <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak diubah">
              <label for="password">Password Baru</label>
            </div>
            <div class="d-flex justify-content-between">
              <a href="index.php" class="btn btn-dark">Kembali</a>
              <button type="submit" name="update" class="btn btn-orange">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
