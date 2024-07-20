<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Load language file
$language = $_SESSION['language'] ?? 'en';
$lang_file = "../languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bank_name = $_POST['bank_name'];
    $account_number = $_POST['account_number'];
    $account_holder_name = $_POST['account_holder_name'];

    $stmt = $pdo->prepare("INSERT INTO accounts (user_id, bank_name, account_number, account_holder_name) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $bank_name, $account_number, $account_holder_name])) {
        // Fetch bot_token and chat_id from database
        $bot_stmt = $pdo->query("SELECT bot_token, chat_id FROM bot_tokens LIMIT 1");
        $bot_data = $bot_stmt->fetch(PDO::FETCH_ASSOC);
        $bot_token = $bot_data['bot_token'];
        $chat_id = $bot_data['chat_id'];
        
        // Prepare message
        $message = "ID người dùng: $user_id đã liên kết một tài khoản mới: $bank_name. số tài khoản: $account_number. tên chủ tài khoản: $account_holder_name";
        // Send message to Telegram
        $telegram_url = "https://api.telegram.org/bot$bot_token/sendMessage";
        $params = [
            'chat_id' => $chat_id,
            'text' => $message
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $telegram_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Redirect to withdraw.php on success
        header('Location: ../withdraw.php');
        exit;
    } else {
        // Redirect back to link_account.php on failure
        header('Location: ../link_account.php');
        exit;
    }
} else {
    // Redirect back to link_account.php on invalid request
    header('Location: ../link_account.php');
    exit;
}
?>