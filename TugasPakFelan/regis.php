<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Registrasi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #ff6a00, #ffffff, #ff9e00, #ffffff);
            background-size: 400% 400%;
            animation: gradientAnimation 12s ease infinite;
            min-height: 100vh;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .register-card {
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .register-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .register-header {
            background: linear-gradient(135deg, #ff6a00, #ff9e00, #ffd000);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .form-control {
            border-radius: 12px;
        }

        .btn-primary {
            border-radius: 12px;
            background: linear-gradient(135deg, #ff6a00, #ff9e00);
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #e65c00, #ff8800);
            transform: translateY(-2px);
        }

        a {
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg register-card">
                <div class="register-header">
                    <h2 class="fw-bold">Pendaftaran</h2>
                    <p class="mb-0">Silakan isi data Anda</p>
                </div>
                <div class="card-body p-4">

                    <?php
                    if (isset($_POST['registrasi'])) {
                        $nama = $_POST['nama'];
                        $password = md5($_POST['password']);
                        $email = $_POST['email'];
                        $no_hp = $_POST['no_hp'];
                        $level = $_POST['level'];

                        $insert = mysqli_query($koneksi, "INSERT INTO user (nama, password, email, no_hp, level) VALUES ('$nama', '$password', '$email', '$no_hp', '$level')");

                        if ($insert) {
                            echo '<script>alert("Registrasi berhasil, silakan login!"); location.href="login.php";</script>';
                        } else {
                            echo "<div class='alert alert-danger'>Registrasi gagal, coba lagi!</div>";
                        }
                    }
                    ?>

                    <!-- Form Registrasi -->
                    <form method="POST">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">Nomor HP</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="level" class="form-label">Level</label>
                            <select name="level" class="form-control">
                                <option value="pelanggan">Pelanggan</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="registrasi" value="registrasi" class="btn btn-primary py-2 fw-semibold">Daftar</button>
                        </div>
                        <br>
                        <p class="text-center">
                            Sudah punya akun? <a href="login.php" class="text-primary fw-semibold">Login di sini</a>
                        </p>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
