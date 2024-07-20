<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$language = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['language']) ? $_SESSION['language'] : 'vi');
$_SESSION['language'] = $language;
$languageFile = "languages/{$language}.json";
$languageData = json_decode(file_get_contents($languageFile), true);
$lang = $languageData; // Thêm dòng này để định nghĩa biến $lang
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.webp">   
    <title><?php echo $lang['tobacco_token_project']; ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style> 
        body {
            font-family: 'Helvetica Neue', sans-serif;
            background-color: #f5f5f5; /* Màu nền nhẹ nhàng */
            margin: 0;
            padding: 20px;
        }
        h1 {
            margin-bottom: 30px;
            color: #2c3e50; /* Màu chữ đậm */
            font-weight: bold;
        }
        .accordion-button {
            background-color: #8e44ad; /* Màu nền sang trọng */
            color: #fff; /* Màu chữ trắng */
            border: 1px solid #8e44ad; /* Màu viền */
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .accordion-button:hover {
            background-color: #732d91; /* Màu khi hover */
            transform: scale(1.05); /* Hiệu ứng phóng to khi hover */
        }
        .card-header {
            background-color: #ecf0f1; /* Màu nền của header */
        }
        .card-body {
            background-color: #fff; /* Màu nền của body */
            color: #34495e; /* Màu chữ của body */
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card-body:hover {
            transform: translateY(-5px); /* Hiệu ứng nổi lên khi hover */
        }
        .fa-arrow-left {
            color: #2c3e50; /* Màu chữ đậm */
            font-size: 24px; /* Kích thước chữ */
            cursor: pointer; /* Con trỏ chuột thành con trỏ khi hover */
        }
        .fa-arrow-left:hover {
            color: #732d91; /* Màu khi hover */
        }
    </style>
</head>
<body>
<i aria-hidden="true" class="fa fa-arrow-left" onclick="goBack()"></i>
<div class="container">
    <h1><?php echo $lang['tobacco_token_info']; ?></h1>
    <img src="img/hello.jpg" alt="Logo" class="logo" style="width: 100%; height: auto;">
    <div class="accordion" id="accordionExample">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h2 class="mb-0">
                    <button class="btn btn-link accordion-button" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <?php echo $lang['project_background']; ?>
                    </button>
                </h2>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class="card-body">
                    <?php echo $lang['project_background_content']; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" id="headingTwo">
                <h2 class="mb-0">
                    <button class="btn btn-link accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <?php echo $lang['project_introduction']; ?>
                    </button>
                </h2>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                <div class="card-body">
                    <?php echo $lang['project_introduction_content']; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" id="headingThree">
                <h2 class="mb-0">
                    <button class="btn btn-link accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <?php echo $lang['vision_and_values']; ?>
                    </button>
                </h2>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                <div class="card-body">
                    <?php echo $lang['vision_and_values_content']; ?>
                </div>
            </div>
        </div>

        <!-- Thêm các phần tử accordion khác tương tự -->

    </div>
</div>
<script>
    function goBack() {
        window.history.back();
    }
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>