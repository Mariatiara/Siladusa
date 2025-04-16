<?php
session_start();

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Warga') {
    header("Location: login.php");
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Pengguna';

// Proses logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Warga</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-sky-100 to-blue-200 min-h-screen flex items-center justify-center">

  <div class="bg-white shadow-lg rounded-xl w-full max-w-lg p-8">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-bold text-blue-700">Selamat Datang, <?= htmlspecialchars($username) ?>!</h1>
      <p class="text-gray-600 mt-1">Silakan input atau lacak aduan Anda di bawah ini</p>
    </div>

    <div class="space-y-4">
      <a href="input_aduan.php" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg transition duration-200">
        Input Aduan
      </a>
      <a href="pelacakan_aduan.php" class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 rounded-lg transition duration-200">
        Lacak Aduan
      </a>
      <a href="dashboard_warga.php?logout=true" class="block w-full text-center bg-red-500 hover:bg-red-600 text-white font-medium py-2 rounded-lg transition duration-200">
        Logout
      </a>
    </div>
  </div>

</body>
</html>
