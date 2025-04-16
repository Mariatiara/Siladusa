<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki role 'Admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");  // Redirect ke login jika belum login atau bukan Admin
    exit();
}

// Mengambil aduan berdasarkan ID yang dipilih
include 'db.php'; // Menyertakan koneksi database
if (isset($_GET['id'])) {
    $aduan_id = $_GET['id'];
    // Query untuk mendapatkan detail aduan
    $sql_aduan = "SELECT tbl_aduan.*, tbl_users.username
                  FROM tbl_aduan
                  JOIN tbl_users ON tbl_aduan.user_id = tbl_users.user_id
                  WHERE tbl_aduan.aduan_id = ?";
    $stmt = $conn->prepare($sql_aduan);
    $stmt->bind_param("i", $aduan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $aduan = $result->fetch_assoc();
} else {
    // Jika tidak ada ID, arahkan ke manajemen aduan
    header("Location: manajemen_aduan.php");
    exit();
}

// Menangani form tanggapan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggapan = $_POST['tanggapan'];
    $status_aduan = $_POST['status_aduan'];

    // Query untuk menyimpan tanggapan
    $sql_tanggapan = "UPDATE tbl_aduan SET tanggapan = ?, status_aduan = ? WHERE aduan_id = ?";
    $stmt = $conn->prepare($sql_tanggapan);
    $stmt->bind_param("ssi", $tanggapan, $status_aduan, $aduan_id);
    if ($stmt->execute()) {
        // Tanggapan berhasil disimpan, arahkan kembali ke manajemen aduan
        header("Location: manajemen_aduan.php");
        exit();
    } else {
        $error_message = "Gagal menyimpan tanggapan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanggapan Aduan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 800px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6c757d;
            box-shadow: none;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-secondary {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #6c757d;
            border: none;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <h2>Tanggapan Aduan</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>
        <form action="tanggapan_aduan.php?id=<?= $aduan_id ?>" method="POST">
            <div class="mb-3">
                <label for="judul_aduan" class="form-label">Judul Aduan</label>
                <input type="text" class="form-control" id="judul_aduan" value="<?= $aduan['judul_aduan'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="deskripsi_aduan" class="form-label">Deskripsi Aduan</label>
                <textarea class="form-control" id="deskripsi_aduan" rows="3" readonly><?= $aduan['deskripsi_aduan'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="lampiran" class="form-label">Lampiran (foto/video)</label>
                <?php if ($aduan['lampiran']): ?>
                    <img src="uploads/<?= $aduan['lampiran'] ?>" alt="Lampiran" class="img-fluid" width="150">
                <?php else: ?>
                    <p>Tidak ada lampiran</p>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="status_aduan" class="form-label">Status Aduan</label>
                <select class="form-select" id="status_aduan" name="status_aduan" required>
                    <option value="Tanggapan" <?= $aduan['status_aduan'] == 'Tanggapan' ? 'selected' : '' ?>>Tanggapan</option>
                    <option value="Selesai" <?= $aduan['status_aduan'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="tanggapan" class="form-label">Tanggapan</label>
                <textarea class="form-control" id="tanggapan" name="tanggapan" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Tanggapan</button>
        </form>
        <a href="manajemen_aduan.php" class="btn btn-secondary mt-3">Kembali ke Manajemen Aduan</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
