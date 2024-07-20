<?php
require_once 'db.php';
session_start();

// Thiết lập múi giờ cho phiên làm việc của MySQL
$pdo->exec("SET time_zone='+07:00'"); // Thiết lập múi giờ cho Việt Nam

// Kiểm tra xem người dùng hiện tại có phải là admin 
if (!isset($_SESSION['username'])) {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
    $stmt->execute(['username' => $_SESSION['username']]);
    $result = $stmt->fetch();
    if (!$result) {
        header("Location: index.php");
        exit;
    }
}

// Lấy tất cả người dùng từ cơ sở dữ liệu cùng với thời gian tin nhắn cuối cùng
$stmt = $pdo->prepare("SELECT sender, MAX(created_at) as last_message_time FROM messages WHERE is_admin = 0 GROUP BY sender ORDER BY last_message_time DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Thêm một truy vấn để kiểm tra số lượng tin nhắn chưa đọc từ mỗi người dùng
$stmt = $pdo->prepare("SELECT sender, COUNT(*) as unread_count FROM messages WHERE recipient = :admin AND is_read = 0 AND is_admin = 0 GROUP BY sender");
$stmt->execute(['admin' => $_SESSION['username']]);
$unread_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$selected_user = $_GET['user'] ?? $users[0]['sender'];
$stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE recipient = :admin AND sender = :user AND is_admin = 0");
$stmt->execute(['admin' => $_SESSION['username'], 'user' => $selected_user]);
$stmt = $pdo->prepare("SELECT *, is_read FROM messages WHERE (sender = :user1 AND is_admin = 0) OR (recipient = :user2 AND is_admin = 1) ORDER BY created_at DESC");
$stmt->bindValue(':user1', $selected_user);
$stmt->bindValue(':user2', $selected_user);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="adminchat1.css">
    <meta http-equiv="refresh" content="120">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div id="chat-container" class="container-fluid">
        <div id="user-list" class="col-md-4">
            <h2>Users</h2>
            <?php foreach ($users as $user): ?>
                <div class="user">
                    <a href="?user=<?php echo urlencode($user['sender']); ?>">
                        <?php echo htmlspecialchars($user['sender']); ?>
                        <?php if (!empty($unread_counts[$user['sender']])): ?>
                            <span class="unread-indicator"></span>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="chat-window" class="col-md-8">
            <h2>Chat with <?php echo htmlspecialchars($selected_user); ?></h2>
            <div class="message-container">
                <?php foreach ($messages as $message): ?>
                    <div class="message <?php echo $message['is_read'] ? 'read' : 'unread'; ?>">
                        <strong><?php echo htmlspecialchars($message['sender']); ?></strong>
                        <p><?php echo htmlspecialchars($message['text']); ?></p>
                        <?php if (!empty($message['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($message['image_url']); ?>" alt="Image" style="max-width: 100%;">
                        <?php endif; ?>
                        <p class="timestamp"><?php echo date('F j, Y, g:i a', strtotime($message['created_at'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <form id="message-form" enctype="multipart/form-data">
                <input type="hidden" name="recipient" value="<?php echo htmlspecialchars($selected_user); ?>">
                <textarea name="message" class="form-control" placeholder="Type your message here..."></textarea><br>
                <label for="image-upload" class="btn btn-secondary">
                    <i class="fas fa-camera"></i>
                </label>
                <input type="file" name="image" id="image-upload" class="form-control-file" style="display: none;"><br>
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#message-form').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: 'send_message.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var res = JSON.parse(response);
                        alert(res.message);
                        if (res.status === 'success') {
                            location.reload();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>