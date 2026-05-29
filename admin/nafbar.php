<?php
$current = basename($_SERVER['PHP_SELF']);
$nav = [
    'index.php'      => 'Dashboard',
    'tempat.php'     => 'Tempat wisata',
    'tiket.php'      => 'Kelola Tiket',
    'transaksi.php'  => 'Transaksi',
    'pengumuman.php' => 'Pengumuman',
];
?>
<nav class="admin-nav">
    <div class="nav-brand">Tiket <span>Admin Panel</span></div>
    <div class="nav-links">
        <?php foreach ($nav as $file => $label) { ?>
            <a href="<?php echo $file; ?>" class="nav-link <?php echo $current===$file?'active':''; ?>">
                <?php echo $label; ?>
            </a>
        <?php } ?>
    </div>
    <div style="margin-top:auto; display:flex; flex-direction:column; gap:4px;">
        <a href="../index.php" class="nav-link" style="opacity:0.6; font-size:13px;">Ke Website</a>
        <a href="../logout.php" class="nav-link" style="color:#ff7675;" onclick="return confirm('Yakin ingin logout?')">Logout</a>
    </div>
</nav>
