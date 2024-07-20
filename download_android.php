<?php
// Set the file name and path of the APK
$apk_file = 'market.apk';
$apk_name = 'market.apk';

// Check if the file exists
if (file_exists($apk_file)) {
    // Set headers to indicate the file type and force download 
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="' . basename($apk_name) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($apk_file));
    // Clear the output buffer
    ob_clean();
    // Read the file and output it to the browser
    readfile($apk_file);
    exit;
} else {
    // If the file doesn't exist, display an error message
    echo 'Error: The file does not exist.';
}
?>