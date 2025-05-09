<?php
$baseUrl = '';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfiles = $nurseProfiles ?? [];
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>static/assets/" data-template="vertical-menu-template-free">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <style>
        /* Tùy chỉnh tổng thể */
        body {
            background-color: #f7f9fc;
            font-family: 'Poppins', sans-serif;
        }
        .container-xxl {
            max-width: 1200px;
        }

        /* Tiêu đề trang */
        h4.fw-bold.py-3.mb-4 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, #0d6efd, #28a745);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            margin-bottom: 40px;
            padding: 15px 0;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }
        h4.fw-bold.py-3.mb-4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 5px;
            background: linear-gradient(45deg, #0d6efd, #28a745);
            border-radius: 3px;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Thẻ hồ sơ y tá */
        .card.mb-4 {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(145deg, #ffffff, #f0f4f8);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card.mb-4:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }
        .card-body {
            padding: 25px;
        }
        .d-flex.align-items-center.mb-3 {
            gap: 15px;
        }
        .rounded-circle.me-3 {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 3px solid #0d6efd;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .d-flex.align-items-center.mb-3 h5 {
            color: #0d6efd;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .d-flex.align-items-center.mb-3 p {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 0;
        }
        .mb-2 {
            color: #5a6268;
            font-size: 0.95rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mb-2 strong {
            color: #343a40;
            font-weight: 600;
        }
        .mb-2 i {
            color: #0d6efd;
            font-size: 1.1rem;
        }
        .mb-2 ul {
            margin: 0;
            padding-left: 20px;
        }
        .mb-2 ul li {
            color: #5a6268;
            font-size: 0.95rem;
        }
        .mb-2 ul li a {
            color: #0d6efd;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .mb-2 ul li a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .text-success {
            color: #28a745 !important;
            font-weight: 600;
        }
        .text-danger {
            color: #dc3545 !important;
            font-weight: 600;
        }

        /* Nút bấm */
        .btn-primary {
            background: linear-gradient(45deg, #28a745, #34c759);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            color: #fff;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #218838, #2eb44f);
            transform: scale(1.05);
            color: #fff;
        }
        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #e4606d);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            color: #fff;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-danger:hover {
            background: linear-gradient(45deg, #c82333, #d43f4c);
            transform: scale(1.05);
            color: #fff;
        }
        .btn i {
            margin-right: 5px;
        }
        .d-flex.gap-2 {
            gap: 10px !important;
            justify-content: center;
        }

        /* Thông báo khi không có hồ sơ */
        .text-center.text-muted {
            color: #6c757d;
            font-size: 1.1rem;
            font-style: italic;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu (Sidebar) -->
        <?php include __DIR__ . '/fragments/menu-admin.php'; ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->
            <?php include __DIR__ . '/fragments/navbar.php'; ?>
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4">Danh Sách Hồ Sơ Y Tá</h4>
                    <!-- Thông báo -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <!-- Danh sách hồ sơ y tá -->
                    <?php if (empty($nurseProfiles)): ?>
                        <div class="text-center text-muted">
                            <p>Hiện tại không có hồ sơ y tá nào để phê duyệt.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($nurseProfiles as $nurse): ?>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?php echo htmlspecialchars($nurse['profile_image'] ?? $baseUrl . 'static/assets/img/avatars/default_profile.jpg'); ?>" alt="Ảnh hồ sơ" class="rounded-circle me-3" />
                                        <div>
                                            <h5 class="mb-1"><?php echo htmlspecialchars($nurse['full_name'] ?? 'Chưa xác định'); ?></h5>
                                            <p class="mb-0"><strong>ID:</strong> <?php echo htmlspecialchars($nurse['nurse_profile_id'] ?? 'Chưa xác định'); ?></p>
                                        </div>
                                    </div>
                                    <p class="mb-2">
                                        <i class="fas fa-user"></i>
                                        <strong>Tên đăng nhập:</strong>
                                        <span><?php echo htmlspecialchars($nurse['username'] ?? 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-envelope"></i>
                                        <strong>Email:</strong>
                                        <span><?php echo htmlspecialchars($nurse['email'] ?? 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-phone"></i>
                                        <strong>Số điện thoại:</strong>
                                        <span><?php echo htmlspecialchars($nurse['phone_number'] ?? 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <strong>Địa chỉ:</strong>
                                        <span><?php echo htmlspecialchars($nurse['address'] ?? 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-user-tag"></i>
                                        <strong>Vai trò:</strong>
                                        <span><?php echo htmlspecialchars($nurse['role'] ?? 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-location-dot"></i>
                                        <strong>Vị trí:</strong>
                                        <span><?php echo htmlspecialchars($nurse['location'] ?? 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-tools"></i>
                                        <strong>Kỹ năng:</strong>
                                        <span><?php echo htmlspecialchars($nurse['skills'] ?? 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-briefcase"></i>
                                        <strong>Kinh nghiệm:</strong>
                                        <span><?php echo htmlspecialchars($nurse['experience_years'] !== null ? $nurse['experience_years'] . ' năm' : 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <strong>Lương giờ:</strong>
                                        <span><?php echo htmlspecialchars($nurse['hourly_rate'] !== null ? number_format($nurse['hourly_rate'], 0, ',', '.') . ' VNĐ/giờ' : 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <strong>Lương ngày:</strong>
                                        <span><?php echo htmlspecialchars($nurse['daily_rate'] !== null ? number_format($nurse['daily_rate'], 0, ',', '.') . ' VNĐ/ngày' : 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <strong>Lương tuần:</strong>
                                        <span><?php echo htmlspecialchars($nurse['weekly_rate'] !== null ? number_format($nurse['weekly_rate'], 0, ',', '.') . ' VNĐ/tuần' : 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Tiểu sử:</strong>
                                        <span><?php echo htmlspecialchars($nurse['bio'] ?? 'Không có thông tin'); ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-certificate"></i>
                                        <strong>Chứng chỉ:</strong>
                                        <?php if (!empty($nurse['certificates'])): ?>
                                            <ul>
                                                <?php foreach ($nurse['certificates'] as $certificate): ?>
                                                    <li>
                                                        <?php echo htmlspecialchars($certificate['certificate_name']); ?>
                                                        (<a href="<?php echo htmlspecialchars($certificate['file_path']); ?>" target="_blank">Xem file</a>)
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <span>Không có thông tin</span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>Trạng thái:</strong>
                                        <span class="<?php echo $nurse['is_approved'] ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $nurse['is_approved'] ? 'Đã phê duyệt' : 'Chưa phê duyệt'; ?>
                                        </span>
                                    </p>
                                    <div class="d-flex gap-2">
                                        <form action="?action=review_nurse_profile" method="post" style="display: inline;">
                                            <input type="hidden" name="nurse_user_id" value="<?php echo htmlspecialchars($nurse['user_id']); ?>">
                                            <input type="hidden" name="action_type" value="approve">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-check"></i> Phê duyệt
                                            </button>
                                        </form>
                                        <form action="?action=review_nurse_profile" method="post" style="display: inline;">
                                            <input type="hidden" name="nurse_user_id" value="<?php echo htmlspecialchars($nurse['user_id']); ?>">
                                            <input type="hidden" name="action_type" value="reject">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-times"></i> Từ chối
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <!-- / Content -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/popper/popper.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/js/menu.js"></script>
<!-- Main JS -->
<script src="<?php echo $baseUrl; ?>static/assets/js/main.js"></script>
<!-- Page JS -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>