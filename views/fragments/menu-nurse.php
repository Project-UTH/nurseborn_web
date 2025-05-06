<?php
$baseUrl = '/nurseborn';
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
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

        <!-- Misc -->
        <li class="menu-item">
            <a href="?action=logout" class="menu-link">
                <i class="menu-icon tf-icons bx bx-log-out"></i>
                <div>Đăng Xuất</div>
            </a>
        </li>
    </ul>
</aside>