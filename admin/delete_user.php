<?php
require 'db.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra xem yêu cầu có phải là DELETE và có user_id hay không
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Lấy dữ liệu từ body của yêu cầu
    parse_str(file_get_contents("php://input"), $data);
    $userId = $data['user_id'] ?? null;

    if ($userId) {
        try {
            // Chuẩn bị câu lệnh SQL để xoá người dùng
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
            $stmt->execute(['id' => $userId]);

            // Kiểm tra xem có hàng nào bị ảnh hưởng không
            if ($stmt->rowCount()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>