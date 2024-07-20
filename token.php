<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require 'db.php'; // Kết nối đến cơ sở dữ liệu

// Kiểm tra xem người dùng đã nhận token chưa
$stmt = $pdo->prepare("SELECT token FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['token']) {
    header('Location: home.php');
    exit;
}

// Tạo token mới nhưng không lưu vào bảng token
function createToken($pdo, $userId) {
    $token = bin2hex(random_bytes(16)); // Tạo token ngẫu nhiên
    $securityCode = bin2hex(random_bytes(8)); // Tạo mã bảo mật ngẫu nhiên
    return [$token, $securityCode];
}

list($token, $securityCode) = createToken($pdo, $_SESSION['user_id']);

// Lấy ngôn ngữ đã chọn từ session
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'vi';
$languageFile = "languages/{$language}.json";
$languageData = json_decode(file_get_contents($languageFile), true);
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $languageData['token_page_title']; ?></title>
    <link rel="icon" href="img/logo.webp">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 100%;
            padding: 15px;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 400px;">
        <h1 class="text-center mb-4"><?php echo $languageData['receive_tokens']; ?></h1>
        <div class="alert alert-info" role="alert">
            <p><?php echo $languageData['token']; ?>: <strong><?php echo $token; ?></strong></p>
            <p><?php echo $languageData['security_code']; ?>: <strong><?php echo $securityCode; ?></strong></p>
        </div>
        <p><?php echo $languageData['save_info']; ?></p>
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="agree" name="agree">
            <label class="form-check-label" for="agree"><?php echo $languageData['agree_terms']; ?></label>
        </div>
        <button type="button" class="btn btn-success btn-block" onclick="confirmAgreement()"><?php echo $languageData['receive_bonus']; ?></button>
        <div id="result" class="mt-3"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmAgreement() {
            if (document.getElementById('agree').checked) {
                const token = "<?php echo $token; ?>";
                const securityCode = "<?php echo $securityCode; ?>";
                
                fetch('api/save_token.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ token: token, securityCode: securityCode })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showAlert('danger', data.error);
                    } else {
                        showAlert('success', '<?php echo $languageData['bonus_received']; ?>');
                        document.querySelector('.alert p:nth-child(1) strong').textContent = data.token;
                        document.querySelector('.alert p:nth-child(2) strong').textContent = data.securityCode;
                        setTimeout(() => {
                            window.location.href = 'home.php';
                        }, 2000); // Chuyển hướng sau 2 giây
                    }
                })
                .catch(error => {
                    showAlert('danger', '<?php echo $languageData['error_occurred']; ?>');
                });
            } else {
                showAlert('warning', '<?php echo $languageData['agree_terms_error']; ?>');
            }
        }

        function showAlert(type, message) {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = `<div class="alert alert-${type}" role="alert">${message}</div>`;
        }
    </script>
</body>
</html>