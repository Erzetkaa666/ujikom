<?php
include 'koneksi.php';

$pesan='';

if(isset($_POST['kirim'])){
    $username = $_POST['username'];

    $cek = mysqli_query($koneksi,"SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($cek)>0){
        $pesan="Permintaan reset password telah dikirim ke admin.";
    } else {
        $pesan="Username tidak ditemukan.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh">
<div class="container col-md-4">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">Lupa Password</div>
        <div class="card-body">
            <?php if($pesan): ?>
                <div class="alert alert-info"><?= $pesan ?></div>
            <?php endif; ?>
            <form method="post">
                <input name="username" class="form-control mb-3" placeholder="Masukkan Username" required>
                <button name="kirim" class="btn btn-primary w-100">Kirim Permintaan</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>