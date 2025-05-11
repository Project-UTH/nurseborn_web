<?php
// Không gọi session_start() ở đây vì session đã được khởi động trong connect.php hoặc MessageController.php
$baseUrl = '/nurseborn';

// Lấy thông tin người dùng từ session
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;

// Xử lý ảnh đại diện và thông tin người dùng
$avatar = ($nurseProfile && !empty($nurseProfile['profile_image'])) ? htmlspecialchars($nurseProfile['profile_image']) : ($baseUrl . '/static/assets/img/avatars/default_profile.jpg');
$fullName = $user ? htmlspecialchars($user['full_name']) : 'Người dùng';
$role = $user ? htmlspecialchars($user['role']) : 'Khách';
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
        /* Tùy chỉnh tổng thể */
        body {
            background: linear-gradient(to bottom, #f5f7fa, #e8ecef);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Card chính */
        .card {
            border: none;
            border-radius: 15px;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-body {
            padding: 30px;
        }

        /* Tiêu đề */
        .card-header {
            background: linear-gradient(45deg, #007bff, #28a745);
            color: #fff;
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
            padding: 20px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            position: relative;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Khu vực chat */
        .chat-container {
            display: flex;
            height: 70vh;
            border: 1px solid #d1d3e2;
            border-radius: 8px;
            background: linear-gradient(145deg, #f8fafc, #e2e8f0);
            margin: 0;
            padding: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Sidebar */
        .sidebar {
            width: 30%;
            background: linear-gradient(to bottom, #f1f5f9, #e2e8f0);
            padding: 20px;
            border-right: 1px solid #d1d3e2;
            overflow-y: auto;
        }
        .sidebar h5 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2d3748;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar li {
            padding: 12px 15px;
            cursor: pointer;
            border-radius: 6px;
            margin-bottom: 8px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            color: #4a5568;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 1rem;
        }
        .sidebar li:hover {
            background-color: #e2e8f0;
            transform: translateX(5px);
        }
        .sidebar li.selected {
            background-color: #cbd5e0;
            font-weight: 600;
            color: #1a202c;
        }

        /* Khu vực chat chính */
        .chat-area {
            width: 70%;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .chat-area h5 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2d3748;
        }

        /* Khu vực tin nhắn */
        .messages {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #edf2f7;
            border-radius: 6px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .message {
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 12px;
            max-width: 70%;
            line-height: 1.4;
            animation: slideIn 0.3s ease-in-out;
        }
        @keyframes slideIn {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .message.sent {
            background: linear-gradient(45deg, #4299e1, #3182ce);
            color: #ffffff;
            margin-left: auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .message.received {
            background: linear-gradient(45deg, #e2e8f0, #d1d5db);
            margin-right: auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .message p {
            margin: 0;
            font-size: 0.95rem;
        }
        .message span {
            font-size: 0.75rem;
            color: #a0aec0;
            display: block;
            margin-top: 5px;
            opacity: 0.8;
        }

        /* Input và nút gửi tin nhắn */
        .message-input {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .message-input input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #d1d3e2;
            border-radius: 6px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .message-input input:focus {
            border-color: #4299e1;
            box-shadow: 0 0 5px rgba(66, 153, 225, 0.3);
        }
        .message-input button {
            padding: 10px 25px;
            background: linear-gradient(45deg, #4299e1, #3182ce);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }
        .message-input button:hover {
            background: linear-gradient(45deg, #3182ce, #2b6cb0);
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
        }

        /* Thông báo lỗi và không có dữ liệu */
        .error-message {
            color: #e53e3e;
            font-size: 0.95rem;
            padding: 10px;
            text-align: center;
        }
        .no-data {
            color: #718096;
            font-size: 0.95rem;
            padding: 10px;
            text-align: center;
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
    let selectedPartnerId = null;
    let lastMessageId = 0;
    const displayedMessages = new Set();

    // Hàm để thêm menu dựa trên vai trò
    function loadMenuBasedOnRole() {
        const menuPlaceholder = document.getElementById('menu-placeholder');

        if (currentUser.role === 'NURSE') {
            menuPlaceholder.innerHTML = `
                <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
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
                        <li class="menu-item ${window.location.search.includes('action=home') ? 'active' : ''}">
                            <a href="?action=home" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                                <div data-i18n="Dashboard">Trang chủ</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=nurse_schedule') ? 'active' : ''}">
                            <a href="?action=nurse_schedule" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-calendar"></i>
                                <div data-i18n="Schedule">Lịch Làm Việc</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=notifications') ? 'active' : ''}">
                            <a href="?action=notifications" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-bell"></i>
                                <div data-i18n="notifications">Thông Báo</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=pending_bookings') ? 'active' : ''}">
                            <a href="?action=pending_bookings" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div data-i18n="Pending Bookings">Lịch Đặt Chờ Xác Nhận</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=nurse_availability') ? 'active' : ''}">
                            <a href="?action=nurse_availability" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check-square"></i>
                                <div data-i18n="Availability">Quản Lý Lịch Làm Việc</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=messages') ? 'active' : ''}">
                            <a href="?action=messages" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-message"></i>
                                <div data-i18n="Messages">Trò chuyện</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=nurse_income') ? 'active' : ''}">
                            <a href="?action=nurse_income" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-wallet"></i>
                                <div data-i18n="nurse_income">Thống Kê Thu Nhập</div>
                            </a>
                        </li>
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Tài Khoản</span>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=nurse_profile') ? 'active open' : ''}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div data-i18n="Account Settings">Cài Đặt Tài Khoản</div>
                            </a>
                            <ul class="menu-sub">
                                <li class="menu-item ${window.location.search.includes('action=nurse_profile') ? 'active' : ''}">
                                    <a href="?action=nurse_profile" class="menu-link">
                                        <div data-i18n="Account">Tài Khoản</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-item">
                            <a href="?action=logout" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-log-out"></i>
                                <div data-i18n="Logout">Đăng Xuất</div>
                            </a>
                        </li>
                    </ul>
                </aside>
            `;
        } else if (currentUser.role === 'FAMILY') {
            menuPlaceholder.innerHTML = `
                <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
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
                        <li class="menu-item ${window.location.search.includes('action=family_home') ? 'active' : ''}">
                            <a href="?action=family_home" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                                <div data-i18n="Analytics">Trang chủ</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=bookings') ? 'active' : ''}">
                            <a href="?action=bookings" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div data-i18n="Analytics">Danh Sách Lịch Đặt</div>
                            </a>
                        </li>
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Quản lý Gia đình</span>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=nursepage') ? 'active' : ''}">
                            <a href="?action=nursepage" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-search"></i>
                                <div data-i18n="FindNurse">Đặt dịch vụ</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=messages') ? 'active' : ''}">
                            <a href="?action=messages" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-message"></i>
                                <div data-i18n="Messages">Trò chuyện</div>
                            </a>
                        </li>
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Tài khoản</span>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=user_profile') ? 'active' : ''}">
                            <a href="?action=user_profile" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div data-i18n="Profile">Hồ sơ</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=update_user') ? 'active' : ''}">
                            <a href="?action=update_user" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div data-i18n="UpdateProfile">Cập nhật hồ sơ</div>
                            </a>
                        </li>
                        <li class="menu-item ${window.location.search.includes('action=notifications') ? 'active' : ''}">
                            <a href="?action=notifications" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-bell"></i>
                                <div data-i18n="notifications">Thông Báo</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=logout" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-log-out"></i>
                                <div data-i18n="Logout">Đăng xuất</div>
                            </a>
                        </li>
                    </ul>
                </aside>
            `;
        } else {
            menuPlaceholder.innerHTML = `
                <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
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
                                <div data-i18n="Login">Đăng nhập</div>
                            </a>
                        </li>
                    </ul>
                </aside>
            `;
        }
    }

    // Hàm để thêm navbar dựa trên vai trò
    function loadNavbarBasedOnRole() {
        const navbarPlaceholder = document.getElementById('navbar-placeholder');

        if (currentUser.role === 'NURSE') {
            navbarPlaceholder.innerHTML = `
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
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
                                                    <span class="fw-semibold d-block">${currentUser.fullName || 'Người dùng'}</span>
                                                    <small class="text-muted">${currentUser.role || 'Khách'}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="?action=nurse_profile">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Hồ sơ của tôi</span>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="?action=logout">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Đăng xuất</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            `;
        } else if (currentUser.role === 'FAMILY') {
            navbarPlaceholder.innerHTML = `
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="${baseUrl}/static/assets/img/avatars/default_profile.jpg" alt="Ảnh đại diện" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="?action=user_profile">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="${baseUrl}/static/assets/img/avatars/default_profile.jpg" alt="Ảnh đại diện" class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">${currentUser.fullName || 'Người dùng'}</span>
                                                    <small class="text-muted">${currentUser.role || 'Khách'}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="?action=user_profile">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Hồ sơ của tôi</span>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="?action=logout">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Đăng xuất</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            `;
        } else {
            navbarPlaceholder.innerHTML = `
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="?action=login">Đăng nhập</a>
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
            if (typeof Menu !== 'undefined') {
                const menuElements = document.querySelectorAll('.menu');
                menuElements.forEach(menu => {
                    new Menu(menu);
                });
                console.log('Menu initialized successfully');
            } else {
                console.error('Menu class is not defined. Ensure menu.js is loaded correctly.');
            }

            // Kiểm tra quyền truy cập cho ADMIN
            if (currentUser.role === 'ADMIN') {
                console.log('Admin không có quyền truy cập vào nhắn tin');
                document.querySelector('.chat-container').innerHTML = '<p class="error-message">Không có quyền truy cập</p>';
            } else {
                loadConversationPartners();
                setInterval(checkNewMessages, 5000);
            }
        } catch (error) {
            console.error('Error initializing menu, navbar, or chat:', error);
        }
    });

    function loadConversationPartners() {
        console.log('Bắt đầu tải danh sách đối tác trò chuyện cho userId:', currentUser.userId);
        fetch(`${baseUrl}/controllers/MessageController.php?action=get_partners&userId=${currentUser.userId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                console.log('Response từ /controllers/MessageController.php?action=get_partners:', response.status, response.statusText);
                if (!response.ok) {
                    if (response.status === 403) {
                        document.querySelector('.chat-container').innerHTML = '<p class="error-message">Bạn không có quyền truy cập trang này</p>';
                        throw new Error('Không có quyền truy cập');
                    }
                    return response.text().then(text => {
                        throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Dữ liệu trả về từ /controllers/MessageController.php?action=get_partners:', data);
                let partners = data;
                if (data.error) {
                    throw new Error(data.error);
                }
                const list = document.getElementById('conversation-list');
                list.innerHTML = '';
                if (!partners || partners.length === 0) {
                    list.innerHTML = '<li class="no-data">Không có cuộc trò chuyện nào</li>';
                    return;
                }
                partners.forEach(partner => {
                    const li = document.createElement('li');
                    li.textContent = partner.fullName || partner.username;
                    li.setAttribute('data-user-id', partner.userId);
                    li.onclick = function() {
                        selectUser(partner);
                    };
                    list.appendChild(li);
                });
            })
            .catch(error => {
                console.error('Lỗi khi lấy danh sách đối tác:', error);
                if (!document.querySelector('.chat-container').innerHTML.includes('Không có quyền truy cập')) {
                    document.getElementById('conversation-list').innerHTML = '<li class="error-message">Lỗi tải danh sách: ' + error.message + '</li>';
                }
            });
    }

    function selectUser(user) {
        selectedPartnerId = user;
        document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('selected'));
        const selectedLi = document.querySelector(`.sidebar li[data-user-id="${user.userId}"]`);
        if (selectedLi) {
            selectedLi.classList.add('selected');
        }
        document.getElementById('chat-with').textContent = `Trò chuyện với ${user.fullName || user.username}`;
        loadMessages();
    }

    function loadMessages() {
        if (!selectedPartnerId) return;
        console.log('Tải tin nhắn giữa', currentUser.userId, 'và', selectedPartnerId.userId);
        fetch(`${baseUrl}/controllers/MessageController.php?action=get_conversation&senderId=${currentUser.userId}&receiverId=${selectedPartnerId.userId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                console.log('Response từ /controllers/MessageController.php?action=get_conversation:', response.status, response.statusText);
                if (!response.ok) {
                    if (response.status === 403) {
                        document.querySelector('.chat-container').innerHTML = '<p class="error-message">Bạn không có quyền truy cập trang này</p>';
                        throw new Error('Không có quyền truy cập');
                    }
                    return response.text().then(text => {
                        throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                    });
                }
                return response.json();
            })
            .then(messages => {
                console.log('Tin nhắn:', messages);
                const messagesDiv = document.getElementById('messages');
                messagesDiv.innerHTML = '';
                displayedMessages.clear();
                if (!messages || messages.length === 0) {
                    messagesDiv.innerHTML = '<p class="no-data">Chưa có tin nhắn</p>';
                    return;
                }
                messages.forEach(message => {
                    displayMessage(message);
                    if (message.message_id > lastMessageId) {
                        lastMessageId = message.message_id;
                    }
                    if (!message.is_read && message.receiver_id === currentUser.userId) {
                        markAsRead(message.message_id);
                    }
                });
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            })
            .catch(error => {
                console.error('Lỗi khi lấy tin nhắn:', error);
                if (!document.querySelector('.chat-container').innerHTML.includes('Không có quyền truy cập')) {
                    document.getElementById('messages').innerHTML = '<p class="error-message">Lỗi tải tin nhắn: ' + error.message + '</p>';
                }
            });
    }

    function checkNewMessages() {
        if (!selectedPartnerId) return;
        fetch(`${baseUrl}/controllers/MessageController.php?action=get_conversation&senderId=${currentUser.userId}&receiverId=${selectedPartnerId.userId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 403) {
                        document.querySelector('.chat-container').innerHTML = '<p class="error-message">Bạn không có quyền truy cập trang này</p>';
                        throw new Error('Không có quyền truy cập');
                    }
                    return response.text().then(text => {
                        throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
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
                    if (!message.is_read && message.receiver_id === currentUser.userId) {
                        markAsRead(message.message_id);
                    }
                });
                if (newMessages.length > 0) {
                    document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
                }
            })
            .catch(error => {
                console.error('Lỗi khi kiểm tra tin nhắn mới:', error);
            });
    }

    function displayMessage(message) {
        if (displayedMessages.has(message.message_id)) {
            return;
        }
        displayedMessages.add(message.message_id);

        const messagesDiv = document.getElementById('messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${message.sender_id === currentUser.userId ? 'sent' : 'received'}`;
        messageDiv.setAttribute('data-message-id', message.message_id);
        messageDiv.innerHTML = `
            <p>${message.content}</p>
            <span>${new Date(message.sent_at).toLocaleTimeString()}</span>
        `;
        messagesDiv.appendChild(messageDiv);
    }

    function sendMessage() {
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
            receiverId: selectedPartnerId.userId,
            content: content
        };

        fetch(`${baseUrl}/controllers/MessageController.php?action=send_message`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(messageData)
        })
            .then(response => {
                console.log('Response từ /controllers/MessageController.php?action=send_message:', response.status, response.statusText);
                if (!response.ok) {
                    if (response.status === 403) {
                        document.querySelector('.chat-container').innerHTML = '<p class="error-message">Bạn không có quyền truy cập trang này</p>';
                        throw new Error('Không có quyền truy cập');
                    }
                    return response.text().then(text => {
                        throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                    });
                }
                return response.json();
            })
            .then(message => {
                console.log('Tin nhắn đã gửi:', message);
                document.getElementById('message-input').value = '';
                displayMessage(message);
                lastMessageId = message.message_id;
                document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
            })
            .catch(error => {
                console.error('Lỗi khi gửi tin nhắn:', error);
                if (!document.querySelector('.chat-container').innerHTML.includes('Không có quyền truy cập')) {
                    alert('Không thể gửi tin nhắn: ' + error.message);
                }
            });
    }

    function markAsRead(messageId) {
        fetch(`${baseUrl}/controllers/MessageController.php?action=mark_message_as_read&messageId=${messageId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                console.log('Response từ /controllers/MessageController.php?action=mark_message_as_read:', response.status, response.statusText);
                if (!response.ok) {
                    if (response.status === 403) {
                        document.querySelector('.chat-container').innerHTML = '<p class="error-message">Bạn không có quyền truy cập trang này</p>';
                        throw new Error('Không có quyền truy cập');
                    }
                    return response.text().then(text => {
                        throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Đánh dấu tin nhắn đã đọc:', data);
            })
            .catch(error => {
                console.error('Lỗi khi đánh dấu tin nhắn đã đọc:', error);
            });
    }
</script>
</body>
</html>