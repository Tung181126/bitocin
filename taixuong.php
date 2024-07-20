<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tobacco App Download</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f5f5f5;
        }
        .app-wrapper {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .app-logo {
            width: 80px;
            border-radius: 50%;
            margin-right: 20px;
        }
        .app-download-button {
            margin-left: 10px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .safe-app-icon {
            width: 20px;
            vertical-align: middle;
            margin-right: 5px;
        }
        .carousel-inner img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .app-section-divider {
            height: 2px;
            background-color: #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <div class="d-flex justify-content-end">
            <a href="index.php" class="btn btn-primary">Back</a>
        </div>
        <header class="d-flex align-items-center my-4">
            <img src="img/logo.webp" alt="Tobacco Logo" class="app-logo">
            <?php
            // Kiểm tra User-Agent của người dùng để xác định hệ điều hành
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
                // Nếu là iOS
                echo '<a href="download_ios.php" class="app-download-button btn btn-success">Download for iOS</a>';
            } elseif (strpos($userAgent, 'Android') !== false) {
                // Nếu là Android
                echo '<a href="download_android.php" class="app-download-button btn btn-success">Download for Android</a>';
            } else {
                // Nếu không phải iOS hoặc Android
                echo '<button class="app-download-button btn btn-success">Tải Xuống</button>';
            }
            ?>
        </header>
        <div class="app-body">
            <div class="app-info">
                <p><strong>Size:</strong> 5.5 MB</p>
                <p><strong>Language:</strong> English, Tiếng Việt, Chinese</p>
                <p><strong>Age Rating:</strong> Above 4 years old</p>
                <p><strong>Copyright:</strong> © 2024 Binice</p>
                <p><img src="wrapper.png" alt="Safe App Icon" class="safe-app-icon"> This is a safe application.</p>
            </div>
            <div class="app-rating my-3 text-center">
                <span class="stars">★★★★★</span>
                <span class="score">26k scores</span>
            </div>
            <div id="appScreenshots" class="carousel slide" data-ride="carousel">
                <h2 class="text-center mb-3">Screenshots</h2>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="screenshot1.png" alt="Screenshot 1" style="width: 100%; height: auto;">
                    </div>
                    <div class="carousel-item">
                        <img src="screenshot2.png" alt="Screenshot 2" style="width: 100%; height: auto;">
                    </div>
                    <div class="carousel-item">
                        <img src="screenshot3.png" alt="Screenshot 3" style="width: 100%; height: auto;">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#appScreenshots" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#appScreenshots" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
        <div class="app-section-divider"></div>
    </div>

    <!-- Bootstrap JS and dependencies CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
