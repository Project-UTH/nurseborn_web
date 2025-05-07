<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NurseProfileModel.php';
require_once __DIR__ . '/../models/NurseAvailabilityModel.php';
require_once __DIR__ . '/../models/BookingModel.php';

// Khởi tạo models
$userModel = new UserModel($conn);
$nurseProfileModel = new NurseProfileModel($conn);
$nurseAvailabilityModel = new NurseAvailabilityModel($conn);
$bookingModel = new BookingModel($conn);

// Hàm kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm xác thực người dùng vai trò FAMILY
function authenticateFamily($redirect = true) {
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

    if ($user['role'] !== 'FAMILY') {
        error_log("User {$_SESSION['user_id']} không có vai trò FAMILY, role hiện tại: {$user['role']}");
        if ($redirect) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=home');
            exit;
        }
        throw new Exception("Người dùng phải có vai trò FAMILY");
    }

    return $user;
}

// Xử lý yêu cầu
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'nurse_list':
        try {
            $user = authenticateFamily();
            // Lấy danh sách y tá được phê duyệt và đã xác minh
            $nurseUsers = $userModel->findByRoleAndIsVerified('NURSE', true);
            $nurseUserIds = array_column($nurseUsers, 'user_id');
            $nurseProfiles = $nurseProfileModel->getApprovedNurseProfilesByUserIds($nurseUserIds);

            $_SESSION['user'] = $user;
            include __DIR__ . '/../views/nursepage.php';
        } catch (Exception $e) {
            error_log("Lỗi khi hiển thị danh sách y tá: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi tải danh sách y tá: " . $e->getMessage();
            include __DIR__ . '/../views/error.php';
        }
        break;

    case 'set_service':
        try {
            $user = authenticateFamily();
            $nurseUserId = filter_input(INPUT_GET, 'nurseUserId', FILTER_SANITIZE_NUMBER_INT);
            if (empty($nurseUserId)) {
                throw new Exception("Thiếu thông tin y tá. Vui lòng chọn y tá từ danh sách.");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $bookingData = [
                    'nurse_user_id' => filter_input(INPUT_POST, 'nurse_user_id', FILTER_SANITIZE_NUMBER_INT),
                    'booking_date' => filter_input(INPUT_POST, 'booking_date', FILTER_SANITIZE_STRING),
                    'service_type' => filter_input(INPUT_POST, 'service_type', FILTER_SANITIZE_STRING),
                    'start_time' => filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_STRING),
                    'end_time' => filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_STRING),
                    'price' => filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                    'notes' => filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING)
                ];
                $bookingModel->createBooking($bookingData, $user['user_id']);
                $_SESSION['success'] = 'Đặt lịch thành công!';
                header("Location: ?action=set_service&nurseUserId=$bookingData[nurse_user_id]");
                exit;
            }

            $nurseUser = $userModel->getUserById($nurseUserId);
            if (!$nurseUser) {
                throw new Exception("Không tìm thấy y tá với ID: $nurseUserId");
            }
            $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($nurseUserId);
            if (!$nurseProfile) {
                throw new Exception("Không tìm thấy NurseProfile cho y tá với ID: $nurseUserId");
            }
            if (!$nurseProfile['is_approved']) {
                throw new Exception("Y tá này chưa được phê duyệt");
            }

            // Đặt giá trị mặc định nếu các trường giá là null
            $nurseProfile['hourly_rate'] = $nurseProfile['hourly_rate'] ?? 0.0;
            $nurseProfile['daily_rate'] = $nurseProfile['daily_rate'] ?? 0.0;
            $nurseProfile['weekly_rate'] = $nurseProfile['weekly_rate'] ?? 0.0;

            $availability = $nurseAvailabilityModel->getAvailabilityByUserId($nurseUserId);

            $_SESSION['user'] = $user;
            include __DIR__ . '/../views/book-nurse.php';
        } catch (Exception $e) {
            error_log("Lỗi khi xử lý đặt lịch: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi tải form đặt lịch: " . $e->getMessage();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Hiển thị lại form với dữ liệu đã nhập
                $nurseUserId = filter_input(INPUT_POST, 'nurse_user_id', FILTER_SANITIZE_NUMBER_INT);
                $nurseUser = $userModel->getUserById($nurseUserId);
                $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($nurseUserId);
                $availability = $nurseAvailabilityModel->getAvailabilityByUserId($nurseUserId);
                $nurseProfile['hourly_rate'] = $nurseProfile['hourly_rate'] ?? 0.0;
                $nurseProfile['daily_rate'] = $nurseProfile['daily_rate'] ?? 0.0;
                $nurseProfile['weekly_rate'] = $nurseProfile['weekly_rate'] ?? 0.0;
                include __DIR__ . '/../views/book-nurse.php';
            } else {
                include __DIR__ . '/../views/error.php';
            }
        }
        break;

    default:
        header('Location: ?action=home');
        break;
}
?>