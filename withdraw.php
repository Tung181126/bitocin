<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Load language file
$language = $_SESSION['language'] ?? 'en';
$lang_file = "languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

// Fetch user accounts
$stmt = $pdo->prepare("SELECT id, account_name, bank_name, account_number, account_holder_name FROM accounts WHERE user_id = ?");
$stmt->execute([$user_id]);
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Redirect if no accounts found
if (empty($accounts)) {
    header('Location: link_account.php');
    exit;
}

// Chỉ lấy tài khoản đầu tiên
$account = $accounts[0];

// Fetch user balance
$stmt = $pdo->prepare("SELECT balance, username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$balance = $user['balance'];
$username = $user['username'];

// Fetch exchange rate from settings
$stmt = $pdo->prepare("SELECT usd_rate FROM settings WHERE id = 1");
$stmt->execute();
$setting = $stmt->fetch(PDO::FETCH_ASSOC);
$exchange_rate = $setting['usd_rate'];

// Calculate the amount that can be withdrawn
$withdrawable_amount = $balance * $exchange_rate;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" href="img/logo.webp">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?= htmlspecialchars($lang['withdraw_money']) ?></title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function showAlert(type, message) {
      Swal.fire({
        icon: type,
        title: message,
        showConfirmButton: true,
        timer: 1500,
        iconHtml: '<i class="bi bi-exclamation-circle"></i>',
        customClass: {
          popup: 'animated tada'
        }
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('form');
      form.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(form);
        fetch('api/withdraw_handler.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          showAlert(data.type, data.message);
          if (data.type === 'success') {
            setTimeout(() => {
              window.location.href = 'withdraw.php';
            }, 1500);
          }
        })
        .catch(error => {
          showAlert('error', 'An error occurred');
        });
      });
    });
  </script>
  <style>
    body {
      background: linear-gradient(to right, #ffecd2, #fcb69f);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .card {
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
    }
    .back-icon {
      font-size: 2rem;
      color: #fcb69f;
      position: absolute;
      top: 20px;
      left: 20px;
    }
    .form-group label {
      font-weight: bold;
    }
    .form-control {
      border-radius: 10px;
    }
    .btn-custom {
      background-color: #fcb69f;
      border: none;
      color: white;
      border-radius: 10px;
      padding: 10px 20px;
      font-size: 1rem;
      transition: background-color 0.3s;
    }
    .btn-custom:hover {
      background-color: #ff9966;
    }
    .success-message {
      color: green;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <a href="home.php" class="back-icon"><i class="bi bi-arrow-left"></i> <?= htmlspecialchars($lang['back']) ?></a>
  <div class="card p-4">
    <div class="card-body">
      <h5 class="card-title text-center"><?= htmlspecialchars($lang['withdraw_money']) ?></h5>
      <form method="post" action="api/withdraw_handler.php">
        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
        <div class="form-group">
          <label><?= htmlspecialchars($lang['bank_name']) ?></label>
          <p class="form-control-plaintext"><?= htmlspecialchars($account['bank_name']) ?></p>
        </div>
        <div class="form-group">
          <label><?= htmlspecialchars($lang['account_number']) ?></label>
          <p class="form-control-plaintext"><?= htmlspecialchars($account['account_number']) ?></p>
        </div>
        <div class="form-group">
          <label><?= htmlspecialchars($lang['account_holder_name']) ?></label>
          <p class="form-control-plaintext"><?= htmlspecialchars($account['account_holder_name']) ?></p>
        </div>
        <div class="form-group">
          <label><?= htmlspecialchars($lang['withdrawable_amount']) ?></label>
          <p class="form-control-plaintext"><?= htmlspecialchars(number_format($withdrawable_amount, 2)) ?> VND</p>
        </div>
        <div class="form-group">
          <label for="amount"><?= htmlspecialchars($lang['amount']) ?></label>
          <div class="input-group">
            <input type="text" class="form-control" id="amount" name="amount" required>
            <div class="input-group-append">
              <button type="button" class="btn btn-outline-secondary" id="max-button"><?= htmlspecialchars($lang['max']) ?></button>
            </div>
          </div>
        </div>
        <div class="form-group">
          <a href="withdraw_history.php" class="btn btn-outline-secondary">
            <i class="fas fa-history"></i> <?= htmlspecialchars($lang['withdraw_history']) ?>
          </a>
        </div>
        <script>
          document.getElementById('amount').addEventListener('input', function (e) {
            let value = e.target.value;
            value = value.replace(/,/g, '');
            value = parseInt(value, 10);
            if (!isNaN(value)) {
              e.target.value = value.toLocaleString('en-US');
            } else {
              e.target.value = '';
            }
          });

          document.getElementById('max-button').addEventListener('click', function () {
            const maxAmount = <?= json_encode(number_format($withdrawable_amount, 2)) ?>;
            document.getElementById('amount').value = maxAmount;
          });
        </script>
        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
        <button type="submit" class="btn btn-custom btn-block"><?= htmlspecialchars($lang['withdraw']) ?></button>
      </form>
    </div>
  </div>
</body>
</html>