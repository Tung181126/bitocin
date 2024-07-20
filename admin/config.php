<?php
session_start();
include_once 'db.php';

// Kiểm tra session username có trong bảng admin không
$username = $_SESSION['username'];
$sql = "SELECT * FROM admin WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $username]);
$result = $stmt->fetch();
if (!$result) {
    header("Location: index.php");
    exit();
}

// Lấy dữ liệu cấu hình
$sql = "SELECT * FROM settings";
$settings = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <title>Cấu Hình</title>
    <style>
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Quản Trị</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="adminchat.php">Chat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Trang Chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Người Dùng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="invest.php">Gói Đầu Tư</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="config.php">Cấu Hình</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="investment_history.php">Lịch Sử Đầu Tư</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Đăng Xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->
    <div class="container mt-5">
        <h1 class="text-center mb-4">Cấu Hình</h1>
        <div class="row">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>USDT Address</th>
                        <th>USD Rate</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($settings as $setting): ?>
                    <tr>
                        <td><?= htmlspecialchars($setting['id']) ?></td>
                        <td><?= htmlspecialchars($setting['usdt_address']) ?></td>
                        <td><?= htmlspecialchars($setting['usd_rate']) ?></td>
                        <td>
                            <a href="edit_setting.php?id=<?= $setting['id'] ?>" class="btn btn-primary">Chỉnh Sửa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?= $_SESSION['message_type'] ?>',
                title: 'Thông báo',
                text: '<?= $_SESSION['message'] ?>'
            });
        });
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    </script>
    <?php endif; ?>

    <script src="js/settings.js"></script>
</body>
</html>