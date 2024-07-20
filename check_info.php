<?php
session_start();
include 'db.php';
// Load language file
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$lang_file = "languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['title']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <i class="bi bi-arrow-left" onclick="goBack()"></i>
    <div class="container mt-5">
        <div class="card p-4 mt-4">
            <h3 class="text-center"><?php echo $lang['enter_token']; ?></h3>
            <form id="check-info-form">
                <div class="mb-3">
                    <label for="token" class="form-label"><?php echo $lang['token']; ?></label>
                    <input type="text" class="form-control" id="token" required>
                </div>
                <div class="mb-3">
                    <label for="security_code" class="form-label"><?php echo $lang['security_code']; ?></label>
                    <input type="text" class="form-control" id="security_code" required>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $lang['check']; ?></button>
            </form>
        </div>
        <div class="user-info card p-4 mt-4" id="user-info" style="display: none;">
            <h3 class="text-center"><?php echo $lang['user_info']; ?></h3>
            <p><strong><?php echo $lang['username']; ?>:</strong> <span id="user-username"></span></p>
            <p><strong><?php echo $lang['balance']; ?>:</strong> <span id="user-balance"></span></p>
            <p><strong><?php echo $lang['invested']; ?>:</strong> <span id="user-invested"></span></p>
            <p><strong><?php echo $lang['investment_duration']; ?>:</strong> <span id="investment-duration"></span></p>
            <p><strong><?php echo $lang['expected_return']; ?>:</strong> <span id="expected-return"></span></p>
            <a href="download_app.php" class="btn btn-primary"><?php echo $lang['download_app']; ?></a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        document.getElementById('check-info-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const token = document.getElementById('token').value;
            const securityCode = document.getElementById('security_code').value;

            fetch(`api_check.php?token=${encodeURIComponent(token)}&security_code=${encodeURIComponent(securityCode)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        toastr.error(data.error, '<?php echo $lang['error']; ?>');
                    
                    } else {
                        document.getElementById('user-username').innerText = data.username;
                        document.getElementById('user-balance').innerText = data.balance;
                        document.getElementById('user-invested').innerText = data.invested;
                        document.getElementById('investment-duration').innerText = data.investment_duration;
                        document.getElementById('expected-return').innerText = data.expected_return;
                        document.getElementById('user-info').style.display = 'block';
                        toastr.success('<?php echo $lang['success_user_info']; ?>');
                    }
                })
                .catch(error => toastr.error('<?php echo $lang['error_fetching_user_info']; ?>' + error, 'Lỗi'));
        });
    </script>   
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>