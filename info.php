<?php
session_start();
require 'db.php'; // Kết nối đến cơ sở dữ liệu

// Load language file based on session
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$lang_file = "languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$password_entered = isset($_POST['password']) ? $_POST['password'] : '';

// Truy vấn thông tin người dùng từ cơ sở dữ liệu
$query = $pdo->prepare("SELECT id, name, email, phone, login_ip, balance, level, invite_link, email_verified FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch();

// Truy vấn token và mã bảo mật từ bảng token
$token_query = $pdo->prepare("SELECT token, security_code FROM token WHERE user_id = ?");
$token_query->execute([$user_id]);
$token_data = $token_query->fetch();

// Truy vấn lịch sử đăng nhập từ bảng login_history
$history_query = $pdo->prepare("SELECT login_time, login_ip FROM login_history WHERE user_id = ? ORDER BY login_time DESC");
$history_query->execute([$user_id]);
$login_history = $history_query->fetchAll();

// Kiểm tra mật khẩu nếu người dùng muốn xem mã bảo mật
$show_password = false;
$password_message = '';
if ($password_entered) {
    $password_query = $pdo->prepare("SELECT password_hashed FROM users WHERE id = ?");
    $password_query->execute([$user_id]);
    $password_data = $password_query->fetch();

    if (password_verify($password_entered, $password_data['password_hashed'])) {
        $show_password = true;
        $password_message = "<div class='alert alert-success'>{$lang['password_correct']}</div>";
    } else {
        $password_message = "<div class='alert alert-danger'>{$lang['password_incorrect']}</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $lang['user_info'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="icon" href="img/logo.webp">
    <style>
        .user-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .user-info .left, .user-info .right {
            width: 48%;
        }
        .user-info .center {
            width: 100%;
            text-align: center;
        }
        .copy-icon {
            cursor: pointer;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <a href="home.php"><i class="bi bi-arrow-left"></i> <?= $lang['back_to_home'] ?></a>
    <a href="logout.php" class="btn btn-danger float-end"><?= $lang['logout'] ?></a>
    <div class="container">
        <h1 class="text-center my-4"><?= $lang['user_info'] ?></h1>
        <div class="user-info">
            <div class="center">
                <img src="img/user.webp" alt="Avatar" class="img-fluid rounded-circle" style="width: 200px; height: 200px;">
                <h2 style="font-size: 2.5rem;"><?= htmlspecialchars($user['name']) ?></h2>
                <p><?= $lang['balance'] ?>: <?= htmlspecialchars($user['balance']) ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th>ID</th><td><?= htmlspecialchars($user['id']) ?></td></tr>
                    <tr><th><?= $lang['email'] ?></th>
                        <td>
                            <?= htmlspecialchars($user['email']) ?>
                            <?php if ($user['email_verified']): ?>
                                <span class="badge bg-success"><?= $lang['verified'] ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger"><?= $lang['not_verified'] ?></span>
                                <div><a href="verify_email.php" class="btn btn-warning btn-sm mt-2"><?= $lang['verify_now'] ?></a></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th><?= $lang['phone'] ?></th><td><?= htmlspecialchars($user['phone']) ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th><?= $lang['login_ip'] ?></th><td><?= htmlspecialchars($user['login_ip']) ?></td></tr>
                    <tr><th><?= $lang['token'] ?></th><td><?= htmlspecialchars($token_data['token']) ?> <span class="copy-icon" onclick="copyToClipboard('<?= htmlspecialchars($token_data['token']) ?>')">📋</span></td></tr>
                    <tr><th><?= $lang['level'] ?></th><td><?= htmlspecialchars($user['level']) ?></td></tr>
                    <tr><th><?= $lang['invite_link'] ?></th><td><?= htmlspecialchars($user['invite_link']) ?></td></tr>
                    <?php if ($show_password): ?>
                        <tr><th><?= $lang['security_code'] ?></th><td><?= htmlspecialchars($token_data['security_code']) ?> <span class="copy-icon" onclick="copyToClipboard('<?= htmlspecialchars($token_data['security_code']) ?>')">📋</span></td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <form method="post" class="mt-4">
            <div class="mb-3">
                <label for="password" class="form-label"><?= $lang['enter_password_to_view_security_code'] ?>:</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary"><?= $lang['confirm'] ?></button>
        </form>
        <?= $password_message ?>

        <h2 class="mt-5"><?= $lang['login_history'] ?></h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><?= $lang['login_time'] ?></th>
                    <th><?= $lang['login_ip'] ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($login_history as $history): ?>
                    <tr>
                        <td><?= htmlspecialchars($history['login_time']) ?></td>
                        <td><?= htmlspecialchars($history['login_ip']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('<?= $lang['copied_to_clipboard'] ?>');
            }, function(err) {
                alert('<?= $lang['copy_failed'] ?>: ', err);
            });
        }

        // Tự động ẩn mã bảo mật sau 60 giây
        <?php if ($show_password): ?>
        setTimeout(function() {
            document.querySelectorAll('tr').forEach(function(row) {
                if (row.innerText.includes('<?= $lang['security_code'] ?>')) {
                    row.style.display = 'none';
                }
            });
        }, 60000);
        <?php endif; ?>
    </script>
</body>
</html>