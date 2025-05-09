<?php
$baseUrl = '/nurseborn_web/';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$webIncomeData = $webIncomeData ?? [];
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
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
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
        <?php include __DIR__ . '/fragments/menu-admin.php'; ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <?php include __DIR__ . '/fragments/navbar.php'; ?>
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <!-- Thống kê người dùng -->
                    <h4 class="fw-bold py-3 mb-4">Thống Kê Người Dùng</h4>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Tổng Gia Đình</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($webIncomeData['familyCount'] ?? 0); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Tổng Y Tá</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($webIncomeData['nurseCount'] ?? 0); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tiêu đề động dựa trên bộ lọc -->
                    <h4 class="fw-bold py-3 mb-4">
                        Thống Kê Doanh Thu
                        <?php
                        $filterTitle = '';
                        if (!empty($webIncomeData['filterType']) && !empty($webIncomeData['filterValue'])) {
                            if ($webIncomeData['filterType'] === 'weekly') {
                                list($year, $week) = explode('-W', $webIncomeData['filterValue']);
                                $filterTitle = "Tuần $week Năm $year";
                            } elseif ($webIncomeData['filterType'] === 'monthly') {
                                list($year, $month) = explode('-', $webIncomeData['filterValue']);
                                $filterTitle = "Tháng $month Năm $year";
                            } elseif ($webIncomeData['filterType'] === 'yearly') {
                                $filterTitle = "Năm " . $webIncomeData['filterValue'];
                            }
                            echo " - " . htmlspecialchars($filterTitle);
                        }
                        ?>
                    </h4>

                    <!-- Thu nhập ngày hiện tại -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Thu Nhập Ngày Hôm Nay</h5>
                            <div class="row text-white">
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-info">
                                        <div class="card-body">
                                            <h6 class="card-title">Số Lượng Đặt Lịch</h6>
                                            <p class="card-text"><?php echo htmlspecialchars($webIncomeData['todayBookingCount'] ?? 0); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-success">
                                        <div class="card-body">
                                            <h6 class="card-title">Thu Nhập Web</h6>
                                            <p class="card-text"><?php echo number_format($webIncomeData['todayWebIncome'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-warning">
                                        <div class="card-body">
                                            <h6 class="card-title">Thu Nhập Thuần Y Tá</h6>
                                            <p class="card-text"><?php echo number_format($webIncomeData['todayNurseIncome'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-danger">
                                        <div class="card-body">
                                            <h6 class="card-title">Thu Nhập Y Tá Sau Chiết Khấu</h6>
                                            <p class="card-text"><?php echo number_format($webIncomeData['todayNurseAfterDiscount'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bộ lọc -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select class="form-control" id="filterType" onchange="onFilterChange()">
                                <option value="weekly" <?php echo ($webIncomeData['filterType'] === 'weekly') ? 'selected' : ''; ?>>Theo Tuần</option>
                                <option value="monthly" <?php echo ($webIncomeData['filterType'] === 'monthly') ? 'selected' : ''; ?>>Theo Tháng</option>
                                <option value="yearly" <?php echo ($webIncomeData['filterType'] === 'yearly') ? 'selected' : ''; ?>>Theo Năm</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="datePickerContainer"></div>
                        <!-- Hiển thị thời gian đã chọn -->
                        <div class="col-md-4">
                            <div class="alert alert-info" id="selectedTime" style="display: <?php echo (!empty($filterTitle)) ? 'block' : 'none'; ?>;">
                                Đang xem thống kê: <span id="selectedTimeValue"><?php echo htmlspecialchars($filterTitle); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê tổng quan -->
                    <div class="row text-white">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">Số Lượng Đặt Lịch</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($webIncomeData['bookingCount'] ?? 0); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">Thu Nhập Web</h5>
                                    <p class="card-text"><?php echo number_format($webIncomeData['webIncome'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Thu Nhập Thuần của Y Tá</h5>
                                    <p class="card-text"><?php echo number_format($webIncomeData['nurseIncome'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-danger">
                                <div class="card-body">
                                    <h5 class="card-title">Thu Nhập Y Tá Sau Chiết Khấu</h5>
                                    <p class="card-text"><?php echo number_format($webIncomeData['nurseAfterDiscount'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
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
    let chartLabels = <?php echo $webIncomeData['chartLabels'] ?? '[]'; ?>;
    let chartData = <?php echo $webIncomeData['chartData'] ?? '[]'; ?>;

    function onFilterChange() {
        const filterType = document.getElementById("filterType").value;
        const container = document.getElementById("datePickerContainer");
        container.innerHTML = "";

        if (filterType === "weekly") {
            container.innerHTML = `<input type="week" id="filterWeek" class="form-control" onchange="applyFilter()">`;
        } else if (filterType === "monthly") {
            container.innerHTML = `<input type="month" id="filterMonth" class="form-control" onchange="applyFilter()">`;
        } else if (filterType === "yearly") {
            container.innerHTML = `
                <select id="filterYear" class="form-control" onchange="applyFilter()">
                    ${generateYearOptions(2020, new Date().getFullYear())}
                </select>`;
        }

        // Reset thời gian đã chọn khi thay đổi loại bộ lọc
        document.getElementById("selectedTime").style.display = "none";
        document.getElementById("selectedTimeValue").innerText = "";
    }

    function generateYearOptions(start, end) {
        let options = "";
        for (let y = end; y >= start; y--) {
            options += `<option value="${y}">${y}</option>`;
        }
        return options;
    }

    function applyFilter() {
        const type = document.getElementById("filterType").value;
        let value = "";
        let displayText = "";

        if (type === "weekly") {
            value = document.getElementById("filterWeek").value;
            if (value) {
                const [year, week] = value.split("-W");
                displayText = `Tuần ${week} Năm ${year}`;
            }
        } else if (type === "monthly") {
            value = document.getElementById("filterMonth").value;
            if (value) {
                const [year, month] = value.split("-");
                displayText = `Tháng ${month} Năm ${year}`;
            }
        } else if (type === "yearly") {
            value = document.getElementById("filterYear").value;
            if (value) {
                displayText = `Năm ${value}`;
            }
        }

        if (!value) {
            document.getElementById("selectedTime").style.display = "none";
            return;
        }

        // Hiển thị thời gian đã chọn
        document.getElementById("selectedTime").style.display = "block";
        document.getElementById("selectedTimeValue").innerText = displayText;

        // Gửi yêu cầu tới server
        window.location.href = `?action=web_income&filterType=${type}&filterValue=${value}`;
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

    window.onload = () => {
        onFilterChange();
    };
</script>
</body>
</html>