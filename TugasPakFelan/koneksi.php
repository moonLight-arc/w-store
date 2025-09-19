<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

 $koneksi = mysqli_connect("localhost", "root", "", "w_store");
?>

