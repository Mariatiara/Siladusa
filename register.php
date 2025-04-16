<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (strlen($password) < 6) {
        $error_message = "Password harus lebih dari 6 karakter.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql_check_email = "SELECT * FROM tbl_users WHERE email = ?";
        $stmt = $conn->prepare($sql_check_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email sudah terdaftar.";
        } else {
            $sql = "INSERT INTO tbl_users (username, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                echo "<div style='text-align:center;margin-top:20px;'>Registrasi berhasil! <a href='login.php' class='text-blue-600 font-semibold underline'>Login</a></div>";
                exit();
            } else {
                $error_message = "Gagal mendaftar: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SILADUSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col md:flex-row">

    <!-- Bagian Kiri -->
    <div class="hidden md:flex md:w-1/2 bg-blue-800 relative items-center justify-center">
        <img src="https://images.unsplash.com/photo-1662706106992-41efbbcc5fb0?q=80&w=1972&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Desa Wonorejo" class="absolute inset-0 object-cover w-full h-full opacity-30">

        <div class="relative z-10 w-full text-white px-10">
          
            <div class="h-full flex flex-col justify-center items-center text-center mt-20">
                <h1 class="text-5xl font-extrabold mb-4 drop-shadow-lg">Selamat Datang</h1>
                <p class="text-2xl font-semibold drop-shadow-md">di Sistem Layanan Aduan Desa <span class="text-blue-200">Wonorejo</span></p>
            </div>
        </div>
    </div>

    <!-- Bagian Kanan: Form Registrasi -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-gradient-to-br from-blue-100 to-sky-200 py-12 px-6">
        <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-xl">
            <h2 class="text-2xl font-bold text-center text-blue-700 mb-6">Daftar</h2>

            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="space-y-5">
                <div>
                    <label for="username" class="block text-gray-700 font-medium mb-1">Username</label>
                    <input type="text" name="username" id="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" name="email" id="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                    <input type="password" name="password" id="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="role" class="block text-gray-700 font-medium mb-1">Role</label>
                    <select name="role" id="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Warga">Warga</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                    Daftar
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-4">
                Sudah punya akun? <a href="login.php" class="text-blue-600 hover:underline">Login</a>
            </p>
        </div>
    </div>

</body>
</html>
