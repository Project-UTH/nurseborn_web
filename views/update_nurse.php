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
        .form-text {
            font-size: 0.85rem;
            color: #6c757d;
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
        <?php include __DIR__ . '/fragments/menu-nurse.php'; ?>
        <div class="layout-page">
            <?php include __DIR__ . '/fragments/navbar-nurse.php'; ?>
            <div class="content-wrapper">
                <div class="content-xxl flex-grow-1 container-p-y">
                    <?php if (!$user): ?>
                        <h2>Bạn chưa đăng nhập</h2>
                        <p>Vui lòng <a href="?action=login">đăng nhập</a> để cập nhật hồ sơ của bạn.</p>
                    <?php else: ?>
                        <h2>Cập nhật hồ sơ y tá</h2>
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

                        <form action="?action=update_nurse" method="post" enctype="multipart/form-data">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Thông tin y tá</h5>
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Vị trí</label>
                                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($nurseProfile['location'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="skills" class="form-label">Kỹ năng</label>
                                        <input type="text" class="form-control" id="skills" name="skills" value="<?php echo htmlspecialchars($nurseProfile['skills'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="experience_years" class="form-label">Số năm kinh nghiệm</label>
                                        <input type="number" class="form-control" id="experience_years" name="experience_years" value="<?php echo htmlspecialchars($nurseProfile['experience_years'] ?? 0); ?>" required min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label for="hourly_rate" class="form-label">Lương giờ (VNĐ)</label>
                                        <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" value="<?php echo htmlspecialchars($nurseProfile['hourly_rate'] ?? 0); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="daily_rate" class="form-label">Lương ngày (VNĐ)</label>
                                        <input type="number" step="0.01" class="form-control" id="daily_rate" name="daily_rate" value="<?php echo htmlspecialchars($nurseProfile['daily_rate'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="weekly_rate" class="form-label">Lương tuần (VNĐ)</label>
                                        <input type="number" step="0.01" class="form-control" id="weekly_rate" name="weekly_rate" value="<?php echo htmlspecialchars($nurseProfile['weekly_rate'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Tiểu sử</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($nurseProfile['bio'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="profile_image" class="form-label">Ảnh đại diện</label>
                                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                        <small class="form-text text-muted">Để nguyên nếu không muốn thay đổi ảnh.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="certificates" class="form-label">Chứng chỉ</label>
                                        <input type="file" class="form-control" id="certificates" name="certificates[]" multiple accept="image/*,application/pdf">
                                        <small class="form-text text-muted">Chọn nhiều file PDF hoặc hình ảnh.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="certificate_names" class="form-label">Tên chứng chỉ (tùy chọn, nhập tên cho từng file)</label>
                                        <textarea class="form-control" id="certificate_names" name="certificate_names" rows="4" placeholder="Nhập tên chứng chỉ, cách nhau bằng dấu phẩy"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                <a href="?action=nurse_profile" class="btn btn-secondary">Hủy</a>
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