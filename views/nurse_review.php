<?php
$pageTitle = 'Thông Tin Chi Tiết Y Tá';
$baseUrl = '/nurseborn';

// Khởi tạo UserModel, NurseProfileModel và FeedbackModel
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NurseProfileModel.php';
require_once __DIR__ . '/../models/FeedbackModel.php';
$userModel = new UserModel($conn);
$nurseProfileModel = new NurseProfileModel($conn);
$feedbackModel = new FeedbackModel($conn);

// Lấy nurseUserId từ query string hoặc session
$nurseUserId = filter_input(INPUT_GET, 'nurseUserId', FILTER_SANITIZE_NUMBER_INT);
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
if (!$nurseUserId && $user && $user['role'] === 'NURSE') {
    $nurseUserId = $user['id'];
}
error_log("Debug: nurseUserId trong nurse_review.php: " . ($nurseUserId ?? 'Not set'));

if (!$nurseUserId) {
    $_SESSION['error'] = "Không tìm thấy ID y tá.";
    error_log("Debug: Không tìm thấy ID y tá.");
    header("Location: $baseUrl/?action=home");
    exit;
}

// Kiểm tra quyền truy cập: Y tá chỉ được xem đánh giá của chính mình
if ($user && $user['role'] === 'NURSE' && $nurseUserId != $user['id']) {
    $_SESSION['error'] = "Bạn chỉ có thể xem đánh giá của chính mình.";
    error_log("Debug: Y tá không có quyền xem đánh giá của y tá khác.");
    header("Location: $baseUrl/nurse-home.php");
    exit;
}

// Lấy thông tin y tá từ bảng users
$nurse = $userModel->getUserById($nurseUserId);
error_log("Debug: Dữ liệu y tá từ bảng users: " . print_r($nurse, true));
if (!$nurse || $nurse['role'] !== 'NURSE') {
    $_SESSION['error'] = "Y tá không tồn tại.";
    error_log("Debug: Y tá không tồn tại hoặc không có vai trò NURSE.");
    header("Location: $baseUrl/?action=home");
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
    $nurse['skills'] = $nurseProfile['specialization'] ?? $nurseProfile['skills'] ?? null;
    error_log("Debug: Skills value: " . ($nurse['skills'] ?? 'Not set'));
    $nurse['location'] = $nurse['location'] ?? $nurseProfile['location'] ?? null;
    error_log("Debug: Location value: " . ($nurse['location'] ?? 'Not set'));
    $nurse['profile_image'] = $nurseProfile['profile_image'] ?? null;
    if (!isset($nurseProfile['certificates'])) {
        $nurseProfile['certificates'] = $nurseProfileModel->getCertificatesByUserId($nurseUserId) ?? [];
        error_log("Debug: Fetched certificates separately: " . print_r($nurseProfile['certificates'], true));
    }
    $nurse['certificates'] = $nurseProfile['certificates'] ?? [];
    error_log("Debug: Certificates in nurse array: " . print_r($nurse['certificates'], true));
} else {
    $nurse['experience_years'] = null;
    $nurse['hourly_rate'] = null;
    $nurse['daily_rate'] = null;
    $nurse['weekly_rate'] = null;
    $nurse['bio'] = null;
    $nurse['skills'] = null;
    $nurse['location'] = $nurse['location'] ?? null;
    $nurse['profile_image'] = null;
    $nurse['certificates'] = [];
}

