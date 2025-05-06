<?php
class CertificateModel {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Lưu chứng chỉ mới
    public function createCertificate($nurseProfileId, $certificateName, $filePath) {
        $stmt = $this->conn->prepare(
            "INSERT INTO certificates (certificate_name, file_path, nurse_profile_id) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("ssi", $certificateName, $filePath, $nurseProfileId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi lưu chứng chỉ");
        }
        return $this->conn->insert_id;
    }

    // Lấy danh sách chứng chỉ theo nurse_profile_id
    public function getCertificatesByNurseProfileId($nurseProfileId) {
        $stmt = $this->conn->prepare("SELECT * FROM certificates WHERE nurse_profile_id = ?");
        $stmt->bind_param("i", $nurseProfileId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy danh sách chứng chỉ theo user_id (tương tự findByNurseProfileUserUserId)
    public function getCertificatesByUserId($userId) {
        $stmt = $this->conn->prepare(
            "SELECT c.* 
             FROM certificates c 
             JOIN nurse_profiles np ON c.nurse_profile_id = np.nurse_profile_id 
             WHERE np.user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Xóa chứng chỉ theo nurse_profile_id
    public function deleteCertificatesByNurseProfileId($nurseProfileId) {
        $stmt = $this->conn->prepare("DELETE FROM certificates WHERE nurse_profile_id = ?");
        $stmt->bind_param("i", $nurseProfileId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi xóa chứng chỉ");
        }
    }

    // Xóa chứng chỉ theo certificate_id
    public function deleteCertificateById($certificateId) {
        $stmt = $this->conn->prepare("DELETE FROM certificates WHERE certificate_id = ?");
        $stmt->bind_param("i", $certificateId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi xóa chứng chỉ");
        }
    }
}
?>