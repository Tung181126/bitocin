<?php
session_start();
include '../db.php';

// Load translations based on selected language
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$translations = json_decode(file_get_contents("../languages/{$language}.json"), true);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $package_id = $_POST['package_id'];
    $package_name = $_POST['package'];
    $amount = $_POST['amount'];
    $cycle = $_POST['cycle'];
    $profit_rate = $_POST['profit_rate']; // Get the profit rate

    try {
        // Get minimum investment for the selected package
        $sql = "SELECT minimum_investment FROM investment_packages WHERE id = :package_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':package_id' => $package_id]);
        $package = $stmt->fetch();

        if ($package && $amount >= $package['minimum_investment']) {
            // Check user balance
            $sql = "SELECT balance FROM users WHERE id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch();

            if ($user && $user['balance'] >= $amount) {
                // Deduct amount from user balance
                $new_balance = $user['balance'] - $amount;
                $sql = "UPDATE users SET balance = :new_balance WHERE id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':new_balance' => $new_balance, ':user_id' => $user_id]);

                // Insert investment record
                $sql = "INSERT INTO investments (user_id, package_id, package_name, amount, cycle, start_date, daily_profit) VALUES (:user_id, :package_id, :package_name, :amount, :cycle, :start_date, :daily_profit)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':package_id' => $package_id,
                    ':package_name' => $package_name,
                    ':amount' => $amount,
                    ':cycle' => $cycle,
                    ':start_date' => date('Y-m-d'),
                    ':daily_profit' => $profit_rate
                ]);

                // Update current investors and current investment in investment_packages
                $sql = "UPDATE investment_packages SET current_investors = current_investors + 1, current_investment = current_investment + :amount WHERE id = :package_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':amount' => $amount, ':package_id' => $package_id]);

                $_SESSION['message'] = $translations['investment_successful'];
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = $translations['insufficient_balance'];
                $_SESSION['message_type'] = 'danger';
            }
        } else {
            $_SESSION['message'] = $translations['minimum_investment_not_met'];
            $_SESSION['message_type'] = 'danger';
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $translations['investment_failed'] . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }

    header('Location: ../home.php');
    exit;
}
?>