<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['type' => 'error', 'message' => 'Session expired.']);
    exit;
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

$language = $_SESSION['language'] ?? 'en';
$lang_file = "../languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // loại bỏ dấu phẩy trong số tiền
    $amount = str_replace(',', '', $_POST['amount']); 
    
    // Validate amount
    if (!is_numeric($amount) || $amount <= 0) {
        echo json_encode(['type' => 'error', 'message' => $lang['amount_error']]);
        exit;
    }

    // Get user balance and withdrawal lock status
    $stmt = $pdo->prepare("SELECT balance, username, withdrawal_locked FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $balance = $user['balance'];

    if ($user['withdrawal_locked']) {
        echo json_encode(['type' => 'error', 'message' => $lang['withdrawal_locked']]);
        exit;
    }

    // Get USD rate from settings
    $stmt = $pdo->prepare("SELECT usd_rate FROM settings LIMIT 1");
    $stmt->execute();
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    $usd_rate = $settings['usd_rate'];

    // Calculate maximum withdrawable amount
    $max_withdrawable = $balance * $usd_rate;

    if ($amount > $max_withdrawable) {
        echo json_encode(['type' => 'error', 'message' => $lang['withdraw_error'] . " " . $lang['withdrawable_amount'] . " " . $max_withdrawable]);
        exit;
    }

    // Get a valid account_id for the user
    $stmt = $pdo->prepare("SELECT id, account_number, bank_name FROM accounts WHERE user_id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$account) {
        echo json_encode(['type' => 'error', 'message' => $lang['no_account_found']]);
        exit;
    }
    $account_id = $account['id'];
    $account_number = $account['account_number'];
    $bank_name = $account['bank_name'];

    // Insert withdrawal record into the database
    $stmt = $pdo->prepare("INSERT INTO withdrawal_history (user_id, account_id, amount, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
    if ($stmt->execute([$user_id, $account_id, $amount])) {
        // Update user balance
        $new_balance = $balance - ($amount / $usd_rate);
        $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->execute([$new_balance, $user_id]);
    
        // Send notification to Telegram bot
        $stmt = $pdo->prepare("SELECT bot_token, chat_id FROM bot_tokens LIMIT 1");
        $stmt->execute();
        $bot = $stmt->fetch(PDO::FETCH_ASSOC);
        $bot_token = $bot['bot_token'];
        $chat_id = $bot['chat_id'];
    
        $message = "Rút tiền thành công!\nTên người dùng: " . $username . "\nSố tiền: " . $amount . "\nSố tài khoản: " . $account_number . "\nTên ngân hàng: " . $bank_name . "\nSố dư: " . $new_balance . "\nNgày tháng: " . date('Y-m-d H:i:s');
        $url = "https://api.telegram.org/bot{$bot_token}/sendMessage?chat_id={$chat_id}&text=" . urlencode($message);
        file_get_contents($url);

        echo json_encode(['type' => 'success', 'message' => $lang['withdraw_success']]);
    } else {
        echo json_encode(['type' => 'error', 'message' => $lang['withdraw_error']]);
    }
}
?>