<?php
require_once __DIR__ . '/../models/FamilyProfileModel.php';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
$availability = isset($availability) ? $availability : ['selected_days' => []];
$weekDates = isset($weekDates) ? $weekDates : [];
$bookingsByDate = isset($bookingsByDate) ? $bookingsByDate : [];
$currentDate = isset($currentDate) ? $currentDate : new DateTime();
$weekOffset = isset($weekOffset) ? $weekOffset : 0;
$pageTitle = 'Lịch Làm Việc';
$baseUrl = '/nurseborn';
$daysOfWeekMapping = ['MONDAY' => 'Thứ Hai', 'TUESDAY' => 'Thứ Ba', 'WEDNESDAY' => 'Thứ Tư', 'THURSDAY' => 'Thứ Năm', 'FRIDAY' => 'Thứ Sáu', 'SATURDAY' => 'Thứ Bảy', 'SUNDAY' => 'Chủ Nhật'];

// Khởi tạo FamilyProfileModel để lấy thông tin khách hàng
$familyProfileModel = new FamilyProfileModel($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Tùy chỉnh tổng thể */
        body {
            background-color: #f7f9fc;
            font-family: 'Poppins', sans-serif;
        }
        .container-p-y {
            max-width: 1200px;
        }

        /* Tiêu đề */
        h5.card-header.text-center {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, #0d6efd, #28a745);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            margin-bottom: 40px;
            padding: 15px 0;
            animation: fadeIn 1s ease-in-out;
        }
        h5.card-header.text-center::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 5px;
            background: linear-gradient(45deg, #0d6efd, #28a745);
            border-radius: 3px;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Card chứa bảng lịch */
        .card.mb-4 {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(145deg, #ffffff, #f0f4f8);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card.mb-4:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }
        .card-body {
            padding: 30px;
        }

        /* Thông báo lỗi và thành công */
        .alert-danger, .alert-success {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .btn-close {
            filter: opacity(0.6);
        }
        .btn-close:hover {
            filter: opacity(1);
        }

        /* Nút điều hướng tuần */
        .text-center.mb-3 {
            margin-bottom: 30px;
        }
        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #28a745);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 500;
            color: #fff;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #218838);
            transform: scale(1.05);
            color: #fff;
        }
        .btn-primary i {
            margin-right: 5px;
        }
        .btn-primary.me-2 i {
            margin-right: 5px;
        }

        /* Bảng lịch */
        .calendar-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .calendar-table th, .calendar-table td {
            border: 1px solid #e5e7eb;
            padding: 15px;
            text-align: center;
            vertical-align: top;
            min-height: 150px;
            transition: background-color 0.3s ease;
        }
        .calendar-table th {
            background: linear-gradient(145deg, #f4f4f4, #e5e7eb);
            font-weight: 600;
            color: #343a40;
            font-size: 1.1rem;
        }
        .calendar-table td {
            background-color: #fafafa;
        }
        .calendar-table td:hover {
            background-color: #f1f3f5;
        }
        .working-day {
            background-color: #d4edda !important; /* Màu xanh nhạt cho ngày làm việc */
        }
        .current-day {
            border: 3px solid #dc3545 !important; /* Viền đỏ cho ngày hiện tại */
            background-color: #fef2f2 !important;
        }

        /* Mục đặt lịch */
        .booking-item {
            background: linear-gradient(145deg, #e9ecef, #dee2e6);
            border-radius: 8px;
            padding: 10px;
            margin: 8px 0;
            font-size: 0.9rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }
        .booking-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .booking-item strong {
            color: #343a40;
            font-weight: 600;
        }
        .booking-item i {
            color: #0d6efd;
            margin-right: 5px;
        }

        /* Nút Hoàn thành và Trò chuyện */
        .btn-complete {
            background: linear-gradient(45deg, #28a745, #34c759);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            color: #fff;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-complete:hover {
            background: linear-gradient(45deg, #218838, #2eb44f);
            transform: scale(1.05);
            color: #fff;
        }
        .btn-chat {
            background: linear-gradient(45deg, #4299e1, #6ab7f5);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            color: #fff;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-chat:hover {
            background: linear-gradient(45deg, #3182ce, #59a5f0);
            transform: scale(1.05);
            color: #fff;
        }
        .btn i {
            margin-right: 5px;
        }
        .text-center.mt-2 {
            margin-top: 10px;
        }

        /* Thông báo không có lịch */
        .booking-item p {
            color: #6c757d;
            font-size: 0.95rem;
            font-style: italic;
        }

        /* Khoảng cách và bố cục */
        .text-center.mt-4 {
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include __DIR__ . '/fragments/menu-nurse.php'; ?>
        <div class="layout-page">
            <?php include __DIR__ . '/fragments/navbar-nurse.php'; ?>
            <div class="content-wrapper">
                <div class="container-p-y">
                    <div class="card mb-4">
                        <h5 class="card-header text-center">Lịch Làm Việc</h5>
                        <div class="card-body">
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <div class="text-center mb-3">
                                <a href="?action=nurse_schedule&weekOffset=<?php echo $weekOffset - 1; ?>" class="btn btn-primary me-2">
                                    <i class="fas fa-chevron-left"></i> Tuần Trước
                                </a>
                                <a href="?action=nurse_schedule&weekOffset=<?php echo $weekOffset + 1; ?>" class="btn btn-primary">
                                    <i class="fas fa-chevron-right"></i> Tuần Sau
                                </a>
                            </div>
                            <table class="calendar-table">
                                <thead>
                                    <tr>
                                        <?php foreach ($weekDates as $date): ?>
                                            <th><?php echo htmlspecialchars($daysOfWeekMapping[strtoupper($date->format('l'))]) . '<br>' . $date->format('d/m/Y'); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php foreach ($weekDates as $date): ?>
                                            <td class="<?php echo in_array($daysOfWeekMapping[strtoupper($date->format('l'))], $availability['selected_days']) ? 'working-day' : ''; ?> <?php echo $date->format('Y-m-d') === $currentDate->format('Y-m-d') ? 'current-day' : ''; ?>">
                                                <?php
                                                $dateKey = $date->format('Y-m-d');
                                                if (isset($bookingsByDate[$dateKey]) && !empty($bookingsByDate[$dateKey])):
                                                    foreach ($bookingsByDate[$dateKey] as $booking):
                                                        $familyUser = $userModel->getUserById($booking['family_user_id']);
                                                        $familyProfile = $familyProfileModel->getFamilyProfileByUserId($booking['family_user_id']);
                                                ?>
                                                        <div class="booking-item">
                                                            <div>
                                                                <i class="fas fa-id-badge"></i>
                                                                <strong>ID Booking:</strong>
                                                                <?php echo htmlspecialchars($booking['booking_id']); ?>
                                                            </div>
                                                            <div>
                                                                <i class="fas fa-user"></i>
                                                                <strong>Khách hàng:</strong>
                                                                <?php echo htmlspecialchars($familyUser['full_name'] ?? 'N/A'); ?>
                                                            </div>
                                                            <div>
                                                                <i class="fas fa-baby"></i>
                                                                <strong>Tên trẻ:</strong>
                                                                <?php echo htmlspecialchars($familyProfile['child_name'] ?? 'N/A'); ?>
                                                            </div>
                                                            <div>
                                                                <i class="fas fa-child"></i>
                                                                <strong>Tuổi trẻ:</strong>
                                                                <?php echo htmlspecialchars($familyProfile['child_age'] ?? 'N/A'); ?>
                                                            </div>
                                                            <div>
                                                                <i class="fas fa-map-marker-alt"></i>
                                                                <strong>Vị trí:</strong>
                                                                <?php echo htmlspecialchars($familyProfile['preferred_location'] ?? 'N/A'); ?>
                                                            </div>
                                                            <div>
                                                                <i class="fas fa-concierge-bell"></i>
                                                                <strong>Loại dịch vụ:</strong>
                                                                <?php echo htmlspecialchars($booking['service_type']); ?>
                                                            </div>
                                                            <?php if ($booking['service_type'] === 'HOURLY'): ?>
                                                                <div>
                                                                    <i class="fas fa-clock"></i>
                                                                    <strong>Giờ bắt đầu:</strong>
                                                                    <?php echo htmlspecialchars($booking['start_time'] ?? 'N/A'); ?>
                                                                </div>
                                                                <div>
                                                                    <i class="fas fa-clock"></i>
                                                                    <strong>Giờ kết thúc:</strong>
                                                                    <?php echo htmlspecialchars($booking['end_time'] ?? 'N/A'); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <i class="fas fa-money-bill-wave"></i>
                                                                <strong>Giá:</strong>
                                                                <?php echo number_format($booking['price']) . ' VND'; ?>
                                                            </div>
                                                            <div>
                                                                <i class="fas fa-sticky-note"></i>
                                                                <strong>Ghi chú:</strong>
                                                                <?php echo htmlspecialchars($booking['notes'] ?? 'Không có ghi chú'); ?>
                                                            </div>
                                                            <div class="text-center mt-2">
                                                                <form action="?action=complete_booking" method="post">
                                                                    <input type="hidden" name="bookingId" value="<?php echo $booking['booking_id']; ?>">
                                                                    <input type="hidden" name="weekOffset" value="<?php echo $weekOffset; ?>">
                                                                    <button type="submit" class="btn btn-complete">
                                                                        <i class="fas fa-check"></i> Hoàn thành
                                                                    </button>
                                                                </form>
                                                                <a href="?action=messages&nurseUserId=<?php echo $booking['family_user_id']; ?>" class="btn btn-chat mt-2">
                                                                    <i class="fas fa-comments"></i> Trò chuyện
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p>Không có lịch</p>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="text-center mt-4">
                                <a href="?action=nurse_availability" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Chỉnh Sửa Lịch Làm Việc
                                </a>
                            </div>
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
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
</body>
</html>