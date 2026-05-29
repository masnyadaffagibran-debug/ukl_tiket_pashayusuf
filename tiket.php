<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }

$id_tempat_filter = isset($_GET['id_tempat']) ? (int)$_GET['id_tempat'] : 0;

if (isset($_POST['tambah'])) {
    $id_tempat = (int)$_POST['id_tempat'];
    $jenis     = mysqli_real_escape_string($conn, $_POST['jenis']);
    $harga     = (float)$_POST['harga'];
    $desk      = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $stok      = (int)$_POST['stok'];
    mysqli_query($conn, "INSERT INTO tiket (id_tempat, jenis, harga, deskripsi, stok) VALUES ('$id_tempat','$jenis','$harga','$desk','$stok')");
    $sukses = "Tiket berhasil ditambahkan!";
}

if (isset($_POST['edit'])) {
    $id    = (int)$_POST['id'];
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $harga = (float)$_POST['harga'];
    $desk  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $stok  = (int)$_POST['stok'];
    mysqli_query($conn, "UPDATE tiket SET jenis='$jenis', harga='$harga', deskripsi='$desk', stok='$stok' WHERE id='$id'");
    $sukses = "Tiket berhasil diperbarui!";
}

if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM tiket WHERE id='" . (int)$_GET['hapus'] . "'");
    header("Location: tiket.php" . ($id_tempat_filter ? "?id_tempat=$id_tempat_filter" : '') . "&hapus=sukses"); exit();
}

$tempat_list = mysqli_query($conn, "SELECT * FROM tempat ORDER BY nama ASC");
$where = $id_tempat_filter ? "WHERE tiket.id_tempat='$id_tempat_filter'" : '';
$tiket_list  = mysqli_query($conn, "SELECT tiket.*, tempat.nama as nama_tempat FROM tiket JOIN tempat ON tiket.id_tempat=tempat.id $where ORDER BY tempat.nama, tiket.harga ASC");
$edit_data   = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tiket WHERE id='" . (int)$_GET['edit'] . "'")) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Tiket - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include "nafbar.php"; ?>
<div class="admin-wrap">
    <div class="admin-topbar"><div><h1>Kelola Tiket</h1><p class="topbar-sub">Atur jenis tiket dan harga per tempat</p></div></div>

    <?php if (isset($sukses)) { ?><div class="alert alert-success"><?php echo $sukses; ?></div><?php } ?>
    <?php if (isset($_GET['hapus']) && $_GET['hapus']==='sukses') { ?><div class="alert alert-success">Tiket berhasil dihapus.</div><?php } ?>

    <div class="section" style="margin-bottom:24px;">
        <div class="section-header">
            <h2><?php echo $edit_data ? 'Edit Tiket' : 'Tambah Tiket Baru'; ?></h2>
            <?php if ($edit_data) { ?><a href="tiket.php" class="link-muted">+ Tambah baru</a><?php } ?>
        </div>
        <form method="POST" style="margin-top:18px; display:flex; flex-direction:column; gap:14px;">
            <?php if ($edit_data) { ?><input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>"><?php } ?>
            <div class="form-inline">
                <?php if (!$edit_data) { ?>
                <select name="id_tempat" class="form-select" required>
                    <option value="">Pilih Tempat
                        
                    </option>
                    <?php
                    $tl = mysqli_query($conn, "SELECT * FROM tempat ORDER BY nama ASC");
                    while ($t = mysqli_fetch_assoc($tl)) { ?>
                        <option value="<?php echo $t['id']; ?>" <?php echo $id_tempat_filter==$t['id']?'selected':''; ?>><?php echo htmlspecialchars($t['nama']); ?></option>
                    <?php } ?>
                </select>
                <?php } ?>
                <input type="text" name="jenis" placeholder="Jenis tiket (mis: Dewasa, Anak, Pelajar)" required class="form-input" value="<?php echo $edit_data?htmlspecialchars($edit_data['jenis']):''; ?>">
                <input type="number" name="harga" placeholder="Harga (Rp)" required class="form-input" step="500" value="<?php echo $edit_data?$edit_data['harga']:''; ?>">
                <input type="number" name="stok" placeholder="Stok" required class="form-input" min="0" value="<?php echo $edit_data?$edit_data['stok']:'100'; ?>">
            </div>
            <input type="text" name="deskripsi" placeholder="Deskripsi tiket (opsional)" class="form-input" value="<?php echo $edit_data?htmlspecialchars($edit_data['deskripsi']):''; ?>">
            <div><button type="submit" name="<?php echo $edit_data?'edit':'tambah'; ?>" class="btn-submit" style="width:auto; padding:12px 28px;"><?php echo $edit_data?'Simpan':'Tambah Tiket'; ?></button></div>
        </form>
    </div>

    <!-- Filter -->
    <div style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap;">
        <a href="tiket.php" class="filter-tab <?php echo !$id_tempat_filter?'active':''; ?>">Semua Tempat</a>
        <?php
        $tl2 = mysqli_query($conn, "SELECT * FROM tempat ORDER BY nama ASC");
        while ($t = mysqli_fetch_assoc($tl2)) { ?>
            <a href="tiket.php?id_tempat=<?php echo $t['id']; ?>" class="filter-tab <?php echo $id_tempat_filter==$t['id']?'active':''; ?>"><?php echo htmlspecialchars($t['nama']); ?></a>
        <?php } ?>
    </div>

    <div class="section">
        <div class="section-header"><h2>Daftar Tiket</h2></div>
        <table>
            <thead><tr><th>Tempat</th><th>Jenis</th><th>Harga</th><th>Stok</th><th>Deskripsi</th><th></th></tr></thead>
            <tbody>
            <?php if (mysqli_num_rows($tiket_list)==0) { ?><tr class="empty-row"><td colspan="6">Belum ada tiket.</td></tr><?php } ?>
            <?php while ($t = mysqli_fetch_assoc($tiket_list)) { ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($t['nama_tempat']); ?></strong></td>
                    <td><?php echo htmlspecialchars($t['jenis']); ?></td>
                    <td>Rp <?php echo number_format($t['harga'], 0, ',', '.'); ?></td>
                    <td><?php echo $t['stok']; ?></td>
                    <td class="text-muted desc-clip"><?php echo htmlspecialchars($t['deskripsi']); ?></td>
                    <td>
                        <div class="action-group">
                            <a href="tiket.php?edit=<?php echo $t['id']; ?>" class="btn-toggle">Edit</a>
                            <a href="tiket.php?hapus=<?php echo $t['id']; ?>" class="btn-delete" onclick="return confirm('Hapus tiket ini?')">Hapus</a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.filter-tab { padding:8px 18px; border-radius:50px; font-family:sans-serif; font-size:13px; font-weight:600; text-decoration:none; border:1.5px solid rgba(245,217,123,0.2); color:#94a3b8; transition:all 0.2s; }
.filter-tab:hover, .filter-tab.active { border-color:#f5d97b; color:#f5d97b; background:rgba(245,217,123,0.08); }
</style>
</body>
</html>