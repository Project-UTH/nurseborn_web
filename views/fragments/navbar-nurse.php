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