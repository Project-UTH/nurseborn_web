<?php
$pageTitle = 'Danh sách Y Tá';
$baseUrl = '/nurseborn';
include 'fragments/head.php';
include 'fragments/navbar.php';

// Khởi tạo FeedbackModel để lấy số sao trung bình
require_once __DIR__ . '/../models/FeedbackModel.php';
$feedbackModel = new FeedbackModel($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Y Tá</title>
    <link rel="stylesheet" href="../assets/vendor/css/core.css">
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css">
    <link rel="stylesheet" href="../assets/css/demo.css">
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f7fa;
        }
        .container {
            max-width: 1200px;
        }
        h2.text-center {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, #0d6efd, #28a745);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
            margin-bottom: 40px;
            padding: 10px 0;
            animation: fadeIn 1s ease-in-out;
        }
        h2.text-center::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(45deg, #0d6efd, #28a745);
            border-radius: 2px;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .search-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 30px;
        }
        #searchFilter {
            border: 2px solid #0d6efd;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        #searchFilter:focus {
            border-color: #28a745;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
            outline: none;
        }
        .form-label {
            color: #495057;
            font-weight: 600;
        }
        .nurse-card .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .nurse-card .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .card-title {
            color: #0d6efd;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .card-text {
            color: #6c757d;
            font-size: 0.95rem;
        }
        .card-text strong {
            color: #343a40;
        }
        .btn-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }
        .btn-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-chat {
            background-color: #4e73df;
            border-color: #4e73df;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-chat:hover {
            background-color: #3b5bdb;
            border-color: #3b5bdb;
        }
        .average-rating {
            color: #ffc107;
            font-size: 1rem;
            margin-top: 5px;
            text-align: center;
        }
        .average-rating i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include 'fragments/menu-family.php'; ?>
        <div class="layout-page">
            <div class="content-wrapper">
                <div class="content-xxl flex-grow-1 container-p-y">
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
                                    error_log("Final Image URL: " . $baseUrl . ($nurse['profile_image'] ?? '/static/assets/img/avatars/default_profile.jpg'));
                                    ?>
                                    <div class="col-md-4 mb-4 nurse-card" 
                                         data-name="<?php echo htmlspecialchars($nurse['full_name']); ?>" 
                                         data-skills="<?php echo htmlspecialchars($nurse['skills'] ?? ''); ?>" 
                                         data-location="<?php echo htmlspecialchars($nurse['location'] ?? ''); ?>">
                                        <div class="card">
                                            <img src="<?php echo $baseUrl . htmlspecialchars($nurse['profile_image'] ?? '/static/assets/img/avatars/default_profile.jpg'); ?>" 
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
                                                <p class="card-text"><strong>Kinh nghiệm:</strong>
                                                    <?php echo htmlspecialchars($nurse['experience_years'] ? $nurse['experience_years'] . ' năm' : 'Chưa có thông tin'); ?>
                                                </p>
                                                <p class="card-text"><strong>Địa điểm:</strong>
                                                    <?php echo htmlspecialchars($nurse['location'] ?? 'Chưa có thông tin'); ?>
                                                </p>
                                                <p class="card-text"><strong>Giá theo giờ:</strong>
                                                    <?php echo htmlspecialchars($nurse['hourly_rate'] ? number_format($nurse['hourly_rate']) . 'đ/giờ' : 'Chưa có giá'); ?>
                                                </p>
                                                <p class="card-text"><strong>Giá theo ngày:</strong>
                                                    <?php echo htmlspecialchars($nurse['daily_rate'] ? number_format($nurse['daily_rate']) . 'đ/ngày' : 'Chưa có giá'); ?>
                                                </p>
                                                <p class="card-text"><strong>Giá theo tuần:</strong>
                                                    <?php echo htmlspecialchars($nurse['weekly_rate'] ? number_format($nurse['weekly_rate']) . 'đ/tuần' : 'Chưa có giá'); ?>
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
            </div>
        </div>
    </div>
</div>

<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/libs/popper/popper.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../assets/vendor/js/menu.js"></script>
<script src="../assets/js/main.js"></script>
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