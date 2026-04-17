<?php
session_start();
include 'koneksi.php';

// ANTI CACHE
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$success = '';
$error = '';

// Ambil data lama
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT * FROM users WHERE id_user='$id_user'
"));

// Update profil
if (isset($_POST['simpan'])) {
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_hp  = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

    mysqli_query($koneksi, "
        UPDATE users 
        SET nama='$nama', alamat='$alamat', no_hp='$no_hp'
        WHERE id_user='$id_user'
    ");

    $success = "Profil berhasil diperbarui.";
}

// Ganti password
if (isset($_POST['ganti_password'])) {
    $pass1 = $_POST['password_baru'];
    $pass2 = $_POST['konfirmasi_password'];

    if ($pass1 !== $pass2) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        mysqli_query($koneksi, "
            UPDATE users SET password='$hash' WHERE id_user='$id_user'
        ");
        $success = "Password berhasil diganti.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Profil Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <a href="siswa_dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

    <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">Edit Profil</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>Username</label>
                    <input class="form-control" value="<?= $user['username'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label>Nama</label>
                    <input name="nama" class="form-control" value="<?= $user['nama'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>Alamat</label>
                    <input name="alamat" class="form-control" value="<?= $user['alamat'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>No HP</label>
                    <input name="no_hp" class="form-control" value="<?= $user['no_hp'] ?>">
                </div>
                <button name="simpan" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-warning">Ganti Password</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>Password Baru</label>
                    <input type="password" name="password_baru" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="konfirmasi_password" class="form-control" required>
                </div>
                <button name="ganti_password" class="btn btn-warning">Ganti Password</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>