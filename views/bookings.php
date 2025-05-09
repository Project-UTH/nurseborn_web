<?php
// Hiển thị lỗi trực tiếp để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$bookings = isset($bookings) ? $bookings : [];
$pageTitle = 'Danh Sách Lịch Đặt';
$baseUrl = '/nurseborn';
error_log("Debug: Đã vào file bookings.php");
error_log("Debug: Dữ liệu bookings trong bookings.php: " . print_r($bookings, true));
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
            max-width: 1300px;
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
        h5.card-header {
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

        /* Card booking */
        .booking-card .card {
            border: none;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
        }
        .booking-card .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .booking-card .card-body {
            padding: 25px;
        }
        .booking-card .card-title {
            color: #007bff;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 0.3px;
        }
        .booking-card .card-text {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1.05rem;
            color: #5a6169;
        }
        .booking-card .card-text strong {
            color: #2c3e50;
            width: 170px;
            font-weight: 600;
        }
        .booking-card .card-text i {
            margin-right: 12px;
            color: #007bff;
            font-size: 1.3rem;
            opacity: 0.8;
        }

        /* Trạng thái */
        .status-pending {
            color: #ffc107;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .status-accepted {
            color: #28a745;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .status-completed {
            color: #17a2b8;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .status-cancelled {
            color: #dc3545;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        /* Nút Đánh giá và Hủy */
        .btn-feedback {
            background: linear-gradient(45deg, #17a2b8, #138496);
            border: none;
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: 600;
            font-size: 1.05rem;
            color: #fff;
            margin: 5px;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-feedback:hover {
            background: linear-gradient(45deg, #138496, #0f6f7e);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(23, 162, 184, 0.3);
        }
        .btn-cancel {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: 600;
            font-size: 1.05rem;
            color: #fff;
            margin: 5px;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-cancel:hover {
            background: linear-gradient(45deg, #c82333, #bd2130);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.3);
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

        /* Khi không có lịch đặt */
        .text-center {
            color: #6c757d;
            font-size: 1.3rem;
            padding: 30px 0;
            font-weight: 500;
            background-color: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Thông báo "Đã đánh giá" */
        .feedback-status {
            text-align: center;
            font-size: 1.05rem;
            font-weight: 600;
            color: #28a745;
            background-color: #e6ffed;
            border-radius: 30px;
            padding: 8px 20px;
            margin: 5px;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
        }
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include __DIR__ . '/fragments/menu-family.php'; ?>
        <div class="layout-page">
            <?php include __DIR__ . '/fragments/navbar-nurse.php'; ?>
            <div class="content-wrapper">
                <div class="content-xxl flex-grow-1 container-p-y">
                    <div class="card mb-4">
                        <h5 class="card-header text-center">Danh Sách Lịch Đặt</h5>
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
                            <!-- Bookings Display -->
                            <?php if (!empty($bookings)): ?>
                                <div class="row">
                                    <?php foreach ($bookings as $booking): ?>
                                        <div class="col-md-4 mb-4 booking-card">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        Y tá: <?php echo htmlspecialchars($booking['nurse_full_name'] ?? 'ID: ' . $booking['nurse_user_id']); ?>
                                                    </h5>
                                                    <p class="card-text">
                                                        <i class="fas fa-id-badge"></i>
                                                        <strong>ID Booking:</strong>
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
                                                    <?php if ($booking['service_type'] === 'HOURLY' && $booking['start_time'] && $booking['end_time']): ?>
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
                                                        <span class="status-<?php echo strtolower($booking['status']); ?>">
                                                            <?php echo htmlspecialchars($booking['status']); ?>
                                                        </span>
                                                    </p>
                                                    <p class="card-text">
                                                        <i class="fas fa-sticky-note"></i>
                                                        <strong>Ghi chú:</strong>
                                                        <?php echo htmlspecialchars($booking['notes'] ?? 'Không có ghi chú'); ?>
                                                    </p>
                                                    <!-- Nút Đánh giá cho trạng thái COMPLETED và chưa được đánh giá -->
                                                    <?php if ($booking['status'] === 'COMPLETED' && !$booking['has_feedback']): ?>
                                                        <div class="text-center">
                                                            <a href="?action=feedback&booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-feedback">Đánh giá</a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <!-- Hiển thị thông báo "Đã đánh giá" nếu booking đã được đánh giá -->
                                                    <?php if ($booking['status'] === 'COMPLETED' && $booking['has_feedback']): ?>
                                                        <div class="text-center">
                                                            <span class="feedback-status">Đã đánh giá</span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <!-- Nút Hủy cho trạng thái PENDING -->
                                                    <?php if ($booking['status'] === 'PENDING'): ?>
                                                        <div class="text-center">
                                                            <form action="?action=cancel_booking&booking_id=<?php echo $booking['booking_id']; ?>" method="post" style="display:inline;">
                                                                <button type="submit" class="btn btn-cancel">Hủy</button>
                                                            </form>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <p>Không có lịch đặt nào.</p>
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
</body>
</html>