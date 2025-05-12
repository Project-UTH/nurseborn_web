<?php
$pageTitle = 'Đánh Giá ';
$baseUrl = '/nurseborn';

// Lấy nurseUserId từ query string hoặc session
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseUserId = filter_input(INPUT_GET, 'nurseUserId', FILTER_SANITIZE_NUMBER_INT);
if (!$nurseUserId && $user) {
    $nurseUserId = $user['user_id'];
}

if (!$nurseUserId) {
    $_SESSION['error'] = "Không tìm thấy ID y tá.";
    header("Location: $baseUrl/?action=home");
    exit;
}

// Kiểm tra quyền truy cập: Y tá chỉ được xem đánh giá của chính mình
if ($user && $user['role'] === 'NURSE' && $nurseUserId != $user['user_id']) {
    $_SESSION['error'] = "Bạn chỉ có thể xem đánh giá của chính mình.";
    header("Location: $baseUrl/?action=home");
    exit;
}

// Khởi tạo UserModel, NurseProfileModel và FeedbackModel
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NurseProfileModel.php';
require_once __DIR__ . '/../models/FeedbackModel.php';
$userModel = new UserModel($conn);
$nurseProfileModel = new NurseProfileModel($conn);
$feedbackModel = new FeedbackModel($conn);

// Lấy thông tin y tá từ bảng users
$nurse = $userModel->getUserById($nurseUserId);
if (!$nurse || $nurse['role'] !== 'NURSE') {
    $_SESSION['error'] = "Y tá không tồn tại.";
    header("Location: $baseUrl/?action=home");
    exit;
}

// Lấy thông tin bổ sung từ bảng nurse_profiles (chỉ lấy các thông tin cần thiết)
$nurseProfile = $nurseProfileModel->getNurseProfileByUserId($nurseUserId);

// Gộp thông tin cần thiết từ nurse_profiles vào mảng $nurse
if ($nurseProfile) {
    $nurse['profile_image'] = $nurseProfile['profile_image'] ?? null;
} else {
    $nurse['profile_image'] = null;
}

// Lấy số sao trung bình và danh sách đánh giá
$averageRating = $feedbackModel->getAverageRatingByNurseUserId($nurseUserId);
$feedbacks = $feedbackModel->getFeedbackByNurseUserId($nurseUserId);
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>/static/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/core.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/theme-default.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/css/demo.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f7f9fc 0%, #e9f1fb 100%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }
        .container {
            max-width: 900px;
            padding: 50px 20px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 50px;
        }
        h2.text-center {
            font-size: 2.7rem;
            font-weight: 700;
            color: #2c3e50;
            position: relative;
            margin-bottom: 20px;
            animation: slideInDown 1s ease-in-out;
        }
        h2.text-center::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            border-radius: 5px;
        }
        @keyframes slideInDown {
            0% { opacity: 0; transform: translateY(-30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .profile-header {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
            animation: fadeInUp 0.8s ease-in-out;
        }
        .profile-header .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #e3f2fd;
            transition: border-color 0.3s ease;
        }
        .profile-header:hover .profile-img {
            border-color: #3498db;
        }
        .profile-header .profile-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-header .profile-info {
            flex: 1;
        }
        .profile-header .profile-info h3 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .profile-header .profile-info .average-rating {
            display: inline-flex;
            align-items: center;
            background: rgba(241, 196, 15, 0.1);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 1.1rem;
            font-weight: 500;
            color: #f1c40f;
        }
        .profile-header .profile-info .average-rating i {
            margin-right: 8px;
        }
        .reviews-section {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            animation: fadeInUp 1s ease-in-out;
        }
        .reviews-section h5 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #3498db;
            margin-bottom: 25px;
        }
        .review-card {
            background: #f9fafc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUp 0.6s ease-in-out;
        }
        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .review-card .rating {
            color: #f1c40f;
            font-weight: 500;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        .review-card .rating i {
            margin-right: 5px;
        }
        .review-card .reviewer {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .review-card .comment {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .review-card .date {
            font-size: 0.9rem;
            color: #adb5bd;
            font-style: italic;
        }
        .alert {
            border-radius: 10px;
            padding: 20px;
            font-size: 1.1rem;
            animation: fadeInUp 0.6s ease-in-out;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
            .profile-header .profile-img {
                width: 120px;
                height: 120px;
                margin-bottom: 20px;
            }
            h2.text-center {
                font-size: 2.2rem;
            }
            .reviews-section {
                padding: 20px;
            }
        }
        @media (max-width: 768px) {
            .container {
                padding: 30px 15px;
            }
            h2.text-center {
                font-size: 2rem;
            }
            .profile-header .profile-img {
                width: 100px;
                height: 100px;
            }
            .profile-header .profile-info h3 {
                font-size: 1.5rem;
            }
            .review-card {
                padding: 15px;
            }
            .review-card .reviewer {
                font-size: 1rem;
            }
            .review-card .rating {
                font-size: 1rem;
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
                <div class="container flex-grow-1 container-p-y">
                    <div class="header-section">
                        <h2 class="text-center mb-4">Đánh Giá</h2>
                    </div>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger text-center">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <?php if (isset($nurse) && !empty($nurse)): ?>
                        <div class="profile-header">
                            <div class="profile-img">
                                <img src="<?php echo $baseUrl . '/' . htmlspecialchars(ltrim($nurse['profile_image'] ?? '/static/assets/img/avatars/default_profile.jpg', '/')); ?>"
                                     alt="Ảnh Y Tá">
                            </div>
                            <div class="profile-info">
                                <h3><?php echo htmlspecialchars($nurse['full_name']); ?></h3>
                                <div class="average-rating">
                                    <i class="fas fa-star"></i>
                                    <?php echo $averageRating > 0 ? $averageRating : 'Chưa có đánh giá'; ?>
                                </div>
                            </div>
                        </div>
                        <div class="reviews-section">
                            <h5>Đánh giá của khách hàng</h5>
                            <?php if (!empty($feedbacks)): ?>
                                <?php foreach ($feedbacks as $feedback): ?>
                                    <div class="review-card">
                                        <div class="rating">
                                            <?php echo htmlspecialchars($feedback['rating']); ?> <i class="fas fa-star"></i>
                                        </div>
                                        <p class="reviewer"><?php echo htmlspecialchars($feedback['family_full_name']); ?></p>
                                        <p class="comment"><?php echo htmlspecialchars($feedback['comment']); ?></p>
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
</body>
</html>