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

// Lấy dữ liệu cấu hình hiện tại
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM settings WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $setting = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Xử lý cập nhật cấu hình
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $usdt_address = $_POST['usdt_address'];
    $usd_rate = $_POST['usd_rate'];

    $sql = "UPDATE settings SET usdt_address = :usdt_address, usd_rate = :usd_rate WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([
        'id' => $id,
        'usdt_address' => $usdt_address,
        'usd_rate' => $usd_rate
    ])) {
        $_SESSION['message'] = "Cấu hình đã được cập nhật thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Có lỗi xảy ra khi cập nhật cấu hình.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: config.php");
    exit();
}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.31/sweetalert2.min.js"></script>
    <title>Chỉnh Sửa Cấu Hình</title>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Chỉnh Sửa Cấu Hình</h1>
        <form action="edit_setting.php" method="POST">
            <input type="hidden" name="id" value="<?= $setting['id'] ?>">
            <div class="mb-3">
                <label for="usdt_address" class="form-label">USDT Address</label>
                <input type="text" class="form-control" id="usdt_address" name="usdt_address" value="<?= htmlspecialchars($setting['usdt_address']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="usd_rate" class="form-label">USD Rate</label>
                <input type="number" class="form-control" id="usd_rate" name="usd_rate" value="<?= htmlspecialchars($setting['usd_rate']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Cập Nhật</button>
        </form>
    </div>
</body>
</html>