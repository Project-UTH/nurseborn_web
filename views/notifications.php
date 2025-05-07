<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
$notifications = isset($notifications) ? $notifications : [];
$pageTitle = 'Thông Báo';
$baseUrl = '/nurseborn';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <title>Thông Báo - NurseBorn</title>
    <meta name="_csrf" content="<?php echo htmlspecialchars(session_id()); ?>">
    <meta name="_csrf_header" content="X-CSRF-TOKEN">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .notification {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            border-left: 5px solid #4a90e2;
            margin-bottom: 15px;
        }
        .notification.unread {
            background-color: #e6f0fa;
            font-weight: 500;
            border-left-color: #ff6f61;
        }
        .notification.read {
            background-color: #f0f0f0;
            border-left-color: #ccc;
        }
        .notification:hover {
            transform: translateY(-3px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        .notification p {
            margin: 0;
            font-size: 16px;
            color: #333;
            text-align: left;
        }
        .notification small {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #777;
        }
        .notification a {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            background-color: #4a90e2;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .notification a:hover {
            background-color: #357abd;
        }
        .error-message {
            color: #e53e3e;
            font-size: 14px;
            padding: 10px;
            text-align: center;
        }
        .card.mb-4 {
            border: none;
            border-radius: 15px;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .card-body {
            padding: 30px;
        }
        h4.text-primary {
            color: #007bff;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .text-center.text-muted {
            color: #6c757d;
            font-size: 1rem;
        }
        .list-unstyled {
            padding: 0;
            margin: 0;
        }
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <div id="menu-placeholder"></div>
        <div class="layout-page">
            <div id="navbar-placeholder"></div>
            <div class="content-wrapper">
                <div class="container-p-y">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="text-primary">Thông Báo</h4>
                            <?php if (empty($notifications)): ?>
                                <p class="text-center text-muted">Không có thông báo nào.</p>
                            <?php else: ?>
                                <ul class="list-unstyled">
                                    <?php foreach ($notifications as $notification): ?>
                                        <li>
                                            <div class="<?php echo $notification['is_read'] ? 'notification read' : 'notification unread'; ?>">
                                                <p><?php echo htmlspecialchars($notification['message']); ?></p>
                                                <small><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($notification['created_at']))); ?></small>
                                                <?php if (!$notification['is_read']): ?>
                                                    <a href="?action=mark_as_read&notificationId=<?php echo $notification['notification_id']; ?>">Đánh dấu đã đọc</a>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
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
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>

<script>
    const currentUser = {
        userId: <?php echo json_encode($user['user_id'] ?? null); ?>,
        username: <?php echo json_encode($user['username'] ?? null); ?>,
        role: <?php echo json_encode($user['role'] ?? null); ?>,
        fullName: <?php echo json_encode($user['full_name'] ?? 'Người dùng'); ?>,
        profileImage: <?php echo json_encode($nurseProfile['profile_image'] ?? $baseUrl . '/static/assets/img/avatars/default_profile.jpg'); ?>
    };

    function loadMenuBasedOnRole() {
        const menuPlaceholder = document.getElementById('menu-placeholder');

        if (currentUser.role === 'NURSE') {
            menuPlaceholder.innerHTML = `
                <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                    <div class="app-brand demo">
                        <a href="/" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <img src="<?php echo $baseUrl; ?>/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
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
                            <a href="/" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                                <div data-i18n="Dashboard">Trang chủ</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=nurse_schedule" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-calendar"></i>
                                <div data-i18n="Schedule">Lịch Làm Việc</div>
                            </a>
                        </li>
                        <li class="menu-item active">
                            <a href="?action=notifications" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-bell"></i>
                                <div data-i18n="notifications">Thông Báo</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=pending_bookings" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div data-i18n="Pending Bookings">Lịch Đặt Chờ Xác Nhận</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=nurse_availability" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check-square"></i>
                                <div data-i18n="Availability">Quản Lý Lịch Làm Việc</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=messages" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-message"></i>
                                <div data-i18n="Messages">Trò chuyện</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=nurse_income" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-wallet"></i>
                                <div data-i18n="nurse_income">Thống Kê Thu Nhập</div>
                            </a>
                        </li>
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Tài Khoản</span>
                        </li>
                        <li class="menu-item">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div data-i18n="Account Settings">Cài Đặt Tài Khoản</div>
                            </a>
                            <ul class="menu-sub">
                                <li class="menu-item">
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
                                <img src="<?php echo $baseUrl; ?>/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
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
                            <a href="?action=family_home" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                                <div data-i18n="Analytics">Home</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=family_bookings" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div data-i18n="Analytics">Danh Sách Lịch Đặt</div>
                            </a>
                        </li>
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Quản lý Gia đình</span>
                        </li>
                        <li class="menu-item">
                            <a href="?action=nurse_list" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-search"></i>
                                <div data-i18n="FindNurse">Đặt dịch vụ</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=messages" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-message"></i>
                                <div data-i18n="Messages">Trò chuyện</div>
                            </a>
                        </li>
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Tài khoản</span>
                        </li>
                        <li class="menu-item">
                            <a href="?action=user_profile" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div data-i18n="Profile">Hồ sơ</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=update_user" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                                <div data-i18n="UpdateProfile">Cập nhật hồ sơ</div>
                            </a>
                        </li>
                        <li class="menu-item active">
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
                                <img src="<?php echo $baseUrl; ?>/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
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
                                        <img src="<?php echo $baseUrl; ?>/static/assets/img/avatars/default_profile.jpg" alt="Ảnh đại diện" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="?action=user_profile">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="<?php echo $baseUrl; ?>/static/assets/img/avatars/default_profile.jpg" alt="Ảnh đại diện" class="w-px-40 h-auto rounded-circle" />
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
        } catch (error) {
            console.error('Error initializing menu or navbar:', error);
        }
    });
</script>
</body>
</html>