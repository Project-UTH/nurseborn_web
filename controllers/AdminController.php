<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NurseProfileModel.php';
require_once __DIR__ . '/../models/BookingModel.php';
require_once __DIR__ . '/../models/FeedbackModel.php';
require_once __DIR__ . '/../controllers/FeedbackController.php';

// Khởi tạo models và controllers
$userModel = new UserModel($conn);
$nurseProfileModel = new NurseProfileModel($conn);
$bookingModel = new BookingModel($conn);
$feedbackModel = new FeedbackModel($conn);
$feedbackController = new FeedbackController($conn);

// Hàm kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm lấy vai trò người dùng
function getUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

// Xử lý yêu cầu
$action = $_GET['action'] ?? 'admin_home';

switch ($action) {
    case 'admin_approved_nurses':
        if (isLoggedIn() && getUserRole() === 'ADMIN') {
            $user = $userModel->getUserById($_SESSION['user_id']);
            if (!$user) {
                error_log("User not found for ID: {$_SESSION['user_id']}");
                $_SESSION['error'] = 'Người dùng không tồn tại';
                session_destroy();
                header('Location: ?action=login');
                exit;
            }

            // Xử lý xóa tài khoản Nurse
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'delete_nurse') {
                $nurseUserId = isset($_POST['nurse_user_id']) ? (int)$_POST['nurse_user_id'] : 0;
                if ($nurseUserId > 0) {
                    try {
                        $userModel->deleteUser($nurseUserId, 'NURSE');
                        $_SESSION['success'] = 'Xóa tài khoản y tá thành công!';
                    } catch (Exception $e) {
                        error_log("Lỗi khi xóa tài khoản y tá: " . $e->getMessage());
                        $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                    }
                    header('Location: ?action=admin_approved_nurses');
                    exit;
                }
            }

            // Lấy danh sách y tá đã duyệt
            $approvedNurses = $nurseProfileModel->getAllApprovedNurseProfiles();
            if (!$approvedNurses) {
                error_log("Lỗi khi lấy danh sách y tá đã duyệt");
                $_SESSION['error'] = 'Lỗi khi lấy danh sách y tá đã duyệt';
                $approvedNurses = [];
            }

            $_SESSION['user'] = $user;
            include __DIR__ . '/../views/admin_approved_nurses.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'admin_feedback':
        error_log("Debug: Đã vào case admin_feedback trong AdminController.php");
        if (isLoggedIn() && getUserRole() === 'ADMIN') {
            $user = $userModel->getUserById($_SESSION['user_id']);
            if (!$user) {
                error_log("User not found for ID: {$_SESSION['user_id']}");
                $_SESSION['error'] = 'Người dùng không tồn tại';
                session_destroy();
                header('Location: ?action=login');
                exit;
            }
            error_log("Debug: User role is ADMIN, user data: " . print_r($user, true));

            // Xử lý xóa đánh giá
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'delete_feedback') {
                $feedbackId = isset($_POST['feedback_id']) ? (int)$_POST['feedback_id'] : 0;
                if ($feedbackId > 0) {
                    try {
                        $feedbackController->deleteFeedback($feedbackId);
                        error_log("Debug: Xóa đánh giá thành công, feedback_id: $feedbackId");
                    } catch (Exception $e) {
                        error_log("Lỗi khi xóa đánh giá: " . $e->getMessage());
                        $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                    }
                    header('Location: ?action=admin_feedback');
                    exit;
                }
            }

            // Lấy bộ lọc từ query string
            $filters = [
                'nurse_id' => isset($_GET['nurse_id']) ? (int)$_GET['nurse_id'] : null,
                'rating' => isset($_GET['rating']) ? (int)$_GET['rating'] : null,
                'start_date' => isset($_GET['start_date']) ? $_GET['start_date'] : null,
                'end_date' => isset($_GET['end_date']) ? $_GET['end_date'] : null,
            ];
            error_log("Debug: Filters: " . print_r($filters, true));

            // Lấy danh sách đánh giá
            try {
                $feedbacks = $feedbackModel->getAllFeedbacks($filters);
                error_log("Debug: Feedbacks data: " . print_r($feedbacks, true));
            } catch (Exception $e) {
                error_log("Lỗi khi lấy danh sách đánh giá: " . $e->getMessage());
                $_SESSION['error'] = 'Lỗi khi lấy danh sách đánh giá: ' . $e->getMessage();
                $feedbacks = [];
            }

            $_SESSION['user'] = $user;
            error_log("Debug: Đang include admin_feedback.php");
            include __DIR__ . '/../views/admin_feedback.php';
        } else {
            error_log("Debug: Không có quyền truy cập admin_feedback, isLoggedIn: " . isLoggedIn() . ", Role: " . getUserRole());
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'admin_bookings':
        error_log("Debug: Đã vào case admin_bookings trong AdminController.php");
        if (isLoggedIn() && getUserRole() === 'ADMIN') {
            $user = $userModel->getUserById($_SESSION['user_id']);
            if (!$user) {
                error_log("User not found for ID: {$_SESSION['user_id']}");
                $_SESSION['error'] = 'Người dùng không tồn tại';
                session_destroy();
                header('Location: ?action=login');
                exit;
            }
            error_log("Debug: User role is ADMIN, user data: " . print_r($user, true));

            // Xử lý xóa lịch đặt
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'delete_booking') {
                $bookingId = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
                if ($bookingId > 0) {
                    try {
                        $bookingModel->deleteBooking($bookingId);
                        $_SESSION['success'] = 'Xóa lịch đặt thành công!';
                        error_log("Debug: Xóa lịch đặt thành công, booking_id: $bookingId");
                    } catch (Exception $e) {
                        error_log("Lỗi khi xóa lịch đặt: " . $e->getMessage());
                        $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                    }
                    header('Location: ?action=admin_bookings');
                    exit;
                }
            }

            // Lấy danh sách tất cả lịch đặt
            try {
                $bookings = $bookingModel->getAllBookings();
                error_log("Debug: Bookings data: " . print_r($bookings, true));
            } catch (Exception $e) {
                error_log("Lỗi khi lấy danh sách lịch đặt: " . $e->getMessage());
                $_SESSION['error'] = 'Lỗi khi lấy danh sách lịch đặt: ' . $e->getMessage();
                $bookings = [];
            }

            $_SESSION['user'] = $user;
            error_log("Debug: Đang include admin-booking.php");
            include __DIR__ . '/../views/admin-booking.php';
        } else {
            error_log("Debug: Không có quyền truy cập admin_bookings, isLoggedIn: " . isLoggedIn() . ", Role: " . getUserRole());
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'admin_family_accounts':
        if (isLoggedIn() && getUserRole() === 'ADMIN') {
            $user = $userModel->getUserById($_SESSION['user_id']);
            if (!$user) {
                error_log("User not found for ID: {$_SESSION['user_id']}");
                $_SESSION['error'] = 'Người dùng không tồn tại';
                session_destroy();
                header('Location: ?action=login');
                exit;
            }

            // Xử lý xóa tài khoản Family
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'delete_family') {
                $familyUserId = isset($_POST['family_user_id']) ? (int)$_POST['family_user_id'] : 0;
                if ($familyUserId > 0) {
                    try {
                        $userModel->deleteUser($familyUserId, 'FAMILY');
                        $_SESSION['success'] = 'Xóa tài khoản Family thành công!';
                    } catch (Exception $e) {
                        error_log("Lỗi khi xóa tài khoản Family: " . $e->getMessage());
                        $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                    }
                    header('Location: ?action=admin_family_accounts');
                    exit;
                }
            }

            // Lấy danh sách tài khoản Family
            $familyAccounts = $userModel->getFamilyAccounts();

            $_SESSION['user'] = $user;
            include __DIR__ . '/../views/admin_family_accounts.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    default:
        $_SESSION['error'] = 'Hành động không hợp lệ';
        header('Location: ?action=admin_home');
        break;
}
?>

