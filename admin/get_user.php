<?php
require 'db.php';

if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    try {
        $stmt = $pdo->prepare('SELECT username, email, phone, balance FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if ($user) {
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching user data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User ID not provided']);
}
?>