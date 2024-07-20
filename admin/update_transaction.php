<?php
include_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $sql = "UPDATE withdrawal_history SET status = 'completed' WHERE id = :id";
    } elseif ($action === 'reject') {
        $sql = "UPDATE withdrawal_history SET status = 'failed' WHERE id = :id";
        // hoàn tiền cho người dùng
        // lấy số tiền trong bảng withdrawal_history
        $sql = "SELECT amount FROM withdrawal_history WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $amount = $stmt->fetchColumn();
        $sql = "UPDATE users SET balance = balance + :amount WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['amount' => $amount, 'user_id' => $user_id]);
    }

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute(['id' => $id])) {
        echo json_encode(['status' => 'success', 'message' => 'Cập nhật giao dịch thành công.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Cập nhật giao dịch thất bại.']);
    }
}
?>