<?php
include 'db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_aduan'])) {
    $judul_aduan = $_POST['judul_aduan'];
    $deskripsi_aduan = $_POST['deskripsi_aduan'];
    $kategori_id = $_POST['kategori_id'];
    $lokasi_aduan = $_POST['lokasi_aduan'];
    $tanggal_kejadian = $_POST['tanggal_kejadian'];
    $lampiran = $_FILES['lampiran']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["lampiran"]["name"]);

    if (move_uploaded_file($_FILES["lampiran"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO tbl_aduan (user_id, judul_aduan, deskripsi_aduan, kategori_id, lokasi_aduan, tanggal_kejadian, lampiran)
                VALUES ('$user_id', '$judul_aduan', '$deskripsi_aduan', '$kategori_id', '$lokasi_aduan', '$tanggal_kejadian', '$lampiran')";
        if ($conn->query($sql) === TRUE) {
            $notif_message = "Aduan berhasil diajukan!";
            $notif_class = "bg-green-100 text-green-700";
            $redirect_url = ($_SESSION['role'] == 'Admin') ? 'dashboard_admin.php' : 'dashboard_warga.php';
        } else {
            $notif_message = "Error: " . $conn->error;
            $notif_class = "bg-red-100 text-red-700";
        }
    } else {
        $notif_message = "Gagal mengupload file.";
        $notif_class = "bg-red-100 text-red-700";
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM tbl_aduan WHERE aduan_id = $delete_id AND user_id = $user_id";
    if ($conn->query($sql) === TRUE) {
        $notif_message = "Aduan berhasil dihapus!";
        $notif_class = "bg-green-100 text-green-700";
    } else {
        $notif_message = "Error: " . $conn->error;
        $notif_class = "bg-red-100 text-red-700";
    }
}

$sql_aduan = "SELECT * FROM tbl_aduan WHERE user_id = $user_id";
$result_aduan = $conn->query($sql_aduan);

$sql_kategori = "SELECT * FROM tbl_kategori_aduan";
$result_kategori = $conn->query($sql_kategori);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Input Aduan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 to-sky-200 min-h-screen py-10 px-4">
    <div class="max-w-5xl mx-auto bg-white shadow-xl rounded-2xl p-8 space-y-10">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-blue-700">Form Pengajuan Aduan</h2>
            <p class="text-sm text-gray-600 mt-1">Silakan isi form berikut untuk menyampaikan aduan Anda.</p>
        </div>

        <div class="flex justify-start mb-4">
    <a href="index.php" class="inline-block bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition">
        ← Kembali
    </a>
</div>


        <?php if (isset($notif_message)): ?>
            <div class="p-4 rounded-lg <?= $notif_class ?> text-center">
                <p class="font-semibold"><?= $notif_message ?></p>
                <?php if (isset($redirect_url)): ?>
                    <a href="<?= $redirect_url ?>" class="block mt-2 text-blue-600 underline">Kembali ke Dashboard</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="input_aduan.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-2 md:col-span-1">
                <label class="block text-gray-700 font-semibold mb-1" for="judul_aduan">Judul Aduan</label>
                <input type="text" name="judul_aduan" id="judul_aduan" class="w-full border rounded-lg p-3" required>
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-gray-700 font-semibold mb-1" for="kategori_id">Kategori Aduan</label>
                <select name="kategori_id" id="kategori_id" class="w-full border rounded-lg p-3" required>
                    <option value="">Pilih Kategori</option>
                    <?php while($row = $result_kategori->fetch_assoc()): ?>
                        <option value="<?= $row['kategori_id']; ?>"><?= $row['nama_kategori']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-span-2">
                <label class="block text-gray-700 font-semibold mb-1" for="deskripsi_aduan">Deskripsi Aduan</label>
                <textarea name="deskripsi_aduan" id="deskripsi_aduan" rows="4" class="w-full border rounded-lg p-3" required></textarea>
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-gray-700 font-semibold mb-1" for="lokasi_aduan">Lokasi Kejadian</label>
                <input type="text" name="lokasi_aduan" id="lokasi_aduan" class="w-full border rounded-lg p-3" required>
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-gray-700 font-semibold mb-1" for="tanggal_kejadian">Tanggal Kejadian</label>
                <input type="date" name="tanggal_kejadian" id="tanggal_kejadian" class="w-full border rounded-lg p-3" required>
            </div>

            <div class="col-span-2">
                <label class="block text-gray-700 font-semibold mb-1" for="lampiran">Lampiran (foto/video)</label>
                <input type="file" name="lampiran" id="lampiran" accept="image/*,video/*" class="w-full border rounded-lg p-3" onchange="previewImage(event)">
                <img id="preview" src="#" class="rounded-lg max-w-xs mt-3 hidden border border-gray-300" />
            </div>

            <div class="col-span-2">
                <button type="submit" name="submit_aduan" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg">
                    Kirim Aduan
                </button>
            </div>
        </form>

        <!-- Divider -->
        <hr class="border-t-2 border-blue-200 my-10">

        <!-- Tabel Aduan -->
        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Daftar Aduan Anda</h3>
        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full table-auto border border-gray-300">
                <thead class="bg-blue-100 text-blue-700">
                    <tr>
                        <th class="py-3 px-4 text-left border">Judul</th>
                        <th class="py-3 px-4 text-left border">Deskripsi</th>
                        <th class="py-3 px-4 text-left border">Lokasi</th>
                        <th class="py-3 px-4 text-left border">Tanggal</th>
                        <th class="py-3 px-4 text-left border">Status</th>
                        <th class="py-3 px-4 text-center border">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 bg-white">
                    <?php while($row = $result_aduan->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border"><?= $row['judul_aduan']; ?></td>
                            <td class="py-3 px-4 border"><?= $row['deskripsi_aduan']; ?></td>
                            <td class="py-3 px-4 border"><?= $row['lokasi_aduan']; ?></td>
                            <td class="py-3 px-4 border"><?= $row['tanggal_kejadian']; ?></td>
                            <td class="py-3 px-4 border"><?= $row['status_aduan']; ?></td>
                            <td class="py-2 px-4 border">
    <div class="flex gap-2">
        <a href="edit_inputaduan.php?aduan_id=<?= $row['aduan_id']; ?>" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm">Edit</a>
        <a href="input_aduan.php?delete_id=<?= $row['aduan_id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</a>
    </div>
</td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const preview = document.getElementById('preview');
                preview.src = reader.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>
