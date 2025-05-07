<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
$pendingBookings = isset($pendingBookings) ? $pendingBookings : [];
$pageTitle = 'Lịch Đặt Chờ Xác Nhận';
$baseUrl = '/nurseborn';
require_once __DIR__ . '/../models/FamilyProfileModel.php';
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
            background-color: #f5f7fa;
        }
        .container {
            max-width: 1200px;
        }

        /* Tiêu đề danh sách y tá */
        h5.card-header.text-center {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, #0d6efd, #28a745);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
            margin-bottom: 40px;
            padding: 10px 0;
            animation: fadeIn 1s ease-in-out;
        }
        h5.card-header.text-center::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(45deg, #0d6efd, #28a745);
            border-radius: 2px;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Thẻ lịch đặt */
        .pending-card .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .pending-card .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .card-title {
            color: #0d6efd;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .card-text {
            color: #6c757d;
            font-size: 0.95rem;
        }
        .card-text strong {
            color: #343a40;
        }
        .btn-accept {
            background-color: #28a745;
            border-color: #28a745;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            color: #ffffff;
        }
        .btn-accept:hover {
            background-color: #218838;
            border-color: #1e7e34;
            color: #ffffff;
        }
        .btn-cancel {
            background-color: #dc3545;
            border-color: #dc3545;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            color: #ffffff;
        }
        .btn-cancel:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: #ffffff;
        }
        .btn-chat {
            background-color: #4e73df;
            border-color: #4e73df;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            color: #ffffff;
        }
        .btn-chat:hover {
            background-color: #3b5bdb;
            border-color: #3b5bdb;
            color: #ffffff;
        }
        .text-center {
            color: #6c757d;
            font-size: 1rem;
        }
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
        .d-flex.gap-2 {
            gap: 10px;
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
                <div class="container">
                    <div class="card mb-4">
                        <h5 class="card-header text-center">Lịch Đặt Chờ Xác Nhận</h5>
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
                            <?php if (empty($pendingBookings)): ?>
                                <div class="text-center">
                                    <p>Không có lịch đặt nào chờ xác nhận.</p>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($pendingBookings as $booking): ?>
                                        <?php
                                        $familyUser = $userModel->getUserById($booking['family_user_id']);
                                        $familyProfile = $familyProfileModel->getFamilyProfileByUserId($booking['family_user_id']);
                                        ?>
                                        <div class="col-md-4 mb-4 pending-card">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($familyUser['full_name'] ?? 'N/A'); ?></h5>
                                                    <p class="card-text"><strong>ID Booking:</strong> <?php echo htmlspecialchars($booking['booking_id']); ?></p>
                                                    <p class="card-text"><strong>Tên trẻ:</strong> <?php echo htmlspecialchars($familyProfile['child_name'] ?? 'N/A'); ?></p>
                                                    <p class="card-text"><strong>Tuổi trẻ:</strong> <?php echo htmlspecialchars($familyProfile['child_age'] ?? 'N/A'); ?></p>
                                                    <p class="card-text"><strong>Vị trí:</strong> <?php echo htmlspecialchars($familyProfile['preferred_location'] ?? 'N/A'); ?></p>
                                                    <p class="card-text"><strong>Ngày đặt:</strong> <?php echo htmlspecialchars($booking['booking_date']); ?></p>
                                                    <p class="card-text"><strong>Loại dịch vụ:</strong> <?php echo htmlspecialchars($booking['service_type']); ?></p>
                                                    <?php if ($booking['service_type'] === 'HOURLY'): ?>
                                                        <p class="card-text"><strong>Giờ bắt đầu:</strong> <?php echo htmlspecialchars($booking['start_time'] ?? 'N/A'); ?></p>
                                                        <p class="card-text"><strong>Giờ kết thúc:</strong> <?php echo htmlspecialchars($booking['end_time'] ?? 'N/A'); ?></p>
                                                    <?php endif; ?>
                                                    <p class="card-text"><strong>Giá:</strong> <?php echo number_format($booking['price']) . ' VND'; ?></p>
                                                    <p class="card-text"><strong>Ghi chú:</strong> <?php echo htmlspecialchars($booking['notes'] ?? 'Không có ghi chú'); ?></p>
                                                    <div class="text-center d-flex gap-2 justify-content-center">
                                                        <form action="?action=accept_booking" method="post" style="display:inline;">
                                                            <input type="hidden" name="bookingId" value="<?php echo $booking['booking_id']; ?>">
                                                            <button type="submit" class="btn btn-accept">Chấp nhận</button>
                                                        </form>
                                                        <form action="?action=cancel_booking" method="post" style="display:inline;">
                                                            <input type="hidden" name="bookingId" value="<?php echo $booking['booking_id']; ?>">
                                                            <button type="submit" class="btn btn-cancel">Hủy</button>
                                                        </form>
                                                        <a href="?action=messages&nurseUserId=<?php echo $booking['family_user_id']; ?>" class="btn btn-chat">Trò chuyện</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
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
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
</body>
</html>