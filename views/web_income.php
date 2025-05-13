<?php
$baseUrl = '/nurseborn/';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$webIncomeData = $webIncomeData ?? [];
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>static/assets/" data-template="vertical-menu-template-free">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        if (typeof Chart === 'undefined') {
            console.error('Chart.js không tải được từ CDN');
            document.write('<script src="<?php echo $baseUrl; ?>static/assets/js/chart.min.js"><\/script>');
        }
    </script>
    <style>
        /* Giữ nguyên CSS như bạn đã cung cấp */
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #e6f0fa 100%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }
        .container-xxl {
            max-width: 1200px;
            padding: 50px 20px;
        }
        h4.fw-bold {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            position: relative;
            margin-bottom: 40px;
            text-align: center;
            animation: slideInDown 1s ease-in-out;
        }
        h4.fw-bold::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            border-radius: 5px;
        }
        @keyframes slideInDown {
            0% { opacity: 0; transform: translateY(-30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .overview-card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUpKILL 0.8s ease-in-out;
            color: #fff;
        }
        .overview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }
        .overview-card .card-body {
            padding: 20px;
            text-align: center;
        }
        .overview-card .card-title {
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 10px;
        }
        .overview-card .card-text {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .family-nurse-card .card-text {
            color: #000 !important;
        }
        .bg-info { background: linear-gradient(45deg, #17a2b8, #00c4b4) !important; }
        .bg-success { background: linear-gradient(45deg, #28a745, #34c759) !important; }
        .bg-warning { background: linear-gradient(45deg, #ffc107, #ffca2c) !important; }
        .bg-danger { background: linear-gradient(45deg, #dc3545, #e4606d) !important; }
        .filter-section {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 30px;
            animation: fadeInUp 1s ease-in-out;
        }
        .filter-section .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .filter-section .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        .filter-section .alert-info {
            background: rgba(209, 236, 241, 0.5);
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 1rem;
            color: #0c5460;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .chart-card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            animation: fadeInUp 1.2s ease-in-out;
        }
        .chart-card .card-body {
            padding: 30px;
        }
        .chart-card .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }
        .chart-card canvas {
            max-height: 400px;
            width: 100% !important;
        }
        @media (max-width: 992px) {
            .container-xxl { padding: 40px 15px; }
            h4.fw-bold { font-size: 2rem; }
            .overview-card .card-body { padding: 15px; }
            .overview-card .card-title { font-size: 1.1rem; }
            .overview-card .card-text { font-size: 1.3rem; }
            .filter-section { padding: 15px; }
            .filter-section .form-control { font-size: 0.9rem; padding: 8px; }
            .chart-card .card-body { padding: 20px; }
            .chart-card .card-title { font-size: 1.3rem; }
        }
        @media (max-width: 768px) {
            .container-xxl { padding: 30px 10px; }
            h4.fw-bold { font-size: 1.8rem; }
            .overview-card .card-title { font-size: 1rem; }
            .overview-card .card-text { font-size: 1.2rem; }
            .filter-section .alert-info { font-size: 0.9rem; }
            .chart-card .card-title { font-size: 1.2rem; }
        }
        @media (max-width: 576px) {
            .chart-card .card-body { padding: 15px; }
            .chart-card canvas { max-height: 300px; }
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
                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4">Thống Kê Người Dùng</h4>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <div class="overview-card family-nurse-card">
                                <div class="card-body">
                                    <h5 class="card-title">Tổng Gia Đình</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($webIncomeData['familyCount'] ?? 0); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="overview-card family-nurse-card">
                                <div class="card-body">
                                    <h5 class="card-title">Tổng Y Tá</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($webIncomeData['nurseCount'] ?? 0); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="fw-bold py-3 mb-4">
                        Thống Kê Doanh Thu
                        <?php
                        $filterTitle = '';
                        if (!empty($webIncomeData['filterType']) && !empty($webIncomeData['filterValue'])) {
                            if ($webIncomeData['filterType'] === 'weekly' && preg_match('/^\d{4}-W\d{2}$/', $webIncomeData['filterValue'])) {
                                list($year, $week) = explode('-W', $webIncomeData['filterValue']);
                                $filterTitle = "Tuần $week Năm $year";
                            } elseif ($webIncomeData['filterType'] === 'monthly' && preg_match('/^\d{4}-\d{2}$/', $webIncomeData['filterValue'])) {
                                list($year, $month) = explode('-', $webIncomeData['filterValue']);
                                $filterTitle = "Tháng $month Năm $year";
                            } elseif ($webIncomeData['filterType'] === 'yearly' && preg_match('/^\d{4}$/', $webIncomeData['filterValue'])) {
                                $filterTitle = "Năm " . $webIncomeData['filterValue'];
                            }
                            echo !empty($filterTitle) ? " - " . htmlspecialchars($filterTitle) : '';
                        }
                        ?>
                    </h4>

                    <div class="filter-section">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <select class="form-control" id="filterType" onchange="onFilterChange()">
                                    <option value="weekly" <?php echo ($webIncomeData['filterType'] === 'weekly') ? 'selected' : ''; ?>>Theo Tuần</option>
                                    <option value="monthly" <?php echo ($webIncomeData['filterType'] === 'monthly') ? 'selected' : ''; ?>>Theo Tháng</option>
                                    <option value="yearly" <?php echo ($webIncomeData['filterType'] === 'yearly') ? 'selected' : ''; ?>>Theo Năm</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="datePickerContainer"></div>
                            <div class="col-md-4">
                                <div class="alert alert-info" id="selectedTime" style="display: <?php echo (!empty($filterTitle)) ? 'flex' : 'none'; ?>;">
                                    <i class="fas fa-filter me-2"></i> Đang xem thống kê: <span id="selectedTimeValue"><?php echo htmlspecialchars($filterTitle); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-3 col-sm-6">
                            <div class="overview-card bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">Số Lượng Đặt Lịch</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($webIncomeData['bookingCount'] ?? 0); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="overview-card bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">Thu Nhập Web</h5>
                                    <p class="card-text"><?php echo number_format($webIncomeData['webIncome'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="overview-card bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Thu Nhập Thuần của Y Tá</h5>
                                    <p class="card-text"><?php echo number_format($webIncomeData['nurseIncome'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="overview-card bg-danger">
                                <div class="card-body">
                                    <h5 class="card-title">Thu Nhập Y Tá Sau Chiết Khấu</h5>
                                    <p class="card-text"><?php echo number_format($webIncomeData['nurseAfterDiscount'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="card-body">
                            <h5 class="card-title">Biểu Đồ Thu Nhập</h5>
                            <canvas id="incomeChart" style="max-height: 400px; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
</div>

<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/popper/popper.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/js/menu.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/js/main.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/js/dashboards-analytics.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script>
    let chartLabels = <?php echo json_encode($webIncomeData['chartLabels'] ?? []); ?>;
    let chartData = <?php echo json_encode($webIncomeData['chartData'] ?? []); ?>;

    function onFilterChange() {
        const filterType = document.getElementById("filterType").value;
        const container = document.getElementById("datePickerContainer");
        container.innerHTML = "";

        let defaultValue = '<?php echo htmlspecialchars($webIncomeData['filterValue'] ?? ''); ?>';
        if (filterType === "weekly") {
            container.innerHTML = `<input type="week" id="filterWeek" class="form-control" value="${defaultValue}" onchange="applyFilter()">`;
        } else if (filterType === "monthly") {
            container.innerHTML = `<input type="month" id="filterMonth" class="form-control" value="${defaultValue}" onchange="applyFilter()">`;
        } else if (filterType === "yearly") {
            container.innerHTML = `
                <select id="filterYear" class="form-control" onchange="applyFilter()">
                    ${generateYearOptions(2020, new Date().getFullYear())}
                </select>`;
            if (defaultValue) {
                document.getElementById("filterYear").value = defaultValue;
            }
        }

        const selectedTime = document.getElementById("selectedTime");
        const selectedTimeValue = document.getElementById("selectedTimeValue");
        if (defaultValue && '<?php echo htmlspecialchars($filterTitle); ?>') {
            selectedTime.style.display = "flex";
            selectedTimeValue.innerText = '<?php echo htmlspecialchars($filterTitle); ?>';
        } else {
            selectedTime.style.display = "none";
        }
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
            value = document.getElementById("filterWeek")?.value || "";
            if (value) {
                const [year, week] = value.split("-W");
                displayText = `Tuần ${week} Năm ${year}`;
            }
        } else if (type === "monthly") {
            value = document.getElementById("filterMonth")?.value || "";
            if (value) {
                const [year, month] = value.split("-");
                displayText = `Tháng ${month} Năm ${year}`;
            }
        } else if (type === "yearly") {
            value = document.getElementById("filterYear")?.value || "";
            if (value) {
                displayText = `Năm ${value}`;
            }
        }

        const selectedTime = document.getElementById("selectedTime");
        const selectedTimeValue = document.getElementById("selectedTimeValue");
        if (!value) {
            selectedTime.style.display = "none";
            selectedTimeValue.innerText = "";
            return;
        }

        selectedTime.style.display = "flex";
        selectedTimeValue.innerText = displayText;

        window.location.href = `?action=web_income&filterType=${encodeURIComponent(type)}&filterValue=${encodeURIComponent(value)}`;
    }

    const ctx = document.getElementById('incomeChart').getContext('2d');
    const incomeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Thu Nhập Y Tá (VNĐ)',
                data: chartData,
                backgroundColor: 'rgba(52, 152, 219, 0.2)',
                borderColor: 'rgba(52, 152, 219, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => value.toLocaleString() + " VNĐ",
                        font: { size: 14 }
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    ticks: { font: { size: 14 } },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: {
                    labels: { font: { size: 14 } }
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