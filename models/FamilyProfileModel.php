<?php
class FamilyProfileModel {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Tạo hồ sơ gia đình mới
    public function createFamilyProfile($userId, $childName, $childAge, $preferredLocation, $specificNeeds) {
        // Kiểm tra user tồn tại và có role FAMILY
        $stmt = $this->conn->prepare("SELECT role FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if (!$user || $user['role'] !== 'FAMILY') {
            throw new Exception("User không tồn tại hoặc không phải gia đình");
        }

        // Kiểm tra hồ sơ đã tồn tại
        $stmt = $this->conn->prepare("SELECT family_profile_id FROM family_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            throw new Exception("Hồ sơ gia đình đã tồn tại");
        }

        // Tạo hồ sơ gia đình
        $stmt = $this->conn->prepare(
            "INSERT INTO family_profiles (child_name, child_age, preferred_location, specific_needs, updated_at, user_id) 
             VALUES (?, ?, ?, ?, NOW(), ?)"
        );
        $stmt->bind_param("ssssi", $childName, $childAge, $preferredLocation, $specificNeeds, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi tạo hồ sơ gia đình");
        }

        return $this->getFamilyProfileByUserId($userId);
    }

    // Cập nhật hồ sơ gia đình
    public function updateFamilyProfile($userId, $childName, $childAge, $preferredLocation, $specificNeeds) {
        // Kiểm tra hồ sơ tồn tại
        $stmt = $this->conn->prepare("SELECT family_profile_id FROM family_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) {
            throw new Exception("Không tìm thấy hồ sơ gia đình");
        }

        // Cập nhật hồ sơ
        $stmt = $this->conn->prepare(
            "UPDATE family_profiles SET child_name = ?, child_age = ?, preferred_location = ?, specific_needs = ?, updated_at = NOW() WHERE user_id = ?"
        );
        $stmt->bind_param("ssssi", $childName, $childAge, $preferredLocation, $specificNeeds, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi cập nhật hồ sơ gia đình");
        }

        return $this->getFamilyProfileByUserId($userId);
    }

    // Lấy hồ sơ gia đình theo user_id
    public function getFamilyProfileByUserId($userId) {
        $stmt = $this->conn->prepare(
            "SELECT fp.*, u.full_name 
             FROM family_profiles fp 
             JOIN users u ON fp.user_id = u.user_id 
             WHERE fp.user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        if (!$profile) {
            return ['user_id' => $userId]; // Trả về DTO rỗng
        }

        return $profile;
    }

    // Xóa hồ sơ gia đình
    public function deleteFamilyProfile($userId) {
        $stmt = $this->conn->prepare("SELECT family_profile_id FROM family_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) {
            throw new Exception("Không tìm thấy hồ sơ gia đình");
        }

        $stmt = $this->conn->prepare("DELETE FROM family_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi xóa hồ sơ gia đình");
        }
    }
}
?>