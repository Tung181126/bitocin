<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch deposit history
$stmt = $pdo->prepare("SELECT * FROM deposits WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load language file
$language = $_SESSION['language'] ?? 'en';
$lang_file = "languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" href="img/logo.webp">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?= htmlspecialchars($lang['deposit_history']) ?></title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
      position: relative; /* Add this line */
    }
    .card {
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 800px;
    }
    .table {
      margin-top: 20px;
    }
    .table th, .table td {
      text-align: center;
    }
    .back-icon {
      font-size: 2rem;
      color: #fcb69f;
      position: absolute;
      top: 20px;
      left: 20px;
      z-index: 1000; /* Add this line */
    }
    .status-pending {
      color: orange;
    }
    .status-completed {
      color: green;
    }
    .status-failed {
      color: red;
    }
    .table-responsive {
      margin-top: 20px;
    }
    .table-dark th {
      background-color: #343a40;
      color: #fcb69f;
    }
  </style>
</head>
<body>
  <a href="deposit.php" class="back-icon"><i class="bi bi-arrow-left"></i> <?= htmlspecialchars($lang['back']) ?></a>
  <div class="card p-4">
    <div class="card-body">
      <h5 class="card-title text-center"><?= htmlspecialchars($lang['deposit_history']) ?></h5>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead class="table-dark">
            <tr>
              <th scope="col"><?= htmlspecialchars($lang['id']) ?></th>
              <th scope="col"><?= htmlspecialchars($lang['amount']) ?></th>
              <th scope="col"><?= htmlspecialchars($lang['status']) ?></th>
              <th scope="col"><?= htmlspecialchars($lang['date']) ?></th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($deposits)): ?>
            <tr>
              <td colspan="4"><?= htmlspecialchars($lang['no_deposits']) ?></td>
            </tr>
            <?php else: ?>
            <?php foreach ($deposits as $deposit): ?>
            <tr>
              <td><?= htmlspecialchars($deposit['id']) ?></td>
              <td><?= htmlspecialchars($deposit['amount']) ?></td>
              <td>
                <?php if ($deposit['status'] == 'pending'): ?>
                  <span class="status-pending"><?= htmlspecialchars($lang['pending']) ?> <i class="fas fa-spinner fa-spin"></i></span>
                <?php elseif ($deposit['status'] == 'completed'): ?>
                  <span class="status-completed"><?= htmlspecialchars($lang['completed']) ?> <i class="fas fa-check-circle"></i></span>
                <?php elseif ($deposit['status'] == 'failed'): ?>
                  <span class="status-failed"><?= htmlspecialchars($lang['failed']) ?> <i class="fas fa-times-circle"></i></span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($deposit['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>