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

// Xử lý dữ liệu từ form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $target_investors = $_POST['target_investors'];
    $minimum_investment = $_POST['minimum_investment'];

    $sql = "INSERT INTO investment_packages (name, description, target_investors, minimum_investment) VALUES (:name, :description, :target_investors, :minimum_investment)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([
        'name' => $name,
        'description' => $description,
        'target_investors' => $target_investors,
        'minimum_investment' => $minimum_investment
    ])) {
        $_SESSION['message'] = "Gói đầu tư đã được thêm thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Có lỗi xảy ra khi thêm gói đầu tư.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: invest.php");
    exit();
}
?>