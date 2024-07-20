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

// Lấy dữ liệu khoản đầu tư hiện tại
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM investments WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $investment = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Xử lý cập nhật khoản đầu tư
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $package_name = $_POST['package_name'];
    $amount = $_POST['amount'];
    $cycle = $_POST['cycle'];
    $start_date = $_POST['start_date'];
    $daily_profit = $_POST['daily_profit'];

    $sql = "UPDATE investments SET package_name = :package_name, amount = :amount, cycle = :cycle, start_date = :start_date, daily_profit = :daily_profit WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([
        'id' => $id,
        'package_name' => $package_name,
        'amount' => $amount,
        'cycle' => $cycle,
        'start_date' => $start_date,
        'daily_profit' => $daily_profit
    ])) {
        $_SESSION['message'] = "Khoản đầu tư đã được cập nhật thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Có lỗi xảy ra khi cập nhật khoản đầu tư.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: investment_history.php");
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
    <title>Chỉnh Sửa Khoản Đầu Tư</title>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Chỉnh Sửa Khoản Đầu Tư</h1>
        <form action="edit_investment.php" method="POST">
            <input type="hidden" name="id" value="<?= $investment['id'] ?>">
            <div class="mb-3">
                <label for="package_name" class="form-label">Tên Gói</label>
                <input type="text" class="form-control" id="package_name" name="package_name" value="<?= htmlspecialchars($investment['package_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Số Tiền Đầu Tư</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?= htmlspecialchars($investment['amount']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="cycle" class="form-label">Chu Kỳ (ngày)</label>
                <input type="number" class="form-control" id="cycle" name="cycle" value="<?= htmlspecialchars($investment['cycle']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Ngày Bắt Đầu</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($investment['start_date']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="daily_profit" class="form-label">Lợi Nhuận Hàng Ngày</label>
                <input type="number" step="0.1" class="form-control" id="daily_profit" name="daily_profit" value="<?= htmlspecialchars($investment['daily_profit']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Cập Nhật</button>
        </form>
    </div>
</body>
</html>