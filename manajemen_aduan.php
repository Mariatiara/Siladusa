<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$searchKeyword = "";
if (isset($_POST['search'])) {
    $searchKeyword = $_POST['search'];
}

include 'db.php';
$sql_aduan = "
    SELECT tbl_aduan.*, tbl_users.username
    FROM tbl_aduan
    JOIN tbl_users ON tbl_aduan.user_id = tbl_users.user_id
    WHERE tbl_aduan.judul_aduan LIKE ? OR tbl_aduan.lokasi_aduan LIKE ? OR tbl_aduan.status_aduan LIKE ?
";
$stmt = $conn->prepare($sql_aduan);
$searchTerm = "%" . $searchKeyword . "%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result_aduan = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Aduan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-100 to-white min-h-screen py-10 px-4">

    <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-blue-700">Manajemen Aduan</h2>
            <a href="dashboard_admin.php" class="text-sm bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg transition">← Kembali ke Dashboard</a>
        </div>

        <!-- Form Pencarian -->
        <form method="POST" action="manajemen_aduan.php" class="mb-6">
            <div class="flex gap-2">
                <input type="text" name="search" placeholder="Cari Judul, Lokasi, atau Status" value="<?= htmlspecialchars($searchKeyword); ?>"
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Cari</button>
            </div>
        </form>

        <!-- Tabel Daftar Aduan -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200 rounded-lg">
                <thead class="bg-blue-100 text-gray-800 text-sm uppercase">
                    <tr>
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Judul</th>
                        <th class="px-4 py-2">Deskripsi</th>
                        <th class="px-4 py-2">Lokasi</th>
                        <th class="px-4 py-2">Pelapor</th>
                        <th class="px-4 py-2">Gambar</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($row = $result_aduan->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2"><?= $row['aduan_id']; ?></td>
                            <td class="px-4 py-2"><?= $row['judul_aduan']; ?></td>
                            <td class="px-4 py-2"><?= $row['deskripsi_aduan']; ?></td>
                            <td class="px-4 py-2"><?= $row['lokasi_aduan']; ?></td>
                            <td class="px-4 py-2"><?= $row['username']; ?></td>
                            <td class="px-4 py-2">
                                <?php if ($row['lampiran']): ?>
                                    <button onclick="setImage('uploads/<?= $row['lampiran']; ?>')" 
                                            class="text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-lg text-sm"
                                            data-modal-target="imageModal" data-modal-toggle="imageModal">
                                        Lihat Foto
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2"><?= $row['status_aduan']; ?></td>
                            <td class="px-4 py-2">
    <div class="flex gap-3">
        <a href="edit_aduan.php?id=<?= $row['aduan_id']; ?>" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-md">Edit</a>
        <a href="delete_aduan.php?id=<?= $row['aduan_id']; ?>" onclick="return confirm('Yakin ingin menghapus?')"
           class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md">Hapus</a>
    </div>
</td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Gambar -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-4 rounded-lg shadow-lg max-w-lg w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Gambar Aduan</h3>
                <button onclick="closeModal()" class="text-gray-600 hover:text-red-500 text-xl">&times;</button>
            </div>
            <img id="modalImage" src="" alt="Lampiran Aduan" class="w-full rounded-lg shadow">
        </div>
    </div>

    <script>
        function setImage(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('modalImage').src = '';
        }

        // Optional: Tutup modal jika klik di luar kontennya
        window.onclick = function(e) {
            const modal = document.getElementById('imageModal');
            if (e.target === modal) {
                closeModal();
            }
        };
    </script>
</body>
</html>
