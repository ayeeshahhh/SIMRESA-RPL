<?php
session_start();
include 'koneksi.php'; // koneksi ke database

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if ($password == $user["password"]) {

        // Cek role, hanya admin & kasir yang boleh
        if ($user['role'] === 'admin' || $user['role'] === 'kasir') {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: kasir_dashboard.php");
            }
            exit();
        } else {
            echo "Anda tidak memiliki akses ke sistem ini.";
        }

    } else {
        echo "Password salah.";
    }
} else {
    echo "Email tidak ditemukan.";
}
?>