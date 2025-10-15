<?php
$host = "localhost";        // 
$user = "root";            // default XAMPP/MariaDB biasanya: root
$pass = "";                // 
$db = "restoran_db";     // database name

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>