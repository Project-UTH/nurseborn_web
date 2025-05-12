<?php
$pageTitle = 'Đăng ký y tá';
$baseUrl = '/nurseborn';
?>
<!DOCTYPE html>
<html lang="vi" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>/static/assets/" data-template="vertical-menu-template-free">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký Y tá - NurseBorn</title>
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
            --danger-color: #dc3545;
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
            max-width: 700px;
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

        .certificate-item {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .btn {
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
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

        .btn-secondary {
            background: linear-gradient(45deg, #6b7280, #9ca3af);
            border: none;
            color: #fff;
        }

        .btn-secondary:hover {
            background: linear-gradient(45deg, #4b5563, #6b7280);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-danger {
            background: linear-gradient(45deg, var(--danger-color), #ef4444);
            border: none;
            color: #fff;
        }

        .btn-danger:hover {
            background: linear-gradient(45deg, #bd2130, var(--danger-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn i {
            margin-right: 0.5rem;
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

            .btn {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .certificate-item {
                padding: 0.75rem;
            }

            .btn {
                width: 100%;
                text-align: center;
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
                    <h4 class="mb-2">ĐĂNG KÝ TÀI KHOẢN Y TÁ</h4>
                    <p class="mb-4">Nhập thông tin của bạn để tham gia hệ thống</p>

                    <form id="registerNurseForm" class="mb-3" action="?action=register_nurse" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="username" class="form-label"><i class="fas fa-user-circle"></i> Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label"><i class="fas fa-lock"></i> Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required />
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label"><i class="fas fa-user"></i> Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label"><i class="fas fa-phone"></i> Số điện thoại</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label"><i class="fas fa-map-marker-alt"></i> Khu vực làm việc</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label"><i class="fas fa-address-card"></i> Tiểu sử (Bio)</label>
                            <textarea class="form-control" id="bio" name="bio"><?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="profile_image" class="form-label"><i class="fas fa-image"></i> Ảnh đại diện</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" />
                        </div>
                        <div class="mb-3">
                            <label for="skills" class="form-label"><i class="fas fa-tools"></i> Kỹ năng chuyên môn</label>
                            <textarea class="form-control" id="skills" name="skills" required><?php echo isset($_POST['skills']) ? htmlspecialchars($_POST['skills']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="experience_years" class="form-label"><i class="fas fa-briefcase"></i> Số năm kinh nghiệm</label>
                            <input type="number" class="form-control" id="experience_years" name="experience_years" value="<?php echo isset($_POST['experience_years']) ? htmlspecialchars($_POST['experience_years']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="hourly_rate" class="form-label"><i class="fas fa-money-bill-wave"></i> Mức giá theo giờ (VNĐ)</label>
                            <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="<?php echo isset($_POST['hourly_rate']) ? htmlspecialchars($_POST['hourly_rate']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="daily_rate" class="form-label"><i class="fas fa-money-bill-wave"></i> Mức giá theo ngày (VNĐ)</label>
                            <input type="number" class="form-control" id="daily_rate" name="daily_rate" value="<?php echo isset($_POST['daily_rate']) ? htmlspecialchars($_POST['daily_rate']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="weekly_rate" class="form-label"><i class="fas fa-money-bill-wave"></i> Mức giá theo tuần (VNĐ)</label>
                            <input type="number" class="form-control" id="weekly_rate" name="weekly_rate" value="<?php echo isset($_POST['weekly_rate']) ? htmlspecialchars($_POST['weekly_rate']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-certificate"></i> Chứng chỉ và bằng cấp</label>
                            <div id="certificates-section">
                                <div class="certificate-item">
                                    <input type="text" class="form-control mb-1" name="certificate_names[]" placeholder="Tên chứng chỉ" value="<?php echo isset($_POST['certificate_names'][0]) ? htmlspecialchars($_POST['certificate_names'][0]) : ''; ?>" />
                                    <input type="file" class="form-control" name="certificates[]" accept=".pdf,.jpg,.jpeg,.png" />
                                    <button type="button" class="btn btn-danger remove-certificate mt-1"><i class="fas fa-trash"></i> Xóa</button>
                                </div>
                            </div>
                            <button type="button" id="add-certificate" class="btn btn-secondary mt-2"><i class="fas fa-plus"></i> Thêm chứng chỉ</button>
                        </div>
                        <input type="hidden" name="role" value="NURSE" />
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
                        <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Đăng ký</button>
                    </form>

                    <p class="text-center">
                        <span>Đã có tài khoản?</span>
                        <a href="?action=login">Đăng nhập ngay</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript để xử lý thêm/xóa chứng chỉ -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const addCertificateButton = document.getElementById("add-certificate");
        if (addCertificateButton) {
            addCertificateButton.addEventListener("click", function() {
                let container = document.getElementById("certificates-section");
                if (container) {
                    let newItem = document.createElement("div");
                    newItem.classList.add("certificate-item");
                    newItem.innerHTML = `
                        <input type="text" class="form-control mb-1" name="certificate_names[]" placeholder="Tên chứng chỉ" />
                        <input type="file" class="form-control" name="certificates[]" accept=".pdf,.jpg,.jpeg,.png" />
                        <button type="button" class="btn btn-danger remove-certificate mt-1"><i class="fas fa-trash"></i> Xóa</button>
                    `;
                    container.appendChild(newItem);
                }
            });
        }

        const certificatesSection = document.getElementById("certificates-section");
        if (certificatesSection) {
            certificatesSection.addEventListener("click", function(event) {
                if (event.target.classList.contains("remove-certificate") || event.target.closest(".remove-certificate")) {
                    event.target.closest(".certificate-item").remove();
                }
            });
        }

        const form = document.getElementById("registerNurseForm");
        if (form) {
            form.addEventListener("submit", function(event) {
                console.log("Form đang được gửi...");
                console.log("Role:", document.querySelector("input[name='role']").value);
            });
        }
    });
</script>

<!-- Core JS -->
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
</body>
</html>