<?php
header('Content-Type: application/json');
include '../db.php';

// Lấy mã mời từ query string
$inviteLink = isset($_GET['invite_link']) ? $_GET['invite_link'] : '';

// Kiểm tra mã mời và lấy thông tin người dùng từ cơ sở dữ liệu
if ($inviteLink) {
    // Thực hiện truy vấn cơ sở dữ liệu để lấy thông tin người dùng dựa trên mã mời
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, u.balance, COALESCE(SUM(i.amount), 0) as invested
        FROM users u
        LEFT JOIN investments i ON u.id = i.user_id
        WHERE u.invite_link = ?
        GROUP BY u.id, u.username, u.balance
    ");
    $stmt->execute([$inviteLink]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $user['status'] = 'success';
    } else {
        $user = ['status' => 'error', 'message' => 'User not found'];
    }

    echo json_encode($user);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid invite link']);
}
?>