<?php
include 'db.php';
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'vi';
$sql = "SELECT * FROM users WHERE id = $user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$invite_link = $user['invite_link'];

$referral_sql = "SELECT COUNT(*) as referral_count FROM users WHERE referrer_id = :user_id";
$referral_stmt = $pdo->prepare($referral_sql);
$referral_stmt->execute(['user_id' => $user_id]);
$referral_count = $referral_stmt->fetch(PDO::FETCH_ASSOC)['referral_count'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"> 
    <title>Invite Friends</title>
    <link rel="icon" href="img/logo.webp">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS for luxury styling -->

    <link href="https://fonts.googleapis.com/css2?family=Trajan+Pro&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('img/invited.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            font-family: 'Arial', sans-serif;
            color: #ffffff;
            margin: 0; /* Ensure no margin for full screen */
            padding: 0; /* Ensure no padding for full screen */
            height: 100vh; /* Full height */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            margin-top: 50px;
        }
        .invite-card {
            background-color: rgba(0, 0, 0, 0.8); /* Darker background for contrast */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            color: #ffffff; /* White text for better contrast */
        }
        .invite-card h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-family: 'Trajan Pro', serif; /* Apply Trajan Pro font */
        }
        .invite-card p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .invite-card a {
            font-size: 1.2rem;
            color: #ffffff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .invite-card a:hover {
            background-color: #0056b3;
        }
        .highlight {
            font-family: 'Trajan Pro', serif; /* Apply Trajan Pro font */
            background-color: #000000;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 2rem;
            color: #007bff;
            cursor: pointer;
        }
        .referral-history-icon {
            font-size: 2rem;
            color: #007bff;
            cursor: pointer;
        }
    </style>
</head>
<body>
<i class="fas fa-arrow-left back-button" onclick="goBack()"></i>
<section id="invite-friends" class="text-center mt-5">
    <h2 class="text-success highlight">Mời Bạn Bè</h2>
    <p class="highlight">Hãy mời bạn bè của bạn tham gia cùng chúng tôi và nhận thưởng!</p>
    <p class="highlight">Bạn đã giới thiệu <strong><?php echo $referral_count; ?></strong> người bạn!</p>
    <i class="fas fa-history referral-history-icon" onclick="openReferralHistory()"></i>
    <div class="input-group mb-3">
        <input type="text" class="form-control" id="inviteLink2" value="<?php echo "index.php?invite_link=" . urlencode($invite_link); ?>" readonly>
        <div class="input-group-append">
            <button class="btn btn-success" type="button" id="copyInviteButton"><i class="fas fa-copy"></i></button>
        </div>
    </div>
</section>

<script>
    document.getElementById('copyInviteButton').addEventListener('click', function() {
        var copyText = document.getElementById("inviteLink2");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Đối với các thiết bị di động

        navigator.clipboard.writeText(copyText.value).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'Liên kết đã được sao chép!',
                text: 'Bạn đã sao chép liên kết mời bạn bè thành công!',
                position: 'topRight'
            });
        }).catch(function(error) {
            console.error('Lỗi khi sao chép văn bản: ', error);
        });
    });
</script>
<script>
    function goBack() {
        window.history.back();
    }
</script>
<script>
    function openReferralHistory() {
        window.location.href = 'referral_history.php';
    }
</script>
</body>
</html>