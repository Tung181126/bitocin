<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Load language file
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'vi';
$lang_file = "languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

// Fetch user account and withdrawal history
$sql = "SELECT a.bank_name, a.account_number, w.amount, w.created_at, w.status
        FROM accounts a
        JOIN withdrawal_history w ON a.id = w.account_id
        WHERE a.user_id = ?
        ORDER BY w.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(array($user_id));
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="img/logo.webp" type="image/x-icon">
    <title><?php echo htmlspecialchars($lang['withdraw_history']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #e8cbc0, #636fa4);
            font-family: 'Helvetica Neue', sans-serif;
            color: #444;
            padding: 20px;
        }
        .container {
            margin-top: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            max-width: 100%;
            overflow-x: auto;
        }
        .status-icon {
            font-size: 1.5em;
            transition: transform 0.3s ease-in-out;
        }
        .status-icon:hover {
            transform: scale(1.2);
        }
        .pending {
            color: #ffc107;
        }
        .completed {
            color: #28a745;
        }
        .failed {
            color: #dc3545;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.3s;
        }
        .thead-light th {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
<i aria-hidden="true" class="fa fa-arrow-left" onclick="goBack()"></i>
<div class="container">
    <h1 class="text-center mb-4"><?php echo htmlspecialchars($lang['withdraw_history']); ?></h1>
    <div class="table-responsive">
        <table class="table table-bordered table-hover mt-4">
            <thead class="thead-light">
            <tr>
                <th><?php echo htmlspecialchars($lang['bank']); ?></th>
                <th><?php echo htmlspecialchars($lang['account_number']); ?></th>
                <th><?php echo htmlspecialchars($lang['amount']); ?></th>
                <th><?php echo htmlspecialchars($lang['created_at']); ?></th>
                <th><?php echo htmlspecialchars($lang['status']); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($result as $row) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['bank_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['account_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td class="<?php echo htmlspecialchars($row['status']); ?>">
                        <i class="status-icon <?php echo getStatusIconClass($row['status']); ?>"></i>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    function goBack() {
        window.history.back();
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
<?php
$stmt = null;
$pdo = null;

function getStatusIconClass($status) {
    switch ($status) {
        case 'pending':
            return 'fas fa-hourglass-half pending';
        case 'completed':
            return 'fas fa-check-circle completed';
        case 'failed':
            return 'fas fa-times-circle failed';
        default:
            return '';
    }
}
?>