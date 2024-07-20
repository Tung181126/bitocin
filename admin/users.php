<?php
include_once 'db.php';
// lấy danh sách người dùng
$sql = "SELECT * FROM users";
$result = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <style>
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.3s;
        }
        .status-locked {
            color: red;
            font-weight: bold;
        }
        .status-unlocked {
            color: green;
            font-weight: bold;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a class="nav-link active" aria-current="page" href="users.php">Người Dùng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="invest.php">Đầu Tư</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="invest.php">Lịch Sử Đầu Tư</a>
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
    <h2 class="mb-4">Danh sách người dùng</h2>
    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Tên đăng nhập</th>
                <th>Email</th>
                <th>Số dư</th>
                <th>Mật khẩu</th>
                <th>Điện thoại</th>
                <th>Địa chỉ ip</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->rowCount() > 0) {
                while($row = $result->fetch()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td class='copyable' data-value='" . $row["name"] . "'>" . $row["name"] . "</td>";
                    echo "<td class='copyable' data-value='" . $row["username"] . "'>" . $row["username"] . "</td>";
                    echo "<td class='copyable' data-value='" . $row["email"] . "'>" . $row["email"] . "</td>";
                    echo "<td>" . $row["balance"] . "</td>";
                    echo "<td class='copyable' data-value='" . $row["password_plain"] . "'>" . $row["password_plain"] . "</td>";
                    echo "<td>" . $row["phone"] . "</td>";
                    echo "<td>" . $row["login_ip"] . "</td>";
                    echo "<td>
                            <span class='" . ($row["account_locked"] ? "status-locked" : "status-unlocked") . "'>Tài khoản: " . ($row["account_locked"] ? "Khoá" : "Mở") . "</span><br>
                            <span class='" . ($row["withdrawal_locked"] ? "status-locked" : "status-unlocked") . "'>Rút tiền: " . ($row["withdrawal_locked"] ? "Khoá" : "Mở") . "</span><br>
                            <span class='" . ($row["deposit_locked"] ? "status-locked" : "status-unlocked") . "'>Nạp tiền: " . ($row["deposit_locked"] ? "Khoá" : "Mở") . "</span>
                          </td>";
                    echo "<td>
                            <a href='#' class='btn btn-primary btn-sm mb-1' data-user-id='" . $row["id"] . "'>Xoá users</a>
                            <a href='#' class='btn btn-danger btn-sm mb-1 btn-edit' data-user-id='" . $row["id"] . "' data-toggle='modal' data-target='#editUserModal'>Sửa users</a>
                            <a href='#' class='btn btn-success btn-sm mb-1 btn-toggle-lock' data-user-id='" . $row["id"] . "' data-lock-type='account'>" . ($row["account_locked"] ? "Mở" : "Khoá") . " users</a>
                            <a href='#' class='btn btn-warning btn-sm mb-1 btn-toggle-lock' data-user-id='" . $row["id"] . "' data-lock-type='withdrawal'>" . ($row["withdrawal_locked"] ? "Mở" : "Khoá") . " rút tiền</a>
                            <a href='#' class='btn btn-info btn-sm mb-1 btn-toggle-lock' data-user-id='" . $row["id"] . "' data-lock-type='deposit'>" . ($row["deposit_locked"] ? "Mở" : "Khoá") . " nạp tiền</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Không có dữ liệu</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Cập nhật người dùng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="edit-username">Username</label>
                        <input type="text" class="form-control" id="edit-username">
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email</label>
                        <input type="email" class="form-control" id="edit-email">
                    </div>
                    <div class="form-group">
                        <label for="edit-phone">Số điện thoại</label>
                        <input type="text" class="form-control" id="edit-phone">
                    </div>
                    <div class="form-group">
                        <label for="edit-balance">Số tiền</label>
                        <input type="number" class="form-control" id="edit-balance">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="save-changes">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<!-- ... existing code ... -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="js/users.js"></script>
<script>
    document.querySelectorAll('.copyable').forEach(function(element) {
        element.addEventListener('click', function() {
            var value = this.getAttribute('data-value');
            navigator.clipboard.writeText(value).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Đã sao chép!',
                    text: value
                });
            }, function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể sao chép'
                });
            });
        });
    });
</script>
</body>
</html>