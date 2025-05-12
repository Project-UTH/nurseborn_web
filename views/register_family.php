<?php
$pageTitle = 'Đăng ký gia đình';
$baseUrl = '/nurseborn';
?>
<!DOCTYPE html>
<html lang="vi" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>/static/assets/" data-template="vertical-menu-template-free">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký gia đình - NurseBorn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container-xxl {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .authentication-wrapper {
            display: flex;
            justify-content: center;
        }

        .authentication-inner {
            width: 100%;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            background-color: var(--card-bg);
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeIn 0.8s ease-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .card-body {
            padding: 2.5rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .app-brand {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .app-brand-link {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .app-brand-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-transform: uppercase;
            margin-left: 0.5rem;
        }

        h4.mb-2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-color);
            text-align: center;
            margin-bottom: 1rem;
        }

        p.mb-4 {
            font-size: 1rem;
            color: var(--muted-color);
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-label {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(37, 99, 235, 0.3);
            outline: none;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .alert {
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }

        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert i {
            font-size: 1.2rem;
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
            width: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #1e40af, var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .text-center {
            font-size: 0.95rem;
            color: var(--muted-color);
            margin-top: 1.5rem;
        }

        .text-center a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .text-center a:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .container-xxl {
                padding: 1.5rem;
                margin: 1rem;
            }

            h4.mb-2 {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 2rem;
            }

            .form-control {
                font-size: 0.95rem;
            }
        }

        @media (max-width: 576px) {
            .app-brand-text {
                font-size: 1.2rem;
            }

            .btn {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body">
                    <div class="app-brand">
                        <a href="?action=home" class="app-brand-link">
                            <img src="<?php echo $baseUrl; ?>/static/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
                            <span class="app-brand-text">Đăng ký</span>
                        </a>
                    </div>
                    <h4 class="mb-2">TẠO TÀI KHOẢN</h4>
                    <p class="mb-4">Điền thông tin bên dưới để bắt đầu</p>

                    <form id="formFamilyRegistration" class="mb-3" action="?action=register_family" method="POST">
                        <div class="mb-3">
                            <label for="full_name" class="form-label"><i class="fas fa-user"></i> Họ và Tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label"><i class="fas fa-user-circle"></i> Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label"><i class="fas fa-lock"></i> Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required />
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label"><i class="fas fa-phone"></i> Số điện thoại</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label"><i class="fas fa-map-marker-alt"></i> Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="child_name" class="form-label"><i class="fas fa-baby"></i> Tên trẻ</label>
                            <input type="text" class="form-control" id="child_name" name="child_name" value="<?php echo isset($_POST['child_name']) ? htmlspecialchars($_POST['child_name']) : ''; ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="child_age" class="form-label"><i class="fas fa-child"></i> Tuổi của trẻ</label>
                            <input type="text" class="form-control" id="child_age" name="child_age" value="<?php echo isset($_POST['child_age']) ? htmlspecialchars($_POST['child_age']) : ''; ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="specific_needs" class="form-label"><i class="fas fa-notes-medical"></i> Nhu cầu cụ thể</label>
                            <textarea class="form-control" id="specific_needs" name="specific_needs"><?php echo isset($_POST['specific_needs']) ? htmlspecialchars($_POST['specific_needs']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="preferred_location" class="form-label"><i class="fas fa-location-dot"></i> Vị trí ưu tiên</label>
                            <input type="text" class="form-control" id="preferred_location" name="preferred_location" value="<?php echo isset($_POST['preferred_location']) ? htmlspecialchars($_POST['preferred_location']) : ''; ?>" />
                        </div>
                        <input type="hidden" name="role" value="FAMILY" />
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="mb-3">
                                <div class="alert alert-error">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="mb-3">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-user-plus"></i> Đăng ký</button>
                    </form>

                    <p class="text-center">
                        <span>Bạn đã có tài khoản?</span>
                        <a href="?action=login">Đăng nhập ngay</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
</body>
</html>