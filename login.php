<?php
session_start();
include "koneksi.php";
$error = "";

if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM `user` WHERE username='$u'");
    $user   = mysqli_fetch_assoc($result);

    if ($user && password_verify($p, $user['password'])) {
        $_SESSION['id']       = $user['id'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama']     = $user['nama_lengkap'];
        if ($user['role'] === 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Museum Tiket</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user/style.css">
</head>
<body>
<div class="wrapper">
    <div class="login-box">
        <h1>Login</h1>
        <p class="login-tagline">Museum Tiket — Wisata Budaya</p>
        <?php if (isset($_GET['daftar'])) { ?>
            <p style="color:#5dcaa5; font-family:sans-serif; font-size:13px; margin-bottom:12px;">Akun berhasil dibuat! Silakan login.</p>
        <?php } ?>
        <?php if ($error) { ?>
            <p style="color:#ff7675; font-family:sans-serif; font-size:13px; margin-bottom:12px;"><?php echo $error; ?></p>
        <?php } ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn-submit">Masuk</button>
        </form>
        <div class="form-footer">Belum punya akun? <a href="daftar.php">Daftar sekarang</a></div>
    </div>
</div>
</body>
</html>