<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);
unset($_SESSION['language']); // Xoá session ngôn ngữ cũ
$_SESSION['language'] = $data['language'];
echo json_encode(['status' => 'success']);
?>