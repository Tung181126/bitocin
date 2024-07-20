<?php
require 'db.php';

if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    try {
        // Toggle the withdrawal lock status
        $stmt = $pdo->prepare("UPDATE users SET withdrawal_locked = NOT withdrawal_locked WHERE id = :id");
        $stmt->execute(['id' => $userId]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error updating withdrawal lock status']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>