<?php
session_start();
include 'db.php';  


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}


if (isset($_GET['id'])) {
    $review_id = $_GET['id'];

    $query = "DELETE FROM tbl_review WHERE review_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $review_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Review berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus review!";
    }

    header("Location: manajemen_review.php");
    exit();
} else {
    $_SESSION['error'] = "ID review tidak ditemukan!";
    header("Location: manajemen_review.php");
    exit();
}
?>