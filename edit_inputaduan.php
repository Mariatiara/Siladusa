<?php
include 'db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$aduan = [];

if (isset($_GET['aduan_id'])) {
    $aduan_id = $_GET['aduan_id'];
    $sql = "SELECT * FROM tbl_aduan WHERE aduan_id = $aduan_id AND user_id = $user_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $aduan = $result->fetch_assoc();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_aduan'])) {
    $aduan_id = $_POST['aduan_id'];
    $judul_aduan = $_POST['judul_aduan'];
    $deskripsi_aduan = $_POST['deskripsi_aduan'];
    $kategori_id = $_POST['kategori_id'];
    $lokasi_aduan = $_POST['lokasi_aduan'];
    $tanggal_kejadian = $_POST['tanggal_kejadian'];
    $lampiran = $_FILES['lampiran']['name'];

    if ($lampiran) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["lampiran"]["name"]);
        move_uploaded_file($_FILES["lampiran"]["tmp_name"], $target_file);
        $sql = "UPDATE tbl_aduan SET judul_aduan = '$judul_aduan', deskripsi_aduan = '$deskripsi_aduan', kategori_id = '$kategori_id', lokasi_aduan = '$lokasi_aduan', tanggal_kejadian = '$tanggal_kejadian', lampiran = '$lampiran' WHERE aduan_id = $aduan_id AND user_id = $user_id";
    } else {
        $sql = "UPDATE tbl_aduan SET judul_aduan = '$judul_aduan', deskripsi_aduan = '$deskripsi_aduan', kategori_id = '$kategori_id', lokasi_aduan = '$lokasi_aduan', tanggal_kejadian = '$tanggal_kejadian' WHERE aduan_id = $aduan_id AND user_id = $user_id";
    }

    if ($conn->query($sql) === TRUE) {
        $notif_message = "Aduan berhasil diperbarui!";
        $notif_class = "success";
    } else {
        $notif_message = "Error: " . $conn->error;
        $notif_class = "error";
    }
}

$sql_kategori = "SELECT * FROM tbl_kategori_aduan";
$result_kategori = $conn->query($sql_kategori);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Aduan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Aduan</h2>
        <a href="input_aduan.php" class="btn btn-secondary mb-3">Kembali ke Daftar Aduan</a>
        <form action="edit_inputaduan.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="aduan_id" value="<?= isset($aduan['aduan_id']) ? $aduan['aduan_id'] : ''; ?>">
            <div class="mb-3">
                <label for="judul_aduan" class="form-label">Judul Aduan</label>
                <input type="text" class="form-control" id="judul_aduan" name="judul_aduan" value="<?= isset($aduan['judul_aduan']) ? $aduan['judul_aduan'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi_aduan" class="form-label">Deskripsi Aduan</label>
                <textarea class="form-control" id="deskripsi_aduan" name="deskripsi_aduan" rows="3" required><?= isset($aduan['deskripsi_aduan']) ? $aduan['deskripsi_aduan'] : ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="kategori_id" class="form-label">Kategori Aduan</label>
                <select class="form-select" id="kategori_id" name="kategori_id" required>
                    <option value="">Pilih Kategori</option>
                    <?php while($row = $result_kategori->fetch_assoc()): ?>
                        <option value="<?= $row['kategori_id']; ?>" <?= (isset($aduan['kategori_id']) && $row['kategori_id'] == $aduan['kategori_id']) ? 'selected' : ''; ?>><?= $row['nama_kategori']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="lokasi_aduan" class="form-label">Lokasi Aduan</label>
                <input type="text" class="form-control" id="lokasi_aduan" name="lokasi_aduan" value="<?= isset($aduan['lokasi_aduan']) ? $aduan['lokasi_aduan'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="tanggal_kejadian" class="form-label">Tanggal Kejadian</label>
                <input type="date" class="form-control" id="tanggal_kejadian" name="tanggal_kejadian" value="<?= isset($aduan['tanggal_kejadian']) ? $aduan['tanggal_kejadian'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="lampiran" class="form-label">Lampiran (foto/video)</label>
                <input type="file" class="form-control" id="lampiran" name="lampiran" accept="image/*,video/*">
                <?php if (isset($aduan['lampiran']) && $aduan['lampiran']): ?>
                    <p>File saat ini: <a href="uploads/<?= $aduan['lampiran']; ?>" target="_blank"><?= $aduan['lampiran']; ?></a></p>
                <?php endif; ?>
            </div>
            <button type="submit" name="update_aduan" class="btn btn-primary">Perbarui Aduan</button>
        </form>

        <?php if (isset($notif_message)): ?>
            <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="notifModalLabel">Notifikasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="<?= $notif_class ?>"><?= $notif_message; ?></p>
                        </div>
                        <div class="modal-footer">
                            <a href="input_aduan.php" class="btn btn-primary">Kembali ke Daftar Aduan</a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (isset($notif_message)): ?>
            var myModal = new bootstrap.Modal(document.getElementById('notifModal'));
            myModal.show();
        <?php endif; ?>
    </script>
</body>
</html>