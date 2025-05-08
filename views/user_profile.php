<?php
$baseUrl = '/nurseborn';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$familyProfile = isset($_SESSION['family_profile']) ? $_SESSION['family_profile'] : null;
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
        h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1a3c34;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 0.5rem;
        }
        h5 {
            font-size: 1.3rem;
            font-weight: 500;
            color: #2e4b5b;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        .btn-danger:hover {
            background-color: #b02a37;
            border-color: #b02a37;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 + p {
            font-size: 1rem;
            color: #666;
        }
        h2 + p a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        h2 + p a:hover {
            text-decoration: underline;
            color: #0056b3;
        }
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include __DIR__ . '/fragments/menu-family.php'; ?>
        <div class="layout-page">
            <?php include __DIR__ . '/fragments/navbar.php'; ?>
            <div class="content-wrapper">
                <div class="content-xxl flex-grow-1 container-p-y">
                    <?php if ($user): ?>
                        <h2>Hồ sơ người dùng</h2>
                        <p><strong>Họ và tên:</strong> <span><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></span></p>
                        <p><strong>Tên đăng nhập:</strong> <span><?php echo htmlspecialchars($user['username'] ?? ''); ?></span></p>
                        <p><strong>Email:</strong> <span><?php echo htmlspecialchars($user['email'] ?? ''); ?></span></p>
                        <p><strong>Số điện thoại:</strong> <span><?php echo htmlspecialchars($user['phone_number'] ?? 'Không có thông tin'); ?></span></p>
                        <p><strong>Địa chỉ:</strong> <span><?php echo htmlspecialchars($user['address'] ?? 'Không có thông tin'); ?></span></p>
                        <p><strong>Vai trò:</strong> <span><?php echo htmlspecialchars($user['role'] ?? ''); ?></span></p>

                        <?php if ($user['role'] === 'FAMILY'): ?>
                            <h5>Thông tin gia đình</h5>
                            <p><strong>Tên trẻ:</strong> <span><?php echo htmlspecialchars($familyProfile['child_name'] ?? 'Không có thông tin'); ?></span></p>
                            <p><strong>Tuổi trẻ:</strong> <span><?php echo htmlspecialchars($familyProfile['child_age'] ?? 'Không có thông tin'); ?></span></p>
                            <p><strong>Nhu cầu cụ thể:</strong> <span><?php echo htmlspecialchars($familyProfile['specific_needs'] ?? 'Không có thông tin'); ?></span></p>
                            <p><strong>Vị trí ưu tiên:</strong> <span><?php echo htmlspecialchars($familyProfile['preferred_location'] ?? 'Không có thông tin'); ?></span></p>
                            <a href="?action=home" class="btn btn-primary">Quay lại trang chủ</a>
                            <a href="?action=update_user" class="btn btn-success">Cập nhật hồ sơ</a>
                            <a href="?action=logout" class="btn btn-danger">Đăng xuất</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <h2>Bạn chưa đăng nhập</h2>
                        <p>Vui lòng <a href="?action=login">đăng nhập</a> để xem hồ sơ của bạn.</p>
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