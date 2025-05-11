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
    ? $nurseProfile['profile_image'] 
    : '/static/assets/img/avatars/default_profile.jpg';

// Debug
error_log("Navbar Profile Image: " . $profileImage);
error_log("Navbar Final Image URL: " . $baseUrl . $profileImage);
?>
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
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
                        <img src="<?php echo $baseUrl . htmlspecialchars($profileImage); ?>" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="?action=nurse_profile">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="<?php echo $baseUrl . htmlspecialchars($profileImage); ?>" alt class="w-px-40 h-auto rounded-circle" />
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