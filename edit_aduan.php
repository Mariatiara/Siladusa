<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $aduan_id = $_GET['id'];

    include 'db.php';
    $sql_aduan = "SELECT * FROM tbl_aduan WHERE aduan_id = ?";
    $stmt = $conn->prepare($sql_aduan);
    $stmt->bind_param("i", $aduan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $aduan = $result->fetch_assoc();
    } else {
        echo "Aduan tidak ditemukan.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul_aduan = $_POST['judul_aduan'];
    $deskripsi_aduan = $_POST['deskripsi_aduan'];
    $status_aduan = $_POST['status_aduan'];

    $sql_update = "UPDATE tbl_aduan SET judul_aduan = ?, deskripsi_aduan = ?, status_aduan = ? WHERE aduan_id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sssi", $judul_aduan, $deskripsi_aduan, $status_aduan, $aduan_id);

    if ($stmt->execute()) {
        echo "<script>alert('Aduan berhasil diperbarui!'); window.location.href='manajemen_aduan.php';</script>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Aduan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-6 text-blue-700">Edit Aduan</h2>

        <form action="edit_aduan.php?id=<?= $aduan['aduan_id'] ?>" method="POST" class="space-y-4">
            <div>
                <label for="judul_aduan" class="block font-medium mb-1">Judul Aduan</label>
                <input type="text" id="judul_aduan" name="judul_aduan" value="<?= htmlspecialchars($aduan['judul_aduan']) ?>" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="deskripsi_aduan" class="block font-medium mb-1">Deskripsi Aduan</label>
                <textarea id="deskripsi_aduan" name="deskripsi_aduan" rows="4" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($aduan['deskripsi_aduan']) ?></textarea>
            </div>

            <div>
                <label for="status_aduan" class="block font-medium mb-1">Status Aduan</label>
                <select id="status_aduan" name="status_aduan" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Menunggu" <?= ($aduan['status_aduan'] == 'Menunggu') ? 'selected' : '' ?>>Menunggu</option>
                    <option value="Diproses" <?= ($aduan['status_aduan'] == 'Diproses') ? 'selected' : '' ?>>Diproses</option>
                    <option value="Selesai" <?= ($aduan['status_aduan'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">Perbarui Aduan</button>
        </form>

        <div class="text-center mt-4">
            <a href="manajemen_aduan.php" class="text-sm text-blue-600 hover:underline">← Kembali ke Manajemen Aduan</a>
        </div>
    </div>
</body>
</html>
