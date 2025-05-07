<?php
require_once __DIR__ . '/NurseProfileModel.php';
require_once __DIR__ . '/UserModel.php';

class NurseAvailabilityModel {
    private $conn;
    private $nurseProfileModel;
    private $userModel;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
        $this->nurseProfileModel = new NurseProfileModel($dbConnection);
        $this->userModel = new UserModel($dbConnection);
    }

    // Lấy danh sách lịch làm việc theo nurse_profile_id
    public function findByNurseProfileNurseProfileId($nurseProfileId) {
        $stmt = $this->conn->prepare(
            "SELECT day_of_week FROM nurse_availabilities WHERE nurse_profile_id = ?"
        );
        $stmt->bind_param("i", $nurseProfileId);
        $stmt->execute();
        $result = $stmt->get_result();
        $days = [];
        while ($row = $result->fetch_assoc()) {
            $days[] = $row['day_of_week'];
        }
        return $days;
    }

    // Xóa lịch làm việc theo nurse_profile_id
    public function deleteByNurseProfileNurseProfileId($nurseProfileId) {
        $stmt = $this->conn->prepare(
            "DELETE FROM nurse_availabilities WHERE nurse_profile_id = ?"
        );
        $stmt->bind_param("i", $nurseProfileId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi xóa lịch làm việc: " . $stmt->error);
        }
    }

    // Lấy danh sách lịch làm việc theo user_id
    public function findByNurseProfileUserUserId($userId) {
        $nurseProfile = $this->nurseProfileModel->getNurseProfileByUserId($userId);
        if (!$nurseProfile) {
            throw new Exception("Không tìm thấy NurseProfile cho user với ID: $userId");
        }
        return $this->findByNurseProfileNurseProfileId($nurseProfile['nurse_profile_id']);
    }

    // Tạo hoặc cập nhật lịch làm việc
    public function createOrUpdateAvailability($userId, $selectedDays) {
        $nurseProfile = $this->nurseProfileModel->getNurseProfileByUserId($userId);
        if (!$nurseProfile) {
            error_log("Không tìm thấy NurseProfile cho userId: $userId");
            throw new Exception("Không tìm thấy NurseProfile cho user với ID: $userId");
        }

        $user = $this->userModel->getUserById($userId);
        if ($user['role'] !== 'NURSE') {
            error_log("User với userId: $userId không có role 'NURSE', role hiện tại: {$user['role']}");
            throw new Exception("User phải có role 'NURSE' để tạo lịch làm việc");
        }

        // Xóa lịch làm việc cũ
        $this->deleteByNurseProfileNurseProfileId($nurseProfile['nurse_profile_id']);

        // Tạo lịch làm việc mới
        $validDays = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
        if (!empty($selectedDays)) {
            foreach ($selectedDays as $day) {
                // Kiểm tra ngày hợp lệ
                if (!in_array($day, $validDays)) {
                    error_log("Ngày làm việc không hợp lệ: " . htmlspecialchars($day));
                    throw new Exception("Ngày làm việc không hợp lệ: " . htmlspecialchars($day));
                }
                $stmt = $this->conn->prepare(
                    "INSERT INTO nurse_availabilities (nurse_profile_id, day_of_week) VALUES (?, ?)"
                );
                $stmt->bind_param("is", $nurseProfile['nurse_profile_id'], $day);
                if (!$stmt->execute()) {
                    error_log("Lỗi khi thêm ngày làm việc: " . $stmt->error);
                    throw new Exception("Lỗi khi thêm ngày làm việc: " . $stmt->error);
                }
            }
        }
    }

    // Lấy lịch làm việc theo user_id (trả về dạng DTO)
    public function getAvailabilityByUserId($userId) {
        $nurseProfile = $this->nurseProfileModel->getNurseProfileByUserId($userId);
        if (!$nurseProfile) {
            error_log("Không tìm thấy NurseProfile cho userId: $userId");
            throw new Exception("Không tìm thấy NurseProfile cho user với ID: $userId");
        }

        $selectedDays = $this->findByNurseProfileNurseProfileId($nurseProfile['nurse_profile_id']);
        return [
            'user_id' => $userId,
            'selected_days' => $selectedDays
        ];
    }
}
?>