<?php
$host = 'localhost'; // Hoặc địa chỉ IP của máy chủ cơ sở dữ liệu
$db   = 'bit';
$user = 'root'; // Tên người dng cơ sở dữ liệu
$pass = ''; // Mật khẩu cơ sở dữ liệu
$charset = 'utf8mb4';

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>