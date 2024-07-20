<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'vi';
$languageFile = "languages/{$language}.json";
$languageData = json_decode(file_get_contents($languageFile), true);
$lang = $languageData; // Thêm dòng này để định nghĩa biến $lang
$user_id = $_SESSION['user_id']; // Giả sử bạn đã lưu user_id trong session
$sql = "SELECT ui.amount, ui.cycle, ui.start_date, ui.daily_profit, ip.name AS package_name FROM investments ui JOIN investment_packages ip ON ui.package_id = ip.id WHERE ui.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$investments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['user_investments_title']; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-vue@2.21.2/dist/bootstrap-vue.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <!--icon back-->
    <a href="home.php"><i class="material-icons">arrow_back</i></a>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3><?php echo $lang['user_investments_title']; ?></h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang['amount']; ?></th>
                            <th><?php echo $lang['cycle']; ?></th>
                            <th><?php echo $lang['start_date']; ?></th>
                            <th><?php echo $lang['daily_profit']; ?></th>
                            <th><?php echo $lang['package_name']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($investments) > 0): ?>
                            <?php foreach ($investments as $investment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($investment['amount']); ?></td>
                                <td><?php echo htmlspecialchars($investment['cycle']); ?></td>
                                <td><?php echo htmlspecialchars($investment['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($investment['daily_profit']); ?></td>
                                <td><?php echo htmlspecialchars($investment['package_name']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-danger"><?php echo $lang['no_investments']; ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>