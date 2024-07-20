<?php
session_start();
include '../db.php';

// Load language file
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$lang_file = "../languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => $lang['login_error']]);
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = $_POST['amount'] ?? 0;

if ($amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => $lang['enter_amount_error']]);
    exit;
}

if (!isset($_FILES['deposit_image']) || $_FILES['deposit_image']['error'] != UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => $lang['upload_image_error']]);
    exit;
}

// Generate a unique name for the image
$imageName = uniqid() . '_' . basename($_FILES['deposit_image']['name']);
$imagePath = '../uploads/' . $imageName;

if (!move_uploaded_file($_FILES['deposit_image']['tmp_name'], $imagePath)) {
    echo json_encode(['status' => 'error', 'message' => $lang['upload_image_error']]);
    exit;
}

try {
    // Check if deposit is locked
    $stmt = $pdo->prepare("SELECT deposit_locked FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user['deposit_locked']) {
        echo json_encode(['status' => 'error', 'message' => $lang['deposit_locked']]);
        exit;
    }

    $pdo->beginTransaction();

    // Insert deposit record with 'pending' status
    $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, status, image_path) VALUES (?, ?, 'pending', ?)");
    $stmt->execute([$user_id, $amount, $imagePath]);

    // Fetch bot token and chat id
    $stmt = $pdo->prepare("SELECT bot_token, chat_id FROM bot_tokens WHERE id = 1"); // Adjust the WHERE clause as needed
    $stmt->execute();
    $botData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($botData) {
        $botToken = $botData['bot_token'];
        $chatId = $botData['chat_id'];

        // Send message to Telegram bot
        $message = "Nhận tiền thành công cho user ID: $user_id, số tiền: $amount, chờ duyệt";
        $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);
        file_get_contents($url);

        // Send image to Telegram bot
        $url = "https://api.telegram.org/bot$botToken/sendPhoto";
        $post_fields = [
            'chat_id' => $chatId,
            'photo' => new CURLFile(realpath($imagePath)),
            'caption' => "Ảnh nạp tiền của user ID: $user_id"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => $lang['deposit_success']]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $lang['error_occurred']]);
}
?>