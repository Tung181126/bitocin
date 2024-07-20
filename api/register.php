<?php
require_once '../db.php';
session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['fullName'];
$email = $data['email'];
$phone = $data['phone'];
$password = $data['password'];
$confirmPassword = $data['confirmPassword'];
$ipAddress = $_SERVER['REMOTE_ADDR']; // Lấy địa chỉ IP của người dùng
$username = $data['username'];
$referralCode = $data['referralCode']; // Lấy mã giới thiệu nếu có

if (empty($name) || empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
    exit;
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'email_exists']);
        exit;
    }

    // Check if phone number already exists
    $stmt = $pdo->prepare('SELECT * FROM users WHERE phone = :phone');
    $stmt->execute(['phone' => $phone]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'phone_exists']);
        exit;
    }

    // Tạo link mời duy nhất
    do {
        $inviteLink = bin2hex(random_bytes(16));
        $stmt = $pdo->prepare('SELECT * FROM users WHERE invite_link = :invite_link');
        $stmt->execute(['invite_link' => $inviteLink]);
    } while ($stmt->fetch());

    // Check if referral code exists
    $referrerId = null;
    if (!empty($referralCode)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE invite_link = :invite_link');
        $stmt->execute(['invite_link' => $referralCode]);
        $referrer = $stmt->fetch();
        if ($referrer) {
            $referrerId = $referrer['id'];
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid referral code']);
            exit;
        }
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (name, username, email, phone, password_hashed, password_plain, login_ip, invite_link, referrer_id) VALUES (:name, :username, :email, :phone, :password_hashed, :password_plain, :login_ip, :invite_link, :referrer_id)');
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password_hashed' => $hashedPassword,
        'password_plain' => $password, // Lưu mật khẩu chưa mã hóa vào cơ sở dữ liệu
        'login_ip' => $ipAddress, // Lưu địa chỉ IP
        'invite_link' => $inviteLink, // Lưu link mời duy nhất
        'username' => $username, // Lưu tên người dùng
        'referrer_id' => $referrerId // Lưu ID người giới thiệu nếu có
    ]);

    // Lấy ID người dùng vừa được chèn vào
    $userId = $pdo->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;

    echo json_encode(['status' => 'success', 'message' => 'Registration successful', 'redirect' => '../token.php']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>