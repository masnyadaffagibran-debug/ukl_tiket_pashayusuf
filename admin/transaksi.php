<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }

$filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$where  = $filter ? "AND transaksi.status='$filter'" : '';

$list = mysqli_query($conn, "
    SELECT transaksi.*, user.username, user.nama_lengkap, tiket.jenis, tempat.nama as nama_tempat
    FROM transaksi
    JOIN `user` ON transaksi.id_user = user.id
    JOIN tiket ON transaksi.id_tiket = tiket.id
    JOIN tempat ON tiket.id_tempat = tempat.id
    WHERE 1=1 $where
    ORDER BY transaksi.created_at DESC
");

$metode_label = [
    'transfer_bca'=>'BCA','transfer_mandiri'=>'Mandiri','transfer_bni'=>'BNI',
    'kartu_kredit'=>'Kartu Kredit','kartu_debit'=>'Kartu Debit'
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transaksi - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
    <style>
        .filter-tab { padding:8px 18px; border-radius:50px; font-family:sans-serif; font-size:13px; font-weight:600; text-decoration:none; border:1.5px solid rgba(245,217,123,0.2); color:#94a3b8; transition:all 0.2s; }
        .filter-tab:hover, .filter-tab.active { border-color:#f5d97b; color:#f5d97b; background:rgba(245,217,123,0.08); }
    </style>
</head>
<body>
<?php include "nafbar.php"; ?>
<div class="admin-wrap">
    <div class="admin-topbar"><div><h1>Semua Transaksi</h1><p class="topbar-sub">Riwayat seluruh pembelian tiket</p></div></div>

    <div style="display:flex; gap:8px; margin-bottom:20px;">
        <a href="transaksi.php" class="filter-tab <?php echo !$filter?'active':''; ?>">Semua</a>
        <a href="transaksi.php?status=lunas" class="filter-tab <?php echo $filter==='lunas'?'active':''; ?>">Lunas</a>
        <a href="transaksi.php?status=pending" class="filter-tab <?php echo $filter==='pending'?'active':''; ?>">Pending</a>
        <a href="transaksi.php?status=dibatalkan" class="filter-tab <?php echo $filter==='dibatalkan'?'active':''; ?>">Dibatalkan</a>
    </div>

    <div class="section">
        <div class="section-header"><h2>Daftar Transaksi</h2><span class="text-muted"><?php echo mysqli_num_rows($list); ?> data</span></div>
        <table>
            <thead><tr><th>Kode Booking</th><th>Pembeli</th><th>Tempat</th><th>Tiket</th><th>Tgl Kunjungan</th><th>Jumlah</th><th>Total</th><th>Metode</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (mysqli_num_rows($list)==0) { ?><tr class="empty-row"><td colspan="9">Belum ada transaksi.</td></tr><?php } ?>
            <?php while ($t = mysqli_fetch_assoc($list)) { ?>
                <tr>
                    <td><code style="color:#f5d97b; font-size:12px;"><?php echo $t['kode_booking']; ?></code></td>
                    <td>
                        <div style="font-weight:600;"><?php echo htmlspecialchars($t['nama_lengkap']); ?></div>
                        <div class="text-muted" style="font-size:12px;">@<?php echo htmlspecialchars($t['username']); ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($t['nama_tempat']); ?></td>
                    <td class="text-muted"><?php echo htmlspecialchars($t['jenis']); ?></td>
                    <td class="text-muted"><?php echo date('d M Y', strtotime($t['tanggal_kunjungan'])); ?></td>
                    <td><?php echo $t['jumlah']; ?></td>
                    <td><strong>Rp <?php echo number_format($t['total_harga'], 0, ',', '.'); ?></strong></td>
                    <td class="text-muted" style="font-size:12px;"><?php echo $metode_label[$t['metode_bayar']] ?? $t['metode_bayar']; ?></td>
                    <td><span class="badge <?php echo $t['status']==='lunas'?'badge-lunas':($t['status']==='pending'?'badge-pending':'badge-batal'); ?>"><?php echo strtoupper($t['status']); ?></span></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
