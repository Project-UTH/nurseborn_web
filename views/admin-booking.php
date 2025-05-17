<?php
// Hiển thị lỗi để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$bookings = isset($bookings) ? $bookings : [];
$feedbackController = new FeedbackController($conn); // Đảm bảo $conn được truyền từ AdminController
$pageTitle = 'Quản Lý Lịch Đặt';
$baseUrl = '/nurseborn';
error_log("Debug: Đã vào file admin-booking.php");
error_log("Debug: Dữ liệu bookings trong admin-booking.php: " . print_r($bookings, true));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to bottom, #e6f0fa, #f5f7fa);
            font-family: 'Segoe UI', 'Arial', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            background-color: #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card-header {
            background: linear-gradient(45deg, #007bff, #28a745);
            color: #fff;
            font-size: 1.8rem;
            font-weight: 600;
            text-align: center;
            padding: 20px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .card-body {
            padding: 30px;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .booking-table th,
        .booking-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .booking-table th {
            background-color: #f8f9fa;
            font-weight: 700;
            color: #2c3e50;
        }

        .booking-table td {
            color: #5a6169;
        }

        .booking-table .actions form {
            display: inline-block;
        }

        .booking-table .actions button {
            color: #dc3545;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            text-decoration: none;
        }

        .booking-table .actions button:hover {
            text-decoration: underline;
        }

        .text-center {
            text-align: center;
            margin-top: 20px;
        }

        .text-center a {
            color: #007bff;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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
            background: none;
            border: none;
            font-size: 1.2rem;
            color: inherit;
            opacity: 0.7;
        }

        .alert .btn-close:hover {
            opacity: 1;
        }

        /* Định dạng cho cột Trạng Thái */
        .status {
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 12px;
            display: inline-block;
            text-align: center;
        }
        .status-completed {
            background-color: #e6ffed;
            color: #28a745;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #d39e00;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #dc3545;
        }
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include __DIR__ . '/fragments/menu-admin.php'; ?>
        <div class="layout-page">
            <?php include __DIR__ . '/fragments/navbar.php'; ?>
            <div class="content-wrapper">
                <div class="content-xxl flex-grow-1 container-p-y">
                    <div class="card">
                        <h5 class="card-header text-center">Quản Lý Lịch Đặt</h5>
                        <div class="card-body">
                            <!-- Messages -->
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">×</button>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">×</button>
                                </div>
                            <?php endif; ?>

                            <!-- Booking List -->
                            <?php if (!empty($bookings)): ?>
                                <table class="booking-table">
                                    <thead>
                                        <tr>
                                            <th>Mã đặt lịch</th>
                                            <th>Tên y tá</th>
                                            <th>Tên khách hàng</th>
                                            <th>Ngày đặt</th>
                                            <th>Thời gian</th>
                                            <th>Giá</th>
                                            <th>Trạng Thái</th> <!-- Thêm cột Trạng Thái -->
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($booking['booking_id'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($booking['nurse_full_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($booking['family_full_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($booking['booking_date'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php
                                                    if (isset($booking['service_type']) && $booking['service_type'] === 'HOURLY') {
                                                        echo htmlspecialchars($booking['start_time'] ?? 'N/A') . ' - ' . htmlspecialchars($booking['end_time'] ?? 'N/A');
                                                    } else {
                                                        echo 'Cả ngày';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo number_format($booking['price'] ?? 0) . ' VND'; ?></td>
                                                <td>
                                                    <?php
                                                    $status = $booking['status'] ?? 'N/A';
                                                    $statusClass = '';
                                                    switch (strtoupper($status)) {
                                                        case 'COMPLETED':
                                                            $statusClass = 'status-completed';
                                                            $statusText = 'Hoàn thành';
                                                            break;
                                                        case 'ACCEPTED':
                                                            $statusClass = 'status-confirmed';
                                                            $statusText = 'Đã xác nhận';
                                                            break;
                                                        case 'PENDING':
                                                            $statusClass = 'status-pending';
                                                            $statusText = 'Đang chờ';
                                                            break;
                                                        case 'CANCELLED':
                                                            $statusClass = 'status-cancelled';
                                                            $statusText = 'Đã hủy';
                                                            break;
                                                        default:
                                                            $statusClass = '';
                                                            $statusText = $status;
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="status <?php echo $statusClass; ?>">
                                                        <?php echo htmlspecialchars($statusText); ?>
                                                    </span>
                                                </td>
                                                <td class="actions">
                                                    <form method="POST" action="?action=admin_bookings" style="display:inline;">
                                                        <input type="hidden" name="action_type" value="delete_booking">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id'] ?? ''; ?>">
                                                        <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa lịch đặt này?')">Xóa</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center">
                                    <p>Không có lịch đặt nào để hiển thị.</p>
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
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
</body>
</html>