<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NurseProfileModel.php';
require_once __DIR__ . '/../models/FeedbackModel.php';

// Khởi tạo models
$userModel = new UserModel($conn);
$nurseProfileModel = new NurseProfileModel($conn);
$feedbackModel = new FeedbackModel($conn);

// Hàm kiểm tra đăng nhập (giữ nguyên từ UserController)
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm lấy vai trò người dùng (giữ nguyên từ UserController)
function getUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

// Hàm xác thực người dùng (giữ nguyên từ UserController)
function authenticateUser($redirect = true) {
    if (!isLoggedIn()) {
        error_log("Người dùng chưa đăng nhập");
        if ($redirect) {
            $_SESSION['error'] = 'Vui lòng đăng nhập';
            header('Location: ?action=login');
            exit;
        }
        throw new Exception("Vui lòng đăng nhập");
    }

    global $userModel;
    $user = $userModel->getUserById($_SESSION['user_id']);
    if (!$user) {
        error_log("User not found for ID: {$_SESSION['user_id']}");
        if ($redirect) {
            $_SESSION['error'] = 'Người dùng không tồn tại';
            session_destroy();
            header('Location: ?action=login');
            exit;
        }
        throw new Exception("Người dùng không tồn tại");
    }

    return $user;
}

// Xử lý action review_nurse
$action = $_GET['action'] ?? 'review_nurse';

switch ($action) {
    case 'review_nurse':
        try {
            // Kiểm tra đăng nhập và phân quyền
            $user = authenticateUser();
            if (getUserRole() !== 'NURSE') {
                error_log("User role " . getUserRole() . " not authorized for review_nurse");
                $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
                header('Location: ?action=home');
                exit;
            }

            // Lấy nurseUserId từ query string hoặc session
            $nurseUserId = filter_input(INPUT_GET, 'nurseUserId', FILTER_SANITIZE_NUMBER_INT);
            if (!$nurseUserId) {
                $nurseUserId = $user['user_id'];
            }

            // Kiểm tra quyền truy cập: Y tá chỉ được xem đánh giá của chính mình
            if ($nurseUserId != $user['user_id']) {
                error_log("Y tá không có quyền xem đánh giá của y tá khác. Requested nurseUserId: $nurseUserId, User id: {$user['user_id']}");
                $_SESSION['error'] = 'Bạn chỉ có thể xem đánh giá của chính mình';
                header('Location: ?action=home');
                exit;
            }

            // Thêm nurseProfile vào session nếu có
            $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($user['user_id']);
            $_SESSION['nurse_profile'] = $nurseProfile; // Có thể là null
            if ($nurseProfile) {
                error_log("NurseProfile userId={$user['user_id']}, profileImage={$nurseProfile['profile_image']}");
            } else {
                error_log("Không tìm thấy NurseProfile cho userId: {$user['user_id']}");
            }

            $_SESSION['user'] = $user;
            include __DIR__ . '/../views/review_nurse.php';
        } catch (Exception $e) {
            error_log("Lỗi khi hiển thị đánh giá y tá: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?action=home');
            exit;
        }
        break;

    default:
        header('Location: ?action=home');
        break;
}
?>