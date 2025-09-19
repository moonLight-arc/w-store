<?php
include 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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

        .login-card {
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .login-header {
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
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card shadow-lg login-card">
                <div class="login-header">
                    <h2 class="fw-bold">Selamat Datang</h2>
                    <p class="mb-0">Silakan login untuk melanjutkan</p>
                </div>
                <div class="card-body p-4">

                    <?php
                    if (isset($_POST['login'])) {
                        $nama     = $_POST['nama'];
                        $password = md5($_POST['password']);

                        $data = mysqli_query($koneksi, "SELECT * FROM user WHERE nama='$nama' AND password='$password'");
                        $cek  = mysqli_num_rows($data);

                        if ($cek > 0) {
                            $user = mysqli_fetch_assoc($data);
                            $_SESSION['user'] = $user;

                            // Cek level
                            if ($user['level'] == 'admin') {
                                echo '<script>alert("Selamat datang Admin!"); location.href="dashboard.php";</script>';
                            } else {
                                echo '<script>alert("Selamat datang Pengguna!"); location.href="index.php";</script>';
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Username atau Password salah</div>";
                        }
                    }
                    ?>

                    <!-- Form login -->
                    <form method="POST">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama anda" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                            </div>
                        </div>
                        <p class="text-center mb-3">
                            Belum punya akun? <a href="regis.php" class="text-primary fw-semibold">Daftar di sini</a>
                        </p>
                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-primary py-2 fw-semibold">Login</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
