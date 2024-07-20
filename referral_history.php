<?php
include_once 'db.php';
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
    header('Location: index.php');
    exit;
}

// Fetch referrals
$referrals = $pdo->prepare("SELECT * FROM users WHERE referrer_id = :user_id");
$referrals->execute(['user_id' => $user_id]);
$referrals = $referrals->fetchAll(PDO::FETCH_ASSOC);

// Fetch deposit and investment data for each referral
foreach ($referrals as &$referral) {
    // Fetch deposits
    $deposits = $pdo->prepare("SELECT * FROM deposits WHERE user_id = :user_id AND status = 'completed'");
    $deposits->execute(['user_id' => $referral['id']]);
    $referral['deposits'] = $deposits->fetchAll(PDO::FETCH_ASSOC);

    // Fetch investments
    $investments = $pdo->prepare("SELECT * FROM investments WHERE user_id = :user_id");
    $investments->execute(['user_id' => $referral['id']]);
    $referral['investments'] = $investments->fetchAll(PDO::FETCH_ASSOC);
}

// Function to calculate progress
function calculateProgress($items, $threshold) {
    $total = count($items);
    return min(100, ($total / $threshold) * 100);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử Giới thiệu</title>
    <!-- Bootstrap CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f8f9fa, #e0e0e0);
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        #loading {
            display: none;
        }
        .progress {
            height: 20px;
        }
    </style>
</head>
<body>
<i class="fa fa-arrow-left" onclick="goBack()"></i>
<script>
    function goBack() {
        window.history.back();
    }
</script>
<div class="container">
    <h1 class="my-4" id="referral-history-title">Lịch sử Giới thiệu</h1>
    <div id="no-referrals" class="text-center" style="display: none;">
        <h2 id="no-referrals-title">Không có người giới thiệu</h2>
        <p id="no-referrals-text">Có vẻ như bạn chưa giới thiệu ai cả.</p>
    </div>

    <div id="loading" class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only" id="loading-text">Đang tải...</span>
        </div>
    </div>

    <div id="referral-list">
        <?php foreach ($referrals as $referral): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" id="referral-title-<?php echo $referral['id']; ?>">Người giới thiệu #<?php echo htmlspecialchars($referral['username']); ?></h5>
                <p class="card-text" id="first-deposit-<?php echo $referral['id']; ?>">Nạp lần đầu: <?php echo htmlspecialchars($referral['deposits'][0]['amount'] ?? '0'); ?> USD</p>
                <div class="progress mb-2">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo calculateProgress($referral['deposits'], 1); ?>%;" aria-valuenow="<?php echo calculateProgress($referral['deposits'], 1); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo calculateProgress($referral['deposits'], 1); ?>%</div>
                </div>
                <p class="card-text" id="second-investment-<?php echo $referral['id']; ?>">Đầu tư 2 lần: <?php echo htmlspecialchars($referral['investments'][1]['amount'] ?? '0'); ?> USD</p>
                <div class="progress mb-2">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo calculateProgress($referral['investments'], 2); ?>%;" aria-valuenow="<?php echo calculateProgress($referral['investments'], 2); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo calculateProgress($referral['investments'], 2); ?>%</div>
                </div>
                <p class="card-text" id="fifth-investment-<?php echo $referral['id']; ?>">Đầu tư 5 lần: <?php echo htmlspecialchars($referral['investments'][4]['amount'] ?? '0'); ?> USD</p>
                <div class="progress mb-2">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo calculateProgress($referral['investments'], 5); ?>%;" aria-valuenow="<?php echo calculateProgress($referral['investments'], 5); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo calculateProgress($referral['investments'], 5); ?>%</div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Bootstrap JS và các dependency -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // JavaScript để xử lý trường hợp không có người giới thiệu và hiển thị loading
    const hasReferrals = <?php echo !empty($referrals) ? 'true' : 'false'; ?>;
    const isLoading = false; // Đổi thành true để mô phỏng đang tải

    if (isLoading) {
        document.getElementById('loading').style.display = 'flex';
        document.getElementById('referral-list').style.display = 'none';
        document.getElementById('no-referrals').style.display = 'none';
    } else if (!hasReferrals) {
        document.getElementById('no-referrals').style.display = 'block';
        document.getElementById('referral-list').style.display = 'none';
    } else {
        document.getElementById('loading').style.display = 'none';
    }

    // Function to change language
    function changeLanguage(lang) {
        fetch(`languages/${lang}.json`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('referral-history-title').innerText = data.referral_history_title;
                document.getElementById('no-referrals-title').innerText = data.no_referrals_title;
                document.getElementById('no-referrals-text').innerText = data.no_referrals_text;
                document.getElementById('loading-text').innerText = data.loading_text;

                <?php foreach ($referrals as $referral): ?>
                document.getElementById('referral-title-<?php echo $referral['id']; ?>').innerText = `${data.referral_title} #<?php echo htmlspecialchars($referral['username']); ?>`;
                document.getElementById('first-deposit-<?php echo $referral['id']; ?>').innerText = `${data.first_deposit}: <?php echo htmlspecialchars($referral['deposits'][0]['amount'] ?? '0'); ?> USD`;
                document.getElementById('second-investment-<?php echo $referral['id']; ?>').innerText = `${data.second_investment}: <?php echo htmlspecialchars($referral['investments'][1]['amount'] ?? '0'); ?> USD`;
                document.getElementById('fifth-investment-<?php echo $referral['id']; ?>').innerText = `${data.fifth_investment}: <?php echo htmlspecialchars($referral['investments'][4]['amount'] ?? '0'); ?> USD`;
                <?php endforeach; ?>

                // Lưu ngôn ngữ đã chọn vào session
                fetch('api/set_languages.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ language: lang })
                });
            })
            .catch(error => console.error('Error loading language file:', error));
    }
</script>
</body>
</html>