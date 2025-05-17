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
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #e6f0fa 100%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }
        .container-p-y {
            padding: 2.5rem;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            max-width: 1400px;
            margin: 2rem auto;
        }
        h4.fw-bold {
            font-size: 2rem;
            font-weight: 700;
            color: #1a3c34;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            animation: slideInDown 0.8s ease-in-out;
        }
        h4.fw-bold::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #007bff, #00c4b4);
            border-radius: 5px;
        }
        @keyframes slideInDown {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
        }
        .table thead {
            background: linear-gradient(45deg, #007bff, #00c4b4);
            color: #fff;
        }
        .table th, .table td {
            padding: 1.2rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
            color: #2c3e50; /* Darker text color */
            font-weight: 500;
        }
        .table th {
            font-weight: 700;
            font-size: 1.1rem;
        }
        .table tbody tr {
            transition: background-color 0.3s ease;
        }
        .table tbody tr:hover {
            background-color: #f1f7ff;
        }
        .table .booking-count {
            font-weight: 600;
            color: #28a745;
            font-size: 1.1rem;
        }
        .text-center.text-muted {
            color: #495057; /* Darker muted text */
            font-size: 1.2rem;
            font-style: italic;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #e4606d);
            border: none;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-danger:hover {
            background: linear-gradient(45deg, #c82333, #d43f4b);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }
        .alert {
            border-radius: 8px;
            padding: 1rem;
            font-size: 1rem;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.5s ease-in-out;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .container-p-y {
                padding: 1.5rem;
                margin: 1.5rem auto;
            }
            h4.fw-bold {
                font-size: 1.8rem;
            }
            .table th, .table td {
                padding: 1rem;
                font-size: 0.95rem;
            }
        }
        @media (max-width: 768px) {
            .container-p-y {
                padding: 1rem;
                margin: 1rem auto;
            }
            h4.fw-bold {
                font-size: 1.6rem;
            }
            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .table th, .table td {
                font-size: 0.9rem;
                padding: 0.8rem;
            }
            .btn-danger {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
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
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($approvedNurses)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Không có y tá nào đã được phê duyệt.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($approvedNurses as $nurse): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($nurse['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($nurse['email']); ?></td>
                                                <td><?php echo htmlspecialchars($nurse['phone_number'] ?? 'Không có thông tin'); ?></td>
                                                <td><?php echo htmlspecialchars($nurse['address'] ?? 'Không có thông tin'); ?></td>
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