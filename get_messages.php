<?php
include 'db.php'; // Include the database connection

session_start();
header('Content-Type: application/json; charset=utf-8'); // Set the content type to JSON with UTF-8 encoding

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$username = $_SESSION['username']; // The current user's username

// Fetch messages from the database where the current user is involved with the admin
$stmt = $pdo->prepare("
    SELECT sender, text, image_url, created_at FROM messages 
    WHERE (sender = :username AND is_admin = 0) OR (recipient = :recipient AND is_admin = 1) 
    ORDER BY created_at ASC
");
// Pass the username for both the sender and recipient parameters
$stmt->execute(['username' => $username, 'recipient' => $username]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the messages as JSON
echo json_encode($messages);
?>