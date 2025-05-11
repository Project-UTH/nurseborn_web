<?php
$pageTitle = 'Thông Tin Chi Tiết Y Tá';
include 'fragments/head.php';
include 'fragments/navbar.php';

// Khởi tạo UserModel, NurseProfileModel và FeedbackModel
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NurseProfileModel.php';
require_once __DIR__ . '/../models/FeedbackModel.php';
$baseUrl = '/nurseborn'; // Cập nhật baseUrl
$userModel = new UserModel($conn);
$nurseProfileModel = new NurseProfileModel($conn);
$feedbackModel = new FeedbackModel($conn);

// Lấy nurseUserId từ query string
$nurseUserId = filter_input(INPUT_GET, 'nurseUserId', FILTER_SANITIZE_NUMBER_INT);
error_log("Debug: nurseUserId trong nurse_review.php: " . $nurseUserId);
if (!$nurseUserId) {
    $_SESSION['error'] = "Không tìm thấy ID y tá.";
    error_log("Debug: Không tìm thấy ID y tá.");
    header('Location: ?action=home');
    exit;
}

// Lấy thông tin y tá từ bảng users
$nurse = $userModel->getUserById($nurseUserId);
error_log("Debug: Dữ liệu y tá từ bảng users: " . print_r($nurse, true));
if (!$nurse || $nurse['role'] !== 'NURSE') {
    $_SESSION['error'] = "Y tá không tồn tại.";
    error_log("Debug: Y tá không tồn tại hoặc không có vai trò NURSE.");
    header('Location: ?action=home');
    exit;
}

// Lấy thông tin bổ sung từ bảng nurse_profiles
$nurseProfile = $nurseProfileModel->getNurseProfileByUserId($nurseUserId);
error_log("Debug: Dữ liệu y tá từ bảng nurse_profiles: " . print_r($nurseProfile, true));

// Gộp thông tin từ nurse_profiles vào mảng $nurse
if ($nurseProfile) {
    $nurse['experience_years'] = $nurseProfile['experience_years'] ?? null;
    $nurse['hourly_rate'] = $nurseProfile['hourly_rate'] ?? null;
    $nurse['daily_rate'] = $nurseProfile['daily_rate'] ?? null;
    $nurse['weekly_rate'] = $nurseProfile['weekly_rate'] ?? null;
    $nurse['bio'] = $nurseProfile['bio'] ?? null;
    $nurse['skills'] = $nurseProfile['specialization'] ?? null;
    $nurse['profile_image'] = $nurseProfile['profile_image'] ?? null; // Thêm dòng này
} else {
    $nurse['experience_years'] = null;
    $nurse['hourly_rate'] = null;
    $nurse['daily_rate'] = null;
    $nurse['weekly_rate'] = null;
    $nurse['bio'] = null;
    $nurse['skills'] = null;
    $nurse['profile_image'] = null; // Thêm dòng này
}

