<?php
$baseUrl = '/nurseborn';
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
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

        <!-- Logout -->
        <li class="menu-item">
            <a href="?action=logout" class="menu-link">
                <i class="menu-icon tf-icons bx bx-log-out"></i>
                <div>Đăng xuất</div>
            </a>
        </li>
    </ul>
</aside>