<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Help</title>
    <link rel="stylesheet" href="livechat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Giới hạn kích thước hình ảnh trong khung chat */
        #chat-box img {
            max-width: 100%;
            max-height: 200px;
            display: block;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <?php
    session_start(); // Bắt đầu phiên làm việc
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php'); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
        exit;
    }
    ?>
    <script>
        var username = "<?php echo $_SESSION['username']; ?>";
    </script>
    <header class="admin-status">
        <img src="img/logo.webp" alt="Admin Avatar" class="admin-avatar">
        <div class="admin-active">
            <div class="admin-name-status">
                <span>CSKH</span>
                <img src="blue.png" alt="Active" class="blue-checkmark">
            </div>
            <div class="admin-active-status">
                <span>Đang hoạt động</span>
                <div class="active-dot"></div>
            </div>
        </div>
    </header>
    <main id="chat-box">
        <!-- This is where the messages will be displayed -->
    </main>
    <div class="input-group">
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <input type="file" id="file" style="display: none;" onchange="sendFile()">
                <button onclick="document.getElementById('file').click()">Send File</button>
            </div>
        </div>
        <input type="text" id="message" placeholder="Enter your message">
        <button onclick="sendMessage()" class="send-button">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
    <!-- Thêm nút quay lại trang chủ -->
    <button onclick="window.location.href='home.php'" class="back-button">Back to Home</button>
    <!-- This script handles the sending and loading of messages -->
    <script src="chat.js"></script>
</body>
</html>