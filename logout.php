<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out</title>
    <script>
        // Clear login status from local storage
        localStorage.removeItem('isLoggedIn');
        // Redirect to index page after clearing local storage
        window.location.href = 'index.php';
    </script>
</head>
<body>
    <p>Logging out...</p>
</body>
</html>