<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../../login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

if (isset($_POST['pinjam'])) {
    $id_buku = (int) $_POST['id_buku'];
    $tgl_kembali = mysqli_real_escape_string($koneksi, $_POST['tgl_kembali']);
    $tgl_pinjam = date('Y-m-d');

    // Validasi tanggal kembali minimal +7 hari
    if (strtotime($tgl_kembali) < strtotime('+7 days')) {
        $error = "❌ Tanggal kembali minimal 7 hari dari sekarang!";
    } else {

        mysqli_begin_transaction($koneksi);

        try {
            // Cek buku & stok TERKINI (lock row)
            $buku = mysqli_fetch_assoc(mysqli_query($koneksi, 
                "SELECT stok FROM buku WHERE id_buku='$id_buku' FOR UPDATE"
            ));

            if (!$buku) {
                throw new Exception("Buku tidak ditemukan.");
            }

            if ($buku['stok'] <= 0) {
                throw new Exception("Stok buku habis.");
            }

            // Cek apakah siswa masih meminjam buku ini
            $cek_pinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "
                SELECT *
                FROM peminjaman 
                WHERE id_user='$id_user' 
                AND id_buku='$id_buku' 
                AND status='dipinjam'
            "));

            if ($cek_pinjam) {
                throw new Exception("Kamu masih meminjam buku ini dan belum mengembalikannya.");
            }

            // Insert peminjaman
            mysqli_query($koneksi, "
            INSERT INTO peminjaman 
            (id_user, id_buku, tgl_pinjam, tgl_kembali, status)
            VALUES
            ('$id_user', '$id_buku', CURDATE(), '$tgl_kembali', 'dipinjam')
            ");

            // Kurangi stok
            mysqli_query($koneksi, "
                UPDATE buku SET stok = stok - 1 
                WHERE id_buku='$id_buku'
            ");

            mysqli_commit($koneksi);
            $success = "✅ Buku berhasil dipinjam!";

        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $error = "❌ " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pinjam Buku - Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../siswa_dashboard.php">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5><i class="bi bi-plus-circle me-2"></i>Pinjam Buku Baru</h5>
            </div>
            <div class="card-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Buku:</label>
                        <select name="id_buku" class="form-select" id="buku_select" required>
                            <option value="">-- Cari Buku --</option>
                            <?php
                            $bukus = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0 ORDER BY judul");
                            while ($b = mysqli_fetch_assoc($bukus)):
                            ?>
                                <option value="<?= $b['id_buku'] ?>" data-stok="<?= $b['stok'] ?>">
                                    <?= htmlspecialchars($b['judul']) ?> - <?= htmlspecialchars($b['pengarang']) ?> (Stok: <?= $b['stok'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tenggat Kembali:</label>
                        <input type="date" name="tgl_kembali" class="form-control" required min="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                    </div>
                    
                    <button type="submit" name="pinjam" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-2"></i>Konfirmasi Pinjam
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('buku_select').addEventListener('change', function() {
            const stok = this.selectedOptions[0]?.dataset.stok;
            if (stok <= 0) {
                this.value = '';
                alert('Stok habis! Pilih buku lain.');
            }
        });
    </script>
</body>
</html>