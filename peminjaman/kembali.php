<?php
include '../koneksi.php';

$id=$_GET['id'];
$data=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM peminjaman WHERE id_pinjam='$id'"));

$tgl_kembali=$data['tgl_kembali'];
$hari_ini=date('Y-m-d');

$selisih=(strtotime($hari_ini)-strtotime($tgl_kembali))/(60*60*24);
$denda=$selisih>0?$selisih*1000:0;

mysqli_query($conn,"UPDATE peminjaman SET status='Kembali' WHERE id_pinjam='$id'");
mysqli_query($conn,"UPDATE buku SET stok=stok+1 WHERE id_buku='$data[id_buku]'");

echo "Buku dikembalikan. Denda: Rp.$denda";
?>