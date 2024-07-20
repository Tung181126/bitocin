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

// Lấy dữ liệu gói đầu tư hiện tại
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM investment_packages WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $package = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Xử lý cập nhật gói đầu tư
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $target_investors = $_POST['target_investors'];
    $minimum_investment = $_POST['minimum_investment'];

    $sql = "UPDATE investment_packages SET name = :name, description = :description, target_investors = :target_investors, minimum_investment = :minimum_investment WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([
        'id' => $id,
        'name' => $name,
        'description' => $description,
        'target_investors' => $target_investors,
        'minimum_investment' => $minimum_investment
    ])) {
        $_SESSION['message'] = "Gói đầu tư đã được cập nhật thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Có lỗi xảy ra khi cập nhật gói đầu tư.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: invest.php");
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
    <title>Chỉnh Sửa Gói Đầu Tư</title>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Chỉnh Sửa Gói Đầu Tư</h1>
        <form action="edit_package.php" method="POST">
            <input type="hidden" name="id" value="<?= $package['id'] ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Tên Gói</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($package['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô Tả</label>
                <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($package['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="target_investors" class="form-label">Nhà Đầu Tư Mục Tiêu</label>
                <input type="number" class="form-control" id="target_investors" name="target_investors" value="<?= $package['target_investors'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="minimum_investment" class="form-label">Đầu Tư Tối Thiểu</label>
                <input type="number" step="0.01" class="form-control" id="minimum_investment" name="minimum_investment" value="<?= $package['minimum_investment'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Cập Nhật</button>
        </form>
    </div>
</body>
</html>