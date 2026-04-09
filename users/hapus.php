<?php
include '../koneksi.php';
$id=$_GET['id'];
mysqli_query($conn,"DELETE FROM anggota WHERE id_anggota='$id'");
header("location:anggota.php");