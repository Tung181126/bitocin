<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputCode = $_POST['code'];
    // kiểm tra mã xác nhận có trùng khớp với mã xác nhận trong csdl hay không
    $sql = "SELECT * FROM admin WHERE verification_code = '$inputCode' AND id=1";
    $result = $pdo->query($sql)->fetch();
    if ($result) {
        echo "success";
    } else {
        echo "failure";
    }
}
?>