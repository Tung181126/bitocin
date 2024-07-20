<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập & Đăng Kí - Sensoda Token</title>
    <link rel="icon" href="img/logo.webp">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <style>
        .dropdown-item img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
        .icon-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .icon-container a img {
            width: 30px;
            height: 30px;
        }
    </style>
    <!-- Thêm SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">
    <script>
        // Function to get query parameter by name
        function getQueryParam(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        // Check if invite_link is present in the URL
        document.addEventListener('DOMContentLoaded', (event) => {
            const inviteLink = getQueryParam('invite_link');
            if (inviteLink) {
                document.getElementById('register-form').style.display = 'block';
                document.getElementById('login-form').style.display = 'none';
                document.getElementById('referral-code').value = inviteLink;
            }
            fetchUserInfo(inviteLink);
        });

        function fetchUserInfo(inviteLink) {
            fetch('api/get_user_info.php?invite_link=' + inviteLink)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        console.log('User data:', data); // Log the data for debugging
                        document.getElementById('user-username').innerText = data.username;
                        document.getElementById('user-balance').innerText = data.balance;
                        document.getElementById('user-invested').innerText = data.invested;
                        document.getElementById('user-info').style.display = 'block';
                    } else {
                        console.error('Failed to fetch user info');
                    }
                })
                .catch(error => console.error('Error fetching user info:', error));
        }
    </script>
</head>
<div style="position: absolute; top: 0; left: 0;">
    <a href="taixuong.php" style="background-color: #007bff; color: white; padding: 10px; border-radius: 5px; text-decoration: none;">
        <i class="bi bi-download" alt="Download"></i>
        <h5 id="download-app-text" style="display: inline; margin-left: 5px;">Tải App</h5>
    </a>
    </a>
</div>

