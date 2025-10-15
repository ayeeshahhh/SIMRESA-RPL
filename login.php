<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'kasir') {
        header("Location: kasir1_dashboard.php"); // sesuaikan nama file dashboard kasir kamu
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ms">

<head>
  <meta charset="UTF-8">
  <title>Login Sidebar</title>
  <link rel="stylesheet" href="styless.css">
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
</head>

<body>
  <div class="sidebar">
    <div class="logo">Simresa</div>

    <h2>Login</h2>
    <form action="proses_login.php" method="POST">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Log In</button>
    </form>
  </div>

  <div class="main-content">
    <div class="background-image">
      <div class="overlay-text">
        <h1>Selamat Datang di Laman Kami!</h1>
        <p>Silakan Login Masuk</p>
      </div>
    </div>
  </div>
</body>

</html>