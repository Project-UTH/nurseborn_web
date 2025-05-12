<?php
$baseUrl = '/nurseborn_web/';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseIncomeData = $nurseIncomeData ?? [];
$pageTitle = 'Thống Kê Thu Nhập Y Tá';
?>
<!DOCTYPE html>
<html lang="vi" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>static/assets/" data-template="vertical-menu-template-free">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Thu Nhập Y Tá - NurseBorn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #22c55e;
            --text-color: #1f2a44;
            --muted-color: #6b7280;
            --card-bg: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --info-color: #17a2b8;
            --success-color: #28a745;
            --warning-color: #f59e0b;
            --danger-color: #dc3545;
        }

        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #dcfce7 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            margin: 0;
        }

        .container-p-y {
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

        h4.fw-bold {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        h4.fw-bold::after {
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
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 2rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1rem;
        }

        .card-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .bg-info {
            background: linear-gradient(45deg, var(--info-color), #4bc0c8) !important;
            color: #fff;
        }

        .bg-success {
            background: linear-gradient(45deg, var(--success-color), #34d399) !important;
            color: #fff;
        }

        .bg-warning {
            background: linear-gradient(45deg, var(--warning-color), #fbbf24) !important;
            color: #fff;
        }

        .bg-danger {
            background: linear-gradient(45deg, var(--danger-color), #ef4444) !important;
            color: #fff;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(37, 99, 235, 0.3);
            outline: none;
        }

        .btn {
            font-size: 1rem;
            font-weight: 500;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), #60a5fa);
            border: none;
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #1e40af, var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .row.mb-4 {
            margin-bottom: 2rem;
        }

        .canvas-container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .row.text-white .col-md-3 {
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 768px) {
            .container-p-y {
                padding: 1.5rem;
                margin: 1rem;
            }

            h4.fw-bold {
                font-size: 1.8rem;
            }

            .card-text {
                font-size: 1.25rem;
            }

            .form-control {
                font-size: 0.95rem;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .row.mb-4 {
                flex-direction: column;
                gap: 1rem;
            }

            .row.mb-4 .col-md-4 {
                width: 100%;
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
                <div class="container-p-y">
                    <h4 class="fw-bold py-3 mb-4">Thống Kê Thu Nhập Y Tá</h4>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <select class="form-control" id="filterType" onchange="onFilterChange()">
                                <option value="DAY" <?php echo ($nurseIncomeData['period'] === 'DAY') ? 'selected' : ''; ?>>Theo Ngày</option>
                                <option value="WEEK" <?php echo ($nurseIncomeData['period'] === 'WEEK') ? 'selected' : ''; ?>>Theo Tuần</option>
                                <option value="MONTH" <?php echo ($nurseIncomeData['period'] === 'MONTH') ? 'selected' : ''; ?>>Theo Tháng</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3" id="datePickerContainer"></div>
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-primary" onclick="applyFilter()"><i class="fas fa-filter"></i> Lọc Thống Kê</button>
                        </div>
                    </div>
                    <div class="row text-white">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-calendar-check"></i> Số Lượng Đặt Lịch</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($nurseIncomeData['bookingCount'] ?? 0); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-money-bill-wave"></i> Phí Nền Tảng</h5>
                                    <p class="card-text"><?php echo number_format($nurseIncomeData['platformFee'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-wallet"></i> Thu Nhập Thuần</h5>
                                    <p class="card-text"><?php echo number_format($nurseIncomeData['totalIncome'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-danger">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-hand-holding-usd"></i> Thu Nhập Sau Chiết Khấu</h5>
                                    <p class="card-text"><?php echo number_format($nurseIncomeData['netIncomeAfterFee'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-chart-line"></i> Biểu Đồ Thu Nhập</h5>
                            <div class="canvas-container">
                                <canvas id="incomeChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $baseUrl; ?>static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>static/assets/js/main.js"></script>
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
                backgroundColor: 'rgba(37, 99, 235, 0.2)',
                borderColor: 'rgba(37, 99, 235, 1)',
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