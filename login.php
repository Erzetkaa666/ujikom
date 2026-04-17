<?php
session_start();
include 'koneksi.php';

// Anti back setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Jika sudah login
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: dashboard.php');
    } else {
        header('Location: siswa_dashboard.php');
    }
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {

        // CEK WAJIB GANTI PASSWORD
        if ($user['wajib_ganti_password'] == 1) {
            $_SESSION['id_user'] = $user['id_user'];
            header("Location: ganti_password.php");
            exit;
        }

        $_SESSION['login'] = true;
        $_SESSION['role'] = $user['role'];
        $_SESSION['id_user'] = $user['id_user'];

        if ($user['role'] == 'admin') {
            header('Location: dashboard.php');
        } else {
            header('Location: siswa_dashboard.php');
        }
        exit;

    } else {
        $error = 'Username / Password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow">
    <div class="container">
        <span class="navbar-brand fw-bold">
            <i class="bi bi-book me-2"></i>Perpustakaan Digital
        </span>
    </div>
</nav>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 85vh;">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h5 class="mb-0">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login Akun
                </h5>
            </div>
            <div class="card-body p-4">

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="text-center mt-2 mb-3">
                        <a href="lupa_password.php">Lupa Password?</a>
                    </div>

                    <button name="login" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="register.php">Belum punya akun? Daftar</a>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>