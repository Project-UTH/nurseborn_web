<?php
$baseUrl = '/nurseborn';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <style>
        .container-p-y {
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        h4 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1a3c34;
            margin-bottom: 1.5rem;
        }
        h5 {
            font-size: 1.3rem;
            font-weight: 500;
            color: #2e4b5b;
            margin-bottom: 1rem;
        }
        .card {
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        p {
            font-size: 1rem;
            margin-bottom: 0.8rem;
            color: #333;
            display: flex;
            align-items: center;
        }
        p strong {
            font-weight: 600;
            color: #1a3c34;
            width: 150px;
            display: inline-block;
        }
        p span {
            color: #555;
            display: inline-block;
        }
        .btn {
            font-size: 0.95rem;
            padding: 0.6rem 1.2rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
            margin-top: 1rem;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        .btn-danger:hover {
            background-color: #b02a37;
            border-color: #b02a37;
        }
        .alert-warning {
            padding: 1rem;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include __DIR__ . '/fragments/menu-nurse.php'; ?>
        <div class="layout-page">
            <?php include __DIR__ . '/fragments/navbar-nurse.php'; ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <?php if ($user): ?>
                        <h4 class="mb-4">Hồ sơ y tá</h4>
                        <div class="card p-4 mb-4">
                            <h5 class="card-title">Thông tin cá nhân của y tá</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Họ và tên:</strong> <span><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></span></p>
                                    <p><strong>Tên đăng nhập:</strong> <span><?php echo htmlspecialchars($user['username'] ?? ''); ?></span></p>
                                    <p><strong>Email:</strong> <span><?php echo htmlspecialchars($user['email'] ?? ''); ?></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Số điện thoại:</strong> <span><?php echo htmlspecialchars($user['phone_number'] ?? 'Không có thông tin'); ?></span></p>
                                    <p><strong>Địa chỉ:</strong> <span><?php echo htmlspecialchars($user['address'] ?? 'Không có thông tin'); ?></span></p>
                                    <p><strong>Vai trò:</strong> <span><?php echo htmlspecialchars($user['role'] ?? ''); ?></span></p>
                                </div>
                            </div>
                        </div>

                        <?php if ($user['role'] === 'NURSE'): ?>
                            <div class="card p-4 mb-4">
                                <h5 class="card-title">Thông tin dịch vụ của y tá</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Vị trí:</strong> <span><?php echo htmlspecialchars($nurseProfile['location'] ?? 'Không có thông tin'); ?></span></p>
                                        <p><strong>Kỹ năng:</strong> <span><?php echo htmlspecialchars($nurseProfile['skills'] ?? 'Không có thông tin'); ?></span></p>
                                        <p><strong>Kinh nghiệm:</strong> <span><?php echo isset($nurseProfile['experience_years']) ? htmlspecialchars($nurseProfile['experience_years']) . ' năm' : 'Không có thông tin'; ?></span></p>
                                        <p><strong>Tiểu sử:</strong> <span><?php echo htmlspecialchars($nurseProfile['bio'] ?? 'Không có thông tin'); ?></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Lương giờ:</strong> <span><?php echo isset($nurseProfile['hourly_rate']) ? htmlspecialchars($nurseProfile['hourly_rate']) . ' VNĐ/giờ' : 'Không có thông tin'; ?></span></p>
                                        <p><strong>Lương ngày:</strong> <span><?php echo isset($nurseProfile['daily_rate']) ? htmlspecialchars($nurseProfile['daily_rate']) . ' VNĐ/ngày' : 'Không có thông tin'; ?></span></p>
                                        <p><strong>Lương tuần:</strong> <span><?php echo isset($nurseProfile['weekly_rate']) ? htmlspecialchars($nurseProfile['weekly_rate']) . ' VNĐ/tuần' : 'Không có thông tin'; ?></span></p>
                                        <p><strong>Trạng thái phê duyệt:</strong> <span><?php echo isset($nurseProfile['is_approved']) ? ($nurseProfile['is_approved'] ? 'Đã phê duyệt' : 'Chưa phê duyệt') : 'Không có thông tin'; ?></span></p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <p><strong>Ảnh đại diện:</strong>
                                        <img src="<?php echo htmlspecialchars($nurseProfile['profile_image'] ?? $baseUrl . '/static/assets/img/avatars/default_profile.jpg'); ?>" alt="Profile Image" style="max-width: 100px;"/>
                                    </p>
                                </div>
                                <?php if (!empty($nurseProfile['certificates'])): ?>
                                    <h6 class="mt-3">Chứng chỉ:</h6>
                                    <ul>
                                        <?php foreach ($nurseProfile['certificates'] as $certificate): ?>
                                            <li>
                                                <span><?php echo htmlspecialchars($certificate['certificate_name'] ?? $certificate['name']); ?></span>
                                                (<a href="<?php echo htmlspecialchars($certificate['file_path']); ?>" target="_blank">Xem file</a>)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p><strong>Chứng chỉ:</strong> Không có thông tin</p>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="?action=home" class="btn btn-primary">Quay lại trang chủ</a>
                                <a href="?action=update_nurse" class="btn btn-success" id="update-nurse-btn">Cập nhật hồ sơ</a>
                                <a href="?action=logout" class="btn btn-danger">Đăng xuất</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <h4>Bạn chưa đăng nhập</h4>
                            <p>Vui lòng <a href="?action=login">đăng nhập</a> để xem hồ sơ y tá của bạn.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

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