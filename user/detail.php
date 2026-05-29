<?php
session_start();
include "../koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tempat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tempat WHERE id='$id' AND status='aktif'"));
if (!$tempat) { header("Location: katalog.php"); exit(); }

$tiket_list = mysqli_query($conn, "SELECT * FROM tiket WHERE id_tempat='$id' ORDER BY harga ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($tempat['nama']); ?> - Tiket</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .page-nav { display:flex; justify-content:space-between; align-items:center; padding:20px 50px; border-bottom:1px solid rgba(245,217,123,0.1); }
        .detail-wrap { max-width:900px; margin:40px auto; padding:0 30px; }
        .detail-img { width:100%; height:400px; object-fit:cover; border-radius:20px; margin-bottom:30px; }
        .detail-meta { display:flex; align-items:center; gap:16px; font-family:sans-serif; font-size:13px; color:#64748b; margin-bottom:16px; }
        .detail-desc { font-family:sans-serif; font-size:15px; color:#cbd5e1; line-height:1.8; margin-bottom:36px; }
        .tiket-section h2 { font-size:24px; margin-bottom:20px; }
        .tiket-list { display:flex; flex-direction:column; gap:14px; }
        .tiket-item { background:#2f5d5d; border:1px solid rgba(245,217,123,0.1); border-radius:16px; padding:20px 24px; display:flex; justify-content:space-between; align-items:center; }
        .tiket-info h4 { font-size:17px; color:#f5d97b; margin:0 0 4px; }
        .tiket-info p { font-family:sans-serif; font-size:13px; color:#94a3b8; margin:0; }
        .tiket-price { text-align:right; }
        .tiket-price .price { font-size:22px; font-weight:bold; color:#f5d97b; }
        .tiket-price small { font-family:sans-serif; font-size:12px; color:#64748b; display:block; }
        .beli-form { display:flex; align-items:center; gap:10px; margin-top:12px; }
        .beli-form input[type="number"] { width:70px; padding:9px 12px; border-radius:50px; border:1.5px solid rgba(245,217,123,0.2); background:rgba(255,255,255,0.06); color:#fff; font-family:sans-serif; font-size:14px; outline:none; text-align:center; }
        .beli-form input[type="date"] { padding:9px 16px; border-radius:50px; border:1.5px solid rgba(245,217,123,0.2); background:rgba(255,255,255,0.06); color:#fff; font-family:sans-serif; font-size:13px; outline:none; }
    </style>
</head>
<body>
<div class="page-nav">
    <a href="katalog.php" style="font-family:'Playfair Display',serif; font-size:16px; color:#f5d97b; text-decoration:none;">← Katalog</a>
    <div style="display:flex; gap:10px;">
        <?php if (isset($_SESSION['id'])) { ?>
            <a href="riwayat.php" class="btn-small">Tiket Saya</a>
        <?php } else { ?>
            <a href="../login.php" class="btn-small">Login</a>
        <?php } ?>
    </div>
</div>

<div class="detail-wrap">
    <img src="../image/<?php echo htmlspecialchars($tempat['gambar']); ?>" class="detail-img" alt="">
    <h1 style="font-size:38px; margin-bottom:12px;"><?php echo htmlspecialchars($tempat['nama']); ?></h1>
    <div class="detail-meta">
        <span><?php echo htmlspecialchars($tempat['lokasi']); ?></span>
    </div>
    <p class="detail-desc"><?php echo nl2br(htmlspecialchars($tempat['deskripsi'])); ?></p>

    <div class="tiket-section">
        <h2>Pilih Tiket</h2>
        <?php if (mysqli_num_rows($tiket_list) == 0) { ?>
            <p style="font-family:sans-serif; color:#94a3b8;">Belum ada tiket tersedia.</p>
        <?php } ?>
        <div class="tiket-list">
        <?php while ($t = mysqli_fetch_assoc($tiket_list)) { ?>
            <div class="tiket-item">
                <div class="tiket-info">
                    <h4><?php echo htmlspecialchars($t['jenis']); ?></h4>
                    <p><?php echo htmlspecialchars($t['deskripsi']); ?></p>
                    <p>Stok: <?php echo $t['stok']; ?> tiket</p>
                </div>
                <div class="tiket-price">
                    <div class="price">Rp <?php echo number_format($t['harga'], 0, ',', '.'); ?></div>
                    <small>/orang</small>
                    <?php if (isset($_SESSION['id']) && $_SESSION['role'] === 'user') { ?>
                        <form method="GET" action="checkout.php" class="beli-form">
                            <input type="hidden" name="id_tiket" value="<?php echo $t['id']; ?>">
                            <input type="date" name="tanggal" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            <input type="number" name="jumlah" value="1" min="1" max="<?php echo $t['stok']; ?>">
                            <button type="submit" name="login" class="btn-submit">Beli</button>
                        </form>
                    <?php } else if (!isset($_SESSION['id'])) { ?>
                        <a href="../login.php" class="btn-main" style="font-size:12px; padding:8px 18px; margin-top:10px;">Login untuk Beli</a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>
</div>
</body>
</html>
