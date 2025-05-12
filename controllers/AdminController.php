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
            $approvedNurses = $nurseProfileModel->getApprovedNurseProfiles();

            // Lấy số lượng đơn hoàn thành cho mỗi y tá
            $nurseBookingCounts = $bookingModel->getNurseRanking();
            $bookingCountsByUserId = [];
            foreach ($nurseBookingCounts as $nurseData) {
                $bookingCountsByUserId[$nurseData['nurse_user_id']] = $nurseData['booking_count'];
            }

            // Thêm số lượng đơn vào danh sách y tá
            foreach ($approvedNurses as &$nurse) {
                $nurse['booking_count'] = $bookingCountsByUserId[$nurse['user_id']] ?? 0;
            }

            $_SESSION['user'] = $user;
            include __DIR__ . '/../views/admin_approved_nurses.php';
        } else {
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