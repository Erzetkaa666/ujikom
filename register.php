<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['login'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_hp    = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek username
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = 'Username sudah digunakan!';
    } else {
        // Generate id_siswa otomatis (1001, 1002, ...)
        $ambil = mysqli_fetch_row(mysqli_query($koneksi, "SELECT MAX(id_siswa) FROM users"))[0];
        $id_siswa_baru = $ambil ? $ambil + 1 : 1001;

        mysqli_query($koneksi, "INSERT INTO users 
            (id_siswa, username, password, role, nama, alamat, no_hp) 
            VALUES 
            ('$id_siswa_baru', '$username', '$password', 'siswa', '$nama', '$alamat', '$no_hp')
        ");

        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar sama seperti dashboard -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <span class="navbar-brand fw-bold">
            <i class="bi bi-book me-2"></i>Perpustakaan Digital
        </span>
    </div>
</nav>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 85vh;">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus me-2"></i>Daftar Siswa Baru
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

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat</label>
                        <input type="text" name="alamat" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">No HP</label>
                        <input type="text" name="no_hp" class="form-control">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" name="register" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle me-2"></i>Daftar
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="login.php">Sudah punya akun? Login</a>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>