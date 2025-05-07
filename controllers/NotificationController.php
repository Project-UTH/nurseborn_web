<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NurseProfileModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

// Khởi tạo models
$userModel = new UserModel($conn);
$nurseProfileModel = new NurseProfileModel($conn);
$notificationModel = new NotificationModel($conn);

// Hàm kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm xác thực người dùng
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

// Xử lý yêu cầu
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'notifications':
        try {
            $user = authenticateUser();
            $notifications = $notificationModel->getAllNotificationsForUser($user['user_id']);

            // Thêm nurseProfile nếu người dùng là NURSE
            if ($user['role'] === 'NURSE') {
                $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($user['user_id']);
                if (!$nurseProfile) {
                    throw new Exception("Không tìm thấy NurseProfile cho userId: {$user['user_id']}");
                }
                $_SESSION['nurse_profile'] = $nurseProfile;
                error_log("NurseProfile userId={$user['user_id']}, profileImage={$nurseProfile['profile_image']}");
            }

            $_SESSION['user'] = $user;
            include __DIR__ . '/../views/notifications.php';
        } catch (Exception $e) {
            error_log("Lỗi khi hiển thị thông báo: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?action=login');
            exit;
        }
        break;

    case 'mark_as_read':
        try {
            $notificationId = filter_input(INPUT_GET, 'notificationId', FILTER_SANITIZE_NUMBER_INT);
            if (empty($notificationId)) {
                throw new Exception("ID thông báo không hợp lệ");
            }
            $notificationModel->markAsRead($notificationId);
            $_SESSION['success'] = 'Đánh dấu thông báo đã đọc thành công';
            header('Location: ?action=notifications');
            exit;
        } catch (Exception $e) {
            error_log("Lỗi khi đánh dấu thông báo đã đọc: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?action=notifications');
            exit;
        }
        break;

    default:
        header('Location: ?action=home');
        break;
}
?>