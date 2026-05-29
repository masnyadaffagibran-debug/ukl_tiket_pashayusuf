<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id'])) { header("Location: login.php"); exit(); }

if (isset($_POST['update'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $hp    = mysqli_real_escape_string($conn, $_POST['no_hp']);
    mysqli_query($conn, "UPDATE `user` SET nama_lengkap='$nama', email='$email', no_hp='$hp' WHERE id='" . (int)$_SESSION['id'] . "'");
    $sukses = "Profil berhasil diperbarui!";
}

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `user` WHERE id='" . (int)$_SESSION['id'] . "'"));
$total_tiket = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM transaksi WHERE id_user='" . (int)$_SESSION['id'] . "' AND status='lunas'"))['t'];
$total_spent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as t FROM transaksi WHERE id_user='" . (int)$_SESSION['id'] . "' AND status='lunas'"))['t'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profil - Museum Tiket</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user/style.css">
    <style>
        .profile-wrap { max-width: 700px; margin: 50px auto; padding: 0 20px; }
        .profile-header { background: #2f5d5d; border: 1px solid rgba(245,217,123,0.1); border-radius: 20px; padding: 32px; margin-bottom: 24px; text-align: center; }
        .profile-header h1 { font-size: 30px; margin: 0 0 4px; }
        .profile-header p { font-family: sans-serif; font-size: 14px; color: #94a3b8; margin: 0 0 20px; }
        .stats-mini { display: flex; justify-content: center; gap: 40px; margin-top: 20px; }
        .stats-mini div { text-align: center; }
        .stats-mini span { font-size: 28px; font-weight: bold; color: #f5d97b; display: block; }
        .stats-mini small { font-family: sans-serif; font-size: 12px; color: #64748b; }
        .form-section { background: #2f5d5d; border: 1px solid rgba(245,217,123,0.1); border-radius: 20px; padding: 28px 32px; }
        .form-section h2 { font-size: 20px; color: #f5d97b; margin: 0 0 20px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
        .form-group label { font-family: sans-serif; font-size: 12px; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; }
        .form-group input { padding: 12px 18px; border-radius: 50px; border: 1.5px solid rgba(245,217,123,0.2); background: rgba(255,255,255,0.06); color: #fff; font-family: sans-serif; font-size: 14px; outline: none; }
        .form-group input:focus { border-color: rgba(245,217,123,0.5); }
        .nav-user { display: flex; justify-content: space-between; align-items: center; padding: 16px 50px; border-bottom: 1px solid rgba(245,217,123,0.1); margin-bottom: 0; }
    </style>
</head>
<body>

<div class="nav-user">
    <a href="index.php" style="font-family:'Playfair Display',serif; font-size:18px; color:#f5d97b; text-decoration:none; font-weight:bold;">← Museum Tiket</a>
    <div style="display:flex; gap:10px;">
        <a href="user/riwayat.php" class="btn-small">Tiket Saya</a>
        <a href="logout.php" class="btn-small" style="border-color:rgba(255,118,117,0.3); color:#ff7675;" onclick="return confirm('Yakin ingin logout?')">Logout</a>
    </div>
</div>

<div class="profile-wrap">
    <?php if (isset($sukses)) { ?><div style="background:rgba(93,202,165,0.15); color:#5dcaa5; border:1px solid rgba(93,202,165,0.25); border-radius:12px; padding:12px 18px; margin-bottom:16px; font-family:sans-serif; font-size:14px;"><?php echo $sukses; ?></div><?php } ?>

    <div class="profile-header">
        <h1><?php echo htmlspecialchars($data['nama_lengkap'] ?: $data['username']); ?></h1>
        <p>@<?php echo htmlspecialchars($data['username']); ?> · <?php echo htmlspecialchars($data['email']); ?></p>
        <div class="stats-mini">
            <div><span><?php echo $total_tiket; ?></span><small>Tiket Dibeli</small></div>
            <div><span>Rp <?php echo number_format($total_spent ?? 0, 0, ',', '.'); ?></span><small>Total Transaksi</small></div>
        </div>
    </div>

    <div class="form-section">
        <h2>Edit Profil</h2>
        <form method="POST">
            <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($data['nama_lengkap']); ?>" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required></div>
            <div class="form-group"><label>No. HP</label><input type="text" name="no_hp" value="<?php echo htmlspecialchars($data['no_hp']); ?>"></div>
            <button type="submit" name="update" class="btn-submit">Simpan Perubahan</button>
        </form>
    </div>
</div>
</body>
</html>