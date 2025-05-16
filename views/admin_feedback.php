<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$pageTitle = 'Quản Lý Đánh Giá';
$baseUrl = '/nurseborn';
error_log("Debug: Đã vào file admin-feedback.php");
error_log("Debug: Dữ liệu user trong admin-feedback.php: " . print_r($user, true));

// $feedbacks và $filters được truyền từ AdminController
$feedbacks = isset($feedbacks) ? $feedbacks : [];
$filters = isset($filters) ? $filters : [
    'nurse_id' => null,
    'rating' => null,
    'start_date' => null,
    'end_date' => null,
];
error_log("Debug: Feedbacks trong admin-feedback.php: " . print_r($feedbacks, true));

// Lấy booking_date từ booking_id (nếu cần)
$bookingModel = new BookingModel($conn); // Đảm bảo $conn được truyền từ AdminController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php 
    error_log("Debug: Đang include head.php");
    include __DIR__ . '/fragments/head.php'; 
    ?>
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

        /* Filter Form */
        .filter-form {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filter-form .form-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-form label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: block;
        }

        .filter-form select,
        .filter-form input[type="date"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            font-size: 1rem;
            background-color: #f8f9fa;
        }

        .filter-form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }

        /* Feedback Table */
        .feedback-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .feedback-table th,
        .feedback-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .feedback-table th {
            background-color: #f8f9fa;
            font-weight: 700;
            color: #2c3e50;
        }

        .feedback-table td {
            color: #5a6169;
        }

        .feedback-table .rating-stars {
            color: #ffd700;
        }

        .feedback-table .actions form {
            display: inline-block;
        }

        .feedback-table .actions button {
            color: #dc3545;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            text-decoration: none;
        }

        .feedback-table .actions button:hover {
            text-decoration: underline;
        }

        .feedback-table .actions a {
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .feedback-table .actions a:hover {
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
    </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php 
        error_log("Debug: Đang include menu-admin.php");
        include __DIR__ . '/fragments/menu-admin.php'; 
        ?>
        <div class="layout-page">
            <?php 
            error_log("Debug: Đang include navbar.php");
            include __DIR__ . '/fragments/navbar.php'; 
            ?>
            <div class="content-wrapper">
                <div class="content-xxl flex-grow-1 container-p-y">
                    <div class="card">
                        <h5 class="card-header text-center">Quản Lý Đánh Giá</h5>
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

                            <!-- Filter Form -->
                            <form class="filter-form" method="GET" action="?action=admin_feedback">
                                <div class="form-group">
                                    <label for="nurse_id">Y tá</label>
                                    <select name="nurse_id" id="nurse_id">
                                        <option value="">Tất cả</option>
                                        <?php
                                        $nursesStmt = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'NURSE'");
                                        if ($nursesStmt) {
                                            while ($nurse = $nursesStmt->fetch_assoc()) {
                                                $selected = ($filters['nurse_id'] == $nurse['user_id']) ? 'selected' : '';
                                                echo "<option value='{$nurse['user_id']}' $selected>" . htmlspecialchars($nurse['full_name']) . "</option>";
                                            }
                                        } else {
                                            error_log("Debug: Lỗi truy vấn danh sách y tá: " . $conn->error);
                                            echo "<option value=''>Lỗi tải danh sách y tá</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="rating">Đánh giá</label>
                                    <select name="rating" id="rating">
                                        <option value="">Tất cả</option>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo ($filters['rating'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?> sao</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="start_date">Từ ngày</label>
                                    <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($filters['start_date'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="end_date">Đến ngày</label>
                                    <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($filters['end_date'] ?? ''); ?>">
                                </div>
                                <button type="submit">Lọc</button>
                            </form>

                            <!-- Feedback List -->
                            <?php if (!empty($feedbacks)): ?>
                                <table class="feedback-table">
                                    <thead>
                                        <tr>
                                            <th>Y tá</th>
                                            <th>Người đánh giá</th>
                                            <th>Đánh giá</th>
                                            <th>Nhận xét</th>
                                            <th>Ngày tạo</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($feedbacks as $feedback): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($feedback['nurse_full_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($feedback['family_full_name'] ?? 'N/A'); ?></td>
                                                <td class="rating-stars"><?php echo str_repeat('★', $feedback['rating'] ?? 0); ?></td>
                                                <td><?php echo htmlspecialchars($feedback['comment'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($feedback['created_at'] ?? ''); ?></td>
                                                <td class="actions">
                                                    <?php
                                                    // Lấy booking_date từ booking_id
                                                    $booking = !empty($feedback['booking_id']) ? $bookingModel->getBookingById($feedback['booking_id']) : null;
                                                    $bookingDate = $booking ? $booking['booking_date'] : null;
                                                    ?>
                                                    <?php if ($bookingDate): ?>
                                                        <a href="?action=admin_bookings&date=<?php echo urlencode($bookingDate); ?>">Xem lịch đặt</a>
                                                    <?php else: ?>
                                                        <span style="color: #dc3545;">Không có lịch đặt</span>
                                                    <?php endif; ?>
                                                    <form method="POST" action="?action=admin_feedback" style="display:inline;">
                                                        <input type="hidden" name="action_type" value="delete_feedback">
                                                        <input type="hidden" name="feedback_id" value="<?php echo $feedback['feedback_id'] ?? ''; ?>">
                                                        <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?')">Xóa</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center">
                                    <p>Không có đánh giá nào để hiển thị.</p>
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

