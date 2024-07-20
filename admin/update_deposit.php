<?php
include_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $status = 'completed';
    } elseif ($action === 'reject') {
        $status = 'failed';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit();
    }

    $sql = "UPDATE deposits SET status = :status WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(['status' => $status, 'id' => $id]);

    if ($action === 'approve') {
        // tìm số dư của user
        $sql = "SELECT balance FROM users WHERE id = (SELECT user_id FROM deposits WHERE id = :id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        $balance = $user['balance'];

        // tìm số tiền nạp
        $sql = "SELECT amount FROM deposits WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $deposit = $stmt->fetch();
        $amount = $deposit['amount'];

        // cập nhật số dư mới
        $newBalance = $balance + $amount;
        $sql = "UPDATE users SET balance = :newBalance WHERE id = (SELECT user_id FROM deposits WHERE id = :id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['newBalance' => $newBalance, 'id' => $id]);
    }

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Cập nhật thất bại']);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Yêu cầu không hợp lệ.'
    ]);
}
?>