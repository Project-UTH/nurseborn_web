<?php
// Đảm bảo $_SESSION có dữ liệu
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$familyProfile = isset($_SESSION['family_profile']) ? $_SESSION['family_profile'] : null;
$pageTitle = 'Trang chủ';
$baseUrl = '/nurseborn'; // Điều chỉnh nếu dự án nằm trong thư mục con
?>
<!DOCTYPE html>
<html lang="vi">
<?php include __DIR__ . '/fragments/head.php'; ?>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu động dựa trên vai trò -->
        <?php
        if ($user && $user['role'] === 'FAMILY') {
            include __DIR__ . '/fragments/menu-family.php';
        } elseif ($user && $user['role'] === 'NURSE') {
            include __DIR__ . '/fragments/menu-nurse.php';
        } elseif ($user && $user['role'] === 'ADMIN') {
            include __DIR__ . '/fragments/menu-admin.php';
        } else {
            // Menu mặc định cho người chưa đăng nhập
            include __DIR__ . '/fragments/menu-family.php';
        }
        ?>

        <div class="layout-page">
            <!-- Navbar -->
            <?php include __DIR__ . '/fragments/navbar.php'; ?>

            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4">Trang chủ <?php echo $user && $user['role'] === 'FAMILY' ? 'Gia đình' : ''; ?></h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Chào mừng đến với NurseBorn</h5>
                                    <p class="card-text">
                                        <?php if ($user && $user['role'] === 'FAMILY'): ?>
                                            Xin chào, <?php echo htmlspecialchars($user['full_name'] ?? 'Người dùng'); ?>!
                                            <?php if ($familyProfile): ?>
                                                <br>Hồ sơ gia đình: Tên trẻ - <?php echo htmlspecialchars($familyProfile['child_name'] ?? 'Chưa có'); ?>
                                            <?php else: ?>
                                                <br>Hồ sơ gia đình: Chưa được tạo. <a href="?action=update_user">Cập nhật ngay</a>.
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Chào mừng bạn đến với NurseBorn! Vui lòng <a href="?action=login">đăng nhập</a> hoặc <a href="?action=role_selection">đăng ký</a> để sử dụng dịch vụ.
                                        <?php endif; ?>
                                    </p>
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
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/dashboards-analytics.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>