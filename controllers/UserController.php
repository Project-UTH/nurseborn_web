<?php
session_start();
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NurseProfileModel.php';
require_once __DIR__ . '/../models/FamilyProfileModel.php';
require_once __DIR__ . '/../models/CertificateModel.php';

// Khởi tạo models
$userModel = new UserModel($conn);
$nurseProfileModel = new NurseProfileModel($conn);
$familyProfileModel = new FamilyProfileModel($conn);
$certificateModel = new CertificateModel($conn);

// Hàm kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm lấy vai trò người dùng
function getUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

// Hàm validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Hàm validate số điện thoại (10-11 số, chỉ chứa số)
function isValidPhoneNumber($phone) {
    return preg_match('/^[0-9]{10,11}$/', $phone);
}

// Xử lý yêu cầu
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
        if (isLoggedIn()) {
            $user = $userModel->getUserById($_SESSION['user_id']);
            if (!$user) {
                error_log("User not found for ID: {$_SESSION['user_id']}");
                $_SESSION['error'] = 'Người dùng không tồn tại';
                session_destroy();
                header('Location: ?action=login');
                exit;
            }
            error_log("User ID: {$_SESSION['user_id']}, Role: {$user['role']}");
            $role = $user['role'];
            if ($role === 'ADMIN') {
                header('Location: ?action=admin_home');
                exit;
            } elseif ($role === 'FAMILY') {
                $familyProfile = $familyProfileModel->getFamilyProfileByUserId($user['user_id']);
                $_SESSION['family_profile'] = $familyProfile;
                include __DIR__ . '/../views/home.php';
            } elseif ($role === 'NURSE') {
                header('Location: ?action=nurse_home');
                exit;
            } else {
                error_log("Unknown role for User ID: {$_SESSION['user_id']}");
                include __DIR__ . '/../views/home.php';
            }
        } else {
            error_log("No user logged in");
            include __DIR__ . '/../views/home.php';
        }
        break;

    case 'admin_home':
        if (isLoggedIn() && getUserRole() === 'ADMIN') {
            $user = $userModel->getUserById($_SESSION['user_id']);
            if (!$user) {
                error_log("User not found for ID: {$_SESSION['user_id']}");
                $_SESSION['error'] = 'Người dùng không tồn tại';
                session_destroy();
                header('Location: ?action=login');
                exit;
            }
            $_SESSION['user'] = $user;
            include __DIR__ . '/../views/admin_dashboard.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'family_home':
        if (isLoggedIn() && getUserRole() === 'FAMILY') {
            $user = $userModel->getUserById($_SESSION['user_id']);
            if (!$user) {
                error_log("User not found for ID: {$_SESSION['user_id']}");
                $_SESSION['error'] = 'Người dùng không tồn tại';
                session_destroy();
                header('Location: ?action=login');
                exit;
            }
            $familyProfile = $familyProfileModel->getFamilyProfileByUserId($user['user_id']);
            $_SESSION['user'] = $user;
            $_SESSION['family_profile'] = $familyProfile;
            include __DIR__ . '/../views/home.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'nurse_home':
        if (isLoggedIn() && getUserRole() === 'NURSE') {
            $user = $userModel->getUserById($_SESSION['user_id']);
            if (!$user) {
                error_log("User not found for ID: {$_SESSION['user_id']}");
                $_SESSION['error'] = 'Người dùng không tồn tại';
                session_destroy();
                header('Location: ?action=login');
                exit;
            }
            $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($user['user_id']);
            if (!$nurseProfile) {
                $_SESSION['error'] = 'Hồ sơ y tá chưa được tạo. Vui lòng cập nhật hồ sơ.';
                header('Location: ?action=register_nurse');
                exit;
            }
            $_SESSION['user'] = $user;
            $_SESSION['nurse_profile'] = $nurseProfile;
            include __DIR__ . '/../views/nurse_home.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $password = $_POST['password'];
            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin';
                include __DIR__ . '/../views/login.php';
                break;
            }
            try {
                $response = $userModel->login($username, $password);
                $_SESSION['user_id'] = $response['user_id'];
                $_SESSION['user_role'] = $response['role'];
                $_SESSION['success'] = 'Đăng nhập thành công';
                header('Location: ?action=home');
            } catch (Exception $e) {
                error_log("Lỗi đăng nhập: " . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                include __DIR__ . '/../views/login.php';
            }
        } else {
            include __DIR__ . '/../views/login.php';
        }
        break;

    case 'role_selection':
        include __DIR__ . '/../views/role-selection.php';
        break;

    case 'register_nurse':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $userData = [
                    'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                    'full_name' => filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING),
                    'password' => $_POST['password'],
                    'phone_number' => filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING) ?? null,
                    'role' => 'NURSE',
                    'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING),
                    'address' => filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING) ?? null
                ];

                // Validation
                if (empty($userData['email']) || !isValidEmail($userData['email'])) {
                    throw new Exception("Email không hợp lệ");
                }
                if (empty($userData['password']) || strlen($userData['password']) < 6) {
                    throw new Exception("Mật khẩu phải dài ít nhất 6 ký tự");
                }
                if (empty($userData['full_name']) || empty($userData['username'])) {
                    throw new Exception("Vui lòng điền đầy đủ họ tên và tên đăng nhập");
                }
                if ($userData['phone_number'] && !isValidPhoneNumber($userData['phone_number'])) {
                    throw new Exception("Số điện thoại không hợp lệ");
                }

                $profileData = [
                    'bio' => filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING) ?? null,
                    'daily_rate' => filter_input(INPUT_POST, 'daily_rate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? null,
                    'experience_years' => filter_input(INPUT_POST, 'experience_years', FILTER_SANITIZE_NUMBER_INT) ?? 0,
                    'hourly_rate' => filter_input(INPUT_POST, 'hourly_rate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? 0,
                    'location' => filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING) ?? 'Unknown',
                    'profile_image' => null,
                    'skills' => filter_input(INPUT_POST, 'skills', FILTER_SANITIZE_STRING) ?? 'None',
                    'weekly_rate' => filter_input(INPUT_POST, 'weekly_rate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? null,
                    'certificates' => []
                ];

                // Validation hồ sơ y tá
                if ($profileData['experience_years'] < 0 || $profileData['experience_years'] > 50) {
                    throw new Exception("Số năm kinh nghiệm phải từ 0 đến 50");
                }
                if ($profileData['hourly_rate'] < 0 || $profileData['hourly_rate'] > 1000000) {
                    throw new Exception("Giá giờ phải từ 0 đến 1,000,000");
                }
                if ($profileData['daily_rate'] !== null && ($profileData['daily_rate'] < 0 || $profileData['daily_rate'] > 10000000)) {
                    throw new Exception("Giá ngày phải từ 0 đến 10,000,000");
                }
                if ($profileData['weekly_rate'] !== null && ($profileData['weekly_rate'] < 0 || $profileData['weekly_rate'] > 50000000)) {
                    throw new Exception("Giá tuần phải từ 0 đến 50,000,000");
                }
                if (empty($profileData['skills']) || empty($profileData['location'])) {
                    throw new Exception("Kỹ năng và khu vực làm việc không được để trống");
                }

                // Xử lý ảnh đại diện
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    if ($_FILES['profile_image']['size'] > 5 * 1024 * 1024) { // 5MB
                        throw new Exception("Ảnh đại diện quá lớn (tối đa 5MB)");
                    }
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $_FILES['profile_image']['tmp_name']);
                    finfo_close($finfo);
                    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
                        throw new Exception("Ảnh đại diện phải là JPEG, PNG hoặc GIF");
                    }
                    $uploadDir = 'uploads/profile_images/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = time() . '_' . basename($_FILES['profile_image']['name']);
                    $filePath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $filePath)) {
                        $profileData['profile_image'] = '/' . $filePath;
                    } else {
                        throw new Exception("Lỗi khi lưu ảnh đại diện");
                    }
                }

                // Xử lý chứng chỉ
                if (isset($_FILES['certificates']) && !empty($_FILES['certificates']['name'][0])) {
                    $uploadDir = 'Uploads/certificates/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $certificateNames = isset($_POST['certificate_names']) ? array_map('trim', $_POST['certificate_names']) : [];
                    $certificates = [];
                    foreach ($_FILES['certificates']['name'] as $key => $name) {
                        if ($_FILES['certificates']['error'][$key] === UPLOAD_ERR_OK) {
                            if ($_FILES['certificates']['size'][$key] > 10 * 1024 * 1024) { // 10MB
                                throw new Exception("Chứng chỉ quá lớn (tối đa 10MB)");
                            }
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mime = finfo_file($finfo, $_FILES['certificates']['tmp_name'][$key]);
                            finfo_close($finfo);
                            if (!in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'])) {
                                throw new Exception("Chứng chỉ phải là PDF, JPEG hoặc PNG");
                            }
                            $fileName = time() . '_' . basename($name);
                            $filePath = $uploadDir . $fileName;
                            if (move_uploaded_file($_FILES['certificates']['tmp_name'][$key], $filePath)) {
                                $certName = isset($certificateNames[$key]) && !empty($certificateNames[$key]) 
                                    ? $certificateNames[$key] 
                                    : $name;
                                $certificates[] = [
                                    'name' => $certName,
                                    'file_path' => '/' . $filePath
                                ];
                            } else {
                                throw new Exception("Lỗi khi lưu chứng chỉ");
                            }
                        }
                    }
                    $profileData['certificates'] = $certificates;
                }

                $userModel->registerUser($userData, $profileData);
                $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                header('Location: ?action=login');
            } catch (Exception $e) {
                error_log("Lỗi đăng ký y tá: " . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                include __DIR__ . '/../views/register_nurse.php';
            }
        } else {
            include __DIR__ . '/../views/register_nurse.php';
        }
        break;

    case 'register_family':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $userData = [
                    'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                    'full_name' => filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING),
                    'password' => $_POST['password'],
                    'phone_number' => filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING) ?? null,
                    'role' => 'FAMILY',
                    'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING),
                    'address' => filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING) ?? null
                ];

                // Validation
                if (empty($userData['email']) || !isValidEmail($userData['email'])) {
                    throw new Exception("Email không hợp lệ");
                }
                if (empty($userData['password']) || strlen($userData['password']) < 6) {
                    throw new Exception("Mật khẩu phải dài ít nhất 6 ký tự");
                }
                if (empty($userData['full_name']) || empty($userData['username'])) {
                    throw new Exception("Vui lòng điền đầy đủ họ tên và tên đăng nhập");
                }
                if ($userData['phone_number'] && !isValidPhoneNumber($userData['phone_number'])) {
                    throw new Exception("Số điện thoại không hợp lệ");
                }

                $profileData = [
                    'child_name' => filter_input(INPUT_POST, 'child_name', FILTER_SANITIZE_STRING) ?? null,
                    'child_age' => filter_input(INPUT_POST, 'child_age', FILTER_SANITIZE_STRING) ?? null,
                    'preferred_location' => filter_input(INPUT_POST, 'preferred_location', FILTER_SANITIZE_STRING) ?? null,
                    'specific_needs' => filter_input(INPUT_POST, 'specific_needs', FILTER_SANITIZE_STRING) ?? null
                ];

                $userModel->registerUser($userData, $profileData);
                $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                header('Location: ?action=login');
            } catch (Exception $e) {
                error_log("Lỗi đăng ký gia đình: " . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                include __DIR__ . '/../views/register_family.php';
            }
        } else {
            include __DIR__ . '/../views/register_family.php';
        }
        break;

    case 'user_profile':
        if (isLoggedIn()) {
            $user = $userModel->getUserById($_SESSION['user_id']);
            if (!$user) {
                error_log("User not found for ID: {$_SESSION['user_id']}");
                $_SESSION['error'] = 'Người dùng không tồn tại';
                session_destroy();
                header('Location: ?action=login');
                exit;
            }
            $_SESSION['user'] = $user;
            if ($user['role'] === 'NURSE') {
                header('Location: ?action=nurse_profile');
            } elseif ($user['role'] === 'FAMILY') {
                $familyProfile = $familyProfileModel->getFamilyProfileByUserId($user['user_id']);
                $_SESSION['family_profile'] = $familyProfile;
                include __DIR__ . '/../views/user_profile.php';
            } else {
                include __DIR__ . '/../views/user_profile.php';
            }
        } else {
            $_SESSION['error'] = 'Vui lòng đăng nhập';
            header('Location: ?action=login');
        }
        break;

    case 'nurse_profile':
        if (isLoggedIn() && getUserRole() === 'NURSE') {
            $user = $userModel->getUserById($_SESSION['user_id']);
            if (!$user) {
                error_log("User not found for ID: {$_SESSION['user_id']}");
                $_SESSION['error'] = 'Người dùng không tồn tại';
                session_destroy();
                header('Location: ?action=login');
                exit;
            }
            $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($user['user_id']);
            if (!$nurseProfile) {
                $_SESSION['error'] = 'Hồ sơ y tá chưa được tạo. Vui lòng cập nhật hồ sơ.';
                header('Location: ?action=register_nurse');
                exit;
            }
            $_SESSION['user'] = $user;
            $_SESSION['nurse_profile'] = $nurseProfile;
            include __DIR__ . '/../views/nurse_profile.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'update_user':
        if (isLoggedIn() && getUserRole() === 'FAMILY') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $userData = [
                        'full_name' => filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING),
                        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                        'phone_number' => filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING) ?? null,
                        'address' => filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING) ?? null
                    ];
                    $familyProfileData = [
                        'child_name' => filter_input(INPUT_POST, 'child_name', FILTER_SANITIZE_STRING) ?? null,
                        'child_age' => filter_input(INPUT_POST, 'child_age', FILTER_SANITIZE_STRING) ?? null,
                        'preferred_location' => filter_input(INPUT_POST, 'preferred_location', FILTER_SANITIZE_STRING) ?? null,
                        'specific_needs' => filter_input(INPUT_POST, 'specific_needs', FILTER_SANITIZE_STRING) ?? null
                    ];

                    // Validation
                    if (empty($userData['email']) || !isValidEmail($userData['email'])) {
                        throw new Exception("Email không hợp lệ");
                    }
                    if (empty($userData['full_name'])) {
                        throw new Exception("Họ tên không được để trống");
                    }
                    if ($userData['phone_number'] && !isValidPhoneNumber($userData['phone_number'])) {
                        throw new Exception("Số điện thoại không hợp lệ");
                    }

                    // Cập nhật thông tin người dùng
                    $stmt = $conn->prepare(
                        "UPDATE users SET full_name = ?, email = ?, phone_number = ?, address = ? WHERE user_id = ?"
                    );
                    $stmt->bind_param("ssssi", $userData['full_name'], $userData['email'], $userData['phone_number'], $userData['address'], $_SESSION['user_id']);
                    if (!$stmt->execute()) {
                        throw new Exception("Lỗi khi cập nhật thông tin người dùng: " . $stmt->error);
                    }

                    // Cập nhật hồ sơ gia đình
                    $familyProfileModel->updateFamilyProfile($_SESSION['user_id'], $familyProfileData['child_name'], $familyProfileData['child_age'], $familyProfileData['preferred_location'], $familyProfileData['specific_needs']);

                    $_SESSION['success'] = 'Cập nhật hồ sơ thành công';
                    header('Location: ?action=user_profile');
                } catch (Exception $e) {
                    error_log("Lỗi cập nhật hồ sơ gia đình: " . $e->getMessage());
                    $_SESSION['error'] = $e->getMessage();
                    include __DIR__ . '/../views/update_user.php';
                }
            } else {
                $user = $userModel->getUserById($_SESSION['user_id']);
                if (!$user) {
                    error_log("User not found for ID: {$_SESSION['user_id']}");
                    $_SESSION['error'] = 'Người dùng không tồn tại';
                    session_destroy();
                    header('Location: ?action=login');
                    exit;
                }
                $familyProfile = $familyProfileModel->getFamilyProfileByUserId($user['user_id']);
                $_SESSION['user'] = $user;
                $_SESSION['family_profile'] = $familyProfile;
                include __DIR__ . '/../views/update_user.php';
            }
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'update_nurse':
        if (isLoggedIn() && getUserRole() === 'NURSE') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $profileData = [
                        'bio' => filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING) ?? null,
                        'daily_rate' => filter_input(INPUT_POST, 'daily_rate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? null,
                        'experience_years' => filter_input(INPUT_POST, 'experience_years', FILTER_SANITIZE_NUMBER_INT) ?? 0,
                        'hourly_rate' => filter_input(INPUT_POST, 'hourly_rate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? 0,
                        'location' => filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING) ?? 'Unknown',
                        'profile_image' => $_SESSION['nurse_profile']['profile_image'] ?? null,
                        'skills' => filter_input(INPUT_POST, 'skills', FILTER_SANITIZE_STRING) ?? 'None',
                        'weekly_rate' => filter_input(INPUT_POST, 'weekly_rate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? null,
                        'certificates' => []
                    ];

                    // Validation
                    if (empty($profileData['skills']) || empty($profileData['location'])) {
                        throw new Exception("Kỹ năng và địa điểm không được để trống");
                    }
                    if ($profileData['experience_years'] < 0 || $profileData['experience_years'] > 50) {
                        throw new Exception("Số năm kinh nghiệm phải từ 0 đến 50");
                    }
                    if ($profileData['hourly_rate'] < 0 || $profileData['hourly_rate'] > 1000000) {
                        throw new Exception("Giá giờ phải từ 0 đến 1,000,000");
                    }
                    if ($profileData['daily_rate'] !== null && ($profileData['daily_rate'] < 0 || $profileData['daily_rate'] > 10000000)) {
                        throw new Exception("Giá ngày phải từ 0 đến 10,000,000");
                    }
                    if ($profileData['weekly_rate'] !== null && ($profileData['weekly_rate'] < 0 || $profileData['weekly_rate'] > 50000000)) {
                        throw new Exception("Giá tuần phải từ 0 đến 50,000,000");
                    }

                    // Xử lý ảnh đại diện
                    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                        if ($_FILES['profile_image']['size'] > 5 * 1024 * 1024) { // 5MB
                            throw new Exception("Ảnh đại diện quá lớn (tối đa 5MB)");
                        }
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime = finfo_file($finfo, $_FILES['profile_image']['tmp_name']);
                        finfo_close($finfo);
                        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
                            throw new Exception("Ảnh đại diện phải là JPEG, PNG hoặc GIF");
                        }
                        $uploadDir = 'Uploads/profile_images/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        // Xóa ảnh đại diện cũ nếu có
                        if ($profileData['profile_image'] && file_exists(__DIR__ . '/../' . ltrim($profileData['profile_image'], '/'))) {
                            unlink(__DIR__ . '/../' . ltrim($profileData['profile_image'], '/'));
                        }
                        $fileName = time() . '_' . basename($_FILES['profile_image']['name']);
                        $filePath = $uploadDir . $fileName;
                        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $filePath)) {
                            $profileData['profile_image'] = '/' . $filePath;
                        } else {
                            throw new Exception("Lỗi khi lưu ảnh đại diện");
                        }
                    }

                    // Xử lý chứng chỉ
                    if (isset($_FILES['certificates']) && !empty($_FILES['certificates']['name'][0])) {
                        $uploadDir = 'Uploads/certificates/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $certificateNames = isset($_POST['certificate_names']) ? array_map('trim', $_POST['certificate_names']) : [];
                        $certificates = [];
                        foreach ($_FILES['certificates']['name'] as $key => $name) {
                            if ($_FILES['certificates']['error'][$key] === UPLOAD_ERR_OK) {
                                if ($_FILES['certificates']['size'][$key] > 10 * 1024 * 1024) { // 10MB
                                    throw new Exception("Chứng chỉ quá lớn (tối đa 10MB)");
                                }
                                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                $mime = finfo_file($finfo, $_FILES['certificates']['tmp_name'][$key]);
                                finfo_close($finfo);
                                if (!in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'])) {
                                    throw new Exception("Chứng chỉ phải là PDF, JPEG hoặc PNG");
                                }
                                $fileName = time() . '_' . basename($name);
                                $filePath = $uploadDir . $fileName;
                                if (move_uploaded_file($_FILES['certificates']['tmp_name'][$key], $filePath)) {
                                    $certName = isset($certificateNames[$key]) && !empty($certificateNames[$key]) 
                                        ? $certificateNames[$key] 
                                        : $name;
                                    $certificates[] = [
                                        'name' => $certName,
                                        'file_path' => '/' . $filePath
                                    ];
                                } else {
                                    throw new Exception("Lỗi khi lưu chứng chỉ");
                                }
                            }
                        }
                        $profileData['certificates'] = $certificates;
                    }

                    $nurseProfileModel->updateNurseProfile(
                        $_SESSION['user_id'], 
                        $profileData['bio'], 
                        $profileData['daily_rate'], 
                        $profileData['experience_years'], 
                        $profileData['hourly_rate'], 
                        $profileData['location'], 
                        $profileData['profile_image'], 
                        $profileData['skills'], 
                        $profileData['weekly_rate'], 
                        $profileData['certificates']
                    );

                    $_SESSION['success'] = 'Cập nhật hồ sơ y tá thành công';
                    header('Location: ?action=nurse_profile');
                } catch (Exception $e) {
                    error_log("Lỗi cập nhật hồ sơ y tá: " . $e->getMessage());
                    $_SESSION['error'] = $e->getMessage();
                    include __DIR__ . '/../views/update_nurse.php';
                }
            } else {
                $user = $userModel->getUserById($_SESSION['user_id']);
                if (!$user) {
                    error_log("User not found for ID: {$_SESSION['user_id']}");
                    $_SESSION['error'] = 'Người dùng không tồn tại';
                    session_destroy();
                    header('Location: ?action=login');
                    exit;
                }
                $nurseProfile = $nurseProfileModel->getNurseProfileByUserId($user['user_id']);
                if (!$nurseProfile) {
                    $_SESSION['error'] = 'Hồ sơ y tá chưa được tạo. Vui lòng cập nhật hồ sơ.';
                    header('Location: ?action=register_nurse');
                    exit;
                }
                $_SESSION['user'] = $user;
                $_SESSION['nurse_profile'] = $nurseProfile;
                include __DIR__ . '/../views/update_nurse.php';
            }
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    // Placeholder cho các action mới
    case 'bookings':
        if (isLoggedIn() && getUserRole() === 'FAMILY') {
            // TODO: Xử lý danh sách lịch đặt
            include __DIR__ . '/../views/bookings.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'nursepage':
        if (isLoggedIn() && getUserRole() === 'FAMILY') {
            // TODO: Xử lý đặt dịch vụ
            include __DIR__ . '/../views/nursepage.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'messages':
        if (isLoggedIn() && in_array(getUserRole(), ['FAMILY', 'NURSE'])) {
            // TODO: Xử lý trò chuyện
            include __DIR__ . '/../views/messages.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'notifications':
        if (isLoggedIn() && in_array(getUserRole(), ['FAMILY', 'NURSE'])) {
            // TODO: Xử lý thông báo
            include __DIR__ . '/../views/notifications.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'nurse_schedule':
        if (isLoggedIn() && getUserRole() === 'NURSE') {
            // TODO: Xử lý lịch làm việc
            include __DIR__ . '/../views/nurse_schedule.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'pending_bookings':
        if (isLoggedIn() && getUserRole() === 'NURSE') {
            // TODO: Xử lý lịch đặt chờ xác nhận
            include __DIR__ . '/../views/pending_bookings.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'nurse_availability':
        if (isLoggedIn() && getUserRole() === 'NURSE') {
            // TODO: Xử lý quản lý lịch làm việc
            include __DIR__ . '/../views/nurse_availability.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'nurse_income':
        if (isLoggedIn() && getUserRole() === 'NURSE') {
            // TODO: Xử lý thống kê thu nhập
            include __DIR__ . '/../views/nurse_income.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'review_nurse_profile':
        if (isLoggedIn() && getUserRole() === 'ADMIN') {
            // TODO: Xử lý duyệt hồ sơ y tá
            include __DIR__ . '/../views/review_nurse_profile.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'web_income':
        if (isLoggedIn() && getUserRole() === 'ADMIN') {
            // TODO: Xử lý thống kê doanh thu
            include __DIR__ . '/../views/web_income.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=login');
        }
        break;

    case 'logout':
        session_destroy();
        header('Location: ?action=login&logout=1');
        exit;

    default:
        include __DIR__ . '/../views/home.php';
        break;
}
?>