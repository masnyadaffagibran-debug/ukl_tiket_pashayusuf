<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['id'])) { header("Location: ../login.php"); exit(); }

$id = (int)($_GET['id'] ?? 0);
$trx = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT transaksi.*, tiket.jenis, tiket.harga, tempat.nama as nama_tempat, tempat.lokasi, tempat.gambar
    FROM transaksi
    JOIN tiket ON transaksi.id_tiket = tiket.id
    JOIN tempat ON tiket.id_tempat = tempat.id
    WHERE transaksi.id='$id' AND transaksi.id_user='" . (int)$_SESSION['id'] . "'
"));
if (!$trx) { header("Location: riwayat.php"); exit(); }

$metode_label = [
    'transfer_bca' => 'Transfer BCA', 'transfer_mandiri' => 'Transfer Mandiri',
    'transfer_bni' => 'Transfer BNI', 'kartu_kredit' => 'Kartu Kredit', 'kartu_debit' => 'Kartu Debit'
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tiket <?php echo $trx['kode_booking']; ?> - Museum Tiket</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .tiket-wrap { max-width: 540px; margin: 50px auto; padding: 0 20px; }
        .tiket-card { background: #2f5d5d; border: 1px solid rgba(245,217,123,0.15); border-radius: 24px; overflow: hidden; }
        .tiket-card-img { width: 100%; height: 200px; object-fit: cover; }
        .tiket-card-body { padding: 28px 30px; }
        .kode-booking { text-align: center; background: rgba(245,217,123,0.1); border: 2px dashed rgba(245,217,123,0.3); border-radius: 14px; padding: 20px; margin-bottom: 24px; }
        .kode-booking .kode { font-size: 32px; font-weight: bold; color: #f5d97b; letter-spacing: 3px; }
        .kode-booking small { font-family: sans-serif; font-size: 12px; color: #94a3b8; display: block; margin-top: 4px; }
        .tiket-detail-row { display: flex; justify-content: space-between; font-family: sans-serif; font-size: 14px; padding: 10px 0; border-bottom: 1px solid rgba(245,217,123,0.06); }
        .tiket-detail-row:last-child { border-bottom: none; }
        .tiket-detail-row span:first-child { color: #64748b; }
        .tiket-detail-row span:last-child { color: #fff; font-weight: 600; }
        .page-nav { display:flex; justify-content:space-between; align-items:center; padding:20px 50px; border-bottom:1px solid rgba(245,217,123,0.1); }
    </style>
</head>
<body>
<div class="page-nav">
    <a href="riwayat.php" style="font-family:'Playfair Display',serif; font-size:16px; color:#f5d97b; text-decoration:none;">← Tiket Saya</a>
    <span class="badge <?php echo $trx['status'] === 'lunas' ? 'badge-lunas' : ($trx['status'] === 'pending' ? 'badge-pending' : 'badge-batal'); ?>"><?php echo strtoupper($trx['status']); ?></span>
</div>

<div class="tiket-wrap">
    <div class="tiket-card">
        <img src="../image/<?php echo htmlspecialchars($trx['gambar']); ?>" class="tiket-card-img" alt="">
        <div class="tiket-card-body">
            <h2 style="font-size:22px; margin-bottom:4px;"><?php echo htmlspecialchars($trx['nama_tempat']); ?></h2>
            <p style="font-family:sans-serif; font-size:13px; color:#64748b; margin-bottom:20px;"><?php echo htmlspecialchars($trx['lokasi']); ?></p>

            <div class="kode-booking">
                <div class="kode"><?php echo $trx['kode_booking']; ?></div>
                <small>Kode Booking — tunjukkan saat masuk</small>
            </div>

            <div class="tiket-detail-row"><span>Jenis Tiket</span><span><?php echo htmlspecialchars($trx['jenis']); ?></span></div>
            <div class="tiket-detail-row"><span>Tanggal Kunjungan</span><span><?php echo date('d M Y', strtotime($trx['tanggal_kunjungan'])); ?></span></div>
            <div class="tiket-detail-row"><span>Jumlah</span><span><?php echo $trx['jumlah']; ?> tiket</span></div>
            <div class="tiket-detail-row"><span>Metode Bayar</span><span><?php echo $metode_label[$trx['metode_bayar']]; ?></span></div>
            <div class="tiket-detail-row"><span>Total Bayar</span><span style="color:#f5d97b;">Rp <?php echo number_format($trx['total_harga'], 0, ',', '.'); ?></span></div>
            <div class="tiket-detail-row"><span>Tanggal Beli</span><span><?php echo date('d M Y H:i', strtotime($trx['created_at'])); ?></span></div>

            <div style="display:flex; gap:10px; margin-top:20px;">
                <a href="riwayat.php" class="btn-outline" style="flex:1; text-align:center; padding:11px;">Riwayat</a>
                <a href="../index.php" class="btn-main" style="flex:1; text-align:center; padding:11px;">Beranda</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>