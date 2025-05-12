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

// Hàm lấy vai trò người dùng
function getUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

// Hàm xác thực và lấy thông tin y tá
function authenticateAndGetNurse($redirect = true) {
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
$action = $_GET['action'] ?? 'nurse_home';

switch ($action) {
    case 'nurse_availability':
        try {
            $user = authenticateAndGetNurse();
            $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($user['user_id']);
            if (!$nurseProfile) {
                $_SESSION['error'] = 'Hồ sơ y tá chưa được tạo. Vui lòng cập nhật hồ sơ.';
                header('Location: ?action=register_nurse');
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $selectedDays = isset($_POST['days_of_week']) && is_array($_POST['days_of_week'])
                    ? array_map('trim', $_POST['days_of_week'])
                    : [];
                $validDays = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
                foreach ($selectedDays as $day) {
                    if (!in_array($day, $validDays)) {
                        throw new Exception("Ngày làm việc không hợp lệ: " . htmlspecialchars($day));
                    }
                }
                $nurseAvailabilityModel->createOrUpdateAvailability($user['user_id'], $selectedDays);
                $_SESSION['success'] = 'Cập nhật lịch làm việc thành công';
                header('Location: ?action=nurse_schedule');
                exit;
            }

            $_SESSION['user'] = $user;
            $_SESSION['nurse_profile'] = $nurseProfile;
            $availability = $nurseAvailabilityModel->getAvailabilityByUserId($user['user_id']);
            include __DIR__ . '/../views/nurse_availability.php';
        } catch (Exception $e) {
            error_log("Lỗi khi xử lý lịch làm việc: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            include __DIR__ . '/../views/nurse_availability.php';
        }
        break;

    case 'nurse_schedule':
    try {
        $user = authenticateAndGetNurse();
        $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($user['user_id']);
        if (!$nurseProfile) {
            $_SESSION['error'] = 'Hồ sơ y tá chưa được tạo. Vui lòng cập nhật hồ sơ.';
            header('Location: ?action=register_nurse');
            exit;
        }

        // Lấy và xác thực weekOffset
        $weekOffset = isset($_GET['weekOffset']) ? (int)$_GET['weekOffset'] : 0;
        error_log("weekOffset: $weekOffset");

        // Tính toán tuần
        $currentDate = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $startOfWeek = (clone $currentDate)->modify('monday this week')->modify("$weekOffset weeks");
        $endOfWeek = (clone $startOfWeek)->modify('+6 days');

        error_log("startOfWeek: " . $startOfWeek->format('Y-m-d'));
        error_log("endOfWeek: " . $endOfWeek->format('Y-m-d'));

        $weekDates = [];
        for ($date = clone $startOfWeek; $date <= $endOfWeek; $date->modify('+1 day')) {
            $weekDates[] = clone $date;
            error_log("Week date: " . $date->format('Y-m-d'));
        }

        $availability = $nurseAvailabilityModel->getAvailabilityByUserId($user['user_id']);
        $acceptedBookings = $bookingModel->getBookingsByNurseUserIdAndStatus($user['user_id'], 'ACCEPTED');

        // Nhóm lịch đặt theo ngày
        $bookingsByDate = [];
        foreach ($acceptedBookings as $booking) {
            $bookingDate = new DateTime($booking['booking_date']);
            if ($bookingDate >= $startOfWeek && $bookingDate <= $endOfWeek) {
                $dateKey = $bookingDate->format('Y-m-d');
                $bookingsByDate[$dateKey][] = $booking;
            }
        }

        $_SESSION['user'] = $user;
        $_SESSION['nurse_profile'] = $nurseProfile;
        include __DIR__ . '/../views/nurse_schedule.php';
    } catch (Exception $e) {
        error_log("Lỗi khi hiển thị lịch: " . $e->getMessage());
        $_SESSION['error'] = $e->getMessage();
        include __DIR__ . '/../views/error.php';
    }
    break;

    case 'complete_booking':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $user = authenticateAndGetNurse();
                $bookingId = filter_input(INPUT_POST, 'bookingId', FILTER_SANITIZE_NUMBER_INT);
                $weekOffset = filter_input(INPUT_POST, 'weekOffset', FILTER_SANITIZE_NUMBER_INT) ?? 0;
                if (empty($bookingId)) {
                    throw new Exception("ID lịch đặt không hợp lệ");
                }
                $bookingModel->completeBooking($bookingId, $user['user_id']);
                $_SESSION['success'] = 'Hoàn thành lịch đặt thành công';
                header("Location: ?action=nurse_schedule&weekOffset=$weekOffset");
                exit;
            } catch (Exception $e) {
                error_log("Lỗi khi hoàn thành lịch đặt: " . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                header("Location: ?action=nurse_schedule&weekOffset=$weekOffset");
                exit;
            }
        }
        break;

    default:
        header('Location: ?action=nurse_home');
        break;
}
?>