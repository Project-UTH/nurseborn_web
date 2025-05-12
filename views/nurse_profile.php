<?php
$baseUrl = '/nurseborn';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Y Tá</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/core.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/theme-default.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/css/demo.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #22c55e;
            --text-color: #1f2a44;
            --muted-color: #6b7280;
            --card-bg: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
        }

        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #dcfce7 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            margin: 0;
        }

        .container-p-y {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h4 {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        h4::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        h5 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 2rem 0 1rem;
            border-left: 4px solid var(--secondary-color);
            padding-left: 1rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .profile-item {
            display: flex;
            align-items: center;
            font-size: 1rem;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .profile-item i {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-right: 1rem;
            width: 24px;
            text-align: center;
        }

        .profile-item strong {
            font-weight: 600;
            color: var(--text-color);
            width: 180px;
            display: inline-block;
        }

        .profile-item span {
            color: var(--muted-color);
            flex: 1;
        }

        .profile-image {
            max-width: 120px;
            border-radius: 8px;
            border: 2px solid var(--primary-color);
            margin-top: 0.5rem;
        }

        .certificate-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .certificate-list li {
            font-size: 0.95rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .certificate-list li a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .certificate-list li a:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            font-size: 1rem;
            font-weight: 500;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), #60a5fa);
            border: none;
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #1e40af, var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-success {
            background: linear-gradient(45deg, var(--secondary-color), #34d399);
            border: none;
            color: #fff;
        }

        .btn-success:hover {
            background: linear-gradient(45deg, #16a34a, var(--secondary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #f87171);
            border: none;
            color: #fff;
        }

        .btn-danger:hover {
            background: linear-gradient(45deg, #b02a37, #dc3545);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .alert-warning {
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #664d03;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-warning i {
            font-size: 1.2rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .container-p-y {
                padding: 1.5rem;
                margin: 1rem;
            }

            h4 {
                font-size: 1.8rem;
            }

            h5 {
                font-size: 1.3rem;
            }

            .profile-item {
                font-size: 0.95rem;
            }

            .profile-item strong {
                width: 140px;
            }

            .profile-image {
                max-width: 100px;
            }

            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .profile-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .profile-item strong {
                width: auto;
            }

            .btn-container {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
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
                <div class="container-p-y">
                    <?php if ($user): ?>
                        <h4>Hồ Sơ Y Tá</h4>
                        <div class="card">
                            <h5>Thông Tin Cá Nhân</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="profile-item">
                                        <i class="fas fa-user"></i>
                                        <strong>Họ và tên:</strong>
                                        <span><?php echo htmlspecialchars($user['full_name'] ?? 'Không có thông tin'); ?></span>
                                    </div>
                                    <div class="profile-item">
                                        <i class="fas fa-user-circle"></i>
                                        <strong>Tên đăng nhập:</strong>
                                        <span><?php echo htmlspecialchars($user['username'] ?? 'Không có thông tin'); ?></span>
                                    </div>
                                    <div class="profile-item">
                                        <i class="fas fa-envelope"></i>
                                        <strong>Email:</strong>
                                        <span><?php echo htmlspecialchars($user['email'] ?? 'Không có thông tin'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="profile-item">
                                        <i class="fas fa-phone"></i>
                                        <strong>Số điện thoại:</strong>
                                        <span><?php echo htmlspecialchars($user['phone_number'] ?? 'Không có thông tin'); ?></span>
                                    </div>
                                    <div class="profile-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <strong>Địa chỉ:</strong>
                                        <span><?php echo htmlspecialchars($user['address'] ?? 'Không có thông tin'); ?></span>
                                    </div>
                                    <div class="profile-item">
                                        <i class="fas fa-user-tag"></i>
                                        <strong>Vai trò:</strong>
                                        <span><?php echo htmlspecialchars($user['role'] ?? 'Không có thông tin'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($user['role'] === 'NURSE'): ?>
                            <div class="card">
                                <h5>Thông Tin Dịch Vụ</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="profile-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <strong>Vị trí:</strong>
                                            <span><?php echo htmlspecialchars($nurseProfile['location'] ?? 'Không có thông tin'); ?></span>
                                        </div>
                                        <div class="profile-item">
                                            <i class="fas fa-tools"></i>
                                            <strong>Kỹ năng:</strong>
                                            <span><?php echo htmlspecialchars($nurseProfile['skills'] ?? 'Không có thông tin'); ?></span>
                                        </div>
                                        <div class="profile-item">
                                            <i class="fas fa-briefcase"></i>
                                            <strong>Kinh nghiệm:</strong>
                                            <span><?php echo isset($nurseProfile['experience_years']) ? htmlspecialchars($nurseProfile['experience_years']) . ' năm' : 'Không có thông tin'; ?></span>
                                        </div>
                                        <div class="profile-item">
                                            <i class="fas fa-address-card"></i>
                                            <strong>Tiểu sử:</strong>
                                            <span><?php echo htmlspecialchars($nurseProfile['bio'] ?? 'Không có thông tin'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-item">
                                            <i class="fas fa-money-bill-wave"></i>
                                            <strong>Lương giờ:</strong>
                                            <span><?php echo isset($nurseProfile['hourly_rate']) ? number_format($nurseProfile['hourly_rate'], 0, ',', '.') . ' VNĐ/giờ' : 'Không có thông tin'; ?></span>
                                        </div>
                                        <div class="profile-item">
                                            <i class="fas fa-money-bill-wave"></i>
                                            <strong>Lương ngày:</strong>
                                            <span><?php echo isset($nurseProfile['daily_rate']) ? number_format($nurseProfile['daily_rate'], 0, ',', '.') . ' VNĐ/ngày' : 'Không có thông tin'; ?></span>
                                        </div>
                                        <div class="profile-item">
                                            <i class="fas fa-money-bill-wave"></i>
                                            <strong>Lương tuần:</strong>
                                            <span><?php echo isset($nurseProfile['weekly_rate']) ? number_format($nurseProfile['weekly_rate'], 0, ',', '.') . ' VNĐ/tuần' : 'Không có thông tin'; ?></span>
                                        </div>
                                        <div class="profile-item">
                                            <i class="fas fa-check-circle"></i>
                                            <strong>Trạng thái phê duyệt:</strong>
                                            <span><?php echo isset($nurseProfile['is_approved']) ? ($nurseProfile['is_approved'] ? 'Đã phê duyệt' : 'Chưa phê duyệt') : 'Không có thông tin'; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="profile-item">
                                        <i class="fas fa-image"></i>
                                        <strong>Ảnh đại diện:</strong>
                                        <span><img src="<?php echo htmlspecialchars($nurseProfile['profile_image'] ?? $baseUrl . '/static/assets/img/avatars/default_profile.jpg'); ?>" alt="Profile Image" class="profile-image"/></span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="profile-item">
                                        <i class="fas fa-certificate"></i>
                                        <strong>Chứng chỉ:</strong>
                                        <span>
                                            <?php if (!empty($nurseProfile['certificates'])): ?>
                                                <ul class="certificate-list">
                                                    <?php foreach ($nurseProfile['certificates'] as $certificate): ?>
                                                        <li>
                                                            <i class="fas fa-file-alt"></i>
                                                            <?php echo htmlspecialchars($certificate['certificate_name'] ?? $certificate['name']); ?>
                                                            (<a href="<?php echo htmlspecialchars($certificate['file_path']); ?>" target="_blank">Xem file</a>)
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                Không có thông tin
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-container">
                                <a href="?action=home" class="btn btn-primary"><i class="fas fa-home"></i> Quay lại trang chủ</a>
                                <a href="?action=update_nurse" class="btn btn-success" id="update-nurse-btn"><i class="fas fa-edit"></i> Cập nhật hồ sơ</a>
                                <a href="?action=logout" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert-warning">
                            <i class="fas fa-exclamation-circle"></i>
                            Bạn chưa đăng nhập. Vui lòng <a href="?action=login">đăng nhập</a> để xem hồ sơ y tá của bạn.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
</body>
</html>