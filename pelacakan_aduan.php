<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki role 'Warga'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Warga') {
    header("Location: login.php");  // Redirect ke login jika belum login atau bukan Warga
    exit();
}

include 'db.php'; // Menyertakan koneksi database


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
    $aduan_id = $_GET['id']; 

    if (!empty($aduan_id)) {
        $sql_review = "INSERT INTO tbl_review (aduan_id, user_id, isi_review) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql_review);
        $stmt->bind_param("iis", $aduan_id, $_SESSION['user_id'], $review);

        if ($stmt->execute()) {
            $review_message = "Review berhasil diberikan.";
            $show_modal = true;
        } else {
            $review_message = "Gagal memberikan review.";
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
<body class="bg-gradient-to-br from-blue-100 to-white min-h-screen py-10 px-4">

<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg p-6">
    <h2 class="text-2xl font-semibold text-blue-700 mb-6 text-center">Pelacakan Aduan</h2>

    <!-- Form Pencarian -->
    <form method="POST" action="pelacakan_aduan.php" class="mb-6">
        <div class="flex gap-2">
            <input type="text" name="search" placeholder="Cari Judul atau Nomor Aduan"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                value="<?= isset($searchKeyword) ? htmlspecialchars($searchKeyword) : '' ?>" required>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Cari
            </button>
        </div>
    </form>

    <!-- Alert Tidak Ditemukan -->
    <?php if (isset($searchKeyword) && !$aduan_found): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <p><strong>Data tidak ditemukan!</strong> Pastikan nomor atau judul aduan benar.</p>
        </div>
    <?php endif; ?>

    <?php if ($aduan_found): ?>
        <!-- Detail Aduan -->
        <div class="space-y-4 mb-6">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Judul Aduan</label>
                <input type="text" value="<?= $aduan_found['judul_aduan'] ?>" readonly
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Deskripsi Aduan</label>
                <textarea readonly rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100"><?= $aduan_found['deskripsi_aduan'] ?></textarea>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Lampiran</label>
                <?php if ($aduan_found['lampiran']): ?>
                    <img src="uploads/<?= $aduan_found['lampiran'] ?>" alt="Lampiran"
                         class="w-40 rounded shadow">
                <?php else: ?>
                    <p class="italic text-gray-500">Tidak ada lampiran.</p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Status</label>
                <input type="text" value="<?= $aduan_found['status_aduan'] ?>" readonly
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100">
            </div>
        </div>

        <!-- Review Section -->
        <?php if ($aduan_found['status_aduan'] == 'Selesai' && !$aduan_found['review']): ?>
            <form method="POST" action="pelacakan_aduan.php?id=<?= $aduan_found['aduan_id'] ?>" class="space-y-4">
                <h3 class="text-lg font-semibold text-blue-700">Berikan Review</h3>
                <textarea name="review" rows="5" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Tulis review Anda di sini..."></textarea>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Kirim Review
                </button>
            </form>
        <?php elseif ($aduan_found['review']): ?>
            <div>
                <h3 class="text-lg font-semibold text-blue-700 mb-2">Review Anda</h3>
                <textarea rows="5" readonly
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100"><?= $aduan_found['review'] ?></textarea>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="mt-6">
        <a href="index.php"
           class="inline-block bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">
            ← Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Modal Notifikasi -->
<?php if (isset($show_modal)): ?>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-blue-700">Review Berhasil</h3>
                <button onclick="window.location.href='pelacakan_aduan.php';" class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
            </div>
            <p class="text-gray-700 mb-4">Review Anda telah berhasil disimpan.</p>
            <div class="flex justify-end gap-2">
                <a href="input_aduan.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Input Aduan Baru</a>
                <button onclick="window.location.href='index.php';" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">Tutup</button>
            </div>
        </div>
    </div>
<?php endif; ?>

</body>
</html>
