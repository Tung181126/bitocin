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

// List of banks based on language
$banks = [];
if ($language == 'vi') {
    $banks = [
        'Vietcombank', 'Techcombank', 'BIDV', 'VietinBank', 'Agribank',
        'ACB', 'MB Bank', 'Sacombank', 'VPBank', 'HDBank', 'SHB', 
        'LienVietPostBank', 'Eximbank', 'TPBank', 'MSB', 'OceanBank', 
        'VIB', 'SeABank', 'BacABank', 'NamABank', 'SCB', 'PGBank',
        'Saigonbank', 'DongABank', 'ABBANK'
    ];
} elseif ($language == 'en') {
    $banks = [
        'Bank of America', 'Chase', 'Wells Fargo', 'Citibank', 'US Bank',
        'PNC Bank', 'Capital One', 'TD Bank', 'BB&T', 'SunTrust Bank'
    ];
} else {
    $banks = [
        'HSBC', 'Barclays', 'Standard Chartered', 'Deutsche Bank', 'BNP Paribas'
    ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" href="img/logo.webp">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?= htmlspecialchars($lang['link_account']) ?></title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jsdelivr.net"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
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
      <h5 class="card-title text-center"><?= htmlspecialchars($lang['link_account']) ?></h5>
      <?php if (isset($success_message)): ?>
      <p class="success-message text-center"><?= htmlspecialchars($success_message) ?></p>
      <?php endif; ?>
      <form method="post" action="api/link_account_handler.php">
        <div class="form-group">
          <label for="bank_name"><?= htmlspecialchars($lang['bank_name']) ?></label>
          <select class="form-control" id="bank_name" name="bank_name" required>
            <?php foreach ($banks as $bank): ?>
            <option value="<?= htmlspecialchars($bank) ?>"><?= htmlspecialchars($bank) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="account_number"><?= htmlspecialchars($lang['account_number']) ?></label>
          <input type="text" class="form-control" id="account_number" name="account_number" required>
        </div>
        <div class="form-group">
          <label for="account_holder_name"><?= htmlspecialchars($lang['account_holder_name']) ?></label>
          <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" required>
        </div>
        <button type="submit" class="btn btn-custom btn-block"><?= htmlspecialchars($lang['link_account']) ?></button>
      </form>
    </div>
  </div>
</body>
</html>