<?php
require_once '../db.php';
session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$emailOrUsername = $data['emailOrUsername'] ?? '';
$password = $data['password'] ?? '';

if (empty($emailOrUsername) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email và mật khẩu là bắt buộc']);
    exit;
}

// Load language file
$language = $_SESSION['language'] ?? 'vi';
$lang_file = "../languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

try {
    // Gỡ lỗi: Ghi lại các giá trị đầu vào
    error_log("emailOrUsername: $emailOrUsername");
    error_log("password: $password");

    $query = 'SELECT * FROM users WHERE email = :email OR username = :username';
    $stmt = $pdo->prepare($query);
    
    // Gỡ lỗi: Ghi lại câu truy vấn SQL và các tham số
    error_log("SQL Query: $query");
    error_log("Parameters: " . print_r(['email' => $emailOrUsername, 'username' => $emailOrUsername], true));
    
    $stmt->execute(['email' => $emailOrUsername, 'username' => $emailOrUsername]);
    $user = $stmt->fetch();

    // Gỡ lỗi: Ghi lại dữ liệu người dùng đã lấy được
    error_log("Fetched user: " . print_r($user, true));

    if ($user && password_verify($password, $user['password_hashed'])) {
        if ($user['account_locked']) {
            echo json_encode(['status' => 'error', 'message' => $lang['account_locked']]);
            exit;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['user_login_ip'] = $user['login_ip'];
        $_SESSION['user_token'] = $user['token'];
        $_SESSION['user_balance'] = $user['balance'];
        $_SESSION['user_level'] = $user['level'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['success'] = 'Đăng nhập thành công';
        
        // Ghi lại lịch sử đăng nhập
        $login_ip = $_SERVER['REMOTE_ADDR'];
        $history_stmt = $pdo->prepare('INSERT INTO login_history (user_id, login_ip) VALUES (:user_id, :login_ip)');
        $history_stmt->execute(['user_id' => $user['id'], 'login_ip' => $login_ip]);

        // Lấy token từ cơ sở dữ liệu dựa trên user_id
        $token_query = 'SELECT token FROM token WHERE user_id = :user_id';
        $token_stmt = $pdo->prepare($token_query);
        $token_stmt->execute(['user_id' => $user['id']]);
        $token_result = $token_stmt->fetch();

        // Kiểm tra token và chuyển hướng nếu cần thiết
        if (empty($token_result['token'])) {
            echo json_encode(['status' => 'success', 'redirect' => 'token.php']);
        } else {
            echo json_encode(['status' => 'success', 'redirect' => 'home.php']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email hoặc mật khẩu không hợp lệ']);
    }
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()]);
}
?>