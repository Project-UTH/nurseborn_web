<?php
$baseUrl = '/nurseborn_web/';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseIncomeData = $nurseIncomeData ?? [];
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>static/assets/" data-template="vertical-menu-template-free">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 500;
            color: #1a3c34;
            margin-bottom: 1rem;
        }
        .card-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 0.5rem;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .text-white .card {
            color: #fff;
        }
        .bg-info {
            background-color: #17a2b8 !important;
        }
        .bg-success {
            background-color: #28a745 !important;
        }
        .bg-warning {
            background-color: #ffc107 !important;
        }
        .bg-danger {
            background-color: #dc3545 !important;
        }
    </style>
</head>

<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu (Sidebar) -->
        <?php include __DIR__ . '/fragments/menu-nurse.php'; ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->
            <?php include __DIR__ . '/fragments/navbar-nurse.php'; ?>
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="content-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4">Thống Kê Thu Nhập Y Tá</h4>

                    <!-- Bộ lọc -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select class="form-control" id="filterType" onchange="onFilterChange()">
                                <option value="DAY" <?php echo ($nurseIncomeData['period'] === 'DAY') ? 'selected' : ''; ?>>Theo Ngày</option>
                                <option value="WEEK" <?php echo ($nurseIncomeData['period'] === 'WEEK') ? 'selected' : ''; ?>>Theo Tuần</option>
                                <option value="MONTH" <?php echo ($nurseIncomeData['period'] === 'MONTH') ? 'selected' : ''; ?>>Theo Tháng</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="datePickerContainer"></div>
                        <div class="col-md-4">
                            <button class="btn btn-primary btn-block" onclick="applyFilter()">Lọc Thống Kê</button>
                        </div>
                    </div>

                    <!-- Thống kê tổng quan -->
                    <div class="row text-white">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">Số Lượng Đặt Lịch</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($nurseIncomeData['bookingCount'] ?? 0); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">Phí Nền Tảng</h5>
                                    <p class="card-text"><?php echo number_format($nurseIncomeData['platformFee'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Thu Nhập Thuần</h5>
                                    <p class="card-text"><?php echo number_format($nurseIncomeData['totalIncome'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-danger">
                                <div class="card-body">
                                    <h5 class="card-title">Thu Nhập Sau Chiết Khấu</h5>
                                    <p class="card-text"><?php echo number_format($nurseIncomeData['netIncomeAfterFee'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Biểu đồ -->
                    <div class="card mt-5">
                        <div class="card-body">
                            <h5 class="card-title">Biểu Đồ Thu Nhập</h5>
                            <canvas id="incomeChart" height="100"></canvas>
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
<!-- Vendors JS -->
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<!-- Main JS -->
<script src="<?php echo $baseUrl; ?>static/assets/js/main.js"></script>
<!-- Page JS -->
<script src="<?php echo $baseUrl; ?>static/assets/js/dashboards-analytics.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script>
    let chartLabels = <?php echo $nurseIncomeData['chartLabels'] ?? '[]'; ?>;
    let chartData = <?php echo $nurseIncomeData['chartData'] ?? '[]'; ?>;

    function onFilterChange() {
        const filterType = document.getElementById("filterType").value;
        const container = document.getElementById("datePickerContainer");
        container.innerHTML = "";

        if (filterType === "DAY") {
            container.innerHTML = `<input type="date" id="specificDate" class="form-control" value="<?php echo htmlspecialchars($nurseIncomeData['specificDate'] ?? date('Y-m-d')); ?>">`;
        } else if (filterType === "WEEK") {
            container.innerHTML = `<input type="week" id="specificDate" class="form-control" value="<?php echo htmlspecialchars($nurseIncomeData['specificDate'] ?? date('Y-\WW')); ?>">`;
        } else if (filterType === "MONTH") {
            container.innerHTML = `<input type="month" id="specificDate" class="form-control" value="<?php echo htmlspecialchars($nurseIncomeData['specificDate'] ?? date('Y-m')); ?>">`;
        }
    }

    function applyFilter() {
        const type = document.getElementById("filterType").value;
        const value = document.getElementById("specificDate").value;

        if (!value) {
            alert("Vui lòng chọn thời gian thống kê!");
            return;
        }

        window.location.href = `?action=nurse_income&period=${type}&specificDate=${value}`;
    }

    const ctx = document.getElementById('incomeChart').getContext('2d');
    const incomeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Thu Nhập Y Tá (VNĐ)',
                data: chartData,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    ticks: {
                        beginAtZero: true,
                        callback: value => value.toLocaleString() + " VNĐ"
                    }
                }
            }
        }
    });

    window.onload = onFilterChange;
</script>
</body>
</html>