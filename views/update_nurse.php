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
    <title>Cập Nhật Hồ Sơ Y Tá</title>
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

        .form-label {
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

        .form-control, .form-control:focus {
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

        .form-text {
            font-size: 0.85rem;
            color: var(--muted-color);
            margin-top: 0.25rem;
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

        .alert {
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert i {
            font-size: 1.2rem;
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

            .form-control {
                font-size: 0.95rem;
            }

            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
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
                    <?php if (!$user): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            Bạn chưa đăng nhập. Vui lòng <a href="?action=login">đăng nhập</a> để cập nhật hồ sơ của bạn.
                        </div>
                    <?php else: ?>
                        <h2>Cập Nhật Hồ Sơ Y Tá</h2>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo htmlspecialchars($_SESSION['error']); ?>
                                <?php unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <?php echo htmlspecialchars($_SESSION['success']); ?>
                                <?php unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <form action="?action=update_nurse" method="post" enctype="multipart/form-data">
                            <div class="card">
                                <h5>Thông Tin Y Tá</h5>
                                <div class="mb-3">
                                    <label for="location" class="form-label"><i class="fas fa-map-marker-alt"></i> Vị trí</label>
                                    <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($nurseProfile['location'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="skills" class="form-label"><i class="fas fa-tools"></i> Kỹ năng</label>
                                    <input type="text" class="form-control" id="skills" name="skills" value="<?php echo htmlspecialchars($nurseProfile['skills'] ?? ''); ?>" required>
                                    <small class="form-text">Nhập các kỹ năng, cách nhau bằng dấu phẩy (ví dụ: Chăm sóc trẻ sơ sinh, Tiêm truyền).</small>
                                </div>
                                <div class="mb-3">
                                    <label for="experience_years" class="form-label"><i class="fas fa-briefcase"></i> Số năm kinh nghiệm</label>
                                    <input type="number" class="form-control" id="experience_years" name="experience_years" value="<?php echo htmlspecialchars($nurseProfile['experience_years'] ?? 0); ?>" required min="0">
                                </div>
                                <div class="mb-3">
                                    <label for="hourly_rate" class="form-label"><i class="fas fa-money-bill-wave"></i> Lương giờ (VNĐ)</label>
                                    <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" value="<?php echo htmlspecialchars($nurseProfile['hourly_rate'] ?? 0); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="daily_rate" class="form-label"><i class="fas fa-money-bill-wave"></i> Lương ngày (VNĐ)</label>
                                    <input type="number" step="0.01" class="form-control" id="daily_rate" name="daily_rate" value="<?php echo htmlspecialchars($nurseProfile['daily_rate'] ?? ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="weekly_rate" class="form-label"><i class="fas fa-money-bill-wave"></i> Lương tuần (VNĐ)</label>
                                    <input type="number" step="0.01" class="form-control" id="weekly_rate" name="weekly_rate" value="<?php echo htmlspecialchars($nurseProfile['weekly_rate'] ?? ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="bio" class="form-label"><i class="fas fa-address-card"></i> Tiểu sử</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($nurseProfile['bio'] ?? ''); ?></textarea>
                                    <small class="form-text">Mô tả ngắn về bản thân và kinh nghiệm làm việc.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label"><i class="fas fa-image"></i> Ảnh đại diện</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                    <small class="form-text">Chọn ảnh định dạng JPG, PNG. Để nguyên nếu không muốn thay đổi.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="certificates" class="form-label"><i class="fas fa-certificate"></i> Chứng chỉ</label>
                                    <input type="file" class="form-control" id="certificates" name="certificates[]" multiple accept="image/*,application/pdf">
                                    <small class="form-text">Chọn nhiều file PDF hoặc hình ảnh (JPG, PNG).</small>
                                </div>
                                <div class="mb-3">
                                    <label for="certificate_names" class="form-label"><i class="fas fa-file-alt"></i> Tên chứng chỉ</label>
                                    <textarea class="form-control" id="certificate_names" name="certificate_names" rows="4" placeholder="Nhập tên chứng chỉ, cách nhau bằng dấu phẩy (ví dụ: Chứng chỉ điều dưỡng, Chứng chỉ chăm sóc trẻ sơ sinh)"><?php echo htmlspecialchars($nurseProfile['certificate_names'] ?? ''); ?></textarea>
                                    <small class="form-text">Nhập tên cho từng chứng chỉ, cách nhau bằng dấu phẩy. Để trống nếu không cần.</small>
                                </div>
                            </div>

                            <div class="btn-container">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu thay đổi</button>
                                <a href="?action=nurse_profile" class="btn btn-secondary"><i class="fas fa-times"></i> Hủy</a>
                            </div>
                        </form>
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