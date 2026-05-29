<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') { header("Location: ../login.php"); exit(); }

$id_tiket = (int)($_GET['id_tiket'] ?? $_POST['id_tiket'] ?? 0);
$tanggal  = $_GET['tanggal']  ?? $_POST['tanggal']  ?? '';
$jumlah   = (int)($_GET['jumlah']  ?? $_POST['jumlah']  ?? 1);

$tiket  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tiket.*, tempat.nama as nama_tempat, tempat.gambar FROM tiket JOIN tempat ON tiket.id_tempat = tempat.id WHERE tiket.id='$id_tiket'"));
if (!$tiket) { header("Location: katalog.php"); exit(); }

$total = $tiket['harga'] * $jumlah;
$error = "";

if (isset($_POST['bayar'])) {
    $metode = $_POST['metode_bayar'];
    $metode_valid = ['transfer_bca','transfer_mandiri','transfer_bni','kartu_kredit','kartu_debit'];
    if (!in_array($metode, $metode_valid)) { $error = "Metode bayar tidak valid!"; }
    elseif ($jumlah < 1 || $jumlah > $tiket['stok']) { $error = "Jumlah tiket tidak valid!"; }
    elseif (empty($tanggal) || $tanggal < date('Y-m-d', strtotime('+1 day'))) { $error = "Tanggal kunjungan tidak valid!"; }
    else {
        $kode = 'MT-' . strtoupper(substr(uniqid(), -8));
        $id_user = (int)$_SESSION['id'];
        mysqli_query($conn, "INSERT INTO transaksi (kode_booking, id_user, id_tiket, tanggal_kunjungan, jumlah, total_harga, metode_bayar, status)
                             VALUES ('$kode','$id_user','$id_tiket','$tanggal','$jumlah','$total','$metode','lunas')");
        // Kurangi stok
        mysqli_query($conn, "UPDATE tiket SET stok=stok-$jumlah WHERE id='$id_tiket'");
        $trx_id = mysqli_insert_id($conn);
        header("Location: tiket.php?id=$trx_id");
        exit();
    }
}

$metode_list = [
    'transfer_bca'     => ['label' => 'Transfer BCA',     'icon' => '🏦', 'info' => 'No. Rek: 1234567890 a/n Museum Tiket'],
    'transfer_mandiri' => ['label' => 'Transfer Mandiri', 'icon' => '🏦', 'info' => 'No. Rek: 0987654321 a/n Museum Tiket'],
    'transfer_bni'     => ['label' => 'Transfer BNI',     'icon' => '🏦', 'info' => 'No. Rek: 1122334455 a/n Museum Tiket'],
    'kartu_kredit'     => ['label' => 'Kartu Kredit',     'icon' => '💳', 'info' => 'Visa / Mastercard'],
    'kartu_debit'      => ['label' => 'Kartu Debit',      'icon' => '💳', 'info' => 'Semua bank'],
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Museum Tiket</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-wrap { max-width: 800px; margin: 40px auto; padding: 0 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .checkout-title { max-width: 800px; margin: 40px auto 0; padding: 0 24px; }
        .box { background: #2f5d5d; border: 1px solid rgba(245,217,123,0.1); border-radius: 18px; padding: 24px 26px; }
        .box h3 { font-size: 18px; color: #f5d97b; margin: 0 0 18px; border-bottom: 1px solid rgba(245,217,123,0.1); padding-bottom: 12px; }
        .summary-row { display: flex; justify-content: space-between; font-family: sans-serif; font-size: 14px; color: #cbd5e1; margin-bottom: 10px; }
        .summary-row.total { font-size: 18px; color: #f5d97b; font-weight: bold; border-top: 1px solid rgba(245,217,123,0.15); padding-top: 12px; margin-top: 8px; }
        .metode-option { display: none; }
        .metode-label { display: flex; align-items: center; gap: 12px; padding: 13px 16px; border-radius: 12px; border: 1.5px solid rgba(245,217,123,0.15); margin-bottom: 10px; cursor: pointer; transition: all 0.2s; font-family: sans-serif; font-size: 14px; color: #cbd5e1; }
        .metode-label:hover { border-color: rgba(245,217,123,0.4); }
        .metode-option:checked + .metode-label { border-color: #f5d97b; background: rgba(245,217,123,0.08); color: #f5d97b; }
        .metode-info { font-size: 12px; color: #64748b; margin-left: auto; }
        .page-nav { display:flex; justify-content:space-between; align-items:center; padding:20px 50px; border-bottom:1px solid rgba(245,217,123,0.1); }
    </style>
</head>
<body>
<div class="page-nav">
    <a href="javascript:history.back()" style="font-family:'Playfair Display',serif; font-size:16px; color:#f5d97b; text-decoration:none;">← Kembali</a>
    <span style="font-family:sans-serif; font-size:13px; color:#94a3b8;">Checkout</span>
</div>

<div class="checkout-title">
    <h1 style="font-size:30px;">Konfirmasi Pemesanan</h1>
</div>

<?php if ($error) { ?><div class="alert alert-error" style="max-width:800px; margin:16px auto; padding:12px 24px;"><?php echo $error; ?></div><?php } ?>

<form method="POST">
    <input type="hidden" name="id_tiket" value="<?php echo $id_tiket; ?>">
    <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
    <input type="hidden" name="jumlah" value="<?php echo $jumlah; ?>">

    <div class="checkout-wrap">
        <!-- Ringkasan -->
        <div class="box">
            <h3>Ringkasan Pesanan</h3>
            <img src="../image/<?php echo htmlspecialchars($tiket['gambar']); ?>" style="width:100%; height:140px; object-fit:cover; border-radius:12px; margin-bottom:16px;">
            <div class="summary-row"><span>Tempat</span><strong style="color:#f5d97b;"><?php echo htmlspecialchars($tiket['nama_tempat']); ?></strong></div>
            <div class="summary-row"><span>Jenis Tiket</span><span><?php echo htmlspecialchars($tiket['jenis']); ?></span></div>
            <div class="summary-row"><span>Tanggal Kunjungan</span><span><?php echo date('d M Y', strtotime($tanggal)); ?></span></div>
            <div class="summary-row"><span>Jumlah</span><span><?php echo $jumlah; ?> tiket</span></div>
            <div class="summary-row"><span>Harga/tiket</span><span>Rp <?php echo number_format($tiket['harga'], 0, ',', '.'); ?></span></div>
            <div class="summary-row total"><span>Total</span><span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span></div>
        </div>

        <!-- Metode Bayar -->
        <div class="box">
            <h3>Metode Pembayaran</h3>
            <?php foreach ($metode_list as $key => $m) { ?>
                <input type="radio" name="metode_bayar" id="<?php echo $key; ?>" value="<?php echo $key; ?>" class="metode-option" required>
                <label for="<?php echo $key; ?>" class="metode-label">
                    <span style="font-size:18px;"><?php echo $m['icon']; ?></span>
                    <span><?php echo $m['label']; ?></span>
                    <span class="metode-info"><?php echo $m['info']; ?></span>
                </label>
            <?php } ?>
            <button type="submit" name="bayar" class="btn-submit" style="margin-top:20px;">Bayar Sekarang — Rp <?php echo number_format($total, 0, ',', '.'); ?></button>
        </div>
    </div>
</form>
</body>
</html>