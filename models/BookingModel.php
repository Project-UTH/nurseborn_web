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

    // Lấy thống kê thu nhập ngày hiện tại
    public function getTodayIncomeStats() {
        $today = date('Y-m-d');
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) as booking_count, SUM(price) as total_price
             FROM bookings 
             WHERE DATE(created_at) = ? AND status = 'COMPLETED'"
        );
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $bookingCount = $result['booking_count'] ?? 0;
        $totalPrice = $result['total_price'] ?? 0;

        // Giả định chiết khấu web là 10%
        $webIncome = $totalPrice * 0.1; // Thu nhập web
        $nurseIncome = $totalPrice; // Thu nhập y tá thuần
        $nurseAfterDiscount = $totalPrice * 0.9; // Thu nhập y tá sau chiết khấu

        return [
            'today_booking_count' => $bookingCount,
            'today_web_income' => $webIncome,
            'today_nurse_income' => $nurseIncome,
            'today_nurse_after_discount' => $nurseAfterDiscount
        ];
    }

    // Lấy thống kê thu nhập theo bộ lọc (tuần, tháng, năm)
    public function getIncomeStats($filterType = null, $filterValue = null) {
        $query = "SELECT COUNT(*) as booking_count, SUM(price) as total_price";
        $conditions = ["status = 'COMPLETED'"];
        $params = [];
        $types = "";

        if ($filterType && $filterValue) {
            if ($filterType === 'weekly') {
                // $filterValue có dạng "YYYY-WW" (ví dụ: "2023-W45")
                list($year, $week) = explode('-W', $filterValue);
                $conditions[] = "YEAR(booking_date) = ? AND WEEK(booking_date, 1) = ?";
                $params[] = $year;
                $params[] = $week;
                $types .= "ii";
            } elseif ($filterType === 'monthly') {
                // $filterValue có dạng "YYYY-MM" (ví dụ: "2023-10")
                list($year, $month) = explode('-', $filterValue);
                $conditions[] = "YEAR(booking_date) = ? AND MONTH(booking_date) = ?";
                $params[] = $year;
                $params[] = $month;
                $types .= "ii";
            } elseif ($filterType === 'yearly') {
                // $filterValue có dạng "YYYY" (ví dụ: "2023")
                $conditions[] = "YEAR(booking_date) = ?";
                $params[] = $filterValue;
                $types .= "i";
            }
        }

        $query .= " FROM bookings";
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $bookingCount = $result['booking_count'] ?? 0;
        $totalPrice = $result['total_price'] ?? 0;

        // Giả định chiết khấu web là 10%
        $webIncome = $totalPrice * 0.1;
        $nurseIncome = $totalPrice;
        $nurseAfterDiscount = $totalPrice * 0.9;

        return [
            'booking_count' => $bookingCount,
            'web_income' => $webIncome,
            'nurse_income' => $nurseIncome,
            'nurse_after_discount' => $nurseAfterDiscount
        ];
    }

    // Lấy dữ liệu biểu đồ thu nhập
    public function getChartData($filterType = null, $filterValue = null) {
        $labels = [];
        $data = [];
        $conditions = ["status = 'COMPLETED'"];
        $params = [];
        $types = "";

        if ($filterType && $filterValue) {
            if ($filterType === 'weekly') {
                // Hiển thị thu nhập theo ngày trong tuần được chọn
                list($year, $week) = explode('-W', $filterValue);
                $conditions[] = "YEAR(booking_date) = ? AND WEEK(booking_date, 1) = ?";
                $params[] = $year;
                $params[] = $week;
                $types .= "ii";

                // Tạo nhãn cho 7 ngày trong tuần
                $startDate = new DateTime();
                $startDate->setISODate($year, $week);
                for ($i = 0; $i < 7; $i++) {
                    $labels[] = $startDate->format('Y-m-d');
                    $startDate->modify('+1 day');
                }
            } elseif ($filterType === 'monthly') {
                // Hiển thị thu nhập theo ngày trong tháng được chọn
                list($year, $month) = explode('-', $filterValue);
                $conditions[] = "YEAR(booking_date) = ? AND MONTH(booking_date) = ?";
                $params[] = $year;
                $params[] = $month;
                $types .= "ii";

                // Tạo nhãn cho các ngày trong tháng
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $labels[] = sprintf("%d-%02d-%02d", $year, $month, $day);
                }
            } elseif ($filterType === 'yearly') {
                // Hiển thị thu nhập theo tháng trong năm được chọn
                $conditions[] = "YEAR(booking_date) = ?";
                $params[] = $filterValue;
                $types .= "i";

                // Tạo nhãn cho 12 tháng
                for ($month = 1; $month <= 12; $month++) {
                    $labels[] = sprintf("%d-%02d", $filterValue, $month);
                }
            }
        } else {
            // Mặc định: hiển thị thu nhập 30 ngày gần nhất
            $endDate = new DateTime();
            $startDate = (clone $endDate)->modify('-29 days');
            for ($date = clone $startDate; $date <= $endDate; $date->modify('+1 day')) {
                $labels[] = $date->format('Y-m-d');
            }
        }

        // Lấy dữ liệu thu nhập
        $query = "SELECT DATE(booking_date) as date, SUM(price) as total_price 
                  FROM bookings 
                  WHERE " . implode(" AND ", $conditions) . " 
                  GROUP BY DATE(booking_date)";
        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Tạo mảng dữ liệu cho biểu đồ
        $incomeData = array_fill(0, count($labels), 0);
        foreach ($result as $row) {
            $dateKey = $filterType === 'yearly' ? substr($row['date'], 0, 7) : $row['date'];
            $index = array_search($dateKey, $labels);
            if ($index !== false) {
                $incomeData[$index] = (float)$row['total_price'];
            }
        }

        return [
            'labels' => $labels,
            'data' => $incomeData
        ];
    }

    // Lấy thống kê thu nhập của y tá theo bộ lọc (DAY, WEEK, MONTH)
    public function getNurseIncomeStats($nurseUserId, $period = 'DAY', $specificDate = null) {
        $query = "SELECT COUNT(*) as booking_count, SUM(price) as total_price";
        $conditions = ["nurse_user_id = ?", "status = 'COMPLETED'"];
        $params = [$nurseUserId];
        $types = "i";

        if ($period && $specificDate) {
            if ($period === 'DAY') {
                // $specificDate có dạng "YYYY-MM-DD"
                $conditions[] = "DATE(booking_date) = ?";
                $params[] = $specificDate;
                $types .= "s";
            } elseif ($period === 'WEEK') {
                // $specificDate có dạng "YYYY-WW"
                list($year, $week) = explode('-W', $specificDate);
                $conditions[] = "YEAR(booking_date) = ? AND WEEK(booking_date, 1) = ?";
                $params[] = $year;
                $params[] = $week;
                $types .= "ii";
            } elseif ($period === 'MONTH') {
                // $specificDate có dạng "YYYY-MM"
                list($year, $month) = explode('-', $specificDate);
                $conditions[] = "YEAR(booking_date) = ? AND MONTH(booking_date) = ?";
                $params[] = $year;
                $params[] = $month;
                $types .= "ii";
            }
        } else {
            // Mặc định: lấy thu nhập ngày hiện tại
            $today = date('Y-m-d');
            $conditions[] = "DATE(booking_date) = ?";
            $params[] = $today;
            $types .= "s";
        }

        $query .= " FROM bookings";
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $bookingCount = $result['booking_count'] ?? 0;
        $totalPrice = $result['total_price'] ?? 0;

        // Giả định chiết khấu nền tảng là 10%
        $platformFee = $totalPrice * 0.1;
        $netIncome = $totalPrice;
        $netIncomeAfterFee = $totalPrice * 0.9;

        return [
            'booking_count' => $bookingCount,
            'platform_fee' => $platformFee,
            'total_income' => $netIncome,
            'net_income_after_fee' => $netIncomeAfterFee
        ];
    }

    // Lấy dữ liệu biểu đồ thu nhập của y tá
    public function getNurseChartData($nurseUserId, $period = 'DAY', $specificDate = null) {
        $labels = [];
        $data = [];
        $conditions = ["nurse_user_id = ?", "status = 'COMPLETED'"];
        $params = [$nurseUserId];
        $types = "i";

        if ($period && $specificDate) {
            if ($period === 'DAY') {
                // Hiển thị thu nhập theo giờ trong ngày được chọn
                $conditions[] = "DATE(booking_date) = ?";
                $params[] = $specificDate;
                $types .= "s";

                // Tạo nhãn cho 24 giờ trong ngày
                for ($hour = 0; $hour < 24; $hour++) {
                    $labels[] = sprintf("%02d:00", $hour);
                }
            } elseif ($period === 'WEEK') {
                // Hiển thị thu nhập theo ngày trong tuần được chọn
                list($year, $week) = explode('-W', $specificDate);
                $conditions[] = "YEAR(booking_date) = ? AND WEEK(booking_date, 1) = ?";
                $params[] = $year;
                $params[] = $week;
                $types .= "ii";

                // Tạo nhãn cho 7 ngày trong tuần
                $startDate = new DateTime();
                $startDate->setISODate($year, $week);
                for ($i = 0; $i < 7; $i++) {
                    $labels[] = $startDate->format('Y-m-d');
                    $startDate->modify('+1 day');
                }
            } elseif ($period === 'MONTH') {
                // Hiển thị thu nhập theo ngày trong tháng được chọn
                list($year, $month) = explode('-', $specificDate);
                $conditions[] = "YEAR(booking_date) = ? AND MONTH(booking_date) = ?";
                $params[] = $year;
                $params[] = $month;
                $types .= "ii";

                // Tạo nhãn cho các ngày trong tháng
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $labels[] = sprintf("%d-%02d-%02d", $year, $month, $day);
                }
            }
        } else {
            // Mặc định: hiển thị thu nhập theo giờ trong ngày hiện tại
            $today = date('Y-m-d');
            $conditions[] = "DATE(booking_date) = ?";
            $params[] = $today;
            $types .= "s";

            for ($hour = 0; $hour < 24; $hour++) {
                $labels[] = sprintf("%02d:00", $hour);
            }
        }

        // Lấy dữ liệu thu nhập
        $query = "SELECT DATE(booking_date) as date, HOUR(start_time) as hour, SUM(price) as total_price 
                  FROM bookings 
                  WHERE " . implode(" AND ", $conditions) . " 
                  GROUP BY DATE(booking_date), HOUR(start_time)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Tạo mảng dữ liệu cho biểu đồ
        $incomeData = array_fill(0, count($labels), 0);
        foreach ($result as $row) {
            if ($period === 'DAY') {
                $hourKey = sprintf("%02d:00", $row['hour']);
                $index = array_search($hourKey, $labels);
            } else {
                $dateKey = $row['date'];
                $index = array_search($dateKey, $labels);
            }
            if ($index !== false) {
                $incomeData[$index] = (float)$row['total_price'];
            }
        }

        return [
            'labels' => $labels,
            'data' => $incomeData
        ];
    }

    // Lấy danh sách xếp hạng thu nhập của y tá (từ cao xuống thấp)
    public function getNurseRanking() {
        $query = "
            SELECT 
                b.nurse_user_id,
                u.full_name,
                u.email,
                u.phone_number,
                COUNT(b.booking_id) as booking_count,
                SUM(b.price) as total_income
            FROM bookings b
            JOIN users u ON b.nurse_user_id = u.user_id
            WHERE b.status = 'COMPLETED'
            GROUP BY b.nurse_user_id, u.full_name, u.email, u.phone_number
            ORDER BY total_income DESC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Thêm thứ hạng (rank) cho từng y tá
        $ranking = [];
        foreach ($result as $index => $nurse) {
            $nurse['rank'] = $index + 1;
            $ranking[] = $nurse;
        }

        return $ranking;
    }

    // Lấy thông tin lịch đặt theo booking_id
    public function getBookingById($bookingId) {
        $stmt = $this->conn->prepare(
            "SELECT b.*, u.full_name AS nurse_full_name 
             FROM bookings b 
             LEFT JOIN users u ON b.nurse_user_id = u.user_id 
             WHERE b.booking_id = ?"
        );

        if (!$stmt) {
            error_log("Prepare failed in getBookingById: " . $this->conn->error);
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $bookingId);

        if (!$stmt->execute()) {
            error_log("Execute failed in getBookingById: " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();

        $stmt->close();

        return $booking;
    }

    // Lấy tất cả lịch đặt
    public function getAllBookings() {
        $stmt = $this->conn->prepare(
            "SELECT b.*, 
                    u1.full_name AS nurse_full_name, 
                    u2.full_name AS family_full_name 
             FROM bookings b 
             JOIN users u1 ON b.nurse_user_id = u1.user_id 
             JOIN users u2 ON b.family_user_id = u2.user_id 
             ORDER BY b.booking_date DESC"
        );

        if (!$stmt) {
            error_log("Prepare failed in getAllBookings: " . $this->conn->error);
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        if (!$stmt->execute()) {
            error_log("Execute failed in getAllBookings: " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $bookings = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $bookings;
    }

    // Lấy tất cả lịch đặt trong một ngày cụ thể
    public function getBookingsByDate($date) {
        $stmt = $this->conn->prepare(
            "SELECT b.*, 
                    u1.full_name AS nurse_full_name, 
                    u2.full_name AS family_full_name 
             FROM bookings b 
             JOIN users u1 ON b.nurse_user_id = u1.user_id 
             JOIN users u2 ON b.family_user_id = u2.user_id 
             WHERE b.booking_date = ? 
             ORDER BY b.start_time ASC"
        );

        if (!$stmt) {
            error_log("Prepare failed in getBookingsByDate: " . $this->conn->error);
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("s", $date);

        if (!$stmt->execute()) {
            error_log("Execute failed in getBookingsByDate: " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $bookings = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $bookings;
    }

    // Xóa lịch đặt
    public function deleteBooking($bookingId) {
        // Xóa các thông báo liên quan đến lịch đặt trước
        $stmt = $this->conn->prepare("DELETE FROM notifications WHERE booking_id = ?");
        if (!$stmt) {
            error_log("Prepare failed in deleteBooking (notifications): " . $this->conn->error);
            throw new Exception("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("i", $bookingId);
        if (!$stmt->execute()) {
            error_log("Execute failed in deleteBooking (notifications): " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();

        // Sau đó xóa lịch đặt
        $stmt = $this->conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
        if (!$stmt) {
            error_log("Prepare failed in deleteBooking (bookings): " . $this->conn->error);
            throw new Exception("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("i", $bookingId);
        if (!$stmt->execute()) {
            error_log("Execute failed in deleteBooking (bookings): " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();
    }
}
?>

