<?php
include_once 'db.php';

$sql = "SELECT d.id, d.user_id, d.amount, d.status, d.created_at, d.image_path, u.username 
        FROM deposits d 
        JOIN users u ON d.user_id = u.id 
        WHERE d.status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($deposits);
?>