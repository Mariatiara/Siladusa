<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit_feedback'])) {
    $review_id = $_POST['review_id'];
    $feedback_admin = $_POST['feedback_admin'];

    $query = "UPDATE tbl_review SET feedback_admin = ?, tanggal_feedback = NOW() WHERE review_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $feedback_admin, $review_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Feedback berhasil diberikan!";
    } else {
        $_SESSION['error'] = "Gagal memberikan feedback!";
    }
}

$query = "SELECT r.review_id, r.isi_review, r.feedback_admin, r.tanggal_review, r.tanggal_feedback, 
                 a.judul_aduan, u.username AS nama_user
          FROM tbl_review r
          JOIN tbl_aduan a ON r.aduan_id = a.aduan_id
          JOIN tbl_users u ON r.user_id = u.user_id
          ORDER BY r.tanggal_review DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Review & Feedback</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-100 to-white min-h-screen py-10 px-4">

<div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-blue-700">Manajemen Review & Feedback</h2>
        <a href="dashboard_admin.php" class="text-sm bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg transition">← Kembali ke Dashboard</a>
    </div>

    <!-- Alert -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Tabel -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200 rounded-lg">
            <thead class="bg-blue-100 text-gray-800 text-sm uppercase">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">Judul Aduan</th>
                    <th class="px-4 py-2">Review</th>
                    <th class="px-4 py-2">Tanggal Review</th>
                    <th class="px-4 py-2">Feedback Admin</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2"><?= $no++ ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['nama_user']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['judul_aduan']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['isi_review']) ?></td>
                            <td class="px-4 py-2"><?= $row['tanggal_review'] ?></td>
                            <td class="px-4 py-2">
                                <?= $row['feedback_admin'] ? htmlspecialchars($row['feedback_admin']) : '<span class="text-red-500 italic">Belum ada</span>' ?>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2 flex-wrap">
                                    <button onclick="openModal(<?= $row['review_id'] ?>, `<?= htmlspecialchars($row['feedback_admin']) ?>`)" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                        <?= $row['feedback_admin'] ? 'Edit' : 'Beri Feedback' ?>
                                    </button>
                                    <a href="delete_review.php?id=<?= $row['review_id'] ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus review ini?')" 
                                       class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                       Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-gray-500 py-4">Belum ada review.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Feedback -->
<div id="feedbackModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-700">Feedback Admin</h3>
            <button onclick="closeModal()" class="text-gray-600 hover:text-red-500 text-xl">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" id="review_id" name="review_id">
            <div class="mb-4">
                <label for="feedback_admin" class="block text-sm font-medium text-gray-700 mb-1">Isi Feedback:</label>
                <textarea id="feedback_admin" name="feedback_admin" rows="4" required
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="submit" name="submit_feedback" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
                <button type="button" onclick="closeModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id, feedback) {
        document.getElementById('review_id').value = id;
        document.getElementById('feedback_admin').value = feedback !== 'null' ? feedback : '';
        document.getElementById('feedbackModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('feedbackModal').classList.add('hidden');
        document.getElementById('review_id').value = '';
        document.getElementById('feedback_admin').value = '';
    }

    window.onclick = function(e) {
        const modal = document.getElementById('feedbackModal');
        if (e.target === modal) {
            closeModal();
        }
    };
</script>

</body>
</html>
