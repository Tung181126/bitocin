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

// Lấy thông tin check-in từ cơ sở dữ liệu
$user_id = $_SESSION['user_id'];
$sql = "SELECT day FROM attendance WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$checkIns = $stmt->fetchAll(PDO::FETCH_COLUMN);

// lấy invite link
$sql = "SELECT invite_link FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$inviteLink = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0, minimum-scale=1.0">
    <title>Tobacco</title>
    <link rel="icon" href="img/logo.webp">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <?php if (isset($_SESSION['message'])): ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: '<?php echo $_SESSION['message_type'] == 'success' ? 'success' : 'error'; ?>',
                    title: '<?php echo $_SESSION['message_type'] == 'success' ? 'Success' : 'Error'; ?>',
                    text: '<?php echo $_SESSION['message']; ?>',
                    position: 'topRight'
                });
            });
        </script>
        <?php
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <header class="bg-success text-white text-center py-3">
        <img src="img/baner.webp" alt="Banner" class="img-fluid mb-3"> <!-- Thêm dòng này -->
        <h1>Tobacco</h1>
        <p><?php echo $lang['token_page_description']; ?></p>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="language-switcher">
            <select class="form-control" id="languageDropdown">
                <option value="?lang=vi" data-icon="img/flags/vi.png" <?php echo $language == 'vi' ? 'selected' : ''; ?>>Vietnamese</option>
                <option value="?lang=en" data-icon="img/flags/en.png" <?php echo $language == 'en' ? 'selected' : ''; ?>>English</option>
                <option value="?lang=zh" data-icon="img/flags/zh.png" <?php echo $language == 'zh' ? 'selected' : ''; ?>>Chinese</option>
            </select>
        </div>
    </header>

    <div class="collapse" id="navbarMenu">
        <div class="bg-light p-4 border rounded shadow-sm">
            <h4 class="text-success"><?php echo $lang['menu']; ?></h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <a href="info.php" class="text-success">
                        <i class="fas fa-user"></i> <?php echo $lang['personal_info']; ?>
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="history.php" class="text-success">
                        <i class="fas fa-history"></i> <?php echo $lang['investment_history']; ?>
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="help.php" class="text-success">
                        <i class="fas fa-question-circle"></i> <?php echo $lang['help']; ?>
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="deposit.php" class="text-success">
                        <i class="fas fa-wallet"></i> <?php echo $lang['deposit']; ?>
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="withdraw.php" class="text-success">
                        <i class="fas fa-money-bill-alt"></i> <?php echo $lang['withdraw']; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container mt-4">
        <?php
        require 'db.php';

        $sql = "SELECT id, name, description, current_investors, target_investors, current_investment, minimum_investment FROM investment_packages";
        $stmt = $pdo->query($sql);
        $packages = $stmt->fetchAll();
        ?>

        <section id="section2">
            <h2 class="text-success"><?php echo $lang['popular_investment_packages']; ?></h2>
            <div class="row">
                <?php
                if (count($packages) > 0) {
                    foreach ($packages as $row) {
                        $progress = ($row["current_investors"] / $row["target_investors"]) * 100;
                        echo '<div class="col-md-4">';
                        echo '    <div class="card mb-4">';
                        echo '        <div class="card-body">';
                        echo '            <h5 class="card-title">' . htmlspecialchars($row["name"]) . '</h5>';
                        echo '            <p class="card-text">' . htmlspecialchars($row["description"]) . '</p>';
                        echo '            <p class="card-text"><strong>' . $lang['current_investors'] . ':</strong> ' . htmlspecialchars($row["current_investors"]) . '</p>';
                        echo '            <p class="card-text"><strong>' . $lang['target_investors'] . ':</strong> ' . htmlspecialchars($row["target_investors"]) . ' ' . $lang['people'] . '</p>';
                        echo '            <p class="card-text"><strong>' . $lang['minimum_investment'] . ':</strong> $' . htmlspecialchars($row["minimum_investment"]) . '</p>'; // Thêm dòng này
                        echo '            <div class="progress">';
                        echo '                <div class="progress-bar" role="progressbar" style="width: ' . $progress . '%;" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100">' . round($progress, 2) . '%</div>';
                        echo '            </div>';
                        echo '            <button type="button" class="btn btn-success mt-3" data-toggle="modal" data-target="#investModal" data-package="' . htmlspecialchars($row["name"]) . '" data-id="' . $row["id"] . '">' . $lang['invest'] . '</button>';
                        echo '            <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#detailsModal" data-id="' . $row["id"] . '">' . $lang['view_details'] . '</button>';
                        echo '        </div>';
                        echo '    </div>';
                        echo '</div>';
                    }
                } else {
                    echo $lang['no_results'];
                }
                ?>
            </div>
        </section>

        <!-- Modal -->
        <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel"><?php echo $lang['investment_package_details']; ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Nội dung chi tiết sẽ được tải động ở đây -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $lang['close']; ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invest Modal -->
        <div class="modal fade" id="investModal" tabindex="-1" aria-labelledby="investModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="investModalLabel"><?php echo $lang['invest_in_package']; ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="api/invest.php" method="POST" class="bg-light p-4 border rounded">
                            <input type="hidden" id="package_id" name="package_id"> <!-- Thêm input ẩn để lưu package_id -->
                            <input type="hidden" id="profit_rate" name="profit_rate"> <!-- Thêm input ẩn để lưu profit_rate -->
                            <div class="form-group">
                                <label for="package"><?php echo $lang['investment_package']; ?>:</label>
                                <input type="text" id="package" name="package" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="amount"><?php echo $lang['investment_amount']; ?> ($):</label>
                                <input type="number" id="amount" name="amount" min="10" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="cycle"><?php echo $lang['investment_cycle']; ?>:</label>
                                <select id="cycle" name="cycle" class="form-control" required>
                                    <option value="7"><?php echo $lang['7_days']; ?></option>
                                    <option value="15"><?php echo $lang['15_days']; ?></option>
                                    <option value="30"><?php echo $lang['30_days']; ?></option>
                                    <option value="60"><?php echo $lang['60_days']; ?></option>
                                    <option value="90"><?php echo $lang['90_days']; ?></option>
                                    <option value="180"><?php echo $lang['180_days']; ?></option>
                                    <option value="365"><?php echo $lang['365_days']; ?></option>
                                </select>
                            </div>
                            <p id="profit"></p>
                            <button type="submit" class="btn btn-success"><?php echo $lang['invest_now']; ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="attendance-icon" id="attendanceIcon">
            <i class="fas fa-calendar-check"></i>
        </div>

        <!-- Attendance Modal -->
        <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="attendanceModalLabel"><?php echo $lang['attendance']; ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="attendance-container">
                            <div class="row">
                                <div class="col-6">
                                    <div class="day-container">
                                        <div class="day-name"><?php echo $lang['monday']; ?></div>
                                        <div class="day-checkbox">
                                            <button id="mon" class="btn btn-success check-in-btn" data-day="mon"><?php echo $lang['check_in']; ?></button>
                                        </div>
                                        <div class="gift-icon">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                    </div>
                                    <div class="day-container">
                                        <div class="day-name"><?php echo $lang['tuesday']; ?></div>
                                        <div class="day-checkbox">
                                            <button id="tue" class="btn btn-success check-in-btn" data-day="tue"><?php echo $lang['check_in']; ?></button>
                                        </div>
                                        <div class="gift-icon">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                    </div>
                                    <div class="day-container">
                                        <div class="day-name"><?php echo $lang['wednesday']; ?></div>
                                        <div class="day-checkbox">
                                            <button id="wed" class="btn btn-success check-in-btn" data-day="wed"><?php echo $lang['check_in']; ?></button>
                                        </div>
                                        <div class="gift-icon">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                    </div>
                                    <div class="day-container">
                                        <div class="day-name"><?php echo $lang['thursday']; ?></div>
                                        <div class="day-checkbox">
                                            <button id="thu" class="btn btn-success check-in-btn" data-day="thu"><?php echo $lang['check_in']; ?></button>
                                        </div>
                                        <div class="gift-icon">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="day-container">
                                        <div class="day-name"><?php echo $lang['friday']; ?></div>
                                        <div class="day-checkbox">
                                            <button id="fri" class="btn btn-success check-in-btn" data-day="fri"><?php echo $lang['check_in']; ?></button>
                                        </div>
                                        <div class="gift-icon">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                    </div>
                                    <div class="day-container">
                                        <div class="day-name"><?php echo $lang['saturday']; ?></div>
                                        <div class="day-checkbox">
                                            <button id="sat" class="btn btn-success check-in-btn" data-day="sat"><?php echo $lang['check_in']; ?></button>
                                        </div>
                                        <div class="gift-icon">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                    </div>
                                    <div class="day-container">
                                        <div class="day-name"><?php echo $lang['sunday']; ?></div>
                                        <div class="day-checkbox">
                                            <button id="sun" class="btn btn-success check-in-btn" data-day="sun"><?php echo $lang['check_in']; ?></button>
                                        </div>
                                        <div class="gift-icon">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $lang['close']; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <section id="section3">
            <h2 class="text-success"><?php echo $lang['investment_function']; ?></h2>
            <p><?php echo $lang['investment_description']; ?></p>
            <p><strong><?php echo $lang['investment_benefits']; ?>:</strong></p>
            <ul>
                <li><?php echo $lang['investment_benefit1']; ?></li>
                <li><?php echo $lang['investment_benefit2']; ?></li>
                <li><?php echo $lang['investment_benefit3']; ?></li>
            </ul>
            <section>
                <h3><?php echo $lang['invest_in_package']; ?></h3>
                <p><?php echo $lang['invest_in_package_description']; ?></p>
            </section>
        </section>
        <script>
            document.getElementById('copyInviteButton').addEventListener('click', function() {
                var copyText = document.getElementById("inviteLink2");
                copyText.select();
                copyText.setSelectionRange(0, 99999); // Đối với các thiết bị di động

                navigator.clipboard.writeText(copyText.value).then(function() {
                    Swal.fire({
                        icon: 'success',
                        title: '<?php echo $lang['link_copied']; ?>',
                        text: copyText.value,
                        position: 'topRight'
                    });
                }).catch(function(error) {
                    console.error('Lỗi khi sao chép văn bản: ', error);
                });
            });
        </script>
        <!-- Bootstrap JS and dependencies -->
    <script>
        $('#detailsModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var packageId = button.data('id');

            $.ajax({
                url: 'api/get_package_details.php',
                type: 'GET',
                data: { id: packageId },
                success: function (data) {
                    $('#detailsModal .modal-body').html(data);
                }
            });
        });

        const profitRates = {
            7: 0.8,
            15: 1.0,
            30: 1.2,
            60: 1.5,
            90: 2.0,
            180: 2.5,
            365: 3.0
        };

        $('#investModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var packageName = button.data('package');
            var packageId = button.data('id'); // Lấy package_id từ data-id
            var modal = $(this);
            modal.find('.modal-title').text('<?php echo $lang['invest_in_package']; ?> ' + packageName);
            modal.find('#package').val(packageName);
            modal.find('#package_id').val(packageId); // Gán giá trị package_id vào input ẩn
            updateProfitRate();
        });

        $('#cycle').on('change', function() {
            updateProfitRate();
        });

        function updateProfitRate() {
            var cycle = $('#cycle').val();
            var profitRate = profitRates[cycle];
            $('#profit').text('<?php echo $lang['profit_rate']; ?>: ' + profitRate + '%/<?php echo $lang['day']; ?>');
            $('#profit_rate').val(profitRate); // Set the hidden input value
        }
    </script>
    <script>
        $(document).ready(function() {
            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var baseUrl = state.element.getAttribute('data-icon');
                var $state = $(
                    '<span><img src="' + baseUrl + '" class="img-flag" /> ' + state.text + '</span>'
                );
                return $state;
            };

            $('#languageDropdown').select2({
                templateResult: formatState,
                templateSelection: formatState
            });

            $('#languageDropdown').on('change', function() {
                window.location.href = $(this).val();
            });
        });
    </script>
    <!-- Invite Friends -->
    <div class="invite-friends" id="inviteFriends">
        <i class="fas fa-users"></i>
    </div>

    <!-- Support Icon -->
    <div class="support-icon" id="supportIcon">
        <i class="fas fa-headset"></i>
    </div>

    <!-- Chat Window -->
    <div class="chat-window" id="chatWindow">
        <iframe src="chatnguoidung.php"></iframe>
        <div class="resize-handle" id="resizeHandle"></div>
    </div>

    <script>
        // Make the support icon draggable
        const supportIcon = document.getElementById('supportIcon');
        const chatWindow = document.getElementById('chatWindow');
        const resizeHandle = document.getElementById('resizeHandle');
        let isDragging = false;

        supportIcon.addEventListener('mousedown', function(e) {
            isDragging = true;
            let offsetX = e.clientX - supportIcon.getBoundingClientRect().left;
            let offsetY = e.clientY - supportIcon.getBoundingClientRect().top;

            function onMouseMove(e) {
                if (isDragging) {
                    supportIcon.style.left = `${e.clientX - offsetX}px`;
                    supportIcon.style.top = `${e.clientY - offsetY}px`;
                }
            }

            function onMouseUp() {
                isDragging = false;
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            }

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        });

        // Check if the device is mobile based on screen width
        function isMobileDevice() {
            return window.innerWidth <= 768; // You can adjust the width threshold as needed
        }

        // Toggle chat window on click
        supportIcon.addEventListener('click', function() {
            if (isMobileDevice()) {
                window.location.href = 'chatnguoidung.php';
            } else {
                if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
                    chatWindow.style.display = 'block';
                } else {
                    chatWindow.style.display = 'none';
                }
            }
        });

        // Handle resizing
        resizeHandle.addEventListener('mousedown', function(e) {
            e.preventDefault();
            document.addEventListener('mousemove', resizeChatWindow);
            document.addEventListener('mouseup', stopResizing);
        });

        function resizeChatWindow(e) {
            chatWindow.style.width = `${e.clientX - chatWindow.getBoundingClientRect().left}px`;
            chatWindow.style.height = `${e.clientY - chatWindow.getBoundingClientRect().top}px`;
        }

        function stopResizing() {
            document.removeEventListener('mousemove', resizeChatWindow);
            document.removeEventListener('mouseup', stopResizing);
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const attendanceIcon = document.getElementById('attendanceIcon');
            const attendanceModal = new bootstrap.Modal(document.getElementById('attendanceModal'));

            attendanceIcon.addEventListener('click', function() {
                attendanceModal.show();
            });

            const days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
            const today = new Date().getDay();
            const todayId = days[today];

            // Disable all buttons initially
            days.forEach(day => {
                const button = document.getElementById(day);
                if (button) {
                    button.disabled = true;
                    button.classList.add('btn-secondary');
                    button.classList.remove('btn-success');
                }
            });

            // Enable today's button if not checked in
            const todayButton = document.getElementById(todayId);
            if (todayButton && !checkIns.includes(todayId)) {
                todayButton.disabled = false;
                todayButton.classList.add('btn-success');
                todayButton.classList.remove('btn-secondary');
            }

            // Mark checked-in days
            checkIns.forEach(day => {
                const button = document.getElementById(day);
                if (button) {
                    button.disabled = true;
                    button.classList.add('btn-secondary');
                    button.classList.remove('btn-success');
                }
            });

        });
        const checkIns = <?php echo json_encode($checkIns); ?>;
    </script>
    <script src="js/home.js"></script>
</body>
</html>
