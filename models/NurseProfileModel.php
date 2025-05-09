<?php
require_once 'CertificateModel.php';

class NurseProfileModel {
    private $conn;  
    private $certificateModel;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
        $this->certificateModel = new CertificateModel($dbConnection);
    }

    // Tạo hồ sơ y tá mới
    public function createNurseProfile($userId, $bio, $dailyRate, $experienceYears, $hourlyRate, $location, $profileImage, $skills, $weeklyRate, $certificates = []) {
        // Kiểm tra user tồn tại và có role NURSE
        $stmt = $this->conn->prepare("SELECT role FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if (!$user || $user['role'] !== 'NURSE') {
            throw new Exception("User không tồn tại hoặc không phải y tá");
        }

        // Kiểm tra hồ sơ đã tồn tại
        $stmt = $this->conn->prepare("SELECT nurse_profile_id FROM nurse_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            throw new Exception("Hồ sơ y tá đã tồn tại");
        }

        // Tạo hồ sơ y tá
        $stmt = $this->conn->prepare(
            "INSERT INTO nurse_profiles (bio, daily_rate, experience_years, hourly_rate, is_approved, location, profile_image, skills, updated_at, weekly_rate, user_id) 
             VALUES (?, ?, ?, ?, 0, ?, ?, ?, NOW(), ?, ?)"
        );
        $stmt->bind_param("sddisssdi", $bio, $dailyRate, $experienceYears, $hourlyRate, $location, $profileImage, $skills, $weeklyRate, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi tạo hồ sơ y tá: " . $stmt->error);
        }

        // Lấy ID hồ sơ vừa tạo
        $nurseProfileId = $this->conn->insert_id;

        // Lưu chứng chỉ (nếu có) bằng CertificateModel
        foreach ($certificates as $certificate) {
            $this->certificateModel->createCertificate($nurseProfileId, $certificate['name'], $certificate['file_path']);
        }

        return $this->getNurseProfileByUserId($userId);
    }

    // Cập nhật hồ sơ y tá
    public function updateNurseProfile($userId, $bio, $dailyRate, $experienceYears, $hourlyRate, $location, $profileImage, $skills, $weeklyRate, $certificates = []) {
        // Kiểm tra hồ sơ tồn tại
        $stmt = $this->conn->prepare("SELECT * FROM nurse_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        if (!$profile) {
            throw new Exception("Không tìm thấy hồ sơ y tá");
        }

        // Cập nhật hồ sơ
        $stmt = $this->conn->prepare(
            "UPDATE nurse_profiles SET bio = ?, daily_rate = ?, experience_years = ?, hourly_rate = ?, location = ?, profile_image = ?, skills = ?, weekly_rate = ?, updated_at = NOW() WHERE user_id = ?"
        );
        $stmt->bind_param("sddisssdi", $bio, $dailyRate, $experienceYears, $hourlyRate, $location, $profileImage, $skills, $weeklyRate, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi cập nhật hồ sơ y tá: " . $stmt->error);
        }

        // Xóa chứng chỉ cũ bằng CertificateModel
        $this->certificateModel->deleteCertificatesByNurseProfileId($profile['nurse_profile_id']);

        // Lưu chứng chỉ mới
        foreach ($certificates as $certificate) {
            $this->certificateModel->createCertificate($profile['nurse_profile_id'], $certificate['name'], $certificate['file_path']);
        }

        return $this->getNurseProfileByUserId($userId);
    }

    // Lấy hồ sơ y tá theo user_id
    public function getNurseProfileByUserId($userId) {
        $stmt = $this->conn->prepare(
            "SELECT np.*, u.full_name, u.is_verified 
             FROM nurse_profiles np 
             JOIN users u ON np.user_id = u.user_id 
             WHERE np.user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        if (!$profile) {
            error_log("No nurse profile found for user_id: $userId");
            return null;
        }

        // Lấy danh sách chứng chỉ bằng CertificateModel
        $profile['certificates'] = $this->certificateModel->getCertificatesByNurseProfileId($profile['nurse_profile_id']);

        return $profile;
    }

    // Xóa hồ sơ y tá
    public function deleteNurseProfile($userId) {
        $stmt = $this->conn->prepare("SELECT nurse_profile_id FROM nurse_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        if (!$profile) {
            throw new Exception("Không tìm thấy hồ sơ y tá");
        }

        // Xóa chứng chỉ bằng CertificateModel
        $this->certificateModel->deleteCertificatesByNurseProfileId($profile['nurse_profile_id']);

        // Xóa hồ sơ
        $stmt = $this->conn->prepare("DELETE FROM nurse_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi xóa hồ sơ y tá: " . $stmt->error);
        }
    }

    // Cập nhật trạng thái phê duyệt
    public function updateApprovalStatus($userId, $isApproved, $adminUserId) {
        $stmt = $this->conn->prepare("SELECT nurse_profile_id FROM nurse_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        if (!$profile) {
            throw new Exception("Không tìm thấy hồ sơ y tá");
        }

        if ($isApproved) {
            // Cập nhật trạng thái phê duyệt
            $stmt = $this->conn->prepare("UPDATE nurse_profiles SET is_approved = 1 WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // Cập nhật trạng thái verified của user
            $stmt = $this->conn->prepare("UPDATE users SET is_verified = 1 WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // Ghi log hành động admin
            $stmt = $this->conn->prepare(
                "INSERT INTO admin_actions (action_date, action_type, description, admin_user_id, target_user_id) 
                 VALUES (NOW(), 'APPROVE_USER', ?, ?, ?)"
            );
            $description = "Phê duyệt hồ sơ y tá cho userId: $userId";
            $stmt->bind_param("sii", $description, $adminUserId, $userId);
            $stmt->execute();
        } else {
            // Xóa chứng chỉ bằng CertificateModel
            $this->certificateModel->deleteCertificatesByNurseProfileId($profile['nurse_profile_id']);

            // Xóa hồ sơ
            $stmt = $this->conn->prepare("DELETE FROM nurse_profiles WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // Xóa user
            $stmt = $this->conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
        }
    }

    // Lấy tất cả hồ sơ y tá chưa được phê duyệt
    public function getAllNurseProfiles() {
        $stmt = $this->conn->prepare(
            "SELECT np.*, u.full_name, u.is_verified, u.username, u.email, u.phone_number, u.address, u.role 
             FROM nurse_profiles np 
             JOIN users u ON np.user_id = u.user_id 
             WHERE np.is_approved = 0"
        );
        $stmt->execute();
        $profiles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($profiles as &$profile) {
            $profile['certificates'] = $this->certificateModel->getCertificatesByNurseProfileId($profile['nurse_profile_id']);
        }

        return $profiles;
    }

    // Lấy tất cả hồ sơ y tá đã được phê duyệt
    public function getApprovedNurseProfiles() {
        $stmt = $this->conn->prepare(
            "SELECT np.*, u.full_name, u.is_verified, u.username, u.email, u.phone_number, u.address, u.role 
             FROM nurse_profiles np 
             JOIN users u ON np.user_id = u.user_id 
             WHERE np.is_approved = 1"
        );
        $stmt->execute();
        $profiles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($profiles as &$profile) {
            $profile['certificates'] = $this->certificateModel->getCertificatesByNurseProfileId($profile['nurse_profile_id']);
        }

        return $profiles;
    }

    // Lấy danh sách hồ sơ y tá đã được phê duyệt theo user_ids
    public function getApprovedNurseProfilesByUserIds($userIds) {
        if (empty($userIds)) {
            return [];
        }

        // Chuyển danh sách user_ids thành chuỗi cho câu truy vấn
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $stmt = $this->conn->prepare(
            "SELECT np.*, u.full_name, u.is_verified, u.username, u.email, u.phone_number, u.address, u.role 
             FROM nurse_profiles np 
             JOIN users u ON np.user_id = u.user_id 
             WHERE np.is_approved = 1 AND np.user_id IN ($placeholders)"
        );

        // Ràng buộc tham số động
        $types = str_repeat('i', count($userIds));
        $stmt->bind_param($types, ...$userIds);
        $stmt->execute();
        $profiles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Lấy chứng chỉ cho từng hồ sơ
        foreach ($profiles as &$profile) {
            $profile['certificates'] = $this->certificateModel->getCertificatesByNurseProfileId($profile['nurse_profile_id']);
        }

        return $profiles;
    }
}
?>