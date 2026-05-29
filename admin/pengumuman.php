<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }

if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi   = mysqli_real_escape_string($conn, $_POST['isi']);
    mysqli_query($conn, "INSERT INTO pengumuman (judul, isi, id_admin) VALUES ('$judul','$isi','" . (int)$_SESSION['id'] . "')");
    $sukses = "Pengumuman berhasil dibuat!";
}
if (isset($_POST['edit'])) {
    $id    = (int)$_POST['id'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi   = mysqli_real_escape_string($conn, $_POST['isi']);
    mysqli_query($conn, "UPDATE pengumuman SET judul='$judul', isi='$isi' WHERE id='$id'");
    $sukses = "Pengumuman diperbarui!";
}
if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM pengumuman WHERE id='" . (int)$_GET['hapus'] . "'");
    header("Location: pengumuman.php?hapus=sukses"); exit();
}

$list = mysqli_query($conn, "SELECT pengumuman.*, user.username FROM pengumuman JOIN `user` ON pengumuman.id_admin=user.id ORDER BY waktu DESC");
$edit_data = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengumuman WHERE id='" . (int)$_GET['edit'] . "'")) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pengumuman - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include "nafbar.php"; ?>
<div class="admin-wrap">
    <div class="admin-topbar"><div><h1>Pengumuman</h1><p class="topbar-sub">Informasi untuk pengunjung website</p></div></div>

    <?php if (isset($sukses)) { ?><div class="alert alert-success"><?php echo $sukses; ?></div><?php } ?>
    <?php if (isset($_GET['hapus']) && $_GET['hapus']==='sukses') { ?><div class="alert alert-success">Pengumuman dihapus.</div><?php } ?>

    <div class="section" style="margin-bottom:24px;">
        <div class="section-header">
            <h2><?php echo $edit_data?'Edit':'Buat'; ?> Pengumuman</h2>
            <?php if ($edit_data) { ?><a href="pengumuman.php" class="link-muted">+ Buat baru</a><?php } ?>
        </div>
        <form method="POST" style="margin-top:18px; display:flex; flex-direction:column; gap:14px;">
            <?php if ($edit_data) { ?><input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>"><?php } ?>
            <input type="text" name="judul" placeholder="Judul pengumuman" required class="form-input" value="<?php echo $edit_data?htmlspecialchars($edit_data['judul']):''; ?>">
            <textarea name="isi" placeholder="Isi pengumuman..." required class="form-textarea"><?php echo $edit_data?htmlspecialchars($edit_data['isi']):''; ?></textarea>
            <div><button type="submit" name="<?php echo $edit_data?'edit':'tambah'; ?>" class="btn-submit" style="width:auto; padding:12px 28px;"><?php echo $edit_data?'Simpan':'Publikasikan'; ?></button></div>
        </form>
    </div>

    <div class="section">
        <div class="section-header"><h2>Riwayat Pengumuman</h2></div>
        <?php if (mysqli_num_rows($list)==0) { ?><p class="text-muted" style="padding:20px 0; font-family:sans-serif; font-size:14px;">Belum ada pengumuman.</p><?php } ?>
        <?php while ($p = mysqli_fetch_assoc($list)) { ?>
        <div style="padding:18px 0; border-bottom:1px solid rgba(245,217,123,0.08); display:flex; justify-content:space-between; align-items:flex-start; gap:20px;">
            <div style="flex:1;">
                <h3 style="font-size:17px; color:#f5d97b; margin:0 0 6px;"><?php echo htmlspecialchars($p['judul']); ?></h3>
                <p style="font-family:sans-serif; font-size:14px; color:#cbd5e1; margin:0 0 8px; line-height:1.6;"><?php echo nl2br(htmlspecialchars($p['isi'])); ?></p>
                <span style="font-family:sans-serif; font-size:11px; color:#64748b;">@<?php echo htmlspecialchars($p['username']); ?> · <?php echo $p['waktu']; ?></span>
            </div>
            <div class="action-group" style="flex-shrink:0;">
                <a href="pengumuman.php?edit=<?php echo $p['id']; ?>" class="btn-toggle">Edit</a>
                <a href="pengumuman.php?hapus=<?php echo $p['id']; ?>" class="btn-delete" onclick="return confirm('Hapus?')">Hapus</a>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
