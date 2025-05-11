<?php
// Hiển thị lỗi trực tiếp để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NurseProfileModel.php';
require_once __DIR__ . '/../models/NurseAvailabilityModel.php';
require_once __DIR__ . '/../models/BookingModel.php';
require_once __DIR__ . '/../models/FeedbackModel.php';

// Khởi tạo models
$userModel = new UserModel($conn);
$nurseProfileModel = new NurseProfileModel($conn);
$nurseAvailabilityModel = new NurseAvailabilityModel($conn);
$bookingModel = new BookingModel($conn);
$feedbackModel = new FeedbackModel($conn);

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

            // Gán mảng $nurses để sử dụng trong nursepage.php
            $nurses = $nurseProfiles;

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

        // Lấy thông tin y tá và giá dịch vụ trước
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

        // Lấy lịch làm việc
        $availability = $nurseAvailabilityModel->getAvailabilityByUserId($nurseUserId);

        // Hợp nhất dữ liệu thành mảng $nurse
        $nurse = array_merge($nurseUser, $nurseProfile);
        $nurse['selected_days'] = $availability['selected_days'] ?? [];

        // Đặt giá trị mặc định nếu các trường giá là null
        $nurse['hourly_rate'] = $nurse['hourly_rate'] ?? 0.0;
        $nurse['daily_rate'] = $nurse['daily_rate'] ?? 0.0;
        $nurse['weekly_rate'] = $nurse['weekly_rate'] ?? 0.0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingData = [
                'nurse_user_id' => filter_input(INPUT_POST, 'nurse_user_id', FILTER_SANITIZE_NUMBER_INT),
                'booking_date' => filter_var($_POST['booking_date'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                'service_type' => filter_var($_POST['service_type'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                'start_time' => filter_var($_POST['start_time'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                'end_time' => filter_var($_POST['end_time'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                'price' => filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'notes' => filter_var($_POST['notes'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)
            ];

            // Debug: Ghi log dữ liệu nhận được từ form
            error_log("Dữ liệu bookingData từ form: " . print_r($bookingData, true));

            // Kiểm tra giá từ form
            if (!isset($bookingData['price']) || $bookingData['price'] <= 0) {
                // Tính lại giá dựa trên service_type và thời gian
                $serviceType = $bookingData['service_type'];
                $price = 0.0;
                if ($serviceType === 'HOURLY') {
                    if (!empty($bookingData['start_time']) && !empty($bookingData['end_time'])) {
                        $start = new DateTime($bookingData['start_time']);
                        $end = new DateTime($bookingData['end_time']);
                        $interval = $start->diff($end);
                        $hours = $interval->h + ($interval->i / 60);
                        $price = $nurse['hourly_rate'] * $hours;
                    }
                } elseif ($serviceType === 'DAILY') {
                    $price = $nurse['daily_rate'];
                } elseif ($serviceType === 'WEEKLY') {
                    $price = $nurse['weekly_rate'];
                }

                // Cập nhật giá vào $bookingData
                $bookingData['price'] = $price;
                error_log("Giá tính lại phía server: " . $bookingData['price']);
            }

            // Kiểm tra giá cuối cùng trước khi lưu
            if ($bookingData['price'] <= 0) {
                throw new Exception("Giá dịch vụ không hợp lệ. Vui lòng kiểm tra lại.");
            }

            $bookingModel->createBooking($bookingData, $user['user_id']);
            $_SESSION['success'] = 'Đặt lịch thành công!';
            error_log("Đã lưu lịch đặt thành công cho family_user_id: " . $user['user_id'] . " với giá: " . $bookingData['price']);
            header("Location: ?action=bookings");
            exit;
        }

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
            $nurse = array_merge($nurseUser, $nurseProfile);
            $nurse['selected_days'] = $availability['selected_days'] ?? [];
            $nurse['hourly_rate'] = $nurse['hourly_rate'] ?? 0.0;
            $nurse['daily_rate'] = $nurse['daily_rate'] ?? 0.0;
            $nurse['weekly_rate'] = $nurse['weekly_rate'] ?? 0.0;
            include __DIR__ . '/../views/book-nurse.php';
        } else {
            include __DIR__ . '/../views/error.php';
        }
    }
    break;

    case 'bookings':
        error_log("Bắt đầu xử lý hành động bookings");
        try {
            $user = authenticateFamily();
            error_log("Debug: user_id hiện tại: " . $user['user_id']);
            
            // Kiểm tra kết nối cơ sở dữ liệu
            if (!$conn) {
                error_log("Lỗi: Kết nối cơ sở dữ liệu thất bại");
                die("Lỗi: Kết nối cơ sở dữ liệu thất bại");
            }
            error_log("Kết nối cơ sở dữ liệu thành công");

            $stmt = $conn->prepare(
                "SELECT b.*, u.full_name AS nurse_full_name 
                 FROM bookings b 
                 LEFT JOIN users u ON b.nurse_user_id = u.user_id 
                 WHERE b.family_user_id = ?"
            );
            if (!$stmt) {
                error_log("Lỗi khi chuẩn bị truy vấn: " . $conn->error);
                die("Lỗi khi chuẩn bị truy vấn: " . $conn->error);
            }
            $stmt->bind_param("i", $user['user_id']);
            error_log("Truy vấn SQL được chuẩn bị thành công");

            $stmt->execute();
            error_log("Truy vấn SQL được thực thi thành công");

            $result = $stmt->get_result();
            $bookings = $result->fetch_all(MYSQLI_ASSOC);
            error_log("Debug: Số lượng lịch đặt tìm thấy: " . count($bookings));
            error_log("Debug: Dữ liệu bookings: " . print_r($bookings, true));

            $_SESSION['user'] = $user;
            
            // Kiểm tra xem file bookings.php có tồn tại không
            $bookingFilePath = __DIR__ . '/../views/bookings.php';
            if (!file_exists($bookingFilePath)) {
                error_log("Lỗi: File bookings.php không tồn tại tại: $bookingFilePath");
                die("Lỗi: File bookings.php không tồn tại tại: $bookingFilePath");
            }
            error_log("File bookings.php tồn tại, đang bao gồm file");
            
            include $bookingFilePath;
            error_log("Đã bao gồm file bookings.php thành công");
        } catch (Exception $e) {
            error_log("Lỗi khi hiển thị danh sách lịch đặt: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi tải danh sách lịch đặt: " . $e->getMessage();
            include __DIR__ . '/../views/error.php';
        }
        break;

    case 'cancel_booking':
        try {
            $user = authenticateFamily();
            $bookingId = filter_input(INPUT_GET, 'booking_id', FILTER_SANITIZE_NUMBER_INT);
            if (empty($bookingId)) {
                throw new Exception("ID lịch đặt không hợp lệ");
            }
            $bookingModel->cancelBookingByFamily($bookingId, $user['user_id']);
            $_SESSION['success'] = 'Hủy lịch đặt thành công!';
            header('Location: ?action=bookings');
            exit;
        } catch (Exception $e) {
            error_log("Lỗi khi hủy lịch đặt: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi hủy lịch đặt: " . $e->getMessage();
            header('Location: ?action=bookings');
            exit;
        }
        break;

    case 'feedback':
        error_log("Bắt đầu xử lý hành động feedback");
        try {
            // Kiểm tra kết nối cơ sở dữ liệu trước
            if (!$conn) {
                error_log("Lỗi: Kết nối cơ sở dữ liệu thất bại");
                throw new Exception("Kết nối cơ sở dữ liệu thất bại");
            }
            error_log("Kết nối cơ sở dữ liệu thành công");

            $user = authenticateFamily();
            error_log("Debug: User hiện tại: " . print_r($user, true));

            $bookingId = filter_input(INPUT_GET, 'booking_id', FILTER_SANITIZE_NUMBER_INT);
            error_log("Debug: Booking ID: " . $bookingId);
            if (empty($bookingId)) {
                throw new Exception("ID lịch đặt không hợp lệ");
            }

            $booking = $bookingModel->getBookingById($bookingId);
            error_log("Debug: Dữ liệu booking: " . print_r($booking, true));
            if (!$booking) {
                throw new Exception("Không tìm thấy lịch đặt với ID: $bookingId");
            }

            if ($booking['family_user_id'] !== $user['user_id']) {
                throw new Exception("Bạn không có quyền đánh giá lịch đặt này.");
            }

            if ($booking['status'] !== 'COMPLETED') {
                throw new Exception("Chỉ có thể đánh giá các lịch đặt đã hoàn thành.");
            }

            if ($booking['has_feedback']) {
                $_SESSION['error'] = "Bạn đã đánh giá lịch đặt này rồi.";
                header('Location: ?action=bookings');
                exit;
            }

            $_SESSION['user'] = $user;

            // Kiểm tra xem file feedback.php có tồn tại không
            $feedbackFilePath = __DIR__ . '/../views/feedback.php';
            if (!file_exists($feedbackFilePath)) {
                error_log("Lỗi: File feedback.php không tồn tại tại: $feedbackFilePath");
                die("Lỗi: File feedback.php không tồn tại tại: $feedbackFilePath");
            }
            error_log("File feedback.php tồn tại, đang bao gồm file");

            include $feedbackFilePath;
            error_log("Đã bao gồm file feedback.php thành công");
        } catch (Exception $e) {
            error_log("Lỗi khi hiển thị form đánh giá: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi tải form đánh giá: " . $e->getMessage();
            header('Location: ?action=bookings');
            exit;
        }
        break;

    case 'submit_feedback':
        try {
            $user = authenticateFamily();
            $bookingId = filter_input(INPUT_POST, 'booking_id', FILTER_SANITIZE_NUMBER_INT);
            $nurseId = filter_input(INPUT_POST, 'nurse_id', FILTER_SANITIZE_NUMBER_INT);
            $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

            if (empty($bookingId) || empty($nurseId) || empty($rating)) {
                throw new Exception("Thông tin đánh giá không đầy đủ.");
            }

            $booking = $bookingModel->getBookingById($bookingId);
            if (!$booking) {
                throw new Exception("Không tìm thấy lịch đặt với ID: $bookingId");
            }

            if ($booking['family_user_id'] !== $user['user_id']) {
                throw new Exception("Bạn không có quyền gửi đánh giá cho lịch đặt này.");
            }

            // Xử lý tệp đính kèm
            $attachmentPath = null;
            if (!empty($_FILES['attachment']['name'][0])) {
                $uploadDir = __DIR__ . '/../uploads/feedback/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                foreach ($_FILES['attachment']['tmp_name'] as $index => $tmpName) {
                    if ($_FILES['attachment']['error'][$index] === UPLOAD_ERR_OK) {
                        $fileName = time() . '_' . basename($_FILES['attachment']['name'][$index]);
                        $filePath = $uploadDir . $fileName;
                        if (move_uploaded_file($tmpName, $filePath)) {
                            $attachmentPath = $filePath;
                            break; // Chỉ lưu một tệp đính kèm
                        }
                    }
                }
            }

            // Lưu đánh giá vào cơ sở dữ liệu
            $feedbackData = [
                'booking_id' => $bookingId,
                'nurse_user_id' => $nurseId,
                'family_user_id' => $user['user_id'],
                'rating' => $rating,
                'comment' => $comment,
                'attachment' => $attachmentPath
            ];
            $feedbackModel->createFeedback($feedbackData);

            // Cập nhật trạng thái has_feedback của booking
            $stmt = $conn->prepare(
                "UPDATE bookings SET has_feedback = 1 WHERE booking_id = ?"
            );
            $stmt->bind_param("i", $bookingId);
            $stmt->execute();

            $_SESSION['success'] = 'Gửi đánh giá thành công!';
            header('Location: ?action=bookings');
            exit;
        } catch (Exception $e) {
            error_log("Lỗi khi gửi đánh giá: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi gửi đánh giá: " . $e->getMessage();
            header('Location: ?action=bookings');
            exit;
        }
        break;

    case 'nurse_review':
        error_log("Bắt đầu xử lý hành động nurse_review");
        try {
            $user = authenticateFamily();
            error_log("Debug: User hiện tại: " . print_r($user, true));

            $nurseUserId = filter_input(INPUT_GET, 'nurseUserId', FILTER_SANITIZE_NUMBER_INT);
            error_log("Debug: nurseUserId: " . $nurseUserId);
            if (empty($nurseUserId)) {
                throw new Exception("Thiếu thông tin y tá.");
            }

            $_SESSION['user'] = $user;

            // Kiểm tra xem file nurse_review.php có tồn tại không
            $nurseReviewFilePath = __DIR__ . '/../views/nurse_review.php';
            if (!file_exists($nurseReviewFilePath)) {
                error_log("Lỗi: File nurse_review.php không tồn tại tại: $nurseReviewFilePath");
                die("Lỗi: File nurse_review.php không tồn tại tại: $nurseReviewFilePath");
            }
            error_log("File nurse_review.php tồn tại, đang bao gồm file");

            include $nurseReviewFilePath;
            error_log("Đã bao gồm file nurse_review.php thành công");
        } catch (Exception $e) {
            error_log("Lỗi khi hiển thị trang đánh giá y tá: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi hiển thị trang đánh giá: " . $e->getMessage();
            header('Location: ?action=home');
            exit;
        }
        break;

    default:
        header('Location: ?action=home');
        break;
}
?>