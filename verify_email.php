<?php
session_start();
require 'db.php'; // Kết nối đến cơ sở dữ liệu

// Load language file based on session
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$lang_file = "languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

$user_id = $_SESSION['user_id'];

// Lấy email hiện tại
$query = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch();
?>

<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['verify_email_title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <a href="info.php" class="fa-solid fa-arrow-left"></a>
    <div class="container">
        <h1 class="text-center my-4"><?= $lang['verify_email_title'] ?></h1>
        <form id="update-email-form">
            <div class="mb-3">
                <label for="email" class="form-label"><?= $lang['your_email'] ?>:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><?= $lang['update_email'] ?></button>
        </form>
        <form id="verify-code-form" class="mt-4">
            <div class="mb-3">
                <label for="code" class="form-label"><?= $lang['verification_code'] ?>:</label>
                <input type="text" class="form-control" id="code" name="code" required>
            </div>
            <button type="submit" class="btn btn-primary"><?= $lang['verify'] ?></button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('update-email-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('update_email', true);

            fetch('api/api_verify_email.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Log the response for debugging
                Swal.fire({
                    icon: data.status === 'success' ? 'success' : 'error',
                    title: data.status === 'success' ? '<?= $lang['success'] ?>' : '<?= $lang['error'] ?>',
                    text: data.message
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '<?= $lang['error'] ?>',
                    text: '<?= $lang['request_error'] ?>'
                });
            });
        });

        document.getElementById('verify-code-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('api/api_verify_email.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Log the response for debugging
                Swal.fire({
                    icon: data.status === 'success' ? 'success' : 'error',
                    title: data.status === 'success' ? '<?= $lang['success'] ?>' : '<?= $lang['error'] ?>',
                    text: data.message
                }).then((result) => {
                    if (result.isConfirmed && data.status === 'success') {
                        window.location.href = 'info.php';
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '<?= $lang['error'] ?>',
                    text: '<?= $lang['request_error'] ?>'
                });
            });
        });
    </script>
</body>
</html>
