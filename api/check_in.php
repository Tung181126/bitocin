<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$day = $_POST['day'];
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en'; // Default to 'en' if not set

// Load language file
$lang_file = "../languages/{$language}.json";
if (file_exists($lang_file)) {
    $translations = json_decode(file_get_contents($lang_file), true);
} else {
    $translations = json_decode(file_get_contents("../languages/en.json"), true); // Fallback to English
}

try {
    // Check if the user has already checked in for the day
    $check_sql = "SELECT * FROM attendance WHERE user_id = :user_id AND day = :day";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute(['user_id' => $user_id, 'day' => $day]);
    
    if ($check_stmt->rowCount() > 0) {
        $message = $translations['already_checked_in'];
        echo json_encode(['success' => false, 'message' => $message]);
    } else {
        // Insert check-in record
        $sql = "INSERT INTO attendance (user_id, day) VALUES (:user_id, :day)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id, 'day' => $day]);

        // Update user balance
        $update_balance_sql = "UPDATE users SET balance = balance + 1 WHERE id = :user_id";
        $update_balance_stmt = $pdo->prepare($update_balance_sql);
        $update_balance_stmt->execute(['user_id' => $user_id]);

        $message = $translations['check_in_success'];
        echo json_encode(['success' => true, 'message' => $message]);
    }
} catch (Exception $e) {
    $message = $translations['check_in_error'] . $e->getMessage();
    echo json_encode(['success' => false, 'message' => $message]);
}
?>