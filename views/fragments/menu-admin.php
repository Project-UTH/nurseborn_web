<?php
$baseUrl = '/nurseborn/';
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="?action=admin_home" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="<?php echo $baseUrl; ?>static/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
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
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'admin_home' ? 'active' : ''; ?>">
            <a href="?action=admin_home" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Trang chủ</div>
            </a>
        </li>

        <!-- Admin Management -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Quản lý Admin</span>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'review_nurse_profile' ? 'active' : ''; ?>">
            <a href="?action=review_nurse_profile" class="menu-link">
                <i class="menu-icon tf-icons bx bx-check-circle"></i>
                <div>Duyệt y tá</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'admin_approved_nurses' ? 'active' : ''; ?>">
            <a href="?action=admin_approved_nurses" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-check"></i>
                <div>Y tá đã duyệt</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'admin_family_accounts' ? 'active' : ''; ?>">
            <a href="?action=admin_family_accounts" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div>Tài khoản Family</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'web_income' ? 'active' : ''; ?>">
            <a href="?action=web_income" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bar-chart"></i>
                <div>Thống kê</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'nurse_ranking' ? 'active' : ''; ?>">
            <a href="?action=nurse_ranking" class="menu-link">
                <i class="menu-icon tf-icons bx bx-trophy"></i>
                <div>Xếp hạng y tá</div>
            </a>
        </li>
        <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'admin_feedback' ? 'active' : ''; ?>">
            <a href="?action=admin_feedback" class="menu-link">
                <i class="menu-icon tf-icons bx bx-trophy"></i>
                <div>Đánh giá</div>
            </a>
        </li>
         <li class="menu-item <?php echo isset($_GET['action']) && $_GET['action'] === 'admin_bookings' ? 'active' : ''; ?>">
            <a href="?action=admin_bookings" class="menu-link">
                <i class="menu-icon tf-icons bx bx-trophy"></i>
                <div>Danh sách lịch đặt</div>
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