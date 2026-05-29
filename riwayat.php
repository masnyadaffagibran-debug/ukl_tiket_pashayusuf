<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') { header("Location: ../login.php"); exit(); }

// Batalkan tiket
if (isset($_GET['batal'])) {
    $id_trx = (int)$_GET['batal'];
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi WHERE id='$id_trx' AND id_user='" . (int)$_SESSION['id'] . "' AND status='lunas'"));
    if ($cek && $cek['tanggal_kunjungan'] > date('Y-m-d')) {
        mysqli_query($conn, "UPDATE transaksi SET status='dibatalkan' WHERE id='$id_trx'");
        mysqli_query($conn, "UPDATE tiket SET stok=stok+{$cek['jumlah']} WHERE id='{$cek['id_tiket']}'");
    }
    header("Location: riwayat.php");
    exit();
}

$filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$where  = $filter ? "AND transaksi.status='$filter'" : '';

$trx_list = mysqli_query($conn, "
    SELECT transaksi.*, tiket.jenis, tempat.nama as nama_tempat, tempat.gambar
    FROM transaksi
    JOIN tiket ON transaksi.id_tiket = tiket.id
    JOIN tempat ON tiket.id_tempat = tempat.id
    WHERE transaksi.id_user='" . (int)$_SESSION['id'] . "' $where
    ORDER BY transaksi.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tiket Saya - Museum Tiket</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .page-nav { display:flex; justify-content:space-between; align-items:center; padding:20px 50px; border-bottom:1px solid rgba(245,217,123,0.1); }
        .riwayat-wrap { max-width: 900px; margin: 40px auto; padding: 0 30px; }
        .filter-tabs { display:flex; gap:8px; margin-bottom:24px; }
        .filter-tab { padding:8px 20px; border-radius:50px; font-family:sans-serif; font-size:13px; font-weight:600; text-decoration:none; border:1.5px solid rgba(245,217,123,0.2); color:#94a3b8; transition:all 0.2s; }
        .filter-tab:hover, .filter-tab.active { border-color:#f5d97b; color:#f5d97b; background:rgba(245,217,123,0.08); }
        .trx-card { background:#2f5d5d; border:1px solid rgba(245,217,123,0.1); border-radius:16px; overflow:hidden; display:flex; margin-bottom:16px; transition:transform 0.15s; }
        .trx-card:hover { transform:translateY(-2px); }
        .trx-img { width:140px; flex-shrink:0; object-fit:cover; }
        .trx-body { padding:20px 22px; flex:1; display:flex; justify-content:space-between; align-items:center; }
        .trx-info h4 { font-size:17px; color:#f5d97b; margin:0 0 6px; }
        .trx-info p { font-family:sans-serif; font-size:13px; color:#94a3b8; margin:0 0 4px; }
        .trx-kode { font-family:monospace; font-size:13px; color:#64748b; }
        .trx-right { text-align:right; }
        .trx-total { font-size:20px; font-weight:bold; color:#f5d97b; margin-bottom:8px; }
        .trx-actions { display:flex; gap:8px; justify-content:flex-end; margin-top:10px; }
    </style>
</head>
<body>
<div class="page-nav">
    <a href="../index.php" style="font-family:'Playfair Display',serif; font-size:16px; color:#f5d97b; text-decoration:none;">← Museum Tiket</a>
    <div style="display:flex; gap:10px;">
        <a href="katalog.php" class="btn-small">Katalog</a>
        <a href="../profile.php" class="btn-small">@<?php echo htmlspecialchars($_SESSION['username']); ?></a>
    </div>
</div>

<div class="riwayat-wrap">
    <h1 style="font-size:32px; margin-bottom:8px;">Tiket Saya</h1>
    <p style="font-family:sans-serif; font-size:14px; color:#94a3b8; margin-bottom:24px;">Riwayat pembelian tiket kamu</p>

    <div class="filter-tabs">
        <a href="riwayat.php" class="filter-tab <?php echo !$filter ? 'active' : ''; ?>">Semua</a>
        <a href="riwayat.php?status=lunas" class="filter-tab <?php echo $filter==='lunas' ? 'active' : ''; ?>">Lunas</a>
        <a href="riwayat.php?status=dibatalkan" class="filter-tab <?php echo $filter==='dibatalkan' ? 'active' : ''; ?>">Dibatalkan</a>
    </div>

    <?php if (mysqli_num_rows($trx_list) == 0) { ?>
        <div style="text-align:center; padding:60px 0;">
            <p style="font-family:sans-serif; font-size:16px; color:#94a3b8;">Belum ada tiket.</p>
            <a href="katalog.php" class="btn-main" style="margin-top:16px; display:inline-flex;">Beli Tiket Sekarang</a>
        </div>
    <?php } ?>

    <?php while ($trx = mysqli_fetch_assoc($trx_list)) { ?>
        <div class="trx-card">
            <img src="../image/<?php echo htmlspecialchars($trx['gambar']); ?>" class="trx-img" alt="">
            <div class="trx-body">
                <div class="trx-info">
                    <h4><?php echo htmlspecialchars($trx['nama_tempat']); ?></h4>
                    <p><?php echo htmlspecialchars($trx['jenis']); ?> · <?php echo $trx['jumlah']; ?> tiket</p>
                    <p><?php echo date('d M Y', strtotime($trx['tanggal_kunjungan'])); ?></p>
                    <div class="trx-kode"><?php echo $trx['kode_booking']; ?></div>
                </div>
                <div class="trx-right">
                    <div class="trx-total">Rp <?php echo number_format($trx['total_harga'], 0, ',', '.'); ?></div>
                    <span class="badge <?php echo $trx['status']==='lunas'?'badge-lunas':($trx['status']==='pending'?'badge-pending':'badge-batal'); ?>">
                        <?php echo strtoupper($trx['status']); ?>
                    </span>
                    <div class="trx-actions">
                        <a href="tiket.php?id=<?php echo $trx['id']; ?>" class="btn-small" style="font-size:12px; padding:7px 16px;">Detail</a>
                        <?php if ($trx['status'] === 'lunas' && $trx['tanggal_kunjungan'] > date('Y-m-d')) { ?>
                            <a href="riwayat.php?batal=<?php echo $trx['id']; ?>" class="btn-danger" style="font-size:12px; padding:7px 16px;"
                               onclick="return confirm('Batalkan tiket ini?')">Batalkan</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
</body>
</html>