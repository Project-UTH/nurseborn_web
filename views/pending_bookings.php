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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Đặt Chờ Xác Nhận - NurseBorn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #22c55e;
            --text-color: #1f2a44;
            --muted-color: #6b7280;
            --card-bg: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --chat-color: #4299e1;
        }

        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #dcfce7 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h5.card-header {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        h5.card-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            background-color: var(--card-bg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 2rem;
        }

        .card-title {
            color: var(--primary-color);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .card-text {
            font-size: 0.95rem;
            color: var(--muted-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-text strong {
            color: var(--text-color);
            font-weight: 600;
            width: 120px;
        }

        .card-text i {
            color: var(--primary-color);
            font-size: 1rem;
        }

        .alert {
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert i {
            font-size: 1.2rem;
        }

        .btn {
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-accept {
            background: linear-gradient(45deg, var(--success-color), #34d399);
            border: none;
            color: #fff;
        }

        .btn-accept:hover {
            background: linear-gradient(45deg, #1e7e34, var(--success-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-cancel {
            background: linear-gradient(45deg, var(--danger-color), #ef4444);
            border: none;
            color: #fff;
        }

        .btn-cancel:hover {
            background: linear-gradient(45deg, #bd2130, var(--danger-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-chat {
            background: linear-gradient(45deg, var(--chat-color), #6ab7f5);
            border: none;
            color: #fff;
        }

        .btn-chat:hover {
            background: linear-gradient(45deg, #3b82ce, var(--chat-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .no-bookings {
            text-align: center;
            font-size: 1rem;
            color: var(--muted-color);
            margin: 2rem 0;
        }

        .d-flex.gap-2 {
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .pending-card .col-md-4 {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
                margin: 1rem;
            }

            h5.card-header {
                font-size: 1.8rem;
            }

            .card-text {
                font-size: 0.9rem;
            }

            .card-text strong {
                width: 100px;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .card-text {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.3rem;
            }

            .card-text strong {
                width: auto;
            }

            .d-flex.gap-2 {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
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
                    <div class="card">
                        <h5 class="card-header">Lịch Đặt Chờ Xác Nhận</h5>
                        <div class="card-body">
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (empty($pendingBookings)): ?>
                                <p class="no-bookings"><i class="fas fa-calendar-times"></i> Không có lịch đặt nào chờ xác nhận.</p>
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
                                                    <div class="card-text">
                                                        <i class="fas fa-id-badge"></i>
                                                        <strong>ID Booking:</strong>
                                                        <?php echo htmlspecialchars($booking['booking_id']); ?>
                                                    </div>
                                                    <div class="card-text">
                                                        <i class="fas fa-baby"></i>
                                                        <strong>Tên trẻ:</strong>
                                                        <?php echo htmlspecialchars($familyProfile['child_name'] ?? 'N/A'); ?>
                                                    </div>
                                                    <div class="card-text">
                                                        <i class="fas fa-child"></i>
                                                        <strong>Tuổi trẻ:</strong>
                                                        <?php echo htmlspecialchars($familyProfile['child_age'] ?? 'N/A'); ?>
                                                    </div>
                                                    <div class="card-text">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <strong>Vị trí:</strong>
                                                        <?php echo htmlspecialchars($familyProfile['preferred_location'] ?? 'N/A'); ?>
                                                    </div>
                                                    <div class="card-text">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <strong>Ngày đặt:</strong>
                                                        <?php echo htmlspecialchars($booking['booking_date']); ?>
                                                    </div>
                                                    <div class="card-text">
                                                        <i class="fas fa-concierge-bell"></i>
                                                        <strong>Loại dịch vụ:</strong>
                                                        <?php echo htmlspecialchars($booking['service_type']); ?>
                                                    </div>
                                                    <?php if ($booking['service_type'] === 'HOURLY'): ?>
                                                        <div class="card-text">
                                                            <i class="fas fa-clock"></i>
                                                            <strong>Giờ bắt đầu:</strong>
                                                            <?php echo htmlspecialchars($booking['start_time'] ?? 'N/A'); ?>
                                                        </div>
                                                        <div class="card-text">
                                                            <i class="fas fa-clock"></i>
                                                            <strong>Giờ kết thúc:</strong>
                                                            <?php echo htmlspecialchars($booking['end_time'] ?? 'N/A'); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="card-text">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                        <strong>Giá:</strong>
                                                        <?php echo number_format($booking['price'], 0, ',', '.') . ' VND'; ?>
                                                    </div>
                                                    <div class="card-text">
                                                        <i class="fas fa-sticky-note"></i>
                                                        <strong>Ghi chú:</strong>
                                                        <?php echo htmlspecialchars($booking['notes'] ?? 'Không có ghi chú'); ?>
                                                    </div>
                                                    <div class="text-center d-flex gap-2">
                                                        <form action="?action=accept_booking" method="post">
                                                            <input type="hidden" name="bookingId" value="<?php echo $booking['booking_id']; ?>">
                                                            <button type="submit" class="btn btn-accept"><i class="fas fa-check"></i> Chấp nhận</button>
                                                        </form>
                                                        <form action="?action=cancel_booking" method="post">
                                                            <input type="hidden" name="bookingId" value="<?php echo $booking['booking_id']; ?>">
                                                            <button type="submit" class="btn btn-cancel"><i class="fas fa-times"></i> Hủy</button>
                                                        </form>
                                                        <a href="?action=messages&nurseUserId=<?php echo $booking['family_user_id']; ?>" class="btn btn-chat"><i class="fas fa-comments"></i> Trò chuyện</a>
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
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
</body>
</html>