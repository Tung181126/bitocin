<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Load language file
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$lang_file = "languages/{$language}.json";
$lang = json_decode(file_get_contents($lang_file), true);

// Get USDT address from settings
$query = $pdo->query("SELECT * FROM settings");
$settings = $query->fetch(PDO::FETCH_ASSOC);
$usdt_address = $settings['usdt_address'];
?>  
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" href="img/logo.webp">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $lang['deposit_usdt'] ?></title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
  <style>
    body {
      background: linear-gradient(to right, #ffecd2, #fcb69f);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .card {
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .btn-primary {
      background: #fcb69f;
      border: none;
    }
    .btn-primary:hover {
      background: #ffecd2;
      color: #333;
    }
    .wallet-icon {
      font-size: 3rem;
      color: #fcb69f;
    }
    .bi {
      font-size: 2rem;
      color: #fcb69f;
    }
    .history-icon {
      font-size: 2rem;
      color: #fcb69f;
      position: absolute;
      top: 20px;
      right: 20px;
    }
  </style>
</head>
<body>
  <div class="card p-4">
    <div class="card-body">
      <a href="home.php"><i class="bi bi-arrow-left"></i></a>
      <a href="deposit_history.php" class="history-icon"><i class="bi bi-clock-history"></i></a>
      <div class="text-center mb-4">
        <i class="fas fa-wallet wallet-icon"></i>
      </div>
      <h5 class="card-title text-center"><?= $lang['deposit_usdt'] ?></h5>
      <form id="usdt-form" enctype="multipart/form-data">
        <div class="form-group">
          <label for="usdt-address"><?= $lang['usdt_address'] ?></label>
          <input type="text" class="form-control" id="usdt-address" readonly>
        </div>
        <div class="form-group">
          <label for="amount"><?= $lang['amount'] ?></label>
          <input type="number" class="form-control" id="amount" placeholder="<?= $lang['enter_amount'] ?>">
        </div>
        <div class="form-group">
          <label for="deposit_image"><?= $lang['upload_image'] ?></label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="deposit_image" name="deposit_image">
            <label class="custom-file-label" for="deposit_image"><?= $lang['choose_file'] ?></label>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block"><?= $lang['submit'] ?></button>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
  <script>
    $(document).ready(function() {
      const usdtAddress = "<?= $usdt_address ?>";
      $('#usdt-address').val(usdtAddress);

      $('#usdt-form').on('submit', function(e) {
        e.preventDefault();
        let amount = $('#amount').val();
        let depositImage = $('#deposit_image')[0].files[0];

        if (amount && depositImage) {
          Swal.fire({
            title: '<?= $lang['processing'] ?>',
            text: '<?= $lang['processing_message'] ?>',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          let formData = new FormData();
          formData.append('amount', amount);
          formData.append('deposit_image', depositImage);

          setTimeout(function() {
            $.ajax({
              url: 'api/deposit_api.php',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response) {
                Swal.close();
                let result = JSON.parse(response);
                if (result.status === 'success') {
                  Swal.fire('Success', result.message, 'success');
                } else {
                  Swal.fire('Error', result.message, 'error');
                }
              },
              error: function() {
                Swal.close();
                Swal.fire('Error', 'An error occurred while processing your request.', 'error');
              }
            });
          }, 2000); // Delay of 2 seconds before processing
        } else {
          Swal.fire('Error', 'Please enter an amount and upload an image', 'error');
        }
      });

      bsCustomFileInput.init();
    });
  </script>
</body>
</html>