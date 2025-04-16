<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki role 'Admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");  // Redirect ke login jika belum login atau bukan Admin
    exit();
}

// Mengambil ID Aduan dari URL
if (isset($_GET['id'])) {
    $aduan_id = $_GET['id'];

    // Menghapus data aduan dari database
    include 'db.php';
    $sql_delete = "DELETE FROM tbl_aduan WHERE aduan_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $aduan_id);

    if ($stmt->execute()) {
        echo "<script>alert('Aduan berhasil dihapus!'); window.location.href='manajemen_aduan.php';</script>";
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
    <title>Hapus Aduan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-6 text-blue-700">Hapus Aduan</h2>

        <p class="text-center text-gray-600 mb-6">Aduan berhasil dihapus! Anda akan diarahkan kembali ke halaman manajemen aduan.</p>

        <div class="text-center">
            <a href="manajemen_aduan.php" class="text-lg text-blue-600 hover:underline">← Kembali ke Manajemen Aduan</a>
        </div>
    </div>
</body>
</html>
