<?php
session_start();
include "../koneksi.php";

$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$where  = $search ? "AND (nama LIKE '%$search%' OR lokasi LIKE '%$search%')" : '';
$tempat = mysqli_query($conn, "SELECT * FROM tempat WHERE status='aktif' $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Katalog Tempat - Tiket</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .page-nav { display:flex; justify-content:space-between; align-items:center; padding:20px 50px; border-bottom:1px solid rgba(245,217,123,0.1); }
        .page-nav-brand { font-family:'Playfair Display',serif; font-size:18px; font-weight:bold; color:#f5d97b; text-decoration:none; }
        .katalog-wrap { max-width:1200px; margin:0 auto; padding:40px 50px; }
        .katalog-header { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:32px; }
        .search-form { display:flex; gap:10px; }
        .search-form input { padding:11px 20px; border-radius:50px; border:1.5px solid rgba(245,217,123,0.2); background:rgba(255,255,255,0.07); color:#fff; font-family:sans-serif; font-size:14px; outline:none; width:260px; }
        .search-form input::placeholder { color:#64748b; }
        .search-form input:focus { border-color:rgba(245,217,123,0.5); }
        .search-form button { padding:11px 22px; border-radius:50px; background:#e5c76b; border:none; color:#1e4d4d; font-family:'Playfair Display',serif; font-weight:bold; font-size:14px; cursor:pointer; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:24px; }
        .card { background:#2f5d5d; border:1px solid rgba(245,217,123,0.1); border-radius:18px; overflow:hidden; transition:transform 0.2s; }
        .card:hover { transform:translateY(-4px); }
        .card img { width:100%; height:200px; object-fit:cover; display:block; }
        .card-body { padding:18px 20px 22px; }
        .card-body h3 { font-size:18px; color:#f5d97b; margin:0 0 6px; }
        .card-lokasi { display:flex; align-items:center; gap:5px; font-family:sans-serif; font-size:12px; color:#64748b; margin-bottom:10px; }
        .card-body p { font-family:sans-serif; font-size:13px; color:#94a3b8; margin:0 0 16px; line-height:1.6; }
        .harga-mulai { font-family:sans-serif; font-size:12px; color:#94a3b8; margin-bottom:14px; }
        .harga-mulai strong { color:#f5d97b; font-size:16px; }
    </style>
</head>
<body>

<div class="page-nav">
    <a href="../index.php" class="page-nav-brand">← Tiket</a>
    <div style="display:flex; gap:10px;">
        <?php if (isset($_SESSION['id'])) { ?>
            <a href="riwayat.php" class="btn-small">Tiket Saya</a>
            <a href="../profile.php" class="btn-small">@<?php echo htmlspecialchars($_SESSION['username']); ?></a>
        <?php } else { ?>
            <a href="../login.php" class="btn-small">Login</a>
        <?php } ?>
    </div>
</div>

<div class="katalog-wrap">
    <div class="katalog-header">
        <div>
            <h1 style="font-size:34px; margin-bottom:6px;">daftar Tempat</h1>
            <p style="font-family:sans-serif; font-size:14px; color:#94a3b8;">Temukan wisata budaya yang kamu inginkan</p>
        </div>
        <form class="search-form" method="GET">
            <input type="text" name="q" placeholder="Cari tempat atau lokasi..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Cari</button>
        </form>
    </div>

    <div class="grid">
        <?php if (mysqli_num_rows($tempat) == 0) { ?>
            <p style="color:#94a3b8; font-family:sans-serif;">Tidak ada tempat ditemukan.</p>
        <?php } ?>
        <?php while ($t = mysqli_fetch_assoc($tempat)) {
            $harga_min = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MIN(harga) as h FROM tiket WHERE id_tempat='{$t['id']}'"));
        ?>
            <div class="card">
                <img src="../image/<?php echo htmlspecialchars($t['gambar']); ?>" alt="">
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($t['nama']); ?></h3>
                    <div class="card-lokasi"><?php echo htmlspecialchars($t['lokasi']); ?></div>
                    <p><?php echo htmlspecialchars(substr($t['deskripsi'], 0, 90)) . '...'; ?></p>
                    <div class="harga-mulai">Mulai dari <strong>Rp <?php echo number_format($harga_min['h'] ?? 0, 0, ',', '.'); ?></strong></div>
                    <a href="detail.php?id=<?php echo $t['id']; ?>" class="btn-main" style="font-size:13px; padding:9px 22px;">Lihat & Beli Tiket</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