<body>
    <div class="container mt-5">
        <!-- Language Selector -->
        <div class="dropdown mb-4 text-right">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <span id="languageDropdownText">Chọn Ngôn Ngữ</span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                <li><a class="dropdown-item" href="#" onclick="changeLanguage('en')">
                    <img src="img/flags/en.png" alt="English"> English
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="changeLanguage('vi')">
                    <img src="img/flags/vi.png" alt="Tiếng Việt"> Tiếng Việt
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="changeLanguage('zh')">
                    <img src="img/flags/zh.png" alt="中文"> 中文
                </a></li>
            </ul>
        </div>
        <!-- End Language Selector -->

        <div class="form-container card p-4" id="login-form">
            <div class="icon-container">
                <img src="img/logo.webp" alt="Sensoda Token Logo" class="logo mx-auto d-block">
            </div>
            <h2 class="text-center" id="login-title">Đăng Nhập</h2>
            <form id="loginForm" action="api/login.php" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" id="email-username" placeholder="Email/Username" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" placeholder="Mật khẩu" required>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember-me">
                    <label class="form-check-label" for="remember-me" id="remember-me-label">Nhớ mật khẩu</label>
                </div>
                <button type="submit" class="btn btn-success btn-block mt-3" id="login-button">Đăng Nhập</button>
                <a href="#" class="forgot-password d-block text-center mt-2" id="forgot-password">Quên mật khẩu?</a>
            </form>
            <div class="footer text-center mt-3">
                <p id="no-account">Chưa có tài khoản? <a href="#" onclick="showRegister()" id="register-link">Đăng Ký Ngay</a></p>
            </div>
            <div class="text-center mt-3">
                <a href="check_info.php">
                    <i class="bi bi-search" alt="Quick Check"></i>
                    <h5>Kiểm tra nhanh</h5>
                </a>
            </div>
        </div>

        <div class="form-container card p-4" id="register-form" style="display: none;">
            <img src="img/logo.webp" alt="Sensoda Token Logo" class="logo mx-auto d-block">
            <h2 class="text-center" id="register-title">Đăng Kí</h2>
            <form id="registerForm" action="api/register.php" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" id="full-name" placeholder="Tên đầy đủ" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="username" placeholder="Tên người dùng" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" id="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="number" class="form-control" id="phone" placeholder="Số điện thoại" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="register-password" placeholder="Mật khẩu" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="confirm-password" placeholder="Xác nhận mật khẩu" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="referral-code" name="referralCode" placeholder="Mã giới thiệu (nếu có)">
                </div>
                <!-- User Info Section -->
                <div id="user-info" class="card p-4 mt-4" style="background-color: #e0f7fa; display: none;">
                    <h3 class="text-center">Thông Tin Người Dùng</h3>
                    <p><strong>Username:</strong> <span id="user-username"></span></p>
                    <p><strong>Số dư:</strong> <span id="user-balance"></span></p>
                    <p><strong>Số tiền đã đầu tư:</strong> <span id="user-invested"></span></p>
                </div>
                <!-- End User Info Section -->
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms" id="terms-conditions">Tôi đồng ý với các <a href="#">Điều khoản</a> và <a href="#">Chính sách bảo mật</a></label>
                </div>
                <button type="submit" class="btn btn-success btn-block mt-3" id="register-button">Đăng Ký</button>
            </form>
            <div class="footer text-center mt-3">
                <p id="already-have-account">Đã có tài khoản? <a href="#" onclick="showLogin()" id="login-link">Đăng Nhập</a></p>
            </div>
         </div>

        <!-- User Info Section -->
        <div id="user-info" class="card p-4 mt-4" style="background-color: #e0f7fa; display: none;">
            <h3 class="text-center">Thông Tin Người Dùng</h3>
            <p><strong>Username:</strong> <span id="user-username"></span></p>
            <p><strong>Số dư:</strong> <span id="user-balance"></span></p>
            <p><strong>Số tiền đã đầu tư:</strong> <span id="user-invested"></span></p>
        </div>
        <!-- End User Info Section -->
    </div>

    <!-- Sử dụng phiên bản jQuery và Bootstrap tương thích -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        function showRegister() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        }

        function showLogin() {
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        }

        function changeLanguage(lang) {
            fetch(`languages/${lang}.json`)
                .then(response => response.json())
                .then(data => {
                    console.log('Language data loaded:', data); // Kiểm tra dữ liệu ngôn ngữ
                    document.getElementById('login-title').innerText = data.login;
                    document.getElementById('email-username').placeholder = data.email_username;
                    document.getElementById('password').placeholder = data.password;
                    document.getElementById('remember-me-label').innerText = data.remember_me;
                    document.getElementById('login-button').innerText = data.login;
                    document.getElementById('forgot-password').innerText = data.forgot_password;
                    document.getElementById('no-account').innerHTML = `${data.no_account} <a href="#" onclick="showRegister()" id="register-link">${data.register}</a>`;
                    document.getElementById('register-title').innerText = data.register;
                    document.getElementById('full-name').placeholder = data.full_name;
                    document.getElementById('email').placeholder = data.email;
                    document.getElementById('phone').placeholder = data.phone;
                    document.getElementById('register-password').placeholder = data.password;
                    document.getElementById('confirm-password').placeholder = data.confirm_password;
                    document.getElementById('terms-conditions').innerHTML = `${data.terms_conditions}`;
                    document.getElementById('register-button').innerText = data.register;
                    document.getElementById('already-have-account').innerHTML = `${data.already_have_account} <a href="#" onclick="showLogin()" id="login-link">${data.login}</a>`;
                    document.getElementById('languageDropdownText').innerText = data.language_selector;
                    document.getElementById('download-app-text').innerText = data.download_app;
                    window.languageData = data; // Lưu dữ liệu ngôn ngữ toàn cục
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

        // Set default language to Vietnamese
        changeLanguage('vi');

        // Function to set a cookie
        function setCookie(name, value, days) {
            const d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + d.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        // Function to get a cookie by name
        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        // Check if remember-me cookie exists and auto-fill the form
        document.addEventListener('DOMContentLoaded', (event) => {
            const emailOrUsername = getCookie('emailOrUsername');
            const password = getCookie('password');
            if (emailOrUsername && password) {
                document.getElementById('email-username').value = emailOrUsername;
                document.getElementById('password').value = password;
                document.getElementById('remember-me').checked = true;
            }

            // Check if user is already logged in
            const isLoggedIn = localStorage.getItem('isLoggedIn');
            if (isLoggedIn) {
                window.location.href = 'home.php'; // Redirect to home page
            }
        });

        // Handle login form submission
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const emailOrUsername = document.getElementById('email-username').value;
            const password = document.getElementById('password').value;
            const rememberMe = document.getElementById('remember-me').checked;

            if (rememberMe) {
                setCookie('emailOrUsername', emailOrUsername, 30); // Save for 30 days
                setCookie('password', password, 30); // Save for 30 days
            } else {
                setCookie('emailOrUsername', '', -1); // Delete cookie
                setCookie('password', '', -1); // Delete cookie
            }

            fetch('api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ emailOrUsername, password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    toastr.success(window.languageData.login_success);
                    localStorage.setItem('isLoggedIn', true); // Set login status in local storage
                    // Redirect based on the response
                    setTimeout(() => {
                        window.location.href = 'home.php'; // Redirect to home page
                    }, 2000);
                } else {
                    if (data.message === 'Tài khoản đã bị khoá' || data.message === '账户已锁定' || data.message === 'Account Locked') {
                        toastr.error(window.languageData.account_locked);
                    } else {
                        toastr.error(window.languageData.login_error);
                    }
                }
            })
            .catch(error => {
                toastr.error('Đã có lỗi xảy ra: ' + error.message);
            });
        });

        // Handle register form submission
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const fullName = document.getElementById('full-name').value;
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const referralCode = document.getElementById('referral-code').value;

            fetch('api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ fullName, username, email, phone, password, confirmPassword, referralCode })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response data:', data); // Kiểm tra dữ liệu phản hồi
                if (data.status === 'success') {
                    toastr.success(window.languageData.registration_success);
                    // Chuyển hướng đến trang token sau một khoảng thời gian ngắn
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    // Xử lý các thông báo lỗi cụ thể
                    if (data.message === 'email_exists') {
                        toastr.error(window.languageData.email_exists);
                    } else if (data.message === 'phone_exists') {
                        toastr.error(window.languageData.phone_exists);
                    } else {
                        toastr.error(data.message);
                    }
                }
            })
            .catch(error => {
                toastr.error('Đã có lỗi xảy ra: ' + error.message);
            });
        });
    </script>
</body>
</html>