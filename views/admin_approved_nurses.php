<?php
$baseUrl = '/nurseborn/';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$approvedNurses = $approvedNurses ?? [];
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>static/assets/" data-template="vertical-menu-template-free">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <style>
        .container-p-y {
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        h4 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1a3c34;
            margin-bottom: 1.5rem;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .table thead {
            background-color: #007bff;
            color: #fff;
        }
        .table th, .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .table th {
            font-weight: 600;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .table .booking-count {
            font-weight: 600;
            color: #28a745;
        }
        .text-center.text-muted {
            color: #6c757d;
            font-size: 1.1rem;
            font-style: italic;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>

<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu (Sidebar) -->
        <?php include __DIR__ . '/fragments/menu-admin.php'; ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->
            <?php include __DIR__ . '/fragments/navbar.php'; ?>
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <!-- Hiển thị thông báo thành công hoặc lỗi -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <h4 class="fw-bold py-3 mb-4">Danh Sách Y Tá Đã Phê Duyệt</h4>
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tên Y Tá</th>
                                        <th>Email</th>
                                        <th>Số Điện Thoại</th>
                                        <th>Địa Chỉ</th>
                                        <th>Số Lượng Đơn Hoàn Thành</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($approvedNurses)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Không có y tá nào đã được phê duyệt.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($approvedNurses as $nurse): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($nurse['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($nurse['email']); ?></td>
                                                <td><?php echo htmlspecialchars($nurse['phone_number'] ?? 'Không có thông tin'); ?></td>
                                                <td><?php echo htmlspecialchars($nurse['address'] ?? 'Không có thông tin'); ?></td>
                                                <td class="booking-count"><?php echo htmlspecialchars($nurse['booking_count']); ?></td>
                                                <td>
                                                    <form method="POST" action="?action=admin_approved_nurses" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản y tá này?');">
                                                        <input type="hidden" name="action_type" value="delete_nurse">
                                                        <input type="hidden" name="nurse_user_id" value="<?php echo htmlspecialchars($nurse['user_id']); ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- / Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/popper/popper.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/js/menu.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/js/main.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/js/dashboards-analytics.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>