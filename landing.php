<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOBACCO Token</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        .hero-section {
            position: relative;
            height: 100vh;
            background: url('https://source.unsplash.com/1600x900/?technology,finance') no-repeat center center/cover;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-section h1 {
            font-size: 4rem;
            font-weight: bold;
        }
        .hero-section p {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .btn-primary {
            background: linear-gradient(45deg, #00b4db, #0083b0);
            border: none;
            padding: 15px 30px;
            font-size: 1.25rem;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #0083b0, #00b4db);
        }
        .features-section, .team-section, .roadmap-section, .join-section {
            padding: 60px 0;
        }
        .features-section .icon, .team-section .team-member img {
            max-width: 100px;
            margin-bottom: 20px;
        }
        .timeline {
            position: relative;
            padding: 0;
            list-style: none;
        }
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
            left: 50%;
            margin-left: -1px;
        }
        .timeline-item {
            padding: 20px 0;
            position: relative;
        }
        .timeline-item:nth-child(even) {
            text-align: left;
        }
        .timeline-item:nth-child(odd) {
            text-align: right;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: #007bff;
            border-radius: 50%;
            top: 20px;
            left: 50%;
            margin-left: -10px;
            z-index: 1;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">TOBACCO Token</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="help.php">Giới thiệu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Sản phẩm</a></li>
                    <li class="nav-item"><a class="nav-link" href="#roadmap">Tầm nhìn</a></li>
                    <li class="nav-item"><a class="nav-link" href="#team">Đội ngũ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#join">Tham gia</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Liên hệ</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex justify-content-center align-items-center">
        <div class="hero-content">
            <h1>Chào mừng đến với Ngân hàng phi tập trung TOBACCO</h1>
            <p>Định hình lại tương lai của blockchain và tài chính</p>
            <a href="index.php" class="btn btn-primary btn-lg">Tham Gia Ngay</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section text-center">
        <div class="container">
            <h2>Giới thiệu về TOBACCO</h2>
            <p>TOBACCO là ngân hàng phi tập trung được ra mắt bởi Tobacco Global Foundation và hàng chục nhóm đam mê công nghệ quốc tế trên khắp thế giới...</p>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section text-center bg-light">
        <div class="container">
            <h2>Các tính năng nổi bật</h2>
            <div class="row">
                <div class="col-md-4">
                    <img src="icon1.webp" alt="Blockchain" class="icon">
                    <h3>Công nghệ Blockchain</h3>
                    <p>Phân cấp, bảo mật, minh bạch...</p>
                </div>
                <div class="col-md-4">
                    <img src="icon2.png" alt="Finance" class="icon">
                    <h3>Tài chính phi tập trung</h3>
                    <p>Giải pháp tài chính toàn cầu...</p>
                </div>
                <div class="col-md-4">
                    <img src="icon3.png" alt="Products" class="icon">
                    <h3>Sản phẩm đa dạng</h3>
                    <p>Thẻ MasterCard, giao dịch tài chính tổng hợp...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Roadmap Section -->
    <section id="roadmap" class="roadmap-section text-center">
        <div class="container">
            <h2>Kế hoạch phát triển</h2>
            <ul class="timeline">
                <li class="timeline-item">
                    <h3>Quý 2 năm 2024</h3>
                    <p>Phát hành thẻ tín dụng vật lý</p>
                </li>
                <li class="timeline-item">
                    <h3>Quý 3 năm 2024</h3>
                    <p>Ra mắt Gamefi</p>
                </li>
                <!-- Thêm các mốc quan trọng khác -->
            </ul>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="team-section text-center bg-light">
        <div class="container">
            <h2>Đội ngũ của chúng tôi</h2>
            <div class="row">
                <div class="col-md-4">
                    <img src="member1.png" alt="John Doe" class="img-fluid rounded-circle">
                    <h3>John Doe</h3>
                    <p>Giám đốc R&D</p>
                </div>
                <div class="col-md-4">
                    <img src="member2.png" alt="Jane Smith" class="img-fluid rounded-circle">
                    <h3>Jane Smith</h3>
                    <p>Chuyên gia blockchain</p>
                </div>
                <!-- Thêm các thành viên khác -->
            </div>
        </div>
    </section>

    <!-- Join Section -->
    <section id="join" class="join-section text-center">
        <div class="container">
            <h2>Tham gia cùng chúng tôi</h2>
            <p>Đầu tư tối thiểu chỉ từ 10$, phần thưởng cấp bậc không giới hạn...</p>
            <a href="#contact" class="btn btn-primary btn-lg">Tham Gia Ngay</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3>TOBACCO Token</h3>
                    <p>Định hình lại tương lai của blockchain và tài chính</p>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><a href="#about" class="text-white">Giới thiệu</a></li>
                        <li><a href="#features" class="text-white">Sản phẩm</a></li>
                        <li><a href="#roadmap" class="text-white">Tầm nhìn</a></li>
                        <li><a href="#team" class="text-white">Đội ngũ</a></li>
                        <li><a href="#contact" class="text-white">Liên hệ</a></li>
                    </ul>
                    <div class="social-icons">
                        <a href="#"><img src="facebook-icon.png" alt="Facebook" style="width: 24px; height: 24px;"></a>
                        <a href="#"><img src="twitter-icon.png" alt="Twitter"></a>
                        <a href="#"><img src="telegram-icon.png" alt="Telegram"></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
