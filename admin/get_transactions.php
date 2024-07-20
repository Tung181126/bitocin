<?php
include_once 'db.php';

$sql = "SELECT wh.id, wh.user_id, u.username, wh.account_id, wh.amount, wh.status, wh.created_at 
        FROM withdrawal_history wh 
        JOIN users u ON wh.user_id = u.id 
        WHERE wh.status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($transactions);
?>