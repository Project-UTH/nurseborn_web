<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
$pageTitle = 'Trang chủ Y tá';
$baseUrl = '/nurseborn';
?>
<!DOCTYPE html>
<html lang="vi">
<?php include __DIR__ . '/fragments/head.php'; ?>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->
        <?php include __DIR__ . '/fragments/menu-nurse.php'; ?>

        <div class="layout-page">
            <!-- Navbar -->
            <?php include __DIR__ . '/fragments/navbar-nurse.php'; ?>

            <div class="content-wrapper">
                <div class="content-xxl flex-grow-1 container-p-y">
                    <!-- Welcome Content -->
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <h5 class="card-title">Chào mừng, <?php echo $user && $user['full_name'] ? htmlspecialchars($user['full_name']) : 'Y Tá'; ?>!</h5>
                            <p class="card-text">
                                Chào mừng bạn đến với NurseBorn! Bạn đã sẵn sàng để bắt đầu công việc chưa?
                                Hãy kiểm tra lịch làm việc và các lịch đặt chờ xác nhận của bạn.
                            </p>
                            <?php if ($nurseProfile && $nurseProfile['location']): ?>
                                <p class="card-text">
                                    <strong>Địa điểm làm việc:</strong> <?php echo htmlspecialchars($nurseProfile['location']); ?>
                                </p>
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