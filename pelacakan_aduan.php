<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Warga') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$aduan_found = null;
if (isset($_POST['search'])) {
    $searchKeyword = $_POST['search'];
    $user_id = $_SESSION['user_id'];

    $sql_aduan = "SELECT tbl_aduan.*, tbl_users.username 
                  FROM tbl_aduan
                  JOIN tbl_users ON tbl_aduan.user_id = tbl_users.user_id
                  WHERE (tbl_aduan.judul_aduan LIKE ? OR tbl_aduan.aduan_id LIKE ?) 
                  AND tbl_aduan.user_id = ?";
    $stmt = $conn->prepare($sql_aduan);
    $searchTerm = "%" . $searchKeyword . "%";
    $stmt->bind_param("ssi", $searchTerm, $searchTerm, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $aduan_found = $result->fetch_assoc();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['review'])) {
    $review = $_POST['review'];

    if (isset($aduan_found['aduan_id']) && !empty($aduan_found['aduan_id'])) {
        $sql_review = "INSERT INTO tbl_review_aduan (aduan_id, user_id, review) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql_review);
        $stmt->bind_param("iis", $aduan_found['aduan_id'], $_SESSION['user_id'], $review);

        if ($stmt->execute()) {
            $sql_delete_aduan = "DELETE FROM tbl_aduan WHERE aduan_id = ?";
            $stmt_delete = $conn->prepare($sql_delete_aduan);
            $stmt_delete->bind_param("i", $aduan_found['aduan_id']);

            if ($stmt_delete->execute()) {
                $review_message = "Review berhasil diberikan dan aduan telah dihapus.";
                $show_modal = true;
            } else {
                $review_message = "Gagal menghapus aduan setelah review diberikan.";
            }
        } else {
            $review_message = "Gagal memberikan review. Coba lagi.";
        }
    } else {
        $review_message = "Aduan ID tidak valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pelacakan Aduan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100 min-h-screen flex items-center justify-center py-10 px-4">
    <div class="w-full max-w-3xl bg-white p-8 rounded-2xl shadow-xl">
        <h2 class="text-3xl font-bold text-center mb-8 text-indigo-700">Pelacakan Aduan</h2>

        <!-- Form Pencarian -->
        <form method="POST" action="pelacakan_aduan.php" class="mb-8">
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" placeholder="Cari Judul atau Nomor Aduan" value="<?= isset($searchKeyword) ? htmlspecialchars($searchKeyword) : '' ?>" required class="flex-grow px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">Cari</button>
            </div>
        </form>

        <?php if ($aduan_found): ?>
            <div class="space-y-5">
                <div>
                    <label class="font-semibold text-gray-700 block mb-1">Judul Aduan</label>
                    <input type="text" readonly class="w-full border px-4 py-3 rounded-lg bg-gray-100" value="<?= $aduan_found['judul_aduan'] ?>">
                </div>
                <div>
                    <label class="font-semibold text-gray-700 block mb-1">Deskripsi Aduan</label>
                    <textarea readonly class="w-full border px-4 py-3 rounded-lg bg-gray-100"><?= $aduan_found['deskripsi_aduan'] ?></textarea>
                </div>
                <div>
                    <label class="font-semibold text-gray-700 block mb-1">Lampiran</label>
                    <?php if ($aduan_found['lampiran']): ?>
                        <img src="uploads/<?= $aduan_found['lampiran'] ?>" alt="Lampiran" class="w-40 rounded-md shadow">
                    <?php else: ?>
                        <p class="text-gray-500 italic">Tidak ada lampiran</p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="font-semibold text-gray-700 block mb-1">Status Aduan</label>
                    <input type="text" readonly class="w-full border px-4 py-3 rounded-lg bg-gray-100" value="<?= $aduan_found['status_aduan'] ?>">
                </div>

                <?php if ($aduan_found['status_aduan'] == 'Selesai' && !$aduan_found['review']): ?>
                    <form method="POST" action="pelacakan_aduan.php?id=<?= $aduan_found['aduan_id'] ?>" class="space-y-3">
                        <label class="text-lg font-semibold block">Berikan Review</label>
                        <textarea name="review" required class="w-full border px-4 py-3 rounded-lg" rows="4" placeholder="Tulis review anda mengenai aduan ini..."></textarea>
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-indigo-700">Kirim Review</button>
                    </form>
                <?php elseif ($aduan_found['review']): ?>
                    <div>
                        <label class="text-lg font-semibold block mb-2">Review Anda</label>
                        <textarea readonly class="w-full border px-4 py-3 rounded-lg bg-gray-100" rows="4"><?= $aduan_found['review'] ?></textarea>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (isset($searchKeyword)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <strong>Data tidak ditemukan!</strong> Pastikan nomor aduan atau judul aduan yang dimasukkan benar.
            </div>
        <?php endif; ?>

        <div class="mt-8 text-center">
            <a href="index.php" class="inline-block bg-gray-600 text-white px-5 py-3 rounded-lg hover:bg-gray-700 transition">Kembali</a>
        </div>
    </div>

    <?php if (isset($show_modal)): ?>
        <div id="notifModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition">
            <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg text-center space-y-4">
                <h5 class="text-2xl font-bold text-indigo-700">Aduan Selesai</h5>
                <p class="font-medium">Review Anda telah berhasil diberikan!</p>
                <p class="text-gray-600">Aduan telah selesai diproses dan review Anda sudah disimpan.</p>
                <div class="flex justify-center gap-3 mt-4">
                    <a href="input_aduan.php" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Kembali ke Input Aduan</a>
                    <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Tutup</button>
                </div>
            </div>
        </div>

        <script>
            function closeModal() {
                document.getElementById('notifModal').style.display = 'none';
            }
        </script>
    <?php endif; ?>
</bo>
</html>
