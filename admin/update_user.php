<?php
require 'db.php';

if (isset($_POST['user_id'], $_POST['username'], $_POST['email'], $_POST['phone'], $_POST['balance'])) {
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $balance = $_POST['balance'];

    try {
        $stmt = $pdo->prepare('UPDATE users SET username = :username, email = :email, phone = :phone, balance = :balance WHERE id = :id');
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'balance' => $balance,
            'id' => $userId
        ]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error updating user data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>