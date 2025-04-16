<?php
session_start();

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
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
  <title>Dashboard Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-blue-100 via-blue-200 to-blue-300 min-h-screen flex items-center justify-center font-sans">

  <div class="bg-white shadow-2xl rounded-2xl w-full max-w-4xl p-10">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-blue-700">Halo, <?= htmlspecialchars($username) ?> 👋</h1>
        <p class="text-gray-600 mt-1">Selamat datang di dashboard <strong>Admin</strong>. Anda dapat mengelola data aduan dan review warga di sini.</p>
      </div>
      <a href="dashboard_admin.php?logout=true" class="text-red-600 hover:underline text-sm font-semibold">
        <i class="fas fa-sign-out-alt mr-1"></i> Logout
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Manajemen Aduan -->
      <a href="manajemen_aduan.php" class="flex items-center justify-between bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-xl shadow-lg transition-transform hover:scale-[1.02]">
        <div>
          <h2 class="text-lg font-semibold mb-1">Manajemen Aduan</h2>
          <p class="text-sm text-blue-100">Lihat dan tangani aduan warga</p>
        </div>
        <i class="fas fa-comments text-3xl"></i>
      </a>

      <!-- Review & Feedback -->
      <a href="manajemen_review.php" class="flex items-center justify-between bg-green-500 hover:bg-green-600 text-white p-6 rounded-xl shadow-lg transition-transform hover:scale-[1.02]">
        <div>
          <h2 class="text-lg font-semibold mb-1">Review & Feedback</h2>
          <p class="text-sm text-green-100">Kelola ulasan dari warga</p>
        </div>
        <i class="fas fa-star text-3xl"></i>
      </a>
    </div>
  </div>

</body>
</html>