// Lấy số sao trung bình và danh sách đánh giá
$averageRating = $feedbackModel->getAverageRatingByNurseUserId($nurseUserId);
$feedbacks = $feedbackModel->getFeedbackByNurseUserId($nurseUserId);
error_log("Debug: Số sao trung bình: " . $averageRating);
error_log("Debug: Danh sách đánh giá: " . print_r($feedbacks, true));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Y Tá</title>
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
            max-width: 1000px;
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
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            margin-bottom: 30px;
        }
        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .card-img-top {
            height: 220px;
            width: 100%;
            object-fit: cover;
            border-radius: 15px 0 0 15px;
            border: 1px solid #e3f2fd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            color: #0d6efd;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .card-body p {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .card-body p strong {
            color: #2c3e50;
            width: 140px;
            display: inline-block;
            font-weight: 700;
            letter-spacing: 0.3px;
            background: linear-gradient(45deg, #e3f2fd, #f8f9fa);
            padding: 2px 5px;
            border-radius: 3px;
        }
        .average-rating {
            color: #ffc107;
            font-size: 1.2rem;
            margin-top: 10px;
            text-align: center;
        }
        .average-rating i {
            margin-right: 5px;
        }
        .bio-container {
            background-color: #f8f9fa;
            border: 1px solid #e3f2fd;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .bio-container h5 {
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .bio-container p {
            margin: 0;
            font-size: 0.95rem;
        }
        .reviews-container {
            background-color: #ffffff;
            border: 1px solid #e3f2fd;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .reviews-container h5 {
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .review-item {
            border-bottom: 1px solid #e3f2fd;
            padding: 10px 0;
        }
        .review-item:last-child {
            border-bottom: none;
        }
        .review-item p {
            margin: 0;
            font-size: 0.95rem;
        }
        .review-item .rating {
            color: #ffc107;
            margin-bottom: 5px;
        }
        .review-item .reviewer {
            font-weight: 600;
            color: #2c3e50;
        }
        .review-item .date {
            color: #6c757d;
            font-size: 0.85rem;
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
                        <h2 class="text-center mb-4">Thông Tin Chi Tiết Y Tá</h2>
                        <?php if (isset($nurse) && !empty($nurse)): ?>
                            <div class="card shadow-lg mb-4">
                                <div class="row g-0">
                                    <div class="col-md-5">
                                        <?php
                                        error_log("Nurse Review Profile Image: " . ($nurse['profile_image'] ?? 'Not set'));
                                        error_log("Nurse Review Final Image URL: " . $baseUrl . ($nurse['profile_image'] ?? '/static/assets/img/avatars/default_profile.jpg'));
                                        ?>
                                        <img src="<?php echo $baseUrl . htmlspecialchars($nurse['profile_image'] ?? '/static/assets/img/avatars/default_profile.jpg'); ?>"
                                             class="img-fluid card-img-top" alt="Ảnh Y Tá">
                                        <div class="average-rating">
                                            <i class="fas fa-star"></i>
                                            <?php echo $averageRating > 0 ? $averageRating : 'Chưa có đánh giá'; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($nurse['full_name']); ?></h5>
                                            <p><strong>Kỹ năng:</strong> <?php echo htmlspecialchars($nurse['skills'] ?? 'Chưa có thông tin'); ?></p>
                                            <p><strong>Kinh nghiệm:</strong> <?php echo htmlspecialchars($nurse['experience_years'] ? $nurse['experience_years'] . ' năm' : 'Chưa có thông tin'); ?></p>
                                            <p><strong>Địa điểm:</strong> <?php echo htmlspecialchars($nurse['location'] ?? 'Chưa có thông tin'); ?></p>
                                            <p><strong>Giá theo giờ:</strong> <?php echo htmlspecialchars($nurse['hourly_rate'] ? number_format($nurse['hourly_rate']) . 'đ/giờ' : 'Chưa có giá'); ?></p>
                                            <p><strong>Giá theo ngày:</strong> <?php echo htmlspecialchars($nurse['daily_rate'] ? number_format($nurse['daily_rate']) . 'đ/ngày' : 'Chưa có giá'); ?></p>
                                            <p><strong>Giá theo tuần:</strong> <?php echo htmlspecialchars($nurse['weekly_rate'] ? number_format($nurse['weekly_rate']) . 'đ/tuần' : 'Chưa có giá'); ?></p>
                                            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($nurse['phone_number'] ?? 'Chưa có thông tin'); ?></p>
                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($nurse['email'] ?? 'Chưa có thông tin'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bio-container">
                                <h5>Giới thiệu</h5>
                                <p><?php echo htmlspecialchars($nurse['bio'] ?? 'Chưa có thông tin'); ?></p>
                            </div>
                            <div class="reviews-container">
                                <h5>Đánh giá của khách hàng</h5>
                                <?php if (!empty($feedbacks)): ?>
                                    <?php foreach ($feedbacks as $feedback): ?>
                                        <div class="review-item">
                                            <div class="rating">
                                                <?php echo htmlspecialchars($feedback['rating']); ?> <i class="fas fa-star"></i>
                                            </div>
                                            <p class="reviewer"><?php echo htmlspecialchars($feedback['family_full_name']); ?></p>
                                            <p><?php echo htmlspecialchars($feedback['comment']); ?></p>
                                            <p class="date">Đánh giá vào: <?php echo htmlspecialchars($feedback['created_at']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>Chưa có đánh giá nào cho y tá này.</p>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center">
                                Không tìm thấy thông tin y tá.
                            </div>
                        <?php endif; ?>
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
<script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboards-analytics.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>