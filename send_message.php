<?php
require_once 'db.php';
session_start(); // Khởi động session để sử dụng biến session

// Thiết lập múi giờ cho phiên làm việc của MySQL
$pdo->exec("SET time_zone='+07:00'"); // Thiết lập múi giờ cho Việt Nam

// Kiểm tra xem có dữ liệu được gửi đến không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender = isset($_SESSION['username']) ? $_SESSION['username'] : null;
    $recipient = isset($_POST['recipient']) ? $_POST['recipient'] : 'admin'; // Giả sử mặc định người nhận là 'admin'
    $text = isset($_POST['message']) ? trim($_POST['message']) : '';
    $file = isset($_FILES['file']) ? $_FILES['file'] : null;

    // Kiểm tra xem người gửi có hợp lệ và tin nhắn có rỗng không
    if ($sender && (!empty($text) || $file)) {
        // Xử lý tệp tin nếu có
        $filePath = null;
        if ($file) {
            $targetDir = "uploads/";
            $filePath = $targetDir .uniqid() . basename($file["name"]);
            if (!move_uploaded_file($file["tmp_name"], $filePath)) {
                echo "Failed to upload file.";
                exit;
            }
        }

        // Chuẩn bị truy vấn để chèn tin nhắn vào cơ sở dữ liệu
        $stmt = $pdo->prepare("INSERT INTO messages (sender, recipient, text, image_url, created_at) VALUES (:sender, :recipient, :text, :image_url, NOW())");
        $stmt->bindParam(':sender', $sender);
        $stmt->bindParam(':recipient', $recipient);
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':image_url', $filePath);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            echo "Message sent successfully.";
        } else {
            echo "Failed to send message.";
        }
    } elseif (!$sender) {
        echo "Invalid sender. The user must be logged in.";
    } else {
        echo "Message cannot be empty.";
    }
} else {
    // Nếu không có dữ liệu POST hoặc không có 'message' trong POST
    echo "No message received.";
}
?>