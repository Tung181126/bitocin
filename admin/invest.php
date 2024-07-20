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

// Lấy dữ liệu các gói đầu tư
$sql = "SELECT * FROM investment_packages";
$packages = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Quản Lý Gói Đầu Tư</title>
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
                        <a class="nav-link active" aria-current="page" href="investment_packages.php">Gói Đầu Tư</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="investment_history.php">Lịch Sử Đầu Tư</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="config.php">Cấu Hình</a>
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
        <h1 class="text-center mb-4">Quản Lý Gói Đầu Tư</h1>
        <button class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#addPackageModal">Thêm Gói Đầu Tư</button>
        <div class="row">
            <?php foreach ($packages as $package): ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($package['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($package['description']) ?></p>
                        <p class="card-text">Nhà đầu tư hiện tại: <?= $package['current_investors'] ?></p>
                        <p class="card-text">Nhà đầu tư mục tiêu: <?= $package['target_investors'] ?></p>
                        <p class="card-text">Đầu tư hiện tại: $<?= number_format($package['current_investment'], 2) ?></p>
                        <p class="card-text">Đầu tư tối thiểu: $<?= number_format($package['minimum_investment'], 2) ?></p>
                        <a href="edit_package.php?id=<?= $package['id'] ?>" class="btn btn-primary">Chỉnh Sửa</a>
                        <a href="delete_package.php?id=<?= $package['id'] ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa gói đầu tư này?')">Xóa</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal Thêm Gói Đầu Tư -->
    <div class="modal fade" id="addPackageModal" tabindex="-1" aria-labelledby="addPackageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_package.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPackageModalLabel">Thêm Gói Đầu Tư</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên Gói</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="target_investors" class="form-label">Nhà Đầu Tư Mục Tiêu</label>
                            <input type="number" class="form-control" id="target_investors" name="target_investors" required>
                        </div>
                        <div class="mb-3">
                            <label for="minimum_investment" class="form-label">Đầu Tư Tối Thiểu</label>
                            <input type="number" step="0.01" class="form-control" id="minimum_investment" name="minimum_investment" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
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

    <script src="js/investment_packages.js"></script>
</body>
</html>