<?php
$koneksi = mysqli_connect("localhost", "root", "", "perpust");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>

