<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NurseProfileModel.php';
require_once __DIR__ . '/../models/BookingModel.php';

// Khởi tạo models
$userModel = new UserModel($conn);
$nurseProfileModel = new NurseProfileModel($conn);
$bookingModel = new BookingModel($conn);

// Hàm kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm xác thực người dùng vai trò NURSE
function authenticateNurse($redirect = true) {
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

    if ($user['role'] !== 'NURSE') {
        error_log("User {$_SESSION['user_id']} không có vai trò NURSE, role hiện tại: {$user['role']}");
        if ($redirect) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=home');
            exit;
        }
        throw new Exception("Người dùng phải có vai trò NURSE");
    }

    return $user;
}

// Xử lý yêu cầu
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'pending_bookings':
        try {
            $user = authenticateNurse();
            $pendingBookings = $bookingModel->getBookingsByNurseUserIdAndStatus($user['user_id'], 'PENDING');
            $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($user['user_id']);
            if (!$nurseProfile) {
                throw new Exception("Không tìm thấy NurseProfile cho userId: {$user['user_id']}");
            }

            $_SESSION['user'] = $user;
            $_SESSION['nurse_profile'] = $nurseProfile;
            include __DIR__ . '/../views/pending_bookings.php';
        } catch (Exception $e) {
            error_log("Lỗi khi hiển thị lịch đặt: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi tải danh sách lịch đặt: " . $e->getMessage();
            include __DIR__ . '/../views/error.php';
        }
        break;

    case 'accept_booking':
        try {
            $user = authenticateNurse();
            $bookingId = filter_input(INPUT_POST, 'bookingId', FILTER_SANITIZE_NUMBER_INT);
            if (empty($bookingId)) {
                throw new Exception("ID lịch đặt không hợp lệ");
            }
            $bookingModel->acceptBooking($bookingId, $user['user_id']);
            $_SESSION['success'] = 'Chấp nhận lịch đặt thành công!';
            header('Location: ?action=nurse_schedule');
            exit;
        } catch (Exception $e) {
            error_log("Lỗi khi chấp nhận lịch đặt: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi chấp nhận lịch đặt: " . $e->getMessage();
            header('Location: ?action=pending_bookings');
            exit;
        }
        break;

    case 'cancel_booking':
        try {
            $user = authenticateNurse();
            $bookingId = filter_input(INPUT_POST, 'bookingId', FILTER_SANITIZE_NUMBER_INT);
            if (empty($bookingId)) {
                throw new Exception("ID lịch đặt không hợp lệ");
            }
            $bookingModel->cancelBooking($bookingId, $user['user_id']);
            $_SESSION['success'] = 'Hủy lịch đặt thành công!';
            header('Location: ?action=pending_bookings');
            exit;
        } catch (Exception $e) {
            error_log("Lỗi khi hủy lịch đặt: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi hủy lịch đặt: " . $e->getMessage();
            header('Location: ?action=pending_bookings');
            exit;
        }
        break;

    default:
        header('Location: ?action=home');
        break;
}
?>