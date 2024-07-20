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

// Lấy tổng số tiền nạp từ bảng deposits
$sql = "SELECT SUM(amount) as total_deposits FROM deposits WHERE status = 'completed'";
$totalDeposits = $pdo->query($sql)->fetchColumn();

// Lấy tổng số tiền rút từ bảng withdrawal_history
$sql = "SELECT SUM(amount) as total_withdrawals FROM withdrawal_history WHERE status = 'completed'";
$totalWithdrawals = $pdo->query($sql)->fetchColumn();

// Lấy tổng số tiền hiện có từ bảng users
$sql = "SELECT SUM(balance) as total_balance FROM users";
$totalBalance = $pdo->query($sql)->fetchColumn();

// Lấy dữ liệu nạp/rút tiền theo tháng
$sql = "SELECT MONTH(created_at) as month, SUM(amount) as total FROM deposits WHERE status = 'completed' GROUP BY MONTH(created_at)";
$depositsData = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT MONTH(created_at) as month, SUM(amount) as total FROM withdrawal_history WHERE status = 'completed' GROUP BY MONTH(created_at)";
$withdrawalsData = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$deposits = array_fill(0, 12, 0);
$withdrawals = array_fill(0, 12, 0);

foreach ($depositsData as $data) {
    $deposits[$data['month'] - 1] = $data['total'];
}

foreach ($withdrawalsData as $data) {
    $withdrawals[$data['month'] - 1] = $data['total'];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Trang Chủ Quản Trị</title>
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
                        <a class="nav-link active" aria-current="page" href="home.php">Trang Chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Người Dùng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="invest.php">Đầu Tư</a>
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
        <h1 class="text-center mb-4">Bảng Điều Khiển Quản Trị</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Người Dùng Đăng Nhập Hôm Nay</h5>
                        <p class="card-text" id="users-logged-in">0</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Tổng Số Dư</h5>
                        <p class="card-text" id="total-balance">$<?= number_format($totalBalance, 2) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Tổng Số Tiền Đã Rút</h5>
                        <p class="card-text" id="total-withdrawn">$<?= number_format($totalWithdrawals, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Biểu Đồ Nạp/Rút Tiền</h5>
                        <canvas id="depositWithdrawChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Phê Duyệt Nạp Tiền</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ID Người Dùng</th>
                                    <th>Tên Người Dùng</th>
                                    <th>Số Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="deposit-table-body">
                                <!-- Dữ liệu sẽ được tải bằng AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Phê Duyệt Rút Tiền</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ID Người Dùng</th>
                                    <th>Tên Người Dùng</th>
                                    <th>Số Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="transaction-table-body">
                                <!-- Dữ liệu sẽ được tải bằng AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Dữ liệu ví dụ, thay thế bằng dữ liệu thực tế của bạn
        const usersLoggedIn = 10;
        const totalBalance = <?= $totalBalance ?>;
        const totalWithdrawn = <?= $totalWithdrawals ?>;
        const depositData = <?= json_encode(array_values($deposits)) ?>;
        const withdrawData = <?= json_encode(array_values($withdrawals)) ?>;

        document.getElementById('users-logged-in').innerText = usersLoggedIn;
        document.getElementById('total-balance').innerText = `$${totalBalance.toFixed(2)}`;
        document.getElementById('total-withdrawn').innerText = `$${totalWithdrawn.toFixed(2)}`;

        const ctx = document.getElementById('depositWithdrawChart').getContext('2d');
        const depositWithdrawChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                datasets: [
                    {
                        label: 'Nạp Tiền',
                        data: depositData,
                        borderColor: 'green',
                        fill: false
                    },
                    {
                        label: 'Rút Tiền',
                        data: withdrawData,
                        borderColor: 'red',
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Tháng'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Số Tiền'
                        }
                    }
                }
            }
        });
    </script>
    <script src="js/home.js"></script>
</body>
</html>