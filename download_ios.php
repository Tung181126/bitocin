<?php
$file = 'market720.mobileconfig'; // Đường dẫn tới file cần tải
if (file_exists($file)) {
    header('Content-Type: application/x-apple-aspen-config');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}
?>