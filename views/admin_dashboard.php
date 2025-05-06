<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$pageTitle = 'Bảng điều khiển Admin';
$baseUrl = '/nurseborn';
?>
<!DOCTYPE html>
<html lang="vi">
<?php include __DIR__ . '/fragments/head.php'; ?>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->
        <?php include __DIR__ . '/fragments/menu-admin.php'; ?>

        <div class="layout-page">
            <!-- Navbar -->
            <?php include __DIR__ . '/fragments/navbar.php'; ?>

            <div class="content-wrapper">
                <div class="content-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4">Trang chủ Admin</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Chào mừng đến với bảng điều khiển Admin</h5>
                                    <p class="card-text">
                                        Đây là trang chủ dành riêng cho Admin. Bạn có thể quản lý hồ sơ y tá, xem thống kê, và thực hiện các tác vụ quản trị khác từ menu bên trái.
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