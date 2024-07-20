<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

require '../db.php'; // Kết nối đến cơ sở dữ liệu

// Lấy dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? null;
$securityCode = $data['securityCode'] ?? null;

if (!$token || !$securityCode) {
    echo json_encode(['error' => 'Invalid token or security code']);
    exit;
}

try {
    // Bắt đầu giao dịch
    $pdo->beginTransaction();

    // Lưu token và mã bảo mật vào cơ sở dữ liệu
    $stmt = $pdo->prepare("INSERT INTO token (user_id, token, security_code) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $token, $securityCode]);

    // Cập nhật số dư của người dùng
    $updateBalanceStmt = $pdo->prepare("UPDATE users SET balance = balance + 1 WHERE id = ?");
    $updateBalanceStmt->execute([$_SESSION['user_id']]);

    // Commit giao dịch
    $pdo->commit();

    echo json_encode(['success' => true, 'token' => $token, 'securityCode' => $securityCode]);
} catch (Exception $e) {
    // Rollback giao dịch nếu có lỗi
    $pdo->rollBack();
    echo json_encode(['error' => 'Failed to save token']);
}
?>