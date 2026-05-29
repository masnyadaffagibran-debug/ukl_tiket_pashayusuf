<?php
include "koneksi.php";
$error = "";
if (isset($_POST['daftar'])) {
    $u    = mysqli_real_escape_string($conn, $_POST['username']);
    $p    = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $cek = mysqli_query($conn, "SELECT id FROM `user` WHERE username='$u'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah dipakai!";
    } else {
        mysqli_query($conn, "INSERT INTO `user` (username, password, nama_lengkap, email, role)
                             VALUES ('$u','$p','$nama','$email','user')");
        header("Location: login.php?daftar=sukses");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar - Museum Tiket</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user/style.css">
</head>
<body>
<div class="wrapper">
    <div class="login-box" style="width:420px;">
        <h1>Daftar Akun</h1>
        <p class="login-tagline">Buat akun untuk mulai pesan tiket</p>
        <?php if ($error) { ?>
            <p style="color:#ff7675; font-family:sans-serif; font-size:13px; margin-bottom:12px;"><?php echo $error; ?></p>
        <?php } ?>
        <form method="POST">
            <input type="text" name="nama_lengkap" placeholder="Nama lengkap" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="daftar" class="btn-submit">Buat Akun</button>
        </form>
        <div class="form-footer">Sudah punya akun? <a href="login.php">Login di sini</a></div>
    </div>
</div>
</body>
</html>