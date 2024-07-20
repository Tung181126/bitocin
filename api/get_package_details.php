<?php
require '../db.php';
session_start();

if (isset($_SESSION['language'])) {
    $language = $_SESSION['language'];
} else {
    $language = 'en';
}

// Định nghĩa biến $lang dựa trên ngôn ngữ đã chọn
$lang = [
    'en' => [
        'current_investors' => 'Current Investors',
        'target_investors' => 'Target Investors',
        'people' => 'people',
        'current_investment' => 'Current Investment',
        'package_not_found' => 'Package not found',
        'invalid_package_id' => 'Invalid package ID'
    ],
    'vi' => [
        'current_investors' => 'Nhà đầu tư hiện tại',
        'target_investors' => 'Nhà đầu tư mục tiêu',
        'people' => 'người',
        'current_investment' => 'Đầu tư hiện tại',
        'package_not_found' => 'Không tìm thấy gói',
        'invalid_package_id' => 'ID gói không hợp lệ'
    ],
    'zh' => [
        'current_investors' => '当前投资者',
        'target_investors' => '目标投资者',
        'people' => '人',
        'current_investment' => '当前投资',
        'package_not_found' => '未找到套餐',
        'invalid_package_id' => '无效的套餐ID'
    ]
][$language];

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT name, description, current_investors, target_investors, current_investment FROM investment_packages WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $package = $stmt->fetch();

    if ($package) {
        $progress = ($package["current_investors"] / $package["target_investors"]) * 100;
        echo '<h5>' . htmlspecialchars($package["name"]) . '</h5>';
        echo '<p>' . htmlspecialchars($package["description"]) . '</p>';
        echo '<p><strong>' . $lang['current_investors'] . ':</strong> ' . htmlspecialchars($package["current_investors"]) . '</p>';
        echo '<p><strong>' . $lang['target_investors'] . ':</strong> ' . htmlspecialchars($package["target_investors"]) . ' ' . $lang['people'] . '</p>';
        echo '<p><strong>' . $lang['current_investment'] . ':</strong> $' . htmlspecialchars($package["current_investment"]) . '</p>';
        echo '<div class="progress">';
        echo '    <div class="progress-bar" role="progressbar" style="width: ' . $progress . '%;" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100">' . round($progress, 2) . '%</div>';
        echo '</div>';
    } else {
        echo $lang['package_not_found'];
    }
} else {
    echo $lang['invalid_package_id'];
}
?>
