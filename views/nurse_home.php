<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
$pageTitle = 'Trang chủ Y tá';
$baseUrl = '/nurseborn';
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>/static/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <!-- Thêm Google Fonts cho Montserrat và Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Các tài nguyên khác từ head.php -->
    <?php include __DIR__ . '/fragments/head.php'; ?>
</head>

<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->
        <?php include __DIR__ . '/fragments/menu-nurse.php'; ?>

        <div class="layout-page">
            <!-- Navbar -->
            <?php include __DIR__ . '/fragments/navbar-nurse.php'; ?>

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-fluid flex-grow-1 container-p-y">
                    <!-- Header Section -->
                    <section class="header-section">
                        <div class="container text-center">
                            <h1 class="display-4">Chào Mừng Y Tá Đến Với NurseBorn</h1>
                            <h2 class="display-5">Bắt Đầu Hành Trình Chăm Sóc</h2>
                            <?php if ($user): ?>
                                <h3>Xin Chào, <?php echo htmlspecialchars($user['full_name']); ?>!</h3>
                            <?php else: ?>
                                <h3>Xin Chào, Y Tá!</h3>
                            <?php endif; ?>
                            <?php if ($nurseProfile && isset($nurseProfile['location'])): ?>
                                <h3>Địa Điểm Làm Việc: <?php echo htmlspecialchars($nurseProfile['location']); ?></h3>
                            <?php else: ?>
                                <h3>Địa Điểm Làm Việc: Chưa có thông tin</h3>
                            <?php endif; ?>
                            <a href="?action=nurse_schedule" class="btn btn-primary btn-lg mt-4 cta-button">Xem Lịch Làm Việc</a>
                            <a href="?action=review_nurse&nurseUserId=<?php echo htmlspecialchars($user['user_id'] ?? ''); ?>" class="btn btn-primary btn-lg mt-4 cta-button ms-3">Xem Đánh Giá Y Tá</a>
                        </div>
                    </section>

                    <!-- Introduction Section -->
                    <section class="intro-section">
                        <div class="container text-center">
                            <h2 class="section-title">Giới Thiệu Về NurseBorn Dành Cho Y Tá</h2>
                            <p class="lead">
                                NurseBorn là nền tảng kết nối y tá chuyên nghiệp với các gia đình cần dịch vụ chăm sóc sức khỏe tại nhà. Là một y tá, bạn có thể dễ dàng quản lý lịch làm việc, nhận lịch đặt từ khách hàng, và cung cấp dịch vụ chăm sóc tận tâm. NurseBorn hỗ trợ bạn trong việc phát triển sự nghiệp và mang lại giá trị cho cộng đồng. Hãy cùng chúng tôi tạo nên sự khác biệt trong chăm sóc sức khỏe!
                            </p>
                        </div>
                    </section>

                    <!-- Features Section -->
                    <section class="features-section">
                        <div class="container">
                            <h2 class="section-title text-center">Tại Sao Y Tá Chọn NurseBorn?</h2>
                            <div class="row">
                                <div class="col-md-3 text-center mb-4">
                                    <div class="feature-card">
                                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                                        <h4>Quản Lý Lịch Linh Hoạt</h4>
                                        <p>Dễ dàng sắp xếp lịch làm việc theo thời gian phù hợp với bạn.</p>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-4">
                                    <div class="feature-card">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h4>Kết Nối Với Gia Đình</h4>
                                        <p>Nhận lịch đặt từ các gia đình cần dịch vụ chăm sóc chuyên nghiệp.</p>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-4">
                                    <div class="feature-card">
                                        <i class="fas fa-money-bill-wave fa-3x mb-3"></i>
                                        <h4>Thu Nhập Ổn Định</h4>
                                        <p>Đảm bảo nguồn thu nhập ổn định với các lịch đặt thường xuyên.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <!-- / Content -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- / Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
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

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
        min-height: 100vh;
        margin: 0;
        overflow-x: hidden;
    }

    /* Header Section */
    .header-section {
        position: relative;
        background: url('<?php echo $baseUrl; ?>/static/assets/img/backgrounds/ytachamtre.jpg') no-repeat center center/cover;
        height: 80vh;
        color: #fff;
        display: flex;
        align-items: center;
        padding: 0 20px;
    }
    .header-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1;
    }
    .header-section .container {
        position: relative;
        z-index: 2;
    }
    .header-section h1 {
        font-size: 4rem;
        font-weight: 800;
        margin-bottom: 15px;
        color: rgb(0, 238, 255);
        font-family: 'Montserrat', sans-serif;
        animation: fadeInDown 1s ease-in-out;
    }
    .header-section h2 {
        font-size: 2.5rem;
        font-weight: 500;
        color: rgb(0, 238, 255);
        font-family: 'Montserrat', sans-serif;
        margin-bottom: 15px;
        animation: fadeInDown 1.2s ease-in-out;
    }
    .header-section h3 {
        font-size: 1.8rem;
        font-weight: 300;
        margin-bottom: 15px;
        color: rgb(224, 249, 251);
        animation: fadeInDown 1.4s ease-in-out;
    }
    .header-section .cta-button {
        background: linear-gradient(45deg, #28a745, #34c759);
        border: none;
        padding: 12px 30px;
        font-size: 1.2rem;
        font-weight: 600;
        transition: transform 0.3s ease, background 0.3s ease;
        margin: 0 10px;
    }
    .header-section .cta-button:hover {
        background: linear-gradient(45deg, #218838, #2eb44f);
        transform: scale(1.1);
    }

    /* Introduction Section */
    .intro-section {
        padding: 80px 20px;
        background: #fff;
        margin: -60px 30px 0;
        border-radius: 30px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        animation: fadeInUp 1s ease-in-out;
    }
    .intro-section .section-title {
        font-size: 2.8rem;
        color: #0d6efd;
        margin-bottom: 25px;
        font-weight: 700;
    }
    .intro-section .lead {
        font-size: 1.2rem;
        color: #6c757d;
        max-width: 900px;
        margin: 0 auto;
        line-height: 1.8;
    }

    /* Features Section */
    .features-section {
        padding: 80px 20px;
        background: #f8f9fa;
    }
    .features-section .section-title {
        font-size: 2.8rem;
        color: #0d6efd;
        margin-bottom: 50px;
        font-weight: 700;
    }
    .features-section .row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }
    .feature-card {
        background: #fff;
        padding: 30px 20px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        flex: 1;
        text-align: center;
        height: 100%;
    }
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }
    .feature-card i {
        color: #0d6efd;
        transition: color 0.3s ease;
    }
    .feature-card:hover i {
        color: #28a745;
    }
    .feature-card h4 {
        font-size: 1.5rem;
        color: #343a40;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .feature-card p {
        font-size: 1rem;
        color: #6c757d;
        margin: 0;
    }

    /* Animations */
    @keyframes fadeInDown {
        0% {
            opacity: 0;
            transform: translateY(-50px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(50px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .header-section {
            height: 70vh;
        }
        .header-section h1 {
            font-size: 3rem;
        }
        .header-section h2 {
            font-size: 2rem;
        }
        .header-section h3 {
            font-size: 1.5rem;
        }
        .intro-section {
            margin: -40px 20px 0;
            padding: 60px 15px;
        }
        .intro-section .section-title {
            font-size: 2.2rem;
        }
        .features-section .section-title {
            font-size: 2.2rem;
        }
        .features-section .row {
            flex-wrap: wrap;
            gap: 30px;
        }
        .feature-card {
            flex: 1 1 45%;
            padding: 25px 15px;
        }
    }
    @media (max-width: 768px) {
        .header-section {
            height: 60vh;
        }
        .header-section h1 {
            font-size: 2.5rem;
        }
        .header-section h2 {
            font-size: 1.8rem;
        }
        .header-section h3 {
            font-size: 1.3rem;
        }
        .header-section .cta-button {
            padding: 10px 20px;
            font-size: 1rem;
            display: inline-block;
            margin: 10px 5px;
        }
        .intro-section {
            margin: -30px 10px 0;
            padding: 40px 15px;
        }
        .intro-section .section-title {
            font-size: 2rem;
        }
        .intro-section .lead {
            font-size: 1rem;
        }
        .features-section {
            padding: 60px 15px;
        }
        .features-section .section-title {
            font-size: 2rem;
        }
        .features-section .row {
            flex-direction: column;
            gap: 30px;
        }
        .feature-card {
            flex: 1 1 100%;
            padding: 20px 15px;
        }
        .feature-card h4 {
            font-size: 1.3rem;
        }
        .feature-card p {
            font-size: 0.9rem;
        }
    }
</style>
</body>
</html>