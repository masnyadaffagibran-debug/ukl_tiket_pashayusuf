<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }

$total_user    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM `user` WHERE role='user'"))['t'];
$total_tempat  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tempat WHERE status='aktif'"))['t'];
$total_trx     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM transaksi"))['t'];
$total_lunas   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM transaksi WHERE status='lunas'"))['t'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as t FROM transaksi WHERE status='lunas'"))['t'];

$recent_trx = mysqli_query($conn, "
    SELECT transaksi.*, user.username, tiket.jenis, tempat.nama as nama_tempat
    FROM transaksi
    JOIN `user` ON transaksi.id_user = user.id
    JOIN tiket ON transaksi.id_tiket = tiket.id
    JOIN tempat ON tiket.id_tempat = tempat.id
    ORDER BY transaksi.created_at DESC LIMIT 8
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Museum Tiket Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include "nafbar.php"; ?>
<div class="admin-wrap">
    <div class="admin-topbar">
        <div>
            <h1>Dashboard</h1>
            <p class="topbar-sub">Selamat datang, <strong>@<?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-card"><div class="stat-label">Pengguna</div><div class="stat-number"><?php echo $total_user; ?></div><a href="#" class="stat-link">Terdaftar</a></div>
        <div class="stat-card"><div class="stat-label">Tempat Aktif</div><div class="stat-number"><?php echo $total_tempat; ?></div><a href="tempat.php" class="stat-link">Kelola →</a></div>
        <div class="stat-card"><div class="stat-label">Total Transaksi</div><div class="stat-number"><?php echo $total_trx; ?></div><a href="transaksi.php" class="stat-link">Lihat →</a></div>
        <div class="stat-card"><div class="stat-label">Pembelian Tiket</div><div class="stat-number"><?php echo $total_lunas; ?></div><a href="transaksi.php?status=lunas" class="stat-link">Lihat →</a></div>
        <div class="stat-card"><div class="stat-label">Total Pendapatan</div><div class="stat-number" style="font-size:22px;">Rp <?php echo number_format($total_revenue ?? 0, 0, ',', '.'); ?></div><a href="transaksi.php" class="stat-link">Lihat →</a></div>
    </div>

    <div class="section">
        <div class="section-header"><h2>Transaksi Terbaru</h2><a href="transaksi.php" class="link-muted">Lihat semua →</a></div>
        <table>
            <thead><tr><th>Kode Booking</th><th>Pembeli</th><th>Tempat</th><th>Jenis Tiket</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
            <?php if (mysqli_num_rows($recent_trx) == 0) { ?>
                <tr class="empty-row"><td colspan="7">Belum ada transaksi.</td></tr>
            <?php } ?>
            <?php while ($t = mysqli_fetch_assoc($recent_trx)) { ?>
                <tr>
                    <td><code style="color:#f5d97b; font-size:13px;"><?php echo $t['kode_booking']; ?></code></td>
                    <td class="text-muted">@<?php echo htmlspecialchars($t['username']); ?></td>
                    <td><?php echo htmlspecialchars($t['nama_tempat']); ?></td>
                    <td class="text-muted"><?php echo htmlspecialchars($t['jenis']); ?></td>
                    <td>Rp <?php echo number_format($t['total_harga'], 0, ',', '.'); ?></td>
                    <td><span class="badge <?php echo $t['status']==='lunas'?'badge-lunas':($t['status']==='pending'?'badge-pending':'badge-batal'); ?>"><?php echo strtoupper($t['status']); ?></span></td>
                    <td class="text-muted" style="font-size:12px;"><?php echo date('d M Y', strtotime($t['created_at'])); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>