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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo - NurseBorn</title>
    <meta name="_csrf" content="<?php echo htmlspecialchars(session_id()); ?>">
    <meta name="_csrf_header" content="X-CSRF-TOKEN">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Đảm bảo layout-container và layout-page chiếm toàn bộ chiều cao */
.layout-container {
    display: flex;
    min-height: 100vh; /* Chiều cao tối thiểu bằng viewport */
}

/* Đảm bảo layout-menu chiếm toàn bộ chiều cao của layout-container */
#layout-menu {
    height: 100%; /* Chiếm toàn bộ chiều cao của container cha */
    min-height: 100vh; /* Đảm bảo chiều cao tối thiểu bằng viewport */
}

/* Đảm bảo layout-page có thể mở rộng để chứa nội dung */
.layout-page {
    flex: 1; /* Mở rộng để chiếm không gian còn lại */
    display: flex;
    flex-direction: column;
}

/* Đảm bảo content-wrapper mở rộng để chứa nội dung */
.content-wrapper {
    flex: 1; /* Mở rộng để chiếm không gian còn lại */
    display: flex;
    flex-direction: column;
}

/* Đảm bảo nội dung bên trong content-wrapper mở rộng */
.content-xxl {
    flex: 1; /* Mở rộng để chiếm không gian còn lại */
}
        :root {
            --primary-color: #2563eb;
            --secondary-color: #22c55e;
            --text-color: #1f2a44;
            --muted-color: #6b7280;
            --card-bg: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --warning-color: #f59e0b;
            --chat-color: #4299e1;
        }

        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #dcfce7 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            margin: 0;
        }

        .container-p-y {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h4 {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        h4::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            background-color: var(--card-bg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 2rem;
        }

        .notification {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            border-left: 5px solid var(--primary-color);
        }

        .notification.unread {
            background-color: #e6f0fa;
            border-left-color: #ff6f61;
        }

        .notification.read {
            background-color: #f8f9fa;
            border-left-color: #d1d5db;
        }

        .notification:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .notification i {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-top: 0.2rem;
        }

        .notification.unread i {
            color: #ff6f61;
        }

        .notification-content {
            flex: 1;
        }

        .notification-content p {
            margin: 0;
            font-size: 1rem;
            color: var(--text-color);
        }

        .notification-content small {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--muted-color);
        }

        .notification-content a {
            display: inline-block;
            margin-top: 0.5rem;
            padding: 0.4rem 0.8rem;
            background: linear-gradient(45deg, var(--primary-color), #60a5fa);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .notification-content a:hover {
            background: linear-gradient(45deg, #1e40af, var(--primary-color));
            transform: translateY(-1px);
        }

        .no-notifications {
            text-align: center;
            font-size: 1rem;
            color: var(--muted-color);
            margin: 2rem 0;
        }

        .btn-container {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .btn {
            font-size: 1rem;
            font-weight: 500;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), #60a5fa);
            border: none;
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #1e40af, var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .alert-warning {
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-warning i {
            font-size: 1.2rem;
        }

        /* Navbar Styles */
        .layout-navbar {
            background: linear-gradient(90deg, var(--primary-color), #60a5fa) !important;
            box-shadow: var(--shadow);
            padding: 0.75rem 1.5rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            animation: fadeIn 0.5s ease-out;
        }

        .layout-menu-toggle {
            display: flex;
            align-items: center;
        }

        .layout-menu-toggle i {
            color: #fff;
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .layout-menu-toggle:hover i {
            transform: scale(1.2);
        }

        .navbar-nav-right {
            display: flex;
            align-items: center;
        }

        .dropdown-user .nav-link {
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            color: #fff;
            transition: background 0.3s ease, color 0.3s ease;
            border-radius: 8px;
        }

        .dropdown-user .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .avatar-online {
            position: relative;
        }

        .avatar-online::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background-color: var(--secondary-color);
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .avatar-online img {
            border: 2px solid #fff;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .avatar-online:hover img {
            transform: scale(1.1);
        }

        .dropdown-menu {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-top: 0.5rem;
            border: none;
            min-width: 200px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            color: var(--text-color);
            font-size: 0.95rem;
            font-weight: 500;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: var(--primary-color);
        }

        .dropdown-item i {
            color: var(--muted-color);
            margin-right: 0.75rem;
            transition: color 0.3s ease;
        }

        .dropdown-item:hover i {
            color: var(--primary-color);
        }

        .dropdown-divider {
            border-top: 1px solid #e5e7eb;
            margin: 0.5rem 0;
        }

        .fw-semibold {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .text-muted {
            color: var(--muted-color) !important;
            font-size: 0.85rem;
        }

        /* Menu Styles */
        .layout-menu {
            background: linear-gradient(180deg, var(--primary-color), #60a5fa) !important;
            box-shadow: var(--shadow);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            width: 260px;
            transition: width 0.3s ease;
            animation: slideIn 0.5s ease-out;
        }

        .app-brand {
            padding: 1.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .app-brand-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .app-brand-link:hover {
            transform: scale(1.05);
        }

        .app-brand-logo img {
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .app-brand-link:hover .app-brand-logo img {
            transform: rotate(360deg);
        }

        .app-brand-text {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .menu-inner-shadow {
            display: none;
        }

        .menu-inner {
            padding: 1rem 0 !important;
        }

        .menu-header {
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .menu-header-text {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
        }

        .menu-item {
            margin: 0.25rem 0;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            font-weight: 500;
            transition: background 0.3s ease, color 0.3s ease, padding-left 0.3s ease;
            border-radius: 8px;
            margin: 0 0.5rem;
        }

        .menu-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            padding-left: 1.5rem;
        }

        .menu-item.active .menu-link {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.1));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .menu-icon {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .menu-link:hover .menu-icon {
            color: #fff;
            transform: scale(1.1);
        }

        .menu-item.active .menu-icon {
            color: #fff;
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
                padding: 0.5rem 1rem;
            }

            .navbar-nav-right {
                padding: 0.25rem 0;
            }
        }

        @media (max-width: 768px) {
            .container-p-y {
                padding: 1.5rem;
                margin: 1rem;
            }

            h4 {
                font-size: 1.8rem;
            }

            .notification-content p {
                font-size: 0.95rem;
            }

            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }

            .avatar-online img {
                width: 32px !important;
                height: 32px !important;
            }

            .dropdown-item {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }

            .dropdown-menu {
                min-width: 180px;
            }

            .app-brand {
                padding: 1rem;
            }

            .app-brand-text {
                font-size: 1rem;
            }

            .menu-link {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }

            .menu-icon {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 576px) {
            .notification {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-container {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                text-align: center;
            }

            .layout-menu {
                width: 100%;
            }

            .menu-header {
                font-size: 0.7rem;
            }

            .dropdown-menu {
                width: 100%;
                margin-top: 0.25rem;
            }
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
                    <div class="card">
                        <div class="card-body">
                            <h4>Thông Báo</h4>
                            <?php if ($user): ?>
                                <?php if (!empty($notifications)): ?>
                                    <div class="btn-container">
                                        <a href="?action=mark_all_as_read" class="btn btn-primary"><i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc</a>
                                    </div>
                                <?php endif; ?>
                                <?php if (empty($notifications)): ?>
                                    <p class="no-notifications"><i class="fas fa-bell-slash"></i> Không có thông báo nào.</p>
                                <?php else: ?>
                                    <?php foreach ($notifications as $notification): ?>
                                        <div class="<?php echo $notification['is_read'] ? 'notification read' : 'notification unread'; ?>">
                                            <i class="fas fa-bell"></i>
                                            <div class="notification-content">
                                                <p><?php echo htmlspecialchars($notification['message']); ?></p>
                                                <small><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($notification['created_at']))); ?></small>
                                                <?php if (!$notification['is_read']): ?>
                                                    <a href="?action=mark_as_read&notificationId=<?php echo $notification['notification_id']; ?>">Đánh dấu đã đọc</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="alert-warning">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Bạn chưa đăng nhập. Vui lòng <a href="?action=login">đăng nhập</a> để xem thông báo.
                                </div>
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
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
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
                <?php
$baseUrl = '/nurseborn/';
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="?action=home" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="<?php echo $baseUrl; ?>/static/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
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
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'home' ? 'active' : ''; ?>">
            <a href="?action=home" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Trang chủ</div>
            </a>
        </li>

        <!-- Schedule -->
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'nurse_schedule' ? 'active' : ''; ?>">
            <a href="?action=nurse_schedule" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div>Lịch Làm Việc</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'notifications' ? 'active' : ''; ?>">
            <a href="?action=notifications" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div>Thông Báo</div>
            </a>
        </li>

        <!-- Pending Bookings -->
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'pending_bookings' ? 'active' : ''; ?>">
            <a href="?action=pending_bookings" class="menu-link">
                <i class="menu-icon tf-icons bx bx-time"></i>
                <div>Lịch Đặt Chờ Xác Nhận</div>
            </a>
        </li>

        <!-- Availability -->
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'nurse_availability' ? 'active' : ''; ?>">
            <a href="?action=nurse_availability" class="menu-link">
                <i class="menu-icon tf-icons bx bx-check-square"></i>
                <div>Quản Lý Lịch Làm Việc</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'messages' ? 'active' : ''; ?>">
            <a href="?action=messages" class="menu-link">
                <i class="menu-icon tf-icons bx bx-check-square"></i>
                <div>Trò chuyện</div>
            </a>
        </li>

        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'nurse_income' ? 'active' : ''; ?>">
            <a href="?action=nurse_income" class="menu-link">
                <i class="menu-icon tf-icons bx bx-wallet"></i>
                <div>Thống Kê Thu Nhập</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'review_nurse' ? 'active' : ''; ?>">
            <a href="?action=review_nurse" class="menu-link">
                <i class="menu-icon tf-icons bx bx-wallet"></i>
                <div>Đánh Giá</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Tài Khoản</span>
        </li>
        <!-- Account Settings -->
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'nurse_profile' ? 'active open' : ''; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div>Cài Đặt Tài Khoản</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'nurse_profile' ? 'active' : ''; ?>">
                    <a href="?action=nurse_profile" class="menu-link">
                        <div>Tài Khoản</div>
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

<style>
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
</style>
            `;
        } else if (currentUser.role === 'FAMILY') {
            menuPlaceholder.innerHTML = `
                <?php
$baseUrl = '/nurseborn/';
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="?action=family_home" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="<?php echo $baseUrl; ?>/static/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
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
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'family_home' ? 'active' : ''; ?>">
            <a href="?action=family_home" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Trang chủ</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'bookings' ? 'active' : ''; ?>">
            <a href="?action=bookings" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div>Danh Sách Lịch Đặt</div>
            </a>
        </li>

        <!-- Family Management -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Quản lý Gia đình</span>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'nursepage' ? 'active' : ''; ?>">
            <a href="?action=nursepage" class="menu-link">
                <i class="menu-icon tf-icons bx bx-search"></i>
                <div>Đặt dịch vụ</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'messages' ? 'active' : ''; ?>">
            <a href="?action=messages" class="menu-link">
                <i class="menu-icon tf-icons bx bx-check-square"></i>
                <div>Trò chuyện</div>
            </a>
        </li>

        <!-- Account Settings -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Tài khoản</span>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'user_profile' ? 'active' : ''; ?>">
            <a href="?action=user_profile" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div>Hồ sơ</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'update_user' ? 'active' : ''; ?>">
            <a href="?action=update_user" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div>Cập nhật hồ sơ</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'notifications' ? 'active' : ''; ?>">
            <a href="?action=notifications" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
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

<style>
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
</style>
            `;
        } else if (currentUser.role === 'ADMIN') {
            menuPlaceholder.innerHTML = `
                <aside id="layout-menu" class="layout-menu menu-vertical menu">
                    <div class="app-brand demo">
                        <a href="?action=admin_dashboard" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <img src="<?php echo $baseUrl; ?>/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
                            </span>
                            <span class="app-brand-text demo text-body fw-bolder text-uppercase">NURSEBORN</span>
                        </a>
                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </div>
                    <div class="menu-inner-shadow"></div>
                    <ul class="menu-inner py-1">
                        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'admin_dashboard' ? 'active' : ''; ?>">
                            <a href="?action=admin_dashboard" class="menu-link">
                                <i class="menu-icon fas fa-tachometer-alt"></i>
                                <div>Bảng Điều Khiển</div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'manage_users' ? 'active' : ''; ?>">
                            <a href="?action=manage_users" class="menu-link">
                                <i class="menu-icon fas fa-users"></i>
                                <div>Quản Lý Người Dùng</div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'manage_bookings' ? 'active' : ''; ?>">
                            <a href="?action=manage_bookings" class="menu-link">
                                <i class="menu-icon fas fa-calendar-check"></i>
                                <div>Quản Lý Lịch Đặt</div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'notifications' ? 'active' : ''; ?>">
                            <a href="?action=notifications" class="menu-link">
                                <i class="menu-icon fas fa-bell"></i>
                                <div>Thông Báo</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="?action=logout" class="menu-link">
                                <i class="menu-icon fas fa-sign-out-alt"></i>
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
                                <img src="<?php echo $baseUrl; ?>/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
                            </span>
                            <span class="app-brand-text demo text-body fw-bolder text-uppercase">NURSEBORN</span>
                        </a>
                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </div>
                    <div class="menu-inner-shadow"></div>
                    <ul class="menu-inner py-1">
                        <li class="menu-item">
                            <a href="?action=login" class="menu-link">
                                <i class="menu-icon fas fa-sign-in-alt"></i>
                                <div>Đăng nhập</div>
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
               <?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
$baseUrl = '/nurseborn';

// Nếu không có $nurseProfile trong session, lấy từ database
if (!$nurseProfile && $user && isset($user['user_id'])) {
    require_once __DIR__ . '/../models/NurseProfileModel.php';
    $nurseProfileModel = new NurseProfileModel($conn);
    $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($user['user_id']);
}

// Đường dẫn ảnh đại diện
$profileImage = $nurseProfile && isset($nurseProfile['profile_image']) 
    ? htmlspecialchars($nurseProfile['profile_image']) 
    : '/static/assets/img/avatars/default_profile.jpg';

// Debug
error_log("Navbar Profile Image: " . $profileImage);
error_log("Navbar Final Image URL: " . $baseUrl . $profileImage);
?>

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
                        <img src="<?php echo $baseUrl . $profileImage; ?>" alt="Ảnh đại diện" class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="?action=nurse_profile">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="<?php echo $baseUrl . $profileImage; ?>" alt="Ảnh đại diện" class="rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block"><?php echo $user ? htmlspecialchars($user['full_name']) : 'Y Tá'; ?></span>
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

<style>
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
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }
    .avatar-online img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        border-radius: 50%;
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
        display: flex;
        align-items: center;
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
        .layout-navbar {
            border-radius: 0;
        }
        .navbar-nav-right {
            padding: 5px 0;
        }
    }
    @media (max-width: 768px) {
        .avatar-online {
            width: 35px;
            height: 35px;
        }
        .avatar-online img {
            width: 100%;
            height: 100%;
        }
        .dropdown-item {
            font-size: 0.9rem;
            padding: 8px 15px;
        }
    }
</style>
            `;
        } else if (currentUser.role === 'FAMILY') {
            navbarPlaceholder.innerHTML = `
               <?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
$baseUrl = '/nurseborn';

$avatar = $nurseProfile && !empty($nurseProfile['profile_image']) ? htmlspecialchars($nurseProfile['profile_image']) : '/static/assets/img/avatars/1.png';
$fullName = $user ? htmlspecialchars($user['full_name']) : 'Người dùng';
$role = $user ? htmlspecialchars($user['role']) : 'Khách';
?>

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
                        <img src="<?php echo $baseUrl . $avatar; ?>" alt="Ảnh đại diện" class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="?action=user_profile">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="<?php echo $baseUrl . $avatar; ?>" alt="Ảnh đại diện" class="rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block"><?php echo $fullName; ?></span>
                                    <small class="text-muted"><?php echo $role; ?></small>
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

<style>
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
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }
    .avatar-online img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        border-radius: 50%;
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
        display: flex;
        align-items: center;
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
        .layout-navbar {
            border-radius: 0;
        }
        .navbar-nav-right {
            padding: 5px 0;
        }
    }
    @media (max-width: 768px) {
        .avatar-online {
            width: 35px;
            height: 35px;
        }
        .avatar-online img {
            width: 100%;
            height: 100%;
        }
        .dropdown-item {
            font-size: 0.9rem;
            padding: 8px 15px;
        }
    }
</style>
            `;
        } else if (currentUser.role === 'ADMIN') {
            navbarPlaceholder.innerHTML = `
               <?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
$baseUrl = '/nurseborn';

$avatar = $nurseProfile && !empty($nurseProfile['profile_image']) ? htmlspecialchars($nurseProfile['profile_image']) : '/static/assets/img/avatars/1.png';
$fullName = $user ? htmlspecialchars($user['full_name']) : 'Người dùng';
$role = $user ? htmlspecialchars($user['role']) : 'Khách';
?>

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
                        <img src="<?php echo $baseUrl . $avatar; ?>" alt="Ảnh đại diện" class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="?action=user_profile">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="<?php echo $baseUrl . $avatar; ?>" alt="Ảnh đại diện" class="rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block"><?php echo $fullName; ?></span>
                                    <small class="text-muted"><?php echo $role; ?></small>
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

<style>
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
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }
    .avatar-online img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        border-radius: 50%;
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
        display: flex;
        align-items: center;
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
        .layout-navbar {
            border-radius: 0;
        }
        .navbar-nav-right {
            padding: 5px 0;
        }
    }
    @media (max-width: 768px) {
        .avatar-online {
            width: 35px;
            height: 35px;
        }
        .avatar-online img {
            width: 100%;
            height: 100%;
        }
        .dropdown-item {
            font-size: 0.9rem;
            padding: 8px 15px;
        }
    }
</style>
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