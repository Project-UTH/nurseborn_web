<?php
$pageTitle = 'Danh sách Y Tá';
$baseUrl = '/nurseborn';

// Khởi tạo FeedbackModel để lấy số sao trung bình
require_once __DIR__ . '/../models/FeedbackModel.php';
$feedbackModel = new FeedbackModel($conn);

// Khởi tạo NurseAvailabilityModel để lấy lịch làm việc
require_once __DIR__ . '/../models/NurseAvailabilityModel.php';
$nurseAvailabilityModel = new NurseAvailabilityModel($conn);
?>
<!DOCTYPE html>
<html lang="vi" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>/static/assets/" data-template="vertical-menu-template-free">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Y Tá - NurseBorn</title>
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
            --warning-color: #f59e0b;
            --chat-color: #4299e1;
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
            max-width: 1200px;
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

        h2.text-center {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        h2.text-center::after {
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

        .search-container {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .search-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
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

        #searchFilter {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        #searchFilter:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(37, 99, 235, 0.3);
            outline: none;
        }

        .nurse-card .card {
            border: none;
            border-radius: var(--border-radius);
            background-color: var(--card-bg);
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .nurse-card .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .card-img-top {
            height: 220px;
            object-fit: cover;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-align: center;
        }

        .average-rating {
            color: var(--warning-color);
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
        }

        .average-rating i {
            font-size: 0.9rem;
        }

        .card-text {
            font-size: 0.95rem;
            color: var(--muted-color);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-text strong {
            color: var(--text-color);
            font-weight: 600;
            width: 130px;
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

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1rem;
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
            background: linear-gradient(45deg, var(--secondary-color), #34d399);
            border: none;
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #16a34a, var(--secondary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-chat {
            background: linear-gradient(45deg, var(--chat-color), #6ab7f5);
            border: none;
            color: #fff;
        }

        .btn-chat:hover {
            background: linear-gradient(45deg, #3b82ce, var(--chat-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-link {
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .btn-link:hover {
            color: #1e40af;
            text-decoration: underline;
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

        .no-nurses {
            text-align: center;
            font-size: 1rem;
            color: var(--muted-color);
            margin: 2rem 0;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .nurse-card .col-md-4 {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .container-p-y {
                padding: 1.5rem;
                margin: 1rem;
            }

            h2.text-center {
                font-size: 2rem;
            }

            .card-img-top {
                height: 180px;
            }

            .card-title {
                font-size: 1.15rem;
            }

            .card-text {
                font-size: 0.9rem;
            }

            .card-text strong {
                width: 110px;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .search-container {
                padding: 1rem;
            }

            #searchFilter {
                font-size: 0.9rem;
            }

            .card-text {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.3rem;
            }

            .card-text strong {
                width: auto;
            }

            .btn-container {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
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
                <div class="container-p-y">
                    <h2 class="text-center">Danh sách Y Tá</h2>
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="search-container">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="searchFilter" class="form-label"><i class="fas fa-search"></i> Tìm kiếm y tá:</label>
                                <input type="text" id="searchFilter" class="form-control" placeholder="Nhập tên, kỹ năng, hoặc địa điểm...">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="nurseList">
                        <?php if (!empty($nurses)): ?>
                            <?php foreach ($nurses as $nurse): ?>
                                <?php
                                $averageRating = $feedbackModel->getAverageRatingByNurseUserId($nurse['user_id']);
                                $availability = $nurseAvailabilityModel->getAvailabilityByUserId($nurse['user_id']);
                                $selectedDays = $availability['selected_days'] ?? [];
                                $nurseUserId = isset($nurse['user_id']) ? htmlspecialchars($nurse['user_id']) : '';
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
                                            <?php echo $averageRating > 0 ? number_format($averageRating, 1) : 'Chưa có đánh giá'; ?>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($nurse['full_name']); ?></h5>
                                            <div class="card-text">
                                                <i class="fas fa-tools"></i>
                                                <strong>Kỹ năng:</strong>
                                                <?php echo htmlspecialchars($nurse['skills'] ?? 'Chưa có thông tin'); ?>
                                            </div>
                                            <div class="card-text">
                                                <i class="fas fa-briefcase"></i>
                                                <strong>Kinh nghiệm:</strong>
                                                <?php echo htmlspecialchars($nurse['experience_years'] ? $nurse['experience_years'] . ' năm' : 'Chưa có thông tin'); ?>
                                            </div>
                                            <div class="card-text">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <strong>Địa điểm:</strong>
                                                <?php echo htmlspecialchars($nurse['location'] ?? 'Chưa có thông tin'); ?>
                                            </div>
                                            <div class="card-text">
                                                <i class="fas fa-money-bill-wave"></i>
                                                <strong>Giá/giờ:</strong>
                                                <?php echo htmlspecialchars($nurse['hourly_rate'] ? number_format($nurse['hourly_rate'], 0, ',', '.') . ' VNĐ' : 'Chưa có giá'); ?>
                                            </div>
                                            <div class="card-text">
                                                <i class="fas fa-money-bill-wave"></i>
                                                <strong>Giá/ngày:</strong>
                                                <?php echo htmlspecialchars($nurse['daily_rate'] ? number_format($nurse['daily_rate'], 0, ',', '.') . ' VNĐ' : 'Chưa có giá'); ?>
                                            </div>
                                            <div class="card-text">
                                                <i class="fas fa-money-bill-wave"></i>
                                                <strong>Giá/tuần:</strong>
                                                <?php echo htmlspecialchars($nurse['weekly_rate'] ? number_format($nurse['weekly_rate'], 0, ',', '.') . ' VNĐ' : 'Chưa có giá'); ?>
                                            </div>
                                            <div class="card-text">
                                                <i class="fas fa-calendar-alt"></i>
                                                <strong>Lịch làm việc:</strong>
                                                <?php if (!empty($selectedDays)): ?>
                                                    <?php echo htmlspecialchars(implode(', ', $selectedDays)); ?>
                                                <?php else: ?>
                                                    Chưa có lịch làm việc
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-text">
                                                <i class="fas fa-certificate"></i>
                                                <strong>Chứng chỉ:</strong>
                                                <?php if (!empty($nurse['certificates'])): ?>
                                                    <ul>
                                                        <?php foreach ($nurse['certificates'] as $certificate): ?>
                                                            <li>
                                                                <?php echo htmlspecialchars($certificate['certificate_name']); ?>
                                                                (<a href="<?php echo $baseUrl . '/' . htmlspecialchars(ltrim($certificate['file_path'], '/')); ?>" target="_blank">Xem file</a>)
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php else: ?>
                                                    Không có thông tin
                                                <?php endif; ?>
                                            </div>
                                            <div class="btn-container">
                                                <?php if (!empty($nurseUserId)): ?>
                                                    <a href="?action=nurse_review&nurseUserId=<?php echo $nurseUserId; ?>" class="btn btn-link"><i class="fas fa-eye"></i> Xem chi tiết</a>
                                                <?php else: ?>
                                                    <span class="text-muted">Không có ID y tá</span>
                                                <?php endif; ?>
                                                <a href="?action=set_service&nurseUserId=<?php echo $nurseUserId; ?>" class="btn btn-primary"><i class="fas fa-calendar-check"></i> Đặt dịch vụ</a>
                                                <a href="?action=messages&nurseUserId=<?php echo $nurseUserId; ?>" class="btn btn-chat"><i class="fas fa-comments"></i> Trò chuyện</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-nurses"><i class="fas fa-user-slash"></i> Không có y tá nào để hiển thị.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
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