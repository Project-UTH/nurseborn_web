
<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurse = isset($nurse) ? $nurse : [];
$bookingData = isset($_POST) ? [
    'nurse_user_id' => filter_input(INPUT_POST, 'nurse_user_id', FILTER_SANITIZE_NUMBER_INT),
    'booking_date' => filter_var($_POST['booking_date'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
    'service_type' => filter_var($_POST['service_type'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
    'start_time' => filter_var($_POST['start_time'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
    'end_time' => filter_var($_POST['end_time'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
    'price' => filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
    'notes' => filter_var($_POST['notes'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)
] : [];
$serviceTypes = ['HOURLY', 'DAILY', 'WEEKLY'];
$pageTitle = 'Đặt Lịch Y Tá';
$baseUrl = '/nurseborn';

// Khởi tạo FeedbackModel để lấy số sao trung bình
require_once __DIR__ . '/../models/FeedbackModel.php';
$feedbackModel = new FeedbackModel($conn);

// Lấy số sao trung bình của y tá
$averageRating = $feedbackModel->getAverageRatingByNurseUserId($nurse['user_id']);

// Kiểm tra xem y tá có giá dịch vụ hợp lệ không
$hasValidPricing = !(
    ($nurse['hourly_rate'] ?? 0) == 0 &&
    ($nurse['daily_rate'] ?? 0) == 0 &&
    ($nurse['weekly_rate'] ?? 0) == 0
);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/vendor/css/core.css">
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css">
    <link rel="stylesheet" href="../assets/css/demo.css">
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <style>
        /* Tùy chỉnh tổng thể */
        body {
            background: linear-gradient(to bottom, #f5f7fa, #e8ecef);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Card chính */
        .card {
            border: none;
            border-radius: 15px;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-body {
            padding: 30px;
        }

        /* Tiêu đề */
        h5.card-header {
            background: linear-gradient(45deg, #007bff, #28a745);
            color: #fff;
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
            padding: 20px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            position: relative;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Thông tin y tá */
        .nurse-info {
            margin-bottom: 30px;
            text-align: center;
        }
        .nurse-info img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #0d6efd;
            margin-bottom: 15px;
        }
        .nurse-info .card-title {
            color: #0d6efd;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .nurse-info .card-text {
            color: #6c757d;
            font-size: 1rem;
        }
        .nurse-info .card-text strong {
            color: #343a40;
        }
        .average-rating {
            color: #ffc107;
            font-size: 1rem;
            margin-top: 5px;
        }
        .average-rating i {
            margin-right: 5px;
        }

        /* Form đặt lịch */
        .form-label {
            font-weight: 600;
            color: #343a40;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        .form-control, .form-select, .form-control-plaintext {
            border-radius: 8px;
            border: 2px solid #0d6efd;
            padding: 12px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
            outline: none;
        }
        .form-control-plaintext {
            color: #6c757d;
            background-color: #f8f9fa;
            border: none;
        }
        .input-group-text {
            border-radius: 8px;
            border: 2px solid #0d6efd;
            border-left: none;
            background-color: #f8f9fa;
            color: #343a40;
            font-weight: 500;
        }
        .time-field {
            display: none;
        }
        .time-field.visible {
            display: block;
        }
        .disabled-option {
            color: #6c757d;
            font-style: italic;
        }
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Nút Đặt Lịch */
        .btn-primary {
            background: linear-gradient(45deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #4338ca, #6d28d9);
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        /* Thông báo */
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 15px;
            font-size: 1rem;
        }
        .alert-success {
            background-color: #e6ffed;
            border-color: #28a745;
            color: #28a745;
        }
        .alert-danger {
            background-color: #ffe6e6;
            border-color: #dc3545;
            color: #dc3545;
        }

        /* Phần giá dịch vụ */
        .pricing-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }
        .pricing-section h6 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
        }
        .pricing-section p {
            margin: 5px 0;
            font-size: 1rem;
            color: #495057;
        }
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include __DIR__ . '/fragments/menu-family.php'; ?>
        <div class="layout-page">
            <?php include __DIR__ . '/fragments/navbar.php'; ?>
            <div class="content-wrapper">
                <div class="container">
                    <div class="card mb-4">
                        <h5 class="card-header">Đặt Lịch Y Tá</h5>
                        <div class="card-body">
                            <!-- Hiển thị thông tin y tá -->
                            <div class="nurse-info">
                                <img src="<?php echo htmlspecialchars($nurse['profile_image'] ?? '../assets/img/avatars/default_profile.jpg'); ?>" 
                                     alt="Ảnh Y Tá"/>
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($nurse['full_name']); ?> | 
                                    <?php echo htmlspecialchars($nurse['skills'] ?? ''); ?>
                                </h5>
                                <p class="card-text"><strong>Kinh nghiệm:</strong>
                                    <?php echo htmlspecialchars($nurse['experience_years'] ? $nurse['experience_years'] . ' năm' : 'Chưa có thông tin'); ?>
                                </p>
                                <p class="card-text"><strong>Địa điểm:</strong>
                                    <?php echo htmlspecialchars($nurse['location'] ?? 'Chưa có thông tin'); ?>
                                </p>
                                <div class="average-rating">
                                    <i class="fas fa-star"></i>
                                    <?php echo $averageRating > 0 ? $averageRating : 'Chưa có đánh giá'; ?>
                                </div>
                            </div>

                            <!-- Hiển thị giá dịch vụ -->
                            <div class="pricing-section">
                                <h6>Giá Dịch Vụ</h6>
                                <p><strong>Theo giờ:</strong> <?php echo number_format($nurse['hourly_rate'] ?? 0, 0, ',', '.') ?> VND/giờ</p>
                                <p><strong>Theo ngày:</strong> <?php echo number_format($nurse['daily_rate'] ?? 0, 0, ',', '.') ?> VND/ngày</p>
                                <p><strong>Theo tuần:</strong> <?php echo number_format($nurse['weekly_rate'] ?? 0, 0, ',', '.') ?> VND/tuần</p>
                            </div>

                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if (!$hasValidPricing): ?>
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    Y tá chưa thiết lập giá dịch vụ. Vui lòng liên hệ y tá để cập nhật giá trước khi đặt lịch.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php else: ?>
                                <form action="?action=set_service&nurseUserId=<?php echo htmlspecialchars($nurse['user_id'] ?? ''); ?>" method="post" class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Y Tá Được Chọn:</label>
                                        <p class="form-control-plaintext"><?php echo htmlspecialchars($nurse['full_name'] ?? 'Không tìm thấy thông tin y tá'); ?></p>
                                        <input type="hidden" name="nurse_user_id" value="<?php echo htmlspecialchars($nurse['user_id'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Lịch Làm Việc:</label>
                                        <p class="form-control-plaintext">
                                            <?php if (!empty($nurse['selected_days'])): ?>
                                                <?php echo htmlspecialchars(implode(', ', $nurse['selected_days'])); ?>
                                            <?php else: ?>
                                                Chưa có lịch làm việc
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="col-12">
                                        <label for="serviceType" class="form-label">Loại Dịch Vụ:</label>
                                        <select id="serviceType" name="service_type" class="form-select" onchange="updatePrice(); toggleTimeFields();" required>
                                            <option value="">-- Chọn loại dịch vụ --</option>
                                            <?php foreach ($serviceTypes as $type): ?>
                                                <?php
                                                // Kiểm tra giá tương ứng với loại dịch vụ
                                                $isServicePriceValid = true;
                                                $invalidReason = '';
                                                if ($type === 'HOURLY' && ($nurse['hourly_rate'] ?? 0) <= 0) {
                                                    $isServicePriceValid = false;
                                                    $invalidReason = '(Chưa thiết lập giá theo giờ)';
                                                } elseif ($type === 'DAILY' && ($nurse['daily_rate'] ?? 0) <= 0) {
                                                    $isServicePriceValid = false;
                                                    $invalidReason = '(Chưa thiết lập giá theo ngày)';
                                                } elseif ($type === 'WEEKLY' && ($nurse['weekly_rate'] ?? 0) <= 0) {
                                                    $isServicePriceValid = false;
                                                    $invalidReason = '(Chưa thiết lập giá theo tuần)';
                                                }
                                                ?>
                                                <option value="<?php echo $type; ?>" <?php echo ($bookingData['service_type'] ?? '') === $type ? 'selected' : ''; ?>
                                                    <?php echo $type === 'WEEKLY' && count($nurse['selected_days']) !== 7 ? 'disabled class="disabled-option"' : ''; ?>
                                                    <?php echo !$isServicePriceValid ? 'disabled class="disabled-option"' : ''; ?>>
                                                    <?php echo htmlspecialchars($type); ?>
                                                    <?php if ($type === 'WEEKLY' && count($nurse['selected_days']) !== 7): ?>
                                                        (Yêu cầu y tá có lịch làm việc đủ 7 ngày)
                                                    <?php endif; ?>
                                                    <?php if (!$isServicePriceValid): ?>
                                                        <?php echo $invalidReason; ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="price" class="form-label">Giá Dịch Vụ:</label>
                                        <div class="input-group">
                                            <input type="text" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($bookingData['price'] ?? '0'); ?>" readonly>
                                            <span class="input-group-text">VND</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="bookingDate" class="form-label">Ngày Đặt Lịch:</label>
                                        <input type="date" id="bookingDate" name="booking_date" class="form-control" value="<?php echo htmlspecialchars($bookingData['booking_date'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-12 time-field" id="startTimeField">
                                        <label for="startTime" class="form-label">Giờ Bắt Đầu:</label>
                                        <input type="time" id="startTime" name="start_time" class="form-control" value="<?php echo htmlspecialchars($bookingData['start_time'] ?? ''); ?>" onchange="updatePrice()">
                                    </div>
                                    <div class="col-12 time-field" id="endTimeField">
                                        <label for="endTime" class="form-label">Giờ Kết Thúc:</label>
                                        <input type="time" id="endTime" name="end_time" class="form-control" value="<?php echo htmlspecialchars($bookingData['end_time'] ?? ''); ?>" onchange="updatePrice()">
                                    </div>
                                    <div class="col-12">
                                        <label for="notes" class="form-label">Ghi Chú:</label>
                                        <textarea id="notes" name="notes" class="form-control"><?php echo htmlspecialchars($bookingData['notes'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary">Đặt Lịch</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>

<script>
    // Lấy danh sách ngày làm việc của y tá và chuyển đổi sang định dạng tiếng Anh
    const availableDays = <?php echo json_encode(array_map(function($day) {
        switch ($day) {
            case 'Chủ Nhật': return 'SUNDAY';
            case 'Thứ Hai': return 'MONDAY';
            case 'Thứ Ba': return 'TUESDAY';
            case 'Thứ Tư': return 'WEDNESDAY';
            case 'Thứ Năm': return 'THURSDAY';
            case 'Thứ Sáu': return 'FRIDAY';
            case 'Thứ Bảy': return 'SATURDAY';
            default: return $day;
        }
    }, $nurse['selected_days'] ?? [])); ?>;
    const daysOfWeek = ['SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY'];

    // Hàm hiển thị/ẩn các trường thời gian dựa trên loại dịch vụ
    function toggleTimeFields() {
        const serviceType = document.getElementById('serviceType');
        if (!serviceType) return; // Kiểm tra nếu không có form (do giá không hợp lệ)
        const startTimeField = document.getElementById('startTimeField');
        const endTimeField = document.getElementById('endTimeField');

        if (serviceType.value === 'HOURLY') {
            startTimeField.classList.add('visible');
            endTimeField.classList.add('visible');
        } else {
            startTimeField.classList.remove('visible');
            endTimeField.classList.remove('visible');
            document.getElementById('startTime').value = '';
            document.getElementById('endTime').value = '';
        }
    }

    function calculateHours() {
        const startTime = document.getElementById('startTime');
        const endTime = document.getElementById('endTime');
        if (!startTime || !endTime || !startTime.value || !endTime.value) return 0;

        const start = new Date(`1970-01-01T${startTime.value}:00`);
        const end = new Date(`1970-01-01T${endTime.value}:00`);
        const diffInMs = end - start;
        const hours = diffInMs / (1000 * 60 * 60);
        return hours > 0 ? hours : 0;
    }

    function updatePrice() {
        const serviceType = document.getElementById('serviceType');
        if (!serviceType) return; // Kiểm tra nếu không có form
        const priceInput = document.getElementById('price');
        const hourlyRate = <?php echo $nurse['hourly_rate'] ?? 0; ?>;
        const dailyRate = <?php echo $nurse['daily_rate'] ?? 0; ?>;
        const weeklyRate = <?php echo $nurse['weekly_rate'] ?? 0; ?>;

        // Debug các giá trị từ CSDL
        console.log("Hourly Rate từ CSDL: " + hourlyRate);
        console.log("Daily Rate từ CSDL: " + dailyRate);
        console.log("Weekly Rate từ CSDL: " + weeklyRate);

        let totalPrice = 0;
        if (serviceType.value === 'HOURLY') {
            const hours = calculateHours();
            console.log("Số giờ tính được: " + hours); // Debug số giờ
            totalPrice = hourlyRate * hours;
        } else if (serviceType.value === 'DAILY') {
            totalPrice = dailyRate; // Sử dụng trực tiếp giá từ CSDL
        } else if (serviceType.value === 'WEEKLY') {
            totalPrice = weeklyRate; // Sử dụng trực tiếp giá từ CSDL
        }

        priceInput.value = totalPrice > 0 ? totalPrice.toFixed(2) : '0';
        console.log("Giá đã cập nhật: " + priceInput.value); // Debug giá
    }

    // Vô hiệu hóa các ngày không có trong lịch làm việc của y tá
    const bookingDateInput = document.getElementById('bookingDate');
    if (bookingDateInput) {
        bookingDateInput.addEventListener('input', function() {
            const selectedDate = new Date(this.value);
            const dayOfWeek = daysOfWeek[selectedDate.getDay()];
            if (!availableDays.includes(dayOfWeek)) {
                alert('Y tá không làm việc vào ngày ' +
                    (dayOfWeek === 'SUNDAY' ? 'Chủ Nhật' :
                     dayOfWeek === 'MONDAY' ? 'Thứ Hai' :
                     dayOfWeek === 'TUESDAY' ? 'Thứ Ba' :
                     dayOfWeek === 'WEDNESDAY' ? 'Thứ Tư' :
                     dayOfWeek === 'THURSDAY' ? 'Thứ Năm' :
                     dayOfWeek === 'FRIDAY' ? 'Thứ Sáu' :
                     dayOfWeek === 'SATURDAY' ? 'Thứ Bảy' : dayOfWeek) +
                    '. Vui lòng chọn ngày khác.');
                this.value = '';
            }
        });
    }

    // Gọi toggleTimeFields và updatePrice khi thay đổi loại dịch vụ
    const serviceTypeSelect = document.getElementById('serviceType');
    if (serviceTypeSelect) {
        serviceTypeSelect.addEventListener('change', function() {
            toggleTimeFields();
            updatePrice();
        });
    }

    // Gọi updatePrice khi thay đổi giờ bắt đầu hoặc giờ kết thúc
    const startTimeInput = document.getElementById('startTime');
    const endTimeInput = document.getElementById('endTime');
    if (startTimeInput) startTimeInput.addEventListener('change', updatePrice);
    if (endTimeInput) endTimeInput.addEventListener('change', updatePrice);

    // Kiểm tra các trường bắt buộc trước khi gửi form
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(event) {
            const serviceType = document.getElementById('serviceType').value;
            const bookingDate = document.getElementById('bookingDate').value;
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            const priceInput = document.getElementById('price');

            // Kiểm tra loại dịch vụ
            if (!serviceType) {
                event.preventDefault();
                alert('Vui lòng chọn loại dịch vụ.');
                return;
            }

            // Kiểm tra ngày đặt lịch
            if (!bookingDate) {
                event.preventDefault();
                alert('Vui lòng chọn ngày đặt lịch.');
                return;
            }

            // Kiểm tra giờ bắt đầu và giờ kết thúc chỉ khi loại dịch vụ là HOURLY
            if (serviceType === 'HOURLY') {
                if (!startTime || !endTime) {
                    event.preventDefault();
                    alert('Giờ bắt đầu và giờ kết thúc không được để trống khi chọn dịch vụ theo giờ.');
                    return;
                }

                // Kiểm tra giờ kết thúc phải lớn hơn giờ bắt đầu
                const start = new Date(`1970-01-01T${startTime}:00`);
                const end = new Date(`1970-01-01T${endTime}:00`);
                if (end <= start) {
                    event.preventDefault();
                    alert('Giờ kết thúc phải lớn hơn giờ bắt đầu.');
                    return;
                }
            }

            // Tính giá trước khi gửi form
            updatePrice();

            // Kiểm tra giá
            if (!priceInput.value || parseFloat(priceInput.value) <= 0) {
                event.preventDefault();
                alert('Vui lòng đảm bảo giá được tính toán hợp lệ.');
                return;
            }

            console.log("Form gửi với giá: " + priceInput.value); // Debug giá trước khi gửi
        });
    }

    // Gọi toggleTimeFields và updatePrice khi trang tải
    window.onload = function() {
        toggleTimeFields();
        updatePrice();
    };
</script>
</body>
</html>