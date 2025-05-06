<?php
$baseUrl = '/nurseborn';
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
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