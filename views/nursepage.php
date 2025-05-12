<?php
$pageTitle = 'Danh sách Y Tá';
$baseUrl = '/nurseborn';


// Khởi tạo FeedbackModel để lấy số sao trung bình
require_once __DIR__ . '/../models/FeedbackModel.php';
$feedbackModel = new FeedbackModel($conn);
?>
<!DOCTYPE html>
<html lang="vi" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>/static/assets/" data-template="vertical-menu-template-free">
<head>
     <?php include __DIR__ . '/fragments/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Y Tá</title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/core.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/theme-default.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/css/demo.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #22c55e;
            --text-color: #1f2a44;
            --muted-color: #6b7280;
            --card-bg: rgba(255, 255, 255, 0.95); /* Slightly transparent for background visibility */
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --border-radius: 12px;
        }

        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #dcfce7 100%); /* Eye-catching gradient */
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            padding: 0 15px;
        }

        h2.text-center {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 3rem;
            padding: 0.75rem 0;
            position: relative;
            animation: fadeIn 1s ease-out;
        }

        h2.text-center::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .search-container {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .search-container:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        #searchFilter {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        #searchFilter:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .nurse-card .card {
            border: none;
            border-radius: var(--border-radius);
            background: var(--card-bg);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .nurse-card .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #edf2f7;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
        }

        .card-text {
            color: var(--muted-color);
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-text strong {
            color: var(--text-color);
            font-weight: 500;
        }

        .card-text i {
            color: var(--primary-color);
            font-size: 1rem;
        }

        .card-text ul {
            padding-left: 1.25rem;
            margin: 0;
        }

        .card-text ul li {
            font-size: 0.9rem;
            color: var(--muted-color);
            margin-bottom: 0.25rem;
        }

        .card-text ul li a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .card-text ul li a:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        .btn-link {
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .btn-link:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--secondary-color), #34d399);
            border: none;
            border-radius: 8px;
            padding: 0.65rem 1.5rem;
            font-weight: 500;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #16a34a, #22c55e);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            color: #fff;
        }

        .btn-chat {
            background: linear-gradient(45deg, #4b5bfa, #7dd3fc);
            border: none;
            border-radius: 8px;
            padding: 0.65rem 1.5rem;
            font-weight: 500;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-chat:hover {
            background: linear-gradient(45deg, #3b82f6, #60a5fa);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            color: #fff;
        }

        .average-rating {
            color: #f59e0b;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            margin: 0.5rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
        }

        .average-rating i {
            font-size: 0.9rem;
            margin-right: 0.3rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            h2.text-center {
                font-size: 2rem;
            }

            .card-img-top {
                height: 180px;
            }

            .nurse-card .card {
                margin-bottom: 1.5rem;
            }

            .btn-primary, .btn-chat {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .card-title {
                font-size: 1.15rem;
            }
        }

        @media (max-width: 576px) {
            .search-container {
                padding: 1rem;
            }

            #searchFilter {
                font-size: 0.9rem;
            }

            .card-title {
                font-size: 1.1rem;
            }

            .card-text {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include 'fragments/menu-family.php'; ?>
        <div class="layout-page">
             <?php include __DIR__ . '/fragments/navbar.php'; ?>
            <div class="content-wrapper">
                <div class="container flex-grow-1 container-p-y">
                    <div class="container mt-5">
                        <h2 class="text-center mb-4">Danh sách Y Tá</h2>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($_SESSION['success']); ?>
                                <?php unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($_SESSION['error']); ?>
                                <?php unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="search-container">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="searchFilter" class="form-label">Tìm kiếm y tá:</label>
                                    <input type="text" id="searchFilter" class="form-control" placeholder="Nhập tên, kỹ năng, hoặc địa điểm...">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="nurseList">
                            <?php if (!empty($nurses)): ?>
                                <?php foreach ($nurses as $nurse): ?>
                                    <?php
                                    // Lấy số sao trung bình của y tá
                                    $averageRating = $feedbackModel->getAverageRatingByNurseUserId($nurse['user_id']);
                                    // Kiểm tra user_id tồn tại trước khi tạo liên kết
                                    $nurseUserId = isset($nurse['user_id']) ? htmlspecialchars($nurse['user_id']) : '';
                                    // Debug đường dẫn ảnh
                                    error_log("Nurse Profile Image: " . ($nurse['profile_image'] ?? 'Not set'));
                                    error_log("Final Image URL: " . $baseUrl . '/' . ltrim($nurse['profile_image'] ?? '/static/assets/img/avatars/default_profile.jpg', '/'));
                                    // Debug chứng chỉ
                                    error_log("Certificates for Nurse ID {$nurse['user_id']}: " . print_r($nurse['certificates'] ?? [], true));
                                    ?>
                                    <div class="col-md-4 mb-4 nurse-card" 
                                         data-name="<?php echo htmlspecialchars($nurse['full_name']); ?>" 
                                         data-skills="<?php echo htmlspecialchars($nurse['skills'] ?? ''); ?>" 
                                         data-location="<?php echo htmlspecialchars($nurse['location'] ?? ''); ?>">
                                        <div class="card">
                                            <img src="<?php echo $baseUrl . '/' . htmlspecialchars(ltrim($nurse['profile_image'] ?? '/static/assets/img/avatars/default_profile.jpg', '/')); ?>" 
                                                 class="card-img-top" alt="Ảnh Y Tá"/>
                                            <div class="average-rating">
                                                <i class="fas fa-star"></i>
                                                <?php echo $averageRating > 0 ? $averageRating : 'Chưa có đánh giá'; ?>
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <?php echo htmlspecialchars($nurse['full_name']); ?> | 
                                                    <?php echo htmlspecialchars($nurse['skills'] ?? ''); ?>
                                                </h5>
                                                <p class="card-text">
                                                    <i class="fas fa-briefcase"></i>
                                                    <strong>Kinh nghiệm:</strong>
                                                    <?php echo htmlspecialchars($nurse['experience_years'] ? $nurse['experience_years'] . ' năm' : 'Chưa có thông tin'); ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-location-dot"></i>
                                                    <strong>Địa điểm:</strong>
                                                    <?php echo htmlspecialchars($nurse['location'] ?? 'Chưa có thông tin'); ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                    <strong>Giá theo giờ:</strong>
                                                    <?php echo htmlspecialchars($nurse['hourly_rate'] ? number_format($nurse['hourly_rate'], 0, ',', '.') . ' VNĐ/giờ' : 'Chưa có giá'); ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                    <strong>Giá theo ngày:</strong>
                                                    <?php echo htmlspecialchars($nurse['daily_rate'] ? number_format($nurse['daily_rate'], 0, ',', '.') . ' VNĐ/ngày' : 'Chưa có giá'); ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                    <strong>Giá theo tuần:</strong>
                                                    <?php echo htmlspecialchars($nurse['weekly_rate'] ? number_format($nurse['weekly_rate'], 0, ',', '.') . ' VNĐ/tuần' : 'Chưa có giá'); ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-certificate"></i>
                                                    <strong>Chứng chỉ:</strong>
                                                    <?php if (!empty($nurse['certificates'])): ?>
                                                        <ul>
                                                            <?php foreach ($nurse['certificates'] as $certificate): ?>
                                                                <?php
                                                                error_log("Certificate File Path: " . ($certificate['file_path'] ?? 'Not set'));
                                                                error_log("Certificate Final URL: " . $baseUrl . '/' . ltrim($certificate['file_path'] ?? 'Not set', '/'));
                                                                ?>
                                                                <li>
                                                                    <?php echo htmlspecialchars($certificate['certificate_name']); ?>
                                                                    (<a href="<?php echo $baseUrl . '/' . htmlspecialchars(ltrim($certificate['file_path'], '/')); ?>" target="_blank">Xem file</a>)
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        <span>Không có thông tin</span>
                                                    <?php endif; ?>
                                                </p>
                                                <div class="d-flex gap-2">
                                                    <?php if (!empty($nurseUserId)): ?>
                                                        <a href="?action=nurse_review&nurseUserId=<?php echo $nurseUserId; ?>" class="btn btn-link">Xem chi tiết</a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Không có ID y tá</span>
                                                    <?php endif; ?>
                                                    <a href="?action=set_service&nurseUserId=<?php echo htmlspecialchars($nurse['user_id']); ?>" class="btn btn-primary">Đặt dịch vụ</a>
                                                    <a href="#" class="btn btn-chat text-white">Trò chuyện</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <p class="text-center">Không có y tá nào để hiển thị.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
</div>

<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/popper/popper.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/menu.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script>
    document.getElementById('searchFilter').addEventListener('input', filterNurses);

    function filterNurses() {
        const searchTerm = document.getElementById('searchFilter').value.toLowerCase();
        const nurses = document.querySelectorAll('.nurse-card');

        nurses.forEach(function(nurse) {
            const name = nurse.getAttribute('data-name') ? nurse.getAttribute('data-name').toLowerCase() : '';
            const skills = nurse.getAttribute('data-skills') ? nurse.getAttribute('data-skills').toLowerCase() : '';
            const location = nurse.getAttribute('data-location') ? nurse.getAttribute('data-location').toLowerCase() : '';

            if (name.includes(searchTerm) || skills.includes(searchTerm) || location.includes(searchTerm)) {
                nurse.style.display = 'block';
            } else {
                nurse.style.display = 'none';
            }
        });
    }
</script>
</body>
</html>

