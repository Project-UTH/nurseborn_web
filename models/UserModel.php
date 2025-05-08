<?php
require_once 'NurseProfileModel.php';
require_once 'FamilyProfileModel.php';

class UserModel {
    private $conn;
    private $nurseProfileModel;
    private $familyProfileModel;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
        $this->nurseProfileModel = new NurseProfileModel($dbConnection);
        $this->familyProfileModel = new FamilyProfileModel($dbConnection);
    }

    // Tìm người dùng theo username
    public function findByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Tìm người dùng theo email
    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Tìm người dùng theo role và is_verified
    public function findByRoleAndIsVerified($role, $isVerified) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = ? AND is_verified = ?");
        $stmt->bind_param("si", $role, $isVerified);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Đăng ký người dùng mới
    public function registerUser($userData, $profileData = null) {
        // Kiểm tra trùng username
        if ($this->findByUsername($userData['username'])) {
            throw new Exception("Username đã được sử dụng");
        }

        // Kiểm tra trùng email
        if ($this->findByEmail($userData['email'])) {
            throw new Exception("Email đã được sử dụng");
        }

        // Mã hóa mật khẩu
        $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT);

        // Chuẩn bị dữ liệu
        $email = $userData['email'];
        $fullName = $userData['full_name'];
        $phoneNumber = $userData['phone_number'] ?? null;
        $role = strtoupper($userData['role']);
        $username = $userData['username'];
        $address = $userData['address'] ?? null;
        $isVerified = ($role === 'FAMILY') ? 1 : 0;

        // Lưu người dùng
        $stmt = $this->conn->prepare(
            "INSERT INTO users (email, full_name, password_hash, phone_number, role, username, address, created_at, is_verified) 
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)"
        );
        $stmt->bind_param("sssssssi", $email, $fullName, $passwordHash, $phoneNumber, $role, $username, $address, $isVerified);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi đăng ký người dùng");
        }

        // Lấy user vừa tạo
        $user = $this->findByUsername($username);

        // Tạo hồ sơ y tá hoặc gia đình
        if ($role === 'NURSE' && $profileData) {
            if (empty($profileData)) {
                throw new Exception("Thông tin hồ sơ y tá là bắt buộc");
            }
            $this->nurseProfileModel->createNurseProfile(
                $user['user_id'],
                $profileData['bio'] ?? null,
                $profileData['daily_rate'] ?? null,
                $profileData['experience_years'] ?? 0,
                $profileData['hourly_rate'] ?? 0,
                $profileData['location'] ?? 'Unknown',
                $profileData['profile_image'] ?? null,
                $profileData['skills'] ?? 'None',
                $profileData['weekly_rate'] ?? null,
                $profileData['certificates'] ?? []
            );
        } elseif ($role === 'FAMILY' && $profileData) {
            if (empty($profileData)) {
                throw new Exception("Thông tin hồ sơ gia đình là bắt buộc");
            }
            $this->familyProfileModel->createFamilyProfile(
                $user['user_id'],
                $profileData['child_name'] ?? null,
                $profileData['child_age'] ?? null,
                $profileData['preferred_location'] ?? null,
                $profileData['specific_needs'] ?? null
            );
        }

        return $user;
    }

    // Đăng nhập
    public function login($username, $password) {
        $user = $this->findByUsername($username);
        if (!$user) {
            throw new Exception("Người dùng không tồn tại");
        }

        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password_hash'])) {
            // Lấy hồ sơ y tá hoặc gia đình
            $response = $user;
            if ($user['role'] === 'NURSE') {
                try {
                    $response['nurse_profile'] = $this->nurseProfileModel->getNurseProfileByUserId($user['user_id']);
                } catch (Exception $e) {
                    // Không có hồ sơ y tá
                }
            } elseif ($user['role'] === 'FAMILY') {
                try {
                    $response['family_profile'] = $this->familyProfileModel->getFamilyProfileByUserId($user['user_id']);
                } catch (Exception $e) {
                    // Không có hồ sơ gia đình
                }
            }
            return $response;
        } else {
            throw new Exception("Sai mật khẩu");
        }
    }

    // Lấy người dùng theo ID
    public function getUserById($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Trong class UserModel
public function updateUser($userId, $userData, $familyData = null) {
    // Cập nhật thông tin người dùng
    $stmt = $this->conn->prepare(
        "UPDATE users SET full_name = ?, email = ?, phone_number = ?, address = ? WHERE user_id = ?"
    );
    $stmt->bind_param("ssssi", $userData['full_name'], $userData['email'], $userData['phone_number'], $userData['address'], $userId);
    if (!$stmt->execute()) {
        throw new Exception("Lỗi khi cập nhật thông tin người dùng: " . $stmt->error);
    }

    // Nếu role là FAMILY, cập nhật thông tin gia đình
    $user = $this->getUserById($userId);
    if ($user['role'] === 'FAMILY' && $familyData) {
        $this->familyProfileModel->updateFamilyProfile($userId, $familyData);
    }

    return $this->getUserById($userId);
}
}
?>