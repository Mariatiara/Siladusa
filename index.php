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
    <meta charset="UTF-8">
    <title>Layanan Aduan Desa Wonorejo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .bg-desa {
            background-image: url('https://images.unsplash.com/photo-1716731049987-c5ec344a9c80?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); /* Ganti dengan foto desa lokal jika ada */
            background-size: cover;
            background-position: center;
        }
        .overlay {
            background-color: rgba(0, 123, 255, 0.4);
        }
    </style>
</head>
<body class="relative min-h-screen bg-desa">
    <div class="overlay absolute inset-0 -z-10"></div>

    <!-- Navbar -->
    <nav class="bg-white bg-opacity-90 shadow-md fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-blue-700">Siladusa</div>
            <div class="space-x-6 flex items-center">
                <a href="#beranda" class="hover:text-blue-600 font-semibold transition duration-300">Beranda</a>
                <a href="input_aduan.php" class="hover:text-blue-600 font-semibold transition duration-300">Input Aduan</a>
                <a href="pelacakan_aduan.php" class="hover:text-blue-600 font-semibold transition duration-300">Lacak Aduan</a>
                <a href="index.php?logout=true" class="ml-4 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg font-semibold transition">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section id="beranda" class="pt-32 pb-20 px-4">
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 items-center gap-12 bg-white bg-opacity-100 p-10 rounded-3xl shadow-xl hover:shadow-2xl transition">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold text-blue-800 mb-4">Desa Wonorejo</h1>
                <p class="text-gray-700 text-lg mb-6">Sampaikan keluhan atau laporan secara digital. Sistem kami menjamin kecepatan, kejelasan, dan kemudahan bagi seluruh warga desa.</p>
                <a href="input_aduan.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-semibold transition duration-300">Laporkan Sekarang</a>
            </div>
            <div>
                <img src="https://img.freepik.com/free-vector/flat-design-illustration-customer-support_23-2148887720.jpg?t=st=1744795224~exp=1744798824~hmac=b5c8ec109a965544557b86a529f2e583d983adc91e381e81072a17f2c2e11188&w=900" class="w-full max-w-md mx-auto">
            </div>
        </div>
    </section>



    <!-- Kolase Foto Desa -->
    <section class="py-20 px-4 bg-blue-50">
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-blue-700 mb-6">Pesona Wonorejo</h2>
            <p class="text-gray-600 mb-10 max-w-2xl mx-auto">Desa kami kaya akan alam, budaya, dan gotong royong. Mari jaga bersama lingkungan dan kenyamanan desa kita.</p>
            <div class="grid md:grid-cols-3 gap-6">
                <img src="https://images.unsplash.com/photo-1531975474574-e9d2732e8386?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="rounded-xl shadow-md hover:scale-105 transition duration-300">
                <img src="https://images.unsplash.com/photo-1523539693385-e5e891eb4465?q=80&w=1978&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="rounded-xl shadow-md hover:scale-105 transition duration-300">
                <img src="https://images.unsplash.com/photo-1696819646359-5d77448b0d3b?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="rounded-xl shadow-md hover:scale-105 transition duration-300">
            </div>
        </div>
    </section>

   <!-- Footer -->
<footer class="bg-blue-600 text-white pt-12">
  <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-3 gap-8 pb-8">
    <!-- Tentang Desa -->
    <div>
      <h3 class="text-xl font-bold mb-3">Tentang Desa Wonorejo</h3>
      <p class="text-sm leading-relaxed text-blue-100">
      Wonorejo adalah sebuah nama desa di wilayah Ngadiluwih, Kabupaten Kediri, Provinsi Jawa Timur, Indonesia. Desa Wonorejo merupakan desa yang terletak di kawasan yang asri dan damai, dengan semangat gotong royong dan kearifan lokal yang kuat. Kami berkomitmen untuk memberikan pelayanan terbaik bagi warga melalui inovasi digital.
      </p>
    </div>

    <!-- Kontak -->
    <div>
      <h3 class="text-xl font-bold mb-3">Kontak Kami</h3>
      <ul class="text-sm text-blue-100 space-y-2">
        <li><i class="fas fa-map-marker-alt mr-2"></i>Jl. Raya Ngadiluwih, Tegalrejo, Wonorejo, Kec. Ngadiluwih, Kabupaten Kediri, Jawa Timur 64171</li>
        <li><i class="fas fa-phone-alt mr-2"></i>085790522890</li>
        <li><i class="fas fa-envelope mr-2"></i>admin@wonorejo.desa.id</li>
      </ul>
    </div>

    <!-- Navigasi Cepat -->
    <div>
      <h3 class="text-xl font-bold mb-3">Navigasi Cepat</h3>
      <ul class="text-sm text-blue-100 space-y-2">
        <li><a href="#beranda" class="hover:underline">Beranda</a></li>
        <li><a href="input_aduan.php" class="hover:underline">Input Aduan</a></li>
        <li><a href="pelacakan_aduan.php" class="hover:underline">Lacak Aduan</a></li>
      </ul>
    </div>
  </div>

  <div class="border-t border-blue-400 mt-8 pt-4 text-center text-sm text-blue-100">
    <p>&copy; <?= date('Y') ?> Siladusa - Desa Wonorejo. Dibuat dengan ❤️ untuk warga.</p>
  </div>
</footer>

<!-- Font Awesome CDN -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

</body>
</html>
