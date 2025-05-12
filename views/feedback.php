<?php
// Hiển thị lỗi trực tiếp để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$booking = isset($booking) ? $booking : null;
$pageTitle = 'Đánh Giá Dịch Vụ';
$baseUrl = '/nurseborn';
error_log("Debug: Đã vào file feedback.php");
error_log("Debug: Dữ liệu user trong feedback.php: " . print_r($user, true));
error_log("Debug: Dữ liệu booking trong feedback.php: " . print_r($booking, true));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Tùy chỉnh tổng thể */
        body {
            background: linear-gradient(to bottom, #e6f0fa, #f5f7fa);
            font-family: 'Segoe UI', 'Arial', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Card chính */
        .card {
            border: none;
            border-radius: 20px;
            background-color: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        .card-body {
            padding: 40px;
        }

        /* Tiêu đề */
        .card-header {
            background: linear-gradient(45deg, #007bff, #28a745);
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            padding: 25px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Thông tin booking */
        .booking-info {
            margin: 40px 0;
            padding: 25px;
            background-color: #f9fafb;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            border-left: 6px solid #007bff;
            transition: background-color 0.3s ease;
        }
        .booking-info:hover {
            background-color: #f1f5f9;
        }
        .booking-info h5 {
            color: #007bff;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 25px;
            letter-spacing: 0.5px;
        }
        .booking-info .card-text {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1.05rem;
            color: #5a6169;
        }
        .booking-info .card-text strong {
            color: #2c3e50;
            width: 170px;
            font-weight: 600;
        }
        .booking-info .card-text i {
            margin-right: 12px;
            color: #007bff;
            font-size: 1.3rem;
            opacity: 0.8;
        }
        .status-completed {
            color: #17a2b8;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        /* Form đánh giá */
        .feedback-form {
            max-width: 550px;
            margin: 0 auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
        }
        .feedback-form:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
            font-size: 1.15rem;
            display: block;
            letter-spacing: 0.2px;
        }
        .rating {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .rating input {
            display: none;
        }
        .rating label {
            font-size: 50px;
            color: #e0e0e0;
            cursor: pointer;
            transition: color 0.3s ease, transform 0.3s ease;
            margin: 0 8px;
        }
        .rating label:hover,
        .rating input:checked ~ label,
        .rating input:hover ~ label,
        .rating label:hover ~ label {
            color: #ffd700;
            transform: scale(1.15);
        }
        select, textarea {
            width: 100%;
            margin-bottom: 25px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 12px 15px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background-color: #f8f9fa;
        }
        select:focus, textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
            outline: none;
            background-color: #fff;
        }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        .mb-3 input[type="file"] {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 10px;
            font-size: 1rem;
            background-color: #f8f9fa;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .mb-3 input[type="file"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
            outline: none;
        }
        .btn-submit {
            background: linear-gradient(45deg, #007bff, #28a745);
            border: none;
            border-radius: 30px;
            padding: 14px 25px;
            font-weight: 600;
            font-size: 1.2rem;
            color: #fff;
            width: 100%;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: linear-gradient(45deg, #0056b3, #218838);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
        }
        .text-muted {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s ease;
            display: inline-block;
            margin-top: 15px;
        }
        .text-muted:hover {
            color: #007bff;
        }

        /* Thông báo */
        .alert {
            border-radius: 12px;
            margin-bottom: 25px;
            padding: 15px 20px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .alert-success {
            background-color: #e6ffed;
            border-left: 5px solid #28a745;
            color: #28a745;
        }
        .alert-danger {
            background-color: #ffe6e6;
            border-left: 5px solid #dc3545;
            color: #dc3545;
        }
        .alert .btn-close {
            padding: 0;
            background: none;
            border: none;
            font-size: 1.2rem;
            color: inherit;
            opacity: 0.7;
        }
        .alert .btn-close:hover {
            opacity: 1;
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
                <div class="content-xxl flex-grow-1 container-p-y">
                    <div class="card mb-4">
                        <h5 class="card-header text-center">Đánh Giá Dịch Vụ</h5>
                        <div class="card-body">
                            <!-- Error Message -->
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">×</button>
                                </div>
                            <?php endif; ?>
                            <!-- Success Message -->
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">×</button>
                                </div>
                            <?php endif; ?>
                            <!-- Debug: Kiểm tra biến $booking -->
                            <?php if (empty($booking)): ?>
                                <div class="alert alert-danger">
                                    Biến $booking trống. Vui lòng kiểm tra logic trong BookingController.php.
                                </div>
                            <?php endif; ?>
                            <!-- Booking Info -->
                            <?php if ($booking): ?>
                                <div class="booking-info">
                                    <h5 class="card-title">
                                        Y tá: <?php echo htmlspecialchars($booking['nurse_full_name']); ?>
                                    </h5>
                                    <p class="card-text">
                                        <i class="fas fa-id-badge"></i>
                                        <strong>Mã đặt lịch:</strong>
                                        <?php echo htmlspecialchars($booking['booking_id']); ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-calendar-alt"></i>
                                        <strong>Ngày đặt:</strong>
                                        <?php echo htmlspecialchars($booking['booking_date']); ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-stethoscope"></i>
                                        <strong>Loại dịch vụ:</strong>
                                        <?php echo htmlspecialchars($booking['service_type']); ?>
                                    </p>
                                    <?php if ($booking['service_type'] === 'HOURLY'): ?>
                                        <p class="card-text">
                                            <i class="fas fa-clock"></i>
                                            <strong>Giờ bắt đầu:</strong>
                                            <?php echo htmlspecialchars($booking['start_time']); ?>
                                            <br>
                                            <i class="fas fa-clock"></i>
                                            <strong>Giờ kết thúc:</strong>
                                            <?php echo htmlspecialchars($booking['end_time']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="card-text">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <strong>Giá:</strong>
                                        <?php echo number_format($booking['price']) . ' VND'; ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Trạng thái:</strong>
                                        <span class="status-completed"><?php echo htmlspecialchars($booking['status']); ?></span>
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-sticky-note"></i>
                                        <strong>Ghi chú:</strong>
                                        <?php echo htmlspecialchars($booking['notes'] ?? 'Không có ghi chú'); ?>
                                    </p>
                                </div>
                                <!-- Feedback Form -->
                                <div class="feedback-form">
                                    <form action="?action=submit_feedback" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>"/>
                                        <input type="hidden" name="nurse_id" value="<?php echo htmlspecialchars($booking['nurse_user_id']); ?>"/>
                                        <div class="mb-3">
                                            <label for="star5" class="form-label">Đánh giá của bạn:</label>
                                            <div class="rating">
                                                <input type="radio" id="star5" name="rating" value="5" required/>
                                                <label for="star5">★</label>
                                                <input type="radio" id="star4" name="rating" value="4"/>
                                                <label for="star4">★</label>
                                                <input type="radio" id="star3" name="rating" value="3"/>
                                                <label for="star3">★</label>
                                                <input type="radio" id="star2" name="rating" value="2"/>
                                                <label for="star2">★</label>
                                                <input type="radio" id="star1" name="rating" value="1"/>
                                                <label for="star1">★</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="comment" class="form-label">Chia sẻ đánh giá của bạn:</label>
                                            <textarea id="comment" name="comment" placeholder="Viết nhận xét của bạn về dịch vụ..." required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="attachment" class="form-label">Tải lên hình ảnh hoặc tệp PDF (tối đa 5 tệp):</label>
                                            <input type="file" id="attachment" name="attachment[]" accept="image/*,.pdf" multiple/>
                                        </div>
                                        <button type="submit" class="btn btn-submit">Gửi Đánh Giá</button>
                                    </form>
                                    <div class="text-center mt-3">
                                        <a href="?action=bookings" class="text-muted">Quay lại danh sách lịch đặt</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <p>Không tìm thấy lịch đặt.</p>
                                </div>
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
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/popper/popper.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/menu.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/dashboards-analytics.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script>
    // Validate file upload
    document.getElementById('attachment').addEventListener('change', function(e) {
        const files = e.target.files;
        const maxFiles = 5;
        const maxFileSize = 5 * 1024 * 1024; // 5MB

        if (files.length > maxFiles) {
            alert(`Bạn chỉ có thể tải lên tối đa ${maxFiles} tệp`);
            e.target.value = '';
            return;
        }

        for (let file of files) {
            if (file.size > maxFileSize) {
                alert('Kích thước tệp không được vượt quá 5MB');
                e.target.value = '';
                return;
            }
        }
    });
</script>
</body>
</html>