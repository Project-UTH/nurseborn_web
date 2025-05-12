<?php
$baseUrl = '/nurseborn';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$familyProfile = isset($_SESSION['family_profile']) ? $_SESSION['family_profile'] : null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Người Dùng</title>
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

        h2 {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        h2::after {
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

        .profile-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-card:hover {
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

        .alert {
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .alert-info {
            background-color: #e0f2fe;
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .container-p-y {
                padding: 1.5rem;
                margin: 1rem;
            }

            h2 {
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
        <?php include __DIR__ . '/fragments/menu-family.php'; ?>
        <div class="layout-page">
            <?php include __DIR__ . '/fragments/navbar.php'; ?>
            <div class="content-wrapper">
                <div class="container-p-y">
                    <?php if ($user): ?>
                        <h2>Hồ Sơ Người Dùng</h2>
                        <div class="profile-card">
                            <h5>Thông Tin Cá Nhân</h5>
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

                        <?php if ($user['role'] === 'FAMILY'): ?>
                            <div class="profile-card">
                                <h5>Thông Tin Gia Đình</h5>
                                <div class="profile-item">
                                    <i class="fas fa-child"></i>
                                    <strong>Tên trẻ:</strong>
                                    <span><?php echo htmlspecialchars($familyProfile['child_name'] ?? 'Không có thông tin'); ?></span>
                                </div>
                                <div class="profile-item">
                                    <i class="fas fa-birthday-cake"></i>
                                    <strong>Tuổi trẻ:</strong>
                                    <span><?php echo htmlspecialchars($familyProfile['child_age'] ?? 'Không có thông tin'); ?></span>
                                </div>
                                <div class="profile-item">
                                    <i class="fas fa-notes-medical"></i>
                                    <strong>Nhu cầu cụ thể:</strong>
                                    <span><?php echo htmlspecialchars($familyProfile['specific_needs'] ?? 'Không có thông tin'); ?></span>
                                </div>
                                <div class="profile-item">
                                    <i class="fas fa-location-dot"></i>
                                    <strong>Vị trí ưu tiên:</strong>
                                    <span><?php echo htmlspecialchars($familyProfile['preferred_location'] ?? 'Không có thông tin'); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="btn-container">
                            <a href="?action=home" class="btn btn-primary"><i class="fas fa-home"></i> Quay lại trang chủ</a>
                            <a href="?action=update_user" class="btn btn-success"><i class="fas fa-edit"></i> Cập nhật hồ sơ</a>
                            <a href="?action=logout" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-exclamation-circle"></i> Bạn chưa đăng nhập. Vui lòng <a href="?action=login">đăng nhập</a> để xem hồ sơ của bạn.
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
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
</body>
</html>