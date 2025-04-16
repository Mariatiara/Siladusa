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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Aduan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-3xl bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-6 text-blue-700">Edit Aduan</h2>

        <?php if (isset($notif_message)): ?>
            <div class="mb-4 px-4 py-3 rounded-md <?= ($notif_class == 'success') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                <?= $notif_message; ?>
            </div>
        <?php endif; ?>

        <form action="edit_inputaduan.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="aduan_id" value="<?= htmlspecialchars($aduan['aduan_id'] ?? '') ?>">

            <div>
                <label for="judul_aduan" class="block font-medium mb-1">Judul Aduan</label>
                <input type="text" id="judul_aduan" name="judul_aduan" value="<?= htmlspecialchars($aduan['judul_aduan'] ?? '') ?>" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="deskripsi_aduan" class="block font-medium mb-1">Deskripsi Aduan</label>
                <textarea id="deskripsi_aduan" name="deskripsi_aduan" rows="3" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($aduan['deskripsi_aduan'] ?? '') ?></textarea>
            </div>

            <div>
                <label for="kategori_id" class="block font-medium mb-1">Kategori Aduan</label>
                <select id="kategori_id" name="kategori_id" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Kategori</option>
                    <?php while($row = $result_kategori->fetch_assoc()): ?>
                        <option value="<?= $row['kategori_id']; ?>" <?= (isset($aduan['kategori_id']) && $row['kategori_id'] == $aduan['kategori_id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($row['nama_kategori']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label for="lokasi_aduan" class="block font-medium mb-1">Lokasi Aduan</label>
                <input type="text" id="lokasi_aduan" name="lokasi_aduan" value="<?= htmlspecialchars($aduan['lokasi_aduan'] ?? '') ?>" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="tanggal_kejadian" class="block font-medium mb-1">Tanggal Kejadian</label>
                <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" value="<?= htmlspecialchars($aduan['tanggal_kejadian'] ?? '') ?>" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="lampiran" class="block font-medium mb-1">Lampiran (foto/video)</label>
                <input type="file" id="lampiran" name="lampiran" accept="image/*,video/*"
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php if (!empty($aduan['lampiran'])): ?>
                    <p class="text-sm mt-2">File saat ini: 
                        <a href="uploads/<?= htmlspecialchars($aduan['lampiran']) ?>" target="_blank" class="text-blue-600 underline">
                            <?= htmlspecialchars($aduan['lampiran']) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>

            <button type="submit" name="update_aduan"
                class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">
                Perbarui Aduan
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="input_aduan.php" class="text-sm text-blue-600 hover:underline">← Kembali ke Daftar Aduan</a>
        </div>
    </div>
</body>
</html>
