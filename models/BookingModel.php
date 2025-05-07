<?php
require_once __DIR__ . '/UserModel.php';
require_once __DIR__ . '/NurseProfileModel.php';
require_once __DIR__ . '/NurseAvailabilityModel.php';
require_once __DIR__ . '/NotificationModel.php';

class BookingModel {
    private $conn;
    private $userModel;
    private $nurseProfileModel;
    private $nurseAvailabilityModel;
    private $notificationModel;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
        $this->userModel = new UserModel($dbConnection);
        $this->nurseProfileModel = new NurseProfileModel($dbConnection);
        $this->nurseAvailabilityModel = new NurseAvailabilityModel($dbConnection);
        $this->notificationModel = new NotificationModel($dbConnection);
    }

    // Đồng bộ dữ liệu từ bookings sang nurse_incomes (chỉ cho trạng thái COMPLETED)
    public function syncBookingsToNurseIncomes() {
        $stmt = $this->conn->prepare(
            "SELECT * FROM bookings WHERE status = 'COMPLETED'"
        );
        $stmt->execute();
        $completedBookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($completedBookings as $booking) {
            $stmt = $this->conn->prepare(
                "SELECT * FROM nurse_incomes 
                 WHERE nurse_user_id = ? AND booking_date = ? AND price = ? 
                 AND service_type = ? AND status = 'COMPLETED'"
            );
            $stmt->bind_param(
                "isds",
                $booking['nurse_user_id'],
                $booking['booking_date'],
                $booking['price'],
                $booking['service_type']
            );
            $stmt->execute();
            $existingIncome = $stmt->get_result()->fetch_assoc();

            if (!$existingIncome) {
                $stmt = $this->conn->prepare(
                    "INSERT INTO nurse_incomes 
                     (nurse_user_id, booking_date, price, service_type, status) 
                     VALUES (?, ?, ?, ?, 'COMPLETED')"
                );
                $stmt->bind_param(
                    "isds",
                    $booking['nurse_user_id'],
                    $booking['booking_date'],
                    $booking['price'],
                    $booking['service_type']
                );
                if (!$stmt->execute()) {
                    error_log("Lỗi khi tạo NurseIncome cho booking_id: {$booking['booking_id']}: " . $stmt->error);
                }
            }
        }
    }

    // Tạo lịch đặt mới
    public function createBooking($bookingData, $familyUserId) {
        // Validation
        if (empty($bookingData['nurse_user_id'])) {
            throw new Exception("NurseUserId không được để trống");
        }
        if (empty($bookingData['booking_date'])) {
            throw new Exception("Ngày đặt lịch không được để trống");
        }
        if (empty($bookingData['service_type'])) {
            throw new Exception("Loại dịch vụ không được để trống");
        }
        if ($bookingData['service_type'] === 'HOURLY') {
            if (empty($bookingData['start_time']) || empty($bookingData['end_time'])) {
                throw new Exception("Giờ bắt đầu và giờ kết thúc không được để trống khi chọn dịch vụ theo giờ");
            }
            if (strtotime($bookingData['end_time']) <= strtotime($bookingData['start_time'])) {
                throw new Exception("Giờ kết thúc phải lớn hơn giờ bắt đầu");
            }
        } else {
            $bookingData['start_time'] = null;
            $bookingData['end_time'] = null;
        }
        if (empty($bookingData['price'])) {
            throw new Exception("Giá không được để trống");
        }

        // Lấy thông tin khách hàng
        $familyUser = $this->userModel->getUserById($familyUserId);
        if (!$familyUser) {
            throw new Exception("Không tìm thấy khách hàng với ID: $familyUserId");
        }
        if ($familyUser['role'] !== 'FAMILY') {
            throw new Exception("Người dùng phải có vai trò FAMILY");
        }

        // Lấy thông tin y tá
        $nurseUser = $this->userModel->getUserById($bookingData['nurse_user_id']);
        if (!$nurseUser) {
            throw new Exception("Không tìm thấy y tá với ID: {$bookingData['nurse_user_id']}");
        }
        if ($nurseUser['role'] !== 'NURSE') {
            throw new Exception("Người dùng phải có vai trò NURSE");
        }

        // Kiểm tra NurseProfile đã được phê duyệt
        $nurseProfile = $this->nurseProfileModel->getNurseProfileByUserId($nurseUser['user_id']);
        if (!$nurseProfile) {
            throw new Exception("Không tìm thấy NurseProfile cho y tá với ID: {$nurseUser['user_id']}");
        }
        if (!$nurseProfile['is_approved']) {
            throw new Exception("Y tá chưa được phê duyệt");
        }

        // Kiểm tra lịch làm việc của y tá
        $bookingDate = new DateTime($bookingData['booking_date']);
        $dayOfWeek = [
            'Sunday' => 'Chủ Nhật',
            'Monday' => 'Thứ Hai',
            'Tuesday' => 'Thứ Ba',
            'Wednesday' => 'Thứ Tư',
            'Thursday' => 'Thứ Năm',
            'Friday' => 'Thứ Sáu',
            'Saturday' => 'Thứ Bảy'
        ][$bookingDate->format('l')];
        $availabilities = $this->nurseAvailabilityModel->findByNurseProfileNurseProfileId($nurseProfile['nurse_profile_id']);
        if (!in_array($dayOfWeek, $availabilities)) {
            $availableDays = implode(", ", $availabilities);
            throw new Exception("Y tá không làm việc vào ngày đã chọn. Các ngày làm việc: $availableDays");
        }

        // Kiểm tra xung đột lịch
        $stmt = $this->conn->prepare(
            "SELECT * FROM bookings 
             WHERE nurse_user_id = ? AND booking_date = ? AND status != 'CANCELLED'"
        );
        $stmt->bind_param("is", $nurseUser['user_id'], $bookingData['booking_date']);
        $stmt->execute();
        $existingBookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($existingBookings as $existingBooking) {
            if (in_array($bookingData['service_type'], ['DAILY', 'WEEKLY'])) {
                throw new Exception("Xung đột lịch cho y tá vào ngày {$bookingData['booking_date']}");
            } elseif ($bookingData['service_type'] === 'HOURLY') {
                if (in_array($existingBooking['service_type'], ['DAILY', 'WEEKLY'])) {
                    throw new Exception("Xung đột lịch cho y tá vào ngày {$bookingData['booking_date']} (đã có lịch cả ngày)");
                }
                if ($existingBooking['start_time'] && $existingBooking['end_time']) {
                    $startTime = strtotime($bookingData['start_time']);
                    $endTime = strtotime($bookingData['end_time']);
                    $existingStart = strtotime($existingBooking['start_time']);
                    $existingEnd = strtotime($existingBooking['end_time']);
                    if (!($endTime <= $existingStart || $startTime >= $existingEnd)) {
                        throw new Exception("Xung đột lịch cho y tá vào khung giờ này");
                    }
                }
            }
        }

        // Tạo Booking
        $stmt = $this->conn->prepare(
            "INSERT INTO bookings 
             (family_user_id, nurse_user_id, service_type, booking_date, start_time, end_time, 
              price, notes, status, created_at, has_feedback) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', NOW(), 0)"
        );
        $stmt->bind_param(
            "iissssds",
            $familyUser['user_id'],
            $nurseUser['user_id'],
            $bookingData['service_type'],
            $bookingData['booking_date'],
            $bookingData['start_time'],
            $bookingData['end_time'],
            $bookingData['price'],
            $bookingData['notes']
        );
        if (!$stmt->execute()) {
            error_log("Lỗi khi tạo lịch đặt: " . $stmt->error);
            throw new Exception("Lỗi khi tạo lịch đặt: " . $stmt->error);
        }

        $bookingId = $this->conn->insert_id;

        // Tạo thông báo cho y tá
        try {
            $message = sprintf(
                "Bạn có một lịch đặt mới từ khách hàng %s vào ngày %s.",
                $familyUser['full_name'],
                $bookingData['booking_date']
            );
            $this->notificationModel->createNotification($nurseUser['user_id'], $message, $bookingId);
        } catch (Exception $e) {
            error_log("Lỗi khi tạo thông báo cho booking_id: $bookingId: " . $e->getMessage());
        }

        return ['booking_id' => $bookingId];
    }

    // Lấy danh sách lịch đặt theo nurse_user_id và status
    public function getBookingsByNurseUserIdAndStatus($nurseUserId, $status) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM bookings WHERE nurse_user_id = ? AND status = ?"
        );
        $stmt->bind_param("is", $nurseUserId, $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Tìm kiếm lịch đặt theo nurse_user_id và status
    public function findByNurseUserUserIdAndStatus($nurseUserId, $status) {
        return $this->getBookingsByNurseUserIdAndStatus($nurseUserId, $status);
    }

    // Chấp nhận lịch đặt
    public function acceptBooking($bookingId, $nurseUserId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM bookings WHERE booking_id = ? AND nurse_user_id = ? AND status = 'PENDING'"
        );
        $stmt->bind_param("ii", $bookingId, $nurseUserId);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        if (!$booking) {
            throw new Exception("Lịch đặt không tồn tại hoặc không ở trạng thái PENDING");
        }

        $stmt = $this->conn->prepare(
            "UPDATE bookings SET status = 'ACCEPTED' WHERE booking_id = ?"
        );
        $stmt->bind_param("i", $bookingId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi chấp nhận lịch đặt: " . $stmt->error);
        }

        // Tạo thông báo cho khách hàng
        try {
            $familyUser = $this->userModel->getUserById($booking['family_user_id']);
            $nurseUser = $this->userModel->getUserById($nurseUserId);
            $message = sprintf(
                "Lịch đặt của bạn vào ngày %s đã được y tá %s xác nhận.",
                $booking['booking_date'],
                $nurseUser['full_name']
            );
            $this->notificationModel->createNotification($familyUser['user_id'], $message, $bookingId);
        } catch (Exception $e) {
            error_log("Lỗi khi tạo thông báo cho booking_id: $bookingId: " . $e->getMessage());
        }
    }

    // Hủy lịch đặt từ phía NURSE
    public function cancelBooking($bookingId, $nurseUserId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM bookings WHERE booking_id = ? AND nurse_user_id = ? AND status = 'PENDING'"
        );
        $stmt->bind_param("ii", $bookingId, $nurseUserId);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        if (!$booking) {
            throw new Exception("Lịch đặt không tồn tại hoặc không ở trạng thái PENDING");
        }

        $stmt = $this->conn->prepare(
            "UPDATE bookings SET status = 'CANCELLED' WHERE booking_id = ?"
        );
        $stmt->bind_param("i", $bookingId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi hủy lịch đặt: " . $stmt->error);
        }

        // Tạo thông báo cho khách hàng
        try {
            $familyUser = $this->userModel->getUserById($booking['family_user_id']);
            $nurseUser = $this->userModel->getUserById($nurseUserId);
            $message = sprintf(
                "Lịch đặt của bạn vào ngày %s đã bị y tá %s hủy.",
                $booking['booking_date'],
                $nurseUser['full_name']
            );
            $this->notificationModel->createNotification($familyUser['user_id'], $message, $bookingId);
        } catch (Exception $e) {
            error_log("Lỗi khi tạo thông báo cho booking_id: $bookingId: " . $e->getMessage());
        }
    }

    // Hủy lịch đặt từ phía FAMILY
    public function cancelBookingByFamily($bookingId, $familyUserId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM bookings WHERE booking_id = ? AND family_user_id = ? AND status = 'PENDING'"
        );
        $stmt->bind_param("ii", $bookingId, $familyUserId);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        if (!$booking) {
            throw new Exception("Lịch đặt không tồn tại hoặc không ở trạng thái PENDING");
        }

        $stmt = $this->conn->prepare(
            "UPDATE bookings SET status = 'CANCELLED' WHERE booking_id = ?"
        );
        $stmt->bind_param("i", $bookingId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi hủy lịch đặt: " . $stmt->error);
        }

        // Tạo thông báo cho y tá
        try {
            $familyUser = $this->userModel->getUserById($familyUserId);
            $nurseUser = $this->userModel->getUserById($booking['nurse_user_id']);
            $message = sprintf(
                "Lịch đặt vào ngày %s đã bị khách hàng %s hủy.",
                $booking['booking_date'],
                $familyUser['full_name']
            );
            $this->notificationModel->createNotification($nurseUser['user_id'], $message, $bookingId);
        } catch (Exception $e) {
            error_log("Lỗi khi tạo thông báo cho booking_id: $bookingId: " . $e->getMessage());
        }
    }

    // Hoàn thành lịch đặt và tạo bản ghi NurseIncome
    public function completeBooking($bookingId, $nurseUserId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM bookings WHERE booking_id = ? AND nurse_user_id = ? AND status = 'ACCEPTED'"
        );
        $stmt->bind_param("ii", $bookingId, $nurseUserId);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        if (!$booking) {
            throw new Exception("Lịch đặt không tồn tại hoặc không ở trạng thái ACCEPTED");
        }

        // Cập nhật trạng thái thành COMPLETED
        $stmt = $this->conn->prepare(
            "UPDATE bookings SET status = 'COMPLETED' WHERE booking_id = ?"
        );
        $stmt->bind_param("i", $bookingId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi hoàn thành lịch đặt: " . $stmt->error);
        }

        // Tạo bản ghi NurseIncome
        $stmt = $this->conn->prepare(
            "SELECT * FROM nurse_incomes 
             WHERE nurse_user_id = ? AND booking_date = ? AND price = ? 
             AND service_type = ? AND status = 'COMPLETED'"
        );
        $stmt->bind_param(
            "isds",
            $booking['nurse_user_id'],
            $booking['booking_date'],
            $booking['price'],
            $booking['service_type']
        );
        $stmt->execute();
        $existingIncome = $stmt->get_result()->fetch_assoc();

        if (!$existingIncome) {
            $stmt = $this->conn->prepare(
                "INSERT INTO nurse_incomes 
                 (nurse_user_id, booking_date, price, service_type, status) 
                 VALUES (?, ?, ?, ?, 'COMPLETED')"
            );
            $stmt->bind_param(
                "isds",
                $booking['nurse_user_id'],
                $booking['booking_date'],
                $booking['price'],
                $booking['service_type']
            );
            if (!$stmt->execute()) {
                error_log("Lỗi khi tạo NurseIncome cho booking_id: $bookingId: " . $stmt->error);
            }
        }

        // Tạo thông báo cho khách hàng
        try {
            $familyUser = $this->userModel->getUserById($booking['family_user_id']);
            $nurseUser = $this->userModel->getUserById($nurseUserId);
            $message = sprintf(
                "Lịch đặt của bạn vào ngày %s đã được y tá %s hoàn thành.",
                $booking['booking_date'],
                $nurseUser['full_name']
            );
            $this->notificationModel->createNotification($familyUser['user_id'], $message, $bookingId);
        } catch (Exception $e) {
            error_log("Lỗi khi tạo thông báo cho booking_id: $bookingId: " . $e->getMessage());
        }
    }

    // Lấy danh sách lịch đặt COMPLETED của FAMILY
    public function getCompletedBookingsForFamily($familyUserId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM bookings WHERE family_user_id = ? AND status = 'COMPLETED'"
        );
        $stmt->bind_param("i", $familyUserId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy tất cả lịch đặt của FAMILY
    public function getBookingsByFamilyUser($familyUserId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM bookings WHERE family_user_id = ?"
        );
        $stmt->bind_param("i", $familyUserId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>