// Lấy số sao trung bình và danh sách đánh giá
$averageRating = $feedbackModel->getAverageRatingByNurseUserId($nurseUserId);
$feedbacks = $feedbackModel->getFeedbackByNurseUserId($nurseUserId);
error_log("Debug: Số sao trung bình: " . ($averageRating ?? 'Not set'));
error_log("Debug: Danh sách đánh giá: " . print_r($feedbacks, true));
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>/static/assets/" data-template="vertical-menu-template-free">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/core.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/theme-default.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/css/demo.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 1000px;
        }
        .menu-link, .menu-link:hover, .menu-link:active, .menu-link:focus,
        .app-brand-link, .app-brand-link:hover, .app-brand-link:active, .app-brand-link:focus {
            text-decoration: none !important;
        }
        .menu-sub .menu-link {
            padding-left: 2.5rem;
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
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            margin-bottom: 30px;
            background: linear-gradient(145deg, #ffffff, #f0f4f8);
        }
        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-8px);
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
            display: flex;
            align-items: center;
            gap: 8px;
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
        .card-body i {
            color: #0d6efd;
            font-size: 1.1rem;
        }
        .card-body ul {
            margin: 0;
            padding-left: 20px;
        }
        .card-body ul li {
            color: #6c757d;
            font-size: 1rem;
        }
        .card-body ul li a {
            color: #0d6efd;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .card-body ul li a:hover {
            color: #0056b3;
            text-decoration: underline;
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
        <!-- Menu động dựa trên vai trò -->
        <?php
        if ($user && $user['role'] === 'FAMILY') {
            include __DIR__ . '/fragments/menu-family.php';
        } elseif ($user && $user['role'] === 'NURSE') {
            include __DIR__ . '/fragments/menu-nurse.php';
        } elseif ($user && $user['role'] === 'ADMIN') {
            include __DIR__ . '/fragments/menu-admin.php';
        } else {
            include __DIR__ . '/fragments/menu-family.php';
        }
        ?>
        <div class="layout-page">
            <!-- Navbar -->
            <?php
            if ($user && $user['role'] === 'NURSE') {
                include __DIR__ . '/fragments/navbar-nurse.php';
            } else {
                include __DIR__ . '/fragments/navbar.php';
            }
            ?>
            <div class="content-wrapper">
                <div class="container flex-grow-1 container-p-y">
                    <h2 class="text-center mb-4">Thông Tin Chi Tiết Y Tá</h2>
                    <?php if (isset($nurse) && !empty($nurse)): ?>
                        <div class="card shadow-lg mb-4">
                            <div class="row g-0">
                                <div class="col-md-5">
                                    <?php
                                    error_log("Nurse Review Profile Image: " . ($nurse['profile_image'] ?? 'Not set'));
                                    error_log("Nurse Review Final Image URL: " . $baseUrl . '/' . ltrim($nurse['profile_image'] ?? '/static/assets/img/avatars/default_profile.jpg', '/'));
                                    ?>
                                    <img src="<?php echo $baseUrl . '/' . htmlspecialchars(ltrim($nurse['profile_image'] ?? '/static/assets/img/avatars/default_profile.jpg', '/')); ?>"
                                         class="img-fluid card-img-top" alt="Ảnh Y Tá">
                                    <div class="average-rating">
                                        <i class="fas fa-star"></i>
                                        <?php echo $averageRating > 0 ? $averageRating : 'Chưa có đánh giá'; ?>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($nurse['full_name']); ?></h5>
                                        <p>
                                            <i class="fas fa-tools"></i>
                                            <strong>Kỹ năng:</strong>
                                            <?php echo htmlspecialchars($nurse['skills'] ?? 'Chưa có thông tin'); ?>
                                        </p>
                                        <p>
                                            <i class="fas fa-briefcase"></i>
                                            <strong>Kinh nghiệm:</strong>
                                            <?php echo htmlspecialchars($nurse['experience_years'] ? $nurse['experience_years'] . ' năm' : 'Chưa có thông tin'); ?>
                                        </p>
                                        <p>
                                            <i class="fas fa-location-dot"></i>
                                            <strong>Địa điểm:</strong>
                                            <?php echo htmlspecialchars($nurse['location'] ?? 'Chưa có thông tin'); ?>
                                        </p>
                                        <p>
                                            <i class="fas fa-money-bill-wave"></i>
                                            <strong>Giá theo giờ:</strong>
                                            <?php echo htmlspecialchars($nurse['hourly_rate'] ? number_format($nurse['hourly_rate'], 0, ',', '.') . ' VNĐ/giờ' : 'Chưa có giá'); ?>
                                        </p>
                                        <p>
                                            <i class="fas fa-money-bill-wave"></i>
                                            <strong>Giá theo ngày:</strong>
                                            <?php echo htmlspecialchars($nurse['daily_rate'] ? number_format($nurse['daily_rate'], 0, ',', '.') . ' VNĐ/ngày' : 'Chưa có giá'); ?>
                                        </p>
                                        <p>
                                            <i class="fas fa-money-bill-wave"></i>
                                            <strong>Giá theo tuần:</strong>
                                            <?php echo htmlspecialchars($nurse['weekly_rate'] ? number_format($nurse['weekly_rate'], 0, ',', '.') . ' VNĐ/tuần' : 'Chưa có giá'); ?>
                                        </p>
                                        <p>
                                            <i class="fas fa-phone"></i>
                                            <strong>Số điện thoại:</strong>
                                            <?php echo htmlspecialchars($nurse['phone_number'] ?? 'Chưa có thông tin'); ?>
                                        </p>
                                        <p>
                                            <i class="fas fa-envelope"></i>
                                            <strong>Email:</strong>
                                            <?php echo htmlspecialchars($nurse['email'] ?? 'Chưa có thông tin'); ?>
                                        </p>
                                        <p>
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