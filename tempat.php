<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }

if (isset($_POST['tambah'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $desk  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $lok   = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $gambar = 'default.jpg';
    if ($_FILES['gambar']['name'] != '') {
        $gambar = time() . '_' . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../image/$gambar");
    }
    mysqli_query($conn, "INSERT INTO tempat (nama, deskripsi, lokasi, gambar) VALUES ('$nama','$desk','$lok','$gambar')");
    $sukses = "Tempat berhasil ditambahkan!";
}

if (isset($_POST['edit'])) {
    $id    = (int)$_POST['id'];
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $desk  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $lok   = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $status = $_POST['status'] === 'aktif' ? 'aktif' : 'nonaktif';
    $set_gambar = '';
    if ($_FILES['gambar']['name'] != '') {
        $gambar = time() . '_' . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../image/$gambar");
        $set_gambar = ", gambar='$gambar'";
    }
    mysqli_query($conn, "UPDATE tempat SET nama='$nama', deskripsi='$desk', lokasi='$lok', status='$status'$set_gambar WHERE id='$id'");
    $sukses = "Tempat berhasil diperbarui!";
}

if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM tempat WHERE id='" . (int)$_GET['hapus'] . "'");
    header("Location: tempat.php?hapus=sukses"); exit();
}

$list = mysqli_query($conn, "SELECT * FROM tempat ORDER BY id DESC");
$edit_data = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tempat WHERE id='" . (int)$_GET['edit'] . "'")) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tempat Budaya - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include "nafbar.php"; ?>
<div class="admin-wrap">
    <div class="admin-topbar"><div><h1>Tempat wisata</h1><p class="topbar-sub">Kelola destinasi wisata</p></div></div>

    <?php if (isset($sukses)) { ?><div class="alert alert-success"><?php echo $sukses; ?></div><?php } ?>
    <?php if (isset($_GET['hapus']) && $_GET['hapus']==='sukses') { ?><div class="alert alert-success">Tempat berhasil dihapus.</div><?php } ?>

    <div class="section" style="margin-bottom:24px;">
        <div class="section-header">
            <h2><?php echo $edit_data ? 'Edit Tempat' : 'Tambah Tempat Baru'; ?></h2>
            <?php if ($edit_data) { ?><a href="tempat.php" class="link-muted">+ Tambah baru</a><?php } ?>
        </div>
        <form method="POST" enctype="multipart/form-data" style="margin-top:18px; display:flex; flex-direction:column; gap:14px;">
            <?php if ($edit_data) { ?><input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>"><?php } ?>
            <div class="form-inline">
                <input type="text" name="nama" placeholder="Nama tempat" required class="form-input" value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama']) : ''; ?>">
                <input type="text" name="lokasi" placeholder="Lokasi / Kota" required class="form-input" value="<?php echo $edit_data ? htmlspecialchars($edit_data['lokasi']) : ''; ?>">
                <input type="file" name="gambar" accept="image/*" class="form-input" <?php echo $edit_data ? '' : 'required'; ?>>
                <?php if ($edit_data) { ?>
                <select name="status" class="form-select">
                    <option value="aktif" <?php echo $edit_data['status']==='aktif'?'selected':''; ?>>Aktif</option>
                    <option value="nonaktif" <?php echo $edit_data['status']==='nonaktif'?'selected':''; ?>>Nonaktif</option>
                </select>
                <?php } ?>
            </div>
            <textarea name="deskripsi" placeholder="Deskripsi tempat..." required class="form-textarea"><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>
            <div><button type="submit" name="<?php echo $edit_data?'edit':'tambah'; ?>" class="btn-submit" style="width:auto; padding:12px 28px;"><?php echo $edit_data?'Simpan':'Tambah Tempat'; ?></button></div>
        </form>
    </div>

    <div class="section">
        <div class="section-header"><h2>Daftar Tempat</h2></div>
        <table>
            <thead><tr><th>Gambar</th><th>Nama</th><th>Lokasi</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (mysqli_num_rows($list) == 0) { ?><tr class="empty-row"><td colspan="5">Belum ada tempat.</td></tr><?php } ?>
            <?php while ($t = mysqli_fetch_assoc($list)) { ?>
                <tr>
                    <td><img src="../image/<?php echo htmlspecialchars($t['gambar']); ?>" class="img-table"></td>
                    <td><strong><?php echo htmlspecialchars($t['nama']); ?></strong></td>
                    <td class="text-muted"><?php echo htmlspecialchars($t['lokasi']); ?></td>
                    <td><span class="badge <?php echo $t['status']==='aktif'?'badge-lunas':'badge-batal'; ?>"><?php echo $t['status']; ?></span></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>