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