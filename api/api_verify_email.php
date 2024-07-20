<?php
session_start();
require '../db.php'; // Kết nối đến cơ sở dữ liệu
require 'vendor/autoload.php'; // Autoload các thư viện, bao gồm PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 0);
error_reporting(E_ALL);

$user_id = $_SESSION['user_id'];
$response = ['status' => 'error', 'message' => ''];

// Load language file based on session
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$lang_file = "../languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'] ?? null;
        $code = $_POST['code'] ?? null;

        if (isset($_POST['update_email'])) {
            $update_query = $pdo->prepare("UPDATE users SET email = ?, email_verified = 0 WHERE id = ?");
            $update_query->execute([$email, $user_id]);

            $verification_code = rand(100000, 999999);
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $code_query = $pdo->prepare("UPDATE users SET verification_code = ?, verification_expires = ? WHERE id = ?");
            $code_query->execute([$verification_code, $expires, $user_id]);

            // Gửi mã xác minh qua email bằng SMTP của Gmail
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'th421891@gmail.com'; // Thay bằng email của bạn
                $mail->Password = 'yvjb oxmx xlgv dtvp'; // Thay bằng mật khẩu của bạn
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('th421891@gmail.com', 'Tobacos');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = '=?UTF-8?B?' . base64_encode($lang['verification_email_subject']) . '?=';
                $mail->Body = "
                <html>
                <head></head>
                <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;'>
                    <div style='width: 100%; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); text-align: center;'>
                        <div style='background-color: #007bff; padding: 10px; color: #fff; border-radius: 10px 10px 0 0;'>
                            <h1 style='margin: 0;'>{$lang['verification_email_title']}</h1>
                        </div>
                        <div style='padding: 20px; text-align: left;'>
                            <p style='font-size: 16px;'>{$lang['dear_user']}</p>
                            <p style='font-size: 16px;'>{$lang['your_verification_code']}:</p>
                            <p style='font-size: 24px; color: #007bff; margin: 20px 0;'>$verification_code</p>
                            <p style='font-size: 16px;'>{$lang['enter_code_in_app']}</p>
                            <p style='font-size: 16px;'>{$lang['thank_you']}</p>
                        </div>
                        <div style='background-color: #007bff; padding: 10px; color: #fff; border-radius: 0 0 10px 10px; font-size: 12px;'>
                            <p style='margin: 0;'>&copy; 2024 Tobacco Token. {$lang['all_rights_reserved']}</p>
                        </div>
                    </div>
                </body>
                </html>";

                $mail->send();
                $response['status'] = 'success';
                $response['message'] = $lang['verification_email_sent'];
            } catch (Exception $e) {
                $response['message'] = "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
            }
        } else {
            $code_query = $pdo->prepare("SELECT verification_code, verification_expires FROM users WHERE id = ?");
            $code_query->execute([$user_id]);
            $user = $code_query->fetch();

            if ($user && $user['verification_code'] == $code && strtotime($user['verification_expires']) > time()) {
                $update_query = $pdo->prepare("UPDATE users SET email_verified = 1, verification_code = NULL, verification_expires = NULL WHERE id = ?");
                $update_query->execute([$user_id]);

                $response['status'] = 'success';
                $response['message'] = $lang['email_verified_success'];
            } else {
                $response['message'] = $lang['invalid_code'];
            }
        }
    }
} catch (Exception $e) {
    $response['message'] = $lang['error_occurred'] . $e->getMessage();
}

echo json_encode($response);
?>
