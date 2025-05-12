<?php
error_log("Starting messages.php execution");

// Không gọi session_start() ở đây vì session đã được khởi động trong connect.php hoặc MessageController.php
$baseUrl = '/nurseborn';

// Lấy thông tin người dùng từ session
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;

// Xử lý ảnh đại diện và thông tin người dùng
$avatar = ($nurseProfile && !empty($nurseProfile['profile_image'])) ? htmlspecialchars($nurseProfile['profile_image']) : ($baseUrl . '/static/assets/img/avatars/default_profile.jpg');
$fullName = $user ? htmlspecialchars($user['full_name']) : 'Người dùng';
$role = $user ? htmlspecialchars($user['role']) : 'Khách';

error_log("Rendering messages.php with user: " . print_r($user, true));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin nhắn - NurseBorn</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/core.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/theme-default.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/css/demo.css">
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/libs/apex-charts/apex-charts.css">
    <!-- Custom CSS -->
    <style>
        /* Đảm bảo box-sizing cho toàn bộ trang */
        *, *:before, *:after {
            box-sizing: border-box;
        }

        /* Tùy chỉnh tổng thể */
        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #dcfce7 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1f2a44;
            line-height: 1.6;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Card chính */
        .card {
            border: none;
            border-radius: 0.9375rem;
            background-color: #fff;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            margin-bottom: 1.875rem;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-0.3125rem);
        }
        .card-body {
            padding: 1.875rem;
        }

        /* Tiêu đề */
        .card-header {
            background: linear-gradient(45deg, #2563eb, #22c55e);
            color: #fff;
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
            padding: 1.25rem;
            border-top-left-radius: 0.9375rem;
            border-top-right-radius: 0.9375rem;
            box-shadow: 0 0.25rem 0.625rem rgba(0, 0, 0, 0.15);
            position: relative;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-1.25rem); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Khu vực chat */
        .chat-container {
            display: flex;
            height: 70vh;
            border: 1px solid #d1d3e2;
            border-radius: 0.5rem;
            background: linear-gradient(145deg, #f8fafc, #e2e8f0);
            margin: 0;
            padding: 0;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
        }

        /* Sidebar */
        .sidebar {
            width: 30%;
            background: linear-gradient(to bottom, #f1f5f9, #e2e8f0);
            padding: 1.25rem;
            border-right: 1px solid #d1d3e2;
            overflow-y: auto;
            min-height: 100%;
        }
        .sidebar h5 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.9375rem;
            color: #2d3748;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar li {
            padding: 0.75rem 0.9375rem;
            cursor: pointer;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
            transition: background-color 0.3s ease, transform 0.3s ease;
            color: #4a5568;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 1rem;
            display: block;
        }
        .sidebar li:hover {
            background-color: #e2e8f0;
            transform: translateX(0.3125rem);
        }
        .sidebar li.selected {
            background-color: #cbd5e0;
            font-weight: 600;
            color: #1a202c;
        }

        /* Khu vực chat chính */
        .chat-area {
            width: 70%;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
        }
        .chat-area h5 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.9375rem;
            color: #2d3748;
        }

        /* Khu vực tin nhắn */
        .messages {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 1.25rem;
            padding: 0.9375rem;
            background-color: #edf2f7;
            border-radius: 0.375rem;
            box-shadow: inset 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
        }
        .message {
            margin-bottom: 0.9375rem;
            padding: 0.625rem 0.9375rem;
            border-radius: 0.75rem;
            max-width: 70%;
            line-height: 1.4;
            animation: slideIn 0.3s ease-in-out;
        }
        @keyframes slideIn {
            0% { opacity: 0; transform: translateY(0.625rem); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .message.sent {
            background: linear-gradient(45deg, #4299e1, #3182ce);
            color: #ffffff;
            margin-left: auto;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }
        .message.received {
            background: linear-gradient(45deg, #e2e8f0, #d1d5db);
            margin-right: auto;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }
        .message p {
            margin: 0;
            font-size: 0.95rem;
        }
        .message span {
            font-size: 0.75rem;
            color: #6b7280;
            display: block;
            margin-top: 0.3125rem;
            opacity: 0.8;
        }

        /* Input và nút gửi tin nhắn */
        .message-input {
            display: flex;
            gap: 0.625rem;
            align-items: center;
        }
        .message-input input {
            flex: 1;
            padding: 0.625rem 0.9375rem;
            border: 1px solid #d1d3e2;
            border-radius: 0.375rem;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .message-input input:focus {
            border-color: #4299e1;
            box-shadow: 0 0 0.3125rem rgba(66, 153, 225, 0.3);
        }
        .message-input button {
            padding: 0.625rem 1.5625rem;
            background: linear-gradient(45deg, #4299e1, #3182ce);
            color: #ffffff;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .message-input button:hover {
            background: linear-gradient(45deg, #3182ce, #2b6cb0);
            transform: scale(1.02);
            box-shadow: 0 0.25rem 0.75rem rgba(66, 153, 225, 0.3);
        }

        /* Thông báo lỗi và không có dữ liệu */
        .error-message {
            color: #e53e3e;
            font-size: 0.95rem;
            padding: 0.625rem;
            text-align: center;
        }
        .no-data {
            color: #718096;
            font-size: 0.95rem;
            padding: 0.625rem;
            text-align: center;
        }

        /* Menu và Navbar Styles (Đồng bộ với đoạn mã bạn cung cấp) */
        .layout-menu {
            background: linear-gradient(180deg, #1e3c72 0%, #2a69ac 100%) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            font-family: 'Poppins', sans-serif;
            width: 250px;
        }
        .app-brand {
            padding: 20px 15px;
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .app-brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .app-brand-logo img {
            border-radius: 50%;
            border: 2px solid #fff;
        }
        .app-brand-text {
            color: #fff;
            font-size: 1.2rem;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
        .layout-menu-toggle i {
            color: #fff;
        }
        .menu-inner-shadow {
            display: none;
        }
        .menu-inner {
            padding: 10px 0 !important;
        }
        .menu-header {
            padding: 10px 15px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .menu-header-text {
            color: rgba(255, 255, 255, 0.7);
        }
        .menu-item {
            margin: 5px 0;
        }
        .menu-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            font-weight: 500;
            transition: background 0.3s ease, color 0.3s ease, padding-left 0.3s ease;
            border-radius: 8px;
            margin: 0 10px;
        }
        .menu-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            padding-left: 25px;
        }
        .menu-item.active .menu-link {
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .menu-icon {
            margin-right: 15px;
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.8);
            transition: color 0.3s ease;
        }
        .menu-link:hover .menu-icon {
            color: #fff;
        }
        .menu-item.active .menu-icon {
            color: #fff;
        }
        .menu-sub {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            margin: 0 10px;
            padding: 5px 0;
        }
        .menu-sub .menu-link {
            padding: 10px 30px;
            font-size: 0.95rem;
        }

        /* Navbar Styles */
        .layout-navbar {
            background: linear-gradient(90deg, #1e3c72 0%, #2a69ac 100%) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 10px 20px;
            border-radius: 0 0 15px 15px;
        }
        .layout-menu-toggle i {
            color: #fff;
            font-size: 1.5rem;
        }
        .navbar-nav-right {
            display: flex;
            align-items: center;
        }
        .dropdown-user .nav-link {
            padding: 8px 15px;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.9);
            transition: background 0.3s ease, color 0.3s ease;
        }
        .dropdown-user .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 8px;
        }
        .avatar-online {
            position: relative;
        }
        .avatar-online img {
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .dropdown-menu {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-top: 10px;
            border: none;
        }
        .dropdown-item {
            padding: 10px 20px;
            color: #343a40;
            font-size: 0.95rem;
            font-weight: 500;
            transition: background 0.3s ease, color 0.3s ease;
        }
        .dropdown-item:hover {
            background: #f8f9fa;
            color: #0d6efd;
        }
        .dropdown-item i {
            color: #6c757d;
            margin-right: 10px;
        }
        .dropdown-item:hover i {
            color: #0d6efd;
        }
        .dropdown-divider {
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        .fw-semibold {
            color: #343a40;
            font-size: 1rem;
        }
        .text-muted {
            color: #6c757d !important;
            font-size: 0.85rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .layout-menu {
                width: 100%;
                position: fixed;
                top: 0;
                left: -100%;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            .layout-menu.menu-open {
                left: 0;
            }
            .layout-navbar {
                border-radius: 0;
            }
            .navbar-nav-right {
                padding: 5px 0;
            }
        }
        @media (max-width: 768px) {
            .avatar-online img {
                width: 35px !important;
                height: 35px !important;
            }
            .dropdown-item {
                font-size: 0.9rem;
                padding: 8px 15px;
            }
        }
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Placeholder để JavaScript thêm menu -->
        <div id="menu-placeholder"></div>

        <div class="layout-page">
            <!-- Placeholder để JavaScript thêm navbar -->
            <div id="navbar-placeholder"></div>

            <div class="content-wrapper">
                <div class="content-xxl flex-grow-1 container-p-y">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Tin nhắn</h5>
                        </div>
                        <div class="card-body">
                            <div class="chat-container">
                                <div class="sidebar">
                                    <h5>Danh sách trò chuyện</h5>
                                    <ul id="conversation-list"></ul>
                                </div>
                                <div class="chat-area">
                                    <h5 id="chat-with">Chọn một người để trò chuyện</h5>
                                    <div class="messages" id="messages"></div>
                                    <div class="message-input">
                                        <input type="text" id="message-input" placeholder="Nhập tin nhắn...">
                                        <button onclick="sendMessage()">Gửi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/popper/popper.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/menu.js"></script>
<!-- Vendors JS -->
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<!-- Main JS -->
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
<!-- Page JS -->
<script src="<?php echo $baseUrl; ?>/static/assets/js/dashboards-analytics.js"></script>
<!-- GitHub Buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script>
    const baseUrl = '<?php echo $baseUrl; ?>';
    const currentUser = {
        userId: <?php echo json_encode($user['user_id'] ?? null); ?>,
        username: <?php echo json_encode($user['username'] ?? null); ?>,
        role: <?php echo json_encode($user['role'] ?? null); ?>,
        fullName: <?php echo json_encode($user['full_name'] ?? 'Người dùng'); ?>,
        profileImage: <?php echo json_encode($avatar); ?>
    };
    const hasSelectedPartner = <?php echo json_encode(isset($data['selectedPartner']) && $data['selectedPartner'] !== null); ?>;
    let selectedPartner = null;
    if (hasSelectedPartner) {
        selectedPartner = <?php echo json_encode($data['selectedPartner'] ?? null); ?>;
    }
    let selectedPartnerId = null;
    let lastMessageId = 0;
    const displayedMessages = new Set();

    // Debug session ngay khi trang tải
    console.log('Current user data:', currentUser);
    console.log('Cookies:', document.cookie);
    console.log('Initial selected partner:', selectedPartner);

    // Hàm để thêm menu dựa trên vai trò (Đồng bộ với đoạn mã bạn cung cấp)
    function loadMenuBasedOnRole() {
        const menuPlaceholder = document.getElementById('menu-placeholder');

        if (currentUser.role === 'NURSE') {
            menuPlaceholder.innerHTML = `
                <aside id="layout-menu" class="layout-menu menu-vertical menu">
                    <div class="app-brand demo">
                        <a href="?action=home" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <img src="${baseUrl}/static/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
                            </span>
                            <span class="app-brand-text demo text-body fw-bolder text-uppercase">NURSEBORN</span>
                        </a>
                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                            <i class="bx bx-chevron-left bx-sm align-middle"></i>
                        </a>
                    </div>

                    <div class="menu-inner-shadow"></div>

                    <ul class="menu-inner py-1">
                        <!-- Dashboard -->
                        <li class="menu-item ${window.location.search.includes('action=home') ? 'active' : ''}">
                            <a href="?action=home" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                                <div>Trang chủ</div>
                            </a>
                        </li>

                        <!-- Schedule -->
                        <li class="menu-item ${window.location.search.includes('action=nurse_schedule') ? 'active' : ''}">
                            <a href="?action=nurse_schedule" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-calendar"></i>
                                <div>Lịch Làm Việc</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=notifications') ? 'active' : ''}">
                            <a href="?action=notifications" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-bell"></i>
                                <div>Thông Báo</div>
                            </a>
                        </li>

                        <!-- Pending Bookings -->
                        <li class="menu-item ${window.location.search.includes('action=pending_bookings') ? 'active' : ''}">
                            <a href="?action=pending_bookings" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div>Lịch Đặt Chờ Xác Nhận</div>
                            </a>
                        </li>

                        <!-- Availability -->
                        <li class="menu-item ${window.location.search.includes('action=nurse_availability') ? 'active' : ''}">
                            <a href="?action=nurse_availability" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check-square"></i>
                                <div>Quản Lý Lịch Làm Việc</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=messages') ? 'active' : ''}">
                            <a href="?action=messages" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check-square"></i>
                                <div>Trò chuyện</div>
                            </a>
                        </li>

                        <li class="menu-item ${window.location.search.includes('action=nurse_income') ? 'active' : ''}">
                            <a href="?action=nurse_income" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-wallet"></i>
                                <div>Thống Kê Thu Nhập</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=review_nurse') ? 'active' : ''}">
                            <a href="?action=review_nurse" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-wallet"></i>
                                <div>Đánh Giá</div>
                            </a>
                        </li>

                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Tài Khoản</span>
                        </li>
                        <!-- Account Settings -->
                        <li class="menu-item ${window.location.search.includes('action=nurse_profile') || window.location.search.includes('action=update_nurse') ? 'active open' : ''}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div>Cài Đặt Tài Khoản</div>
                            </a>
                            <ul class="menu-sub">
                                <li class="menu-item ${window.location.search.includes('action=nurse_profile') ? 'active' : ''}">
                                    <a href="?action=nurse_profile" class="menu-link">
                                        <div>Tài Khoản</div>
                                    </a>
                                </li>
                                <li class="menu-item ${window.location.search.includes('action=update_nurse') ? 'active' : ''}">
                                    <a href="?action=update_nurse" class="menu-link">
                                        <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                        <div>Cập nhật hồ sơ</div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Logout -->
                        <li class="menu-item">
                            <a href="?action=logout" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-log-out"></i>
                                <div>Đăng Xuất</div>
                            </a>
                        </li>
                    </ul>
                </aside>
            `;
        } else if (currentUser.role === 'FAMILY') {
            menuPlaceholder.innerHTML = `
                <aside id="layout-menu" class="layout-menu menu-vertical menu">
                    <div class="app-brand demo">
                        <a href="?action=family_home" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <img src="${baseUrl}/static/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
                            </span>
                            <span class="app-brand-text demo text-body fw-bolder text-uppercase">NURSEBORN</span>
                        </a>
                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                            <i class="bx bx-chevron-left bx-sm align-middle"></i>
                        </a>
                    </div>

                    <div class="menu-inner-shadow"></div>

                    <ul class="menu-inner py-1">
                        <!-- Dashboard -->
                        <li class="menu-item ${window.location.search.includes('action=family_home') ? 'active' : ''}">
                            <a href="?action=family_home" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                                <div>Trang chủ</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=bookings') ? 'active' : ''}">
                            <a href="?action=bookings" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div>Danh Sách Lịch Đặt</div>
                            </a>
                        </li>

                        <!-- Family Management -->
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Quản lý Gia đình</span>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=nursepage') ? 'active' : ''}">
                            <a href="?action=nursepage" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-search"></i>
                                <div>Đặt dịch vụ</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=messages') ? 'active' : ''}">
                            <a href="?action=messages" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check-square"></i>
                                <div>Trò chuyện</div>
                            </a>
                        </li>

                        <!-- Account Settings -->
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Tài khoản</span>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=user_profile') || window.location.search.includes('action=update_user') ? 'active open' : ''}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div>Cài Đặt Tài Khoản</div>
                            </a>
                            <ul class="menu-sub">
                                <li class="menu-item ${window.location.search.includes('action=user_profile') ? 'active' : ''}">
                                    <a href="?action=user_profile" class="menu-link">
                                        <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                        <div>Hồ sơ</div>
                                    </a>
                                </li>
                                <li class="menu-item ${window.location.search.includes('action=update_user') ? 'active' : ''}">
                                    <a href="?action=update_user" class="menu-link">
                                        <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                        <div>Cập nhật hồ sơ</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=notifications') ? 'active' : ''}">
                            <a href="?action=notifications" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-bell"></i>
                                <div>Thông Báo</div>
                            </a>
                        </li>
                        
                        <!-- Logout -->
                        <li class="menu-item">
                            <a href="?action=logout" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-log-out"></i>
                                <div>Đăng xuất</div>
                            </a>
                        </li>
                    </ul>
                </aside>
            `;
        } else if (currentUser.role === 'ADMIN') {
            menuPlaceholder.innerHTML = `
                <aside id="layout-menu" class="layout-menu menu-vertical menu">
                    <div class="app-brand demo">
                        <a href="?action=admin_dashboard" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <img src="${baseUrl}/static/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
                            </span>
                            <span class="app-brand-text demo text-body fw-bolder text-uppercase">NURSEBORN</span>
                        </a>
                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                            <i class="bx bx-chevron-left bx-sm align-middle"></i>
                        </a>
                    </div>
                    <div class="menu-inner-shadow"></div>
                    <ul class="menu-inner py-1">
                        <li class="menu-item ${window.location.search.includes('action=admin_dashboard') ? 'active' : ''}">
                            <a href="?action=admin_dashboard" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                                <div>Bảng Điều Khiển</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=manage_users') ? 'active' : ''}">
                            <a href="?action=manage_users" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-user"></i>
                                <div>Quản Lý Người Dùng</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=manage_bookings') ? 'active' : ''}">
                            <a href="?action=manage_bookings" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-calendar-check"></i>
                                <div>Quản Lý Lịch Đặt</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=notifications') ? 'active' : ''}">
                            <a href="?action=notifications" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-bell"></i>
                                <div>Thông Báo</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=logout" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-log-out"></i>
                                <div>Đăng xuất</div>
                            </a>
                        </li>
                    </ul>
                </aside>
            `;
        } else {
            menuPlaceholder.innerHTML = `
                <aside id="layout-menu" class="layout-menu menu-vertical menu">
                    <div class="app-brand demo">
                        <a href="/" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <img src="${baseUrl}/static/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
                            </span>
                            <span class="app-brand-text demo text-body fw-bolder text-uppercase">NURSEBORN</span>
                        </a>
                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                            <i class="bx bx-chevron-left bx-sm align-middle"></i>
                        </a>
                    </div>
                    <div class="menu-inner-shadow"></div>
                    <ul class="menu-inner py-1">
                        <li class="menu-item">
                            <a href="?action=login" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-log-in"></i>
                                <div>Đăng nhập</div>
                            </a>
                        </li>
                    </ul>
                </aside>
            `;
        }
    }

    // Hàm để thêm navbar dựa trên vai trò (Đồng bộ với đoạn mã bạn cung cấp)
    function loadNavbarBasedOnRole() {
        const navbarPlaceholder = document.getElementById('navbar-placeholder');

        if (currentUser.role === 'NURSE') {
            navbarPlaceholder.innerHTML = `
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="${currentUser.profileImage}" alt="Ảnh đại diện" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="?action=nurse_profile">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="${currentUser.profileImage}" alt="Ảnh đại diện" class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">${currentUser.fullName || 'Y Tá'}</span>
                                                    <small class="text-muted">Y Tá</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="?action=nurse_profile">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Hồ sơ</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="?action=logout">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Đăng xuất</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>
            `;
        } else if (currentUser.role === 'FAMILY') {
            navbarPlaceholder.innerHTML = `
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Người dùng -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="${baseUrl}/static/assets/img/avatars/1.png" alt="Ảnh đại diện" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="?action=user_profile">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="${baseUrl}/static/assets/img/avatars/1.png" alt="Ảnh đại diện" class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">${currentUser.fullName || 'Người dùng'}</span>
                                                    <small class="text-muted">${currentUser.role || 'Khách'}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="?action=user_profile">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Hồ sơ của tôi</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="?action=logout">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Đăng xuất</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ Người dùng -->
                        </ul>
                    </div>
                </nav>
            `;
        } else if (currentUser.role === 'ADMIN') {
            navbarPlaceholder.innerHTML = `
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Người dùng -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="${baseUrl}/static/assets/img/avatars/1.png" alt="Ảnh đại diện" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="?action=user_profile">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="${baseUrl}/static/assets/img/avatars/1.png" alt="Ảnh đại diện" class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">${currentUser.fullName || 'Người dùng'}</span>
                                                    <small class="text-muted">${currentUser.role || 'Khách'}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="?action=user_profile">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Hồ sơ của tôi</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="?action=logout">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Đăng xuất</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ Người dùng -->
                        </ul>
                    </div>
                </nav>
            `;
        } else {
            navbarPlaceholder.innerHTML = `
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center" id="layout-navbar">
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="?action=login"><i class="fas fa-sign-in-alt me-2"></i>Đăng nhập</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            `;
        }
    }

    // Gọi hàm loadMenuBasedOnRole và loadNavbarBasedOnRole khi trang được tải
    document.addEventListener('DOMContentLoaded', function() {
        try {
            loadMenuBasedOnRole();
            loadNavbarBasedOnRole();

            // Bỏ qua khởi tạo Menu để tránh lỗi, chỉ ghi log
            if (typeof Menu !== 'undefined') {
                const menuElements = document.querySelectorAll('.menu');
                console.log('Menu elements found:', menuElements);
                try {
                    menuElements.forEach(menu => {
                        console.log('Initializing Menu for element:', menu);
                        new Menu(menu);
                    });
                    console.log('Menu initialized successfully');
                } catch (menuError) {
                    console.error('Failed to initialize Menu:', menuError);
                    // Tiếp tục thực thi các chức năng khác dù Menu thất bại
                }
            } else {
                console.error('Menu class is not defined. Ensure menu.js is loaded correctly.');
            }

            // Kiểm tra quyền truy cập cho ADMIN
            if (currentUser.role === 'ADMIN') {
                console.log('Admin không có quyền truy cập vào nhắn tin');
                document.querySelector('.chat-container').innerHTML = '<p class="error-message">Không có quyền truy cập</p>';
            } else if (!currentUser.userId) {
                console.log('No userId found in currentUser, redirecting to login');
                document.querySelector('.chat-container').innerHTML = '<p class="error-message">Phiên đăng nhập không hợp lệ. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
            } else {
                loadConversationPartners();
                if (hasSelectedPartner && selectedPartner) {
                    console.log('Selecting initial partner from URL:', selectedPartner);
                    selectedPartnerId = selectedPartner.userId;
                    selectPartnerFromNursePage(selectedPartner);
                }
                setInterval(checkNewMessages, 5000);
            }
        } catch (error) {
            console.error('Error initializing menu, navbar, or chat:', error);
            document.querySelector('.chat-container').innerHTML = `<p class="error-message">Lỗi khởi tạo: ${error.message}</p>`;
        }
    });

    function loadConversationPartners() {
        if (!currentUser.userId) {
            console.error('No userId available for fetching partners');
            document.getElementById('conversation-list').innerHTML = '<li class="error-message">Phiên đăng nhập không hợp lệ. Vui lòng <a href="?action=login">đăng nhập lại</a>.</li>';
            return;
        }

        console.log('Fetching partners for userId:', currentUser.userId);
        console.log('URL:', `${baseUrl}/controllers/MessageController.php?action=get_partners&userId=${currentUser.userId}`);
        fetch(`${baseUrl}/controllers/MessageController.php?action=get_partners&userId=${currentUser.userId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include'
        })
            .then(response => {
                console.log('Response status:', response.status, response.statusText);
                console.log('Response headers:', response.headers.get('content-type'));
                if (!response.ok) {
                    if (response.status === 403) {
                        return response.text().then(text => {
                            console.log('Raw error response:', text);
                            try {
                                const data = JSON.parse(text);
                                if (data.action === 'redirect' && data.redirectUrl) {
                                    window.location.href = data.redirectUrl;
                                    return;
                                }
                                throw new Error('Không có quyền truy cập: ' + text);
                            } catch (e) {
                                if (text.includes('Warning:') || text.includes('Error:')) {
                                    document.querySelector('.chat-container').innerHTML = '<p class="error-message">Phiên đăng nhập không hợp lệ. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
                                } else {
                                    document.querySelector('.chat-container').innerHTML = '<p class="error-message">Bạn chưa đăng nhập hoặc phiên đã hết hạn. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
                                }
                                throw new Error(`Không có quyền truy cập: ${text}`);
                            }
                        });
                    }
                    return response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error(`Phản hồi từ server không phải JSON hợp lệ: ${text}`);
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Partners data:', data);
                const list = document.getElementById('conversation-list');
                list.innerHTML = '';
                if (data.error) {
                    list.innerHTML = `<li class="error-message">${data.error}</li>`;
                    return;
                }
                if (!data || data.length === 0) {
                    list.innerHTML = '<li class="no-data">Không có đối tác nào để trò chuyện. Hãy bắt đầu một cuộc trò chuyện mới!</li>';
                    return;
                }
                data.forEach(partner => {
                    const li = document.createElement('li');
                    li.textContent = partner.fullName || partner.username || 'Người dùng không xác định';
                    li.setAttribute('data-user-id', partner.userId);
                    li.onclick = function() {
                        selectPartner(partner);
                    };
                    list.appendChild(li);
                });
            })
            .catch(error => {
                console.error('Error fetching partners:', error);
                document.getElementById('conversation-list').innerHTML = `<li class="error-message">Lỗi tải danh sách trò chuyện: ${error.message}</li>`;
            });
    }

    function selectPartnerFromNursePage(partner) {
        console.log('Selecting partner from nurse page:', partner);
        if (!partner || !partner.userId) {
            console.error('Invalid partner data:', partner);
            return;
        }
        selectedPartnerId = partner.userId;
        document.getElementById('chat-with').textContent = `Trò chuyện với ${partner.fullName || partner.username}`;
        loadMessages();
    }

    function selectPartner(partner) {
        console.log('Selecting partner:', partner);
        if (!partner || !partner.userId) {
            console.error('Invalid partner data:', partner);
            return;
        }
        selectedPartnerId = partner.userId;
        // Xóa class 'selected' khỏi tất cả các mục
        document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('selected'));
        // Thêm class 'selected' cho mục được chọn
        const selectedLi = document.querySelector(`.sidebar li[data-user-id="${partner.userId}"]`);
        if (selectedLi) {
            selectedLi.classList.add('selected');
        } else {
            console.error('Could not find list item for partner:', partner);
        }
        document.getElementById('chat-with').textContent = `Trò chuyện với ${partner.fullName || partner.username}`;
        // Đặt lại lastMessageId và displayedMessages để tải toàn bộ tin nhắn mới
        lastMessageId = 0;
        displayedMessages.clear();
        loadMessages();
    }

    function loadMessages() {
        if (!currentUser.userId) {
            console.error('No userId available for loading messages');
            document.getElementById('messages').innerHTML = '<p class="error-message">Phiên đăng nhập không hợp lệ. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
            return;
        }

        if (!selectedPartnerId) {
            console.log('No selected partner, clearing messages');
            document.getElementById('messages').innerHTML = '<p class="no-data">Vui lòng chọn một người để trò chuyện</p>';
            return;
        }

        console.log('Loading messages between', currentUser.userId, 'and', selectedPartnerId);
        fetch(`${baseUrl}/controllers/MessageController.php?action=get_conversation&senderId=${currentUser.userId}&receiverId=${selectedPartnerId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include'
        })
            .then(response => {
                console.log('Conversation response status:', response.status, response.statusText);
                console.log('Response headers:', response.headers.get('content-type'));
                if (!response.ok) {
                    if (response.status === 403) {
                        return response.text().then(text => {
                            console.log('Raw error response:', text);
                            try {
                                const data = JSON.parse(text);
                                if (data.action === 'redirect' && data.redirectUrl) {
                                    window.location.href = data.redirectUrl;
                                    return;
                                }
                                throw new Error('Không có quyền truy cập: ' + text);
                            } catch (e) {
                                if (text.includes('Warning:') || text.includes('Error:')) {
                                    document.querySelector('.chat-container').innerHTML = '<p class="error-message">Phiên đăng nhập không hợp lệ. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
                                } else {
                                    document.querySelector('.chat-container').innerHTML = '<p class="error-message">Bạn chưa đăng nhập hoặc phiên đã hết hạn. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
                                }
                                throw new Error(`Không có quyền truy cập: ${text}`);
                            }
                        });
                    }
                    return response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error(`Phản hồi từ server không phải JSON hợp lệ: ${text}`);
                        }
                    });
                }
                return response.json();
            })
            .then(messages => {
                console.log('Messages:', messages);
                const messagesDiv = document.getElementById('messages');
                messagesDiv.innerHTML = '';
                displayedMessages.clear();
                if (!messages || messages.length === 0) {
                    messagesDiv.innerHTML = '<p class="no-data">Chưa có tin nhắn. Gửi tin nhắn đầu tiên!</p>';
                    return;
                }
                messages.forEach(message => {
                    displayMessage(message);
                    if (message.message_id > lastMessageId) {
                        lastMessageId = message.message_id;
                    }
                    if (!message.is_read && message.receiver_id == currentUser.userId) {
                        markAsRead(message.message_id);
                    }
                });
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                document.getElementById('messages').innerHTML = `<p class="error-message">Lỗi tải tin nhắn: ${error.message}</p>`;
            });
    }

    function checkNewMessages() {
        if (!currentUser.userId || !selectedPartnerId) return;

        console.log('Checking new messages between', currentUser.userId, 'and', selectedPartnerId);
        fetch(`${baseUrl}/controllers/MessageController.php?action=get_conversation&senderId=${currentUser.userId}&receiverId=${selectedPartnerId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include'
        })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 403) {
                        return response.text().then(text => {
                            console.log('Raw error response:', text);
                            try {
                                const data = JSON.parse(text);
                                if (data.action === 'redirect' && data.redirectUrl) {
                                    window.location.href = data.redirectUrl;
                                    return;
                                }
                                throw new Error('Không có quyền truy cập: ' + text);
                            } catch (e) {
                                if (text.includes('Warning:') || text.includes('Error:')) {
                                    document.querySelector('.chat-container').innerHTML = '<p class="error-message">Phiên đăng nhập không hợp lệ. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
                                } else {
                                    document.querySelector('.chat-container').innerHTML = '<p class="error-message">Bạn chưa đăng nhập hoặc phiên đã hết hạn. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
                                }
                                throw new Error(`Không có quyền truy cập: ${text}`);
                            }
                        });
                    }
                    return response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error(`Phản hồi từ server không phải JSON hợp lệ: ${text}`);
                        }
                    });
                }
                return response.json();
            })
            .then(messages => {
                const newMessages = messages.filter(message => message.message_id > lastMessageId);
                newMessages.forEach(message => {
                    displayMessage(message);
                    if (message.message_id > lastMessageId) {
                        lastMessageId = message.message_id;
                    }
                    if (!message.is_read && message.receiver_id == currentUser.userId) {
                        markAsRead(message.message_id);
                    }
                });
                if (newMessages.length > 0) {
                    document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
                }
            })
            .catch(error => {
                console.error('Error checking new messages:', error);
            });
    }

    // Hàm hiển thị tin nhắn
    function displayMessage(message) {
        if (displayedMessages.has(message.message_id)) {
            return;
        }
        displayedMessages.add(message.message_id);

        const messagesDiv = document.getElementById('messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${message.sender_id == currentUser.userId ? 'sent' : 'received'}`;
        messageDiv.setAttribute('data-message-id', message.message_id);

        // Hiển thị thời gian giống hệt trong cơ sở dữ liệu, nhưng bỏ micro giây (nếu có)
        let formattedTime = message.sent_at || 'Không xác định';
        if (message.sent_at) {
            // Cắt bỏ phần micro giây (nếu có)
            formattedTime = message.sent_at.split('.')[0];
        }

        messageDiv.innerHTML = `
            <p>${message.content}</p>
            <span>${formattedTime}</span>
        `;
        messagesDiv.appendChild(messageDiv);
    }

    function sendMessage() {
        if (!currentUser.userId) {
            alert('Phiên đăng nhập không hợp lệ. Vui lòng đăng nhập lại.');
            window.location.href = '?action=login';
            return;
        }

        if (!selectedPartnerId) {
            alert('Vui lòng chọn một người để trò chuyện');
            return;
        }
        const content = document.getElementById('message-input').value.trim();
        if (!content) {
            alert('Vui lòng nhập nội dung tin nhắn');
            return;
        }

        const messageData = {
            senderId: currentUser.userId,
            receiverId: selectedPartnerId,
            content: content
        };

        console.log('Sending message:', messageData);
        fetch(`${baseUrl}/controllers/MessageController.php?action=send_message`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify(messageData)
        })
            .then(response => {
                console.log('Send message response status:', response.status, response.statusText);
                console.log('Response headers:', response.headers.get('content-type'));
                if (!response.ok) {
                    if (response.status === 403) {
                        return response.text().then(text => {
                            console.log('Raw error response:', text);
                            try {
                                const data = JSON.parse(text);
                                if (data.action === 'redirect' && data.redirectUrl) {
                                    window.location.href = data.redirectUrl;
                                    return;
                                }
                                throw new Error('Không có quyền truy cập: ' + text);
                            } catch (e) {
                                if (text.includes('Warning:') || text.includes('Error:')) {
                                    document.querySelector('.chat-container').innerHTML = '<p class="error-message">Phiên đăng nhập không hợp lệ. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
                                } else {
                                    document.querySelector('.chat-container').innerHTML = '<p class="error-message">Bạn chưa đăng nhập hoặc phiên đã hết hạn. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
                                }
                                throw new Error(`Không có quyền truy cập: ${text}`);
                            }
                        });
                    }
                    return response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error(`Phản hồi từ server không phải JSON hợp lệ: ${text}`);
                        }
                    });
                }
                return response.json();
            })
            .then(message => {
                console.log('Message sent:', message);
                document.getElementById('message-input').value = '';
                displayMessage(message);
                lastMessageId = message.message_id;
                document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
                // Tải lại danh sách đối tác để cập nhật nếu đối tác mới được thêm
                loadConversationPartners();
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert(`Không thể gửi tin nhắn: ${error.message}`);
            });
    }

    function markAsRead(messageId) {
        if (!currentUser.userId) {
            console.error('No userId available for marking message as read');
            return;
        }

        console.log('Marking message as read:', messageId);
        fetch(`${baseUrl}/controllers/MessageController.php?action=mark_message_as_read&messageId=${messageId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include'
        })
            .then(response => {
                console.log('Mark as read response status:', response.status, response.statusText);
                console.log('Response headers:', response.headers.get('content-type'));
                if (!response.ok) {
                    if (response.status === 403) {
                        return response.text().then(text => {
                            console.log('Raw error response:', text);
                            try {
                                const data = JSON.parse(text);
                                if (data.action === 'redirect' && data.redirectUrl) {
                                    window.location.href = data.redirectUrl;
                                    return;
                                }
                                throw new Error('Không có quyền truy cập: ' + text);
                            } catch (e) {
                                if (text.includes('Warning:') || text.includes('Error:')) {
                                    document.querySelector('.chat-container').innerHTML = '<p class="error-message">Phiên đăng nhập không hợp lệ. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
                                } else {
                                    document.querySelector('.chat-container').innerHTML = '<p class="error-message">Bạn chưa đăng nhập hoặc phiên đã hết hạn. Vui lòng <a href="?action=login">đăng nhập lại</a>.</p>';
                                }
                                throw new Error(`Không có quyền truy cập: ${text}`);
                            }
                        });
                    }
                    return response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error(`Phản hồi từ server không phải JSON hợp lệ: ${text}`);
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Marked message as read:', data);
            })
            .catch(error => {
                console.error('Error marking message as read:', error);
            });
    }
</script>
</body>
</html>
<?php
error_log("messages.php execution completed");
?>