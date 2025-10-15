<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kasir') {
    header("Location: login.php");
    exit();
}
?>

<h1>Dashboard Kasir</h1>
<p>Selamat datang, <?= $_SESSION['nama'] ?></p>
<a href="logout.php">Logout</a>