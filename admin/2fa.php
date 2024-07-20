<?php
session_start();
require 'db.php';

// Fetch bot token and chat ID from the database
$stmt = $pdo->query("SELECT bot_token, chat_id FROM bot_tokens LIMIT 1");
$bot = $stmt->fetch();

if ($bot) {
    $botToken = $bot['bot_token'];
    $chatId = $bot['chat_id'];

    // Generate a verification code
    $verificationCode = rand(100000, 999999);

    // Store the verification code in the session
    $_SESSION['verification_code'] = $verificationCode;

    // Store the verification code in the admin table
    $stmt = $pdo->prepare("UPDATE admin SET verification_code = ? WHERE id = 1"); // Assuming you are updating the admin with id 1
    $stmt->execute([$verificationCode]);

    // Send the verification code to the bot
    $message = "Mã xác nhận của sếp là: $verificationCode";
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);

    file_get_contents($url);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Verification</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        .body{
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .code-input {
            width: 40px;
            height: 40px;
            text-align: center;
            font-size: 24px;
            margin: 0 5px; /* Change margin to horizontal spacing */
        }
        .valid-code {
            border-color: green;
        }
        .invalid-code {
            border-color: red;
        }
        #code-inputs {
            display: flex;
            flex-direction: row; /* Change to row direction */
            justify-content: center; /* Center align inputs horizontally */
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Ensure full height for centering */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">2FA Verification</h2>
        <div class="form-group text-center">
            <label for="code">Verification Code</label>
            <div id="code-inputs">
                <input type="text" class="form-control code-input" maxlength="1" required>
                <input type="text" class="form-control code-input" maxlength="1" required>
                <input type="text" class="form-control code-input" maxlength="1" required>
                <input type="text" class="form-control code-input" maxlength="1" required>
                <input type="text" class="form-control code-input" maxlength="1" required>
                <input type="text" class="form-control code-input" maxlength="1" required>
            </div>
        </div>
    </div>
    <script>
        const inputs = document.querySelectorAll('.code-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                if (index === inputs.length - 1) {
                    const code = Array.from(inputs).map(input => input.value).join('');
                    if (code.length === 6) {
                        fetch('verify.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'code=' + code
                        })
                        .then(response => response.text())
                        .then(result => {
                            if (result.trim() === 'success') {
                                inputs.forEach(input => {
                                    input.classList.remove('invalid-code');
                                    input.classList.add('valid-code');
                                });
                                toastr.success('Xác minh thành công! Đang chuyển hướng đến trang chủ...', 'Thành công');
                                setTimeout(() => {
                                    window.location.href = 'home.php';
                                }, 3000);
                            } else {
                                inputs.forEach(input => {
                                    input.classList.remove('valid-code');
                                    input.classList.add('invalid-code');
                                });
                                toastr.error('Xác minh thất bại. Vui lòng thử lại.', 'Lỗi');
                            }
                        });
                    }
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
                inputs.forEach(input => {
                    input.classList.remove('invalid-code');
                });
            });
        });
    </script>
</body>
</html>