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
        .form-label {
            font-weight: 500;
            color: #1a3c34;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 0.5rem;
        }
        .btn {
            font-size: 0.95rem;
            padding: 0.6rem 1.2rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
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
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }
        .alert {
            padding: 1rem;
            border-radius: 5px;
        }
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
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
                    <?php if (!$user): ?>
                        <h2>Bạn chưa đăng nhập</h2>
                        <p>Vui lòng <a href="?action=login">đăng nhập</a> để cập nhật hồ sơ của bạn.</p>
                    <?php else: ?>
                        <h2>Cập nhật hồ sơ người dùng</h2>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($_SESSION['error']); ?>
                                <?php unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo htmlspecialchars($_SESSION['success']); ?>
                                <?php unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <form action="?action=update_user" method="post" enctype="multipart/form-data">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Thông tin cá nhân</h5>
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Họ và tên</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone_number" class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Địa chỉ</label>
                                        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <?php if ($user['role'] === 'FAMILY'): ?>
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Thông tin gia đình</h5>
                                        <div class="mb-3">
                                            <label for="child_name" class="form-label">Tên trẻ</label>
                                            <input type="text" class="form-control" id="child_name" name="child_name" value="<?php echo htmlspecialchars($familyProfile['child_name'] ?? ''); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="child_age" class="form-label">Tuổi trẻ (tháng)</label>
                                            <input type="number" class="form-control" id="child_age" name="child_age" value="<?php echo htmlspecialchars($familyProfile['child_age'] ?? ''); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="specific_needs" class="form-label">Nhu cầu cụ thể</label>
                                            <textarea class="form-control" id="specific_needs" name="specific_needs" rows="4"><?php echo htmlspecialchars($familyProfile['specific_needs'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="preferred_location" class="form-label">Vị trí ưu tiên</label>
                                            <input type="text" class="form-control" id="preferred_location" name="preferred_location" value="<?php echo htmlspecialchars($familyProfile['preferred_location'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                <a href="?action=user_profile" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
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