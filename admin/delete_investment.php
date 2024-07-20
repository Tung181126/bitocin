<?php
session_start();
include_once 'db.php';

// Kiểm tra session username có trong bảng admin không
$username = $_SESSION['username'];
$sql = "SELECT * FROM admin WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $username]);
$result = $stmt->fetch();
if (!$result) {
    header("Location: index.php");
    exit();
}

// Xử lý xóa khoản đầu tư
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM investments WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute(['id' => $id])) {
        $_SESSION['message'] = "Khoản đầu tư đã được xóa thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Có lỗi xảy ra khi xóa khoản đầu tư.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: investment_history.php");
    exit();
}
?>