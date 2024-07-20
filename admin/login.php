<?php
session_start(); // Bắt đầu session

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xử lý đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $ip = $_SERVER['REMOTE_ADDR'];

    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Cập nhật địa chỉ IP
            $update_ip_sql = "UPDATE admin SET ip = ? WHERE username = ?";
            $update_stmt = $conn->prepare($update_ip_sql);
            $update_stmt->bind_param("ss", $ip, $username);
            $update_stmt->execute();

            // Đăng nhập thành công, chuyển đến trang 2fa
            $_SESSION['username'] = $username;
            header("Location: 2fa.php");
            exit();
        } else {
            $_SESSION['error'] = 'Invalid password.';
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = 'No user found.';
        header("Location: index.php");
        exit();
    }
}
$conn->close();
?>