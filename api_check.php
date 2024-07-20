<?php
include 'db.php'; // Include the database connection

// Load language file
session_start();
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$lang_file = "languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

header('Content-Type: application/json; charset=utf-8'); // Set the content type to JSON with UTF-8 encoding

// Check if token and security_code are provided
if (!isset($_GET['token']) || !isset($_GET['security_code'])) {
    echo json_encode(['error' => $lang['error_token_security_code_required']]);
    exit;
}

$token = $_GET['token'];
$security_code = $_GET['security_code'];

// Fetch user info from the database using token and security code
$stmt = $pdo->prepare("
    SELECT u.username, u.balance, i.amount AS invested, i.start_date
    FROM users u
    JOIN token t ON u.id = t.user_id
    LEFT JOIN investments i ON u.id = i.user_id
    WHERE t.token = :token AND t.security_code = :security_code
");
$stmt->execute(['token' => $token, 'security_code' => $security_code]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userInfo) {
    // Calculate the duration of the investment
    $startDate = new DateTime($userInfo['start_date']);
    $currentDate = new DateTime();
    $interval = $startDate->diff($currentDate);
    $userInfo['investment_duration'] = $interval->format('%a days');

    // Calculate the expected return (example calculation)
    $userInfo['expected_return'] = $userInfo['invested'] * 1.1; // Assuming a 10% return

    echo json_encode($userInfo);
} else {
    echo json_encode(['error' => $lang['error_invalid_token_security_code']]);
}
?>