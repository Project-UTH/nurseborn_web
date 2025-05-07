<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$nurseProfile = isset($_SESSION['nurse_profile']) ? $_SESSION['nurse_profile'] : null;
$availability = isset($availability) && is_array($availability) ? $availability : ['user_id' => $user['user_id'] ?? null, 'selected_days' => []];
$pageTitle = 'Chọn Ngày Làm Việc';
$baseUrl = '/nurseborn';
$daysOfWeek = ['Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ Nhật'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Tùy chỉnh tổng thể */
        body {
            background-color: #f7f9fc;
            font-family: 'Poppins', sans-serif;
        }
        .container-p-y {
            max-width: 1200px;
        }

        /* Tiêu đề */
        h5.card-header.text-center {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, #0d6efd, #28a745);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            margin-bottom: 40px;
            padding: 15px 0;
            animation: fadeIn 1s ease-in-out;
        }
        h5.card-header.text-center::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 5px;
            background: linear-gradient(45deg, #0d6efd, #28a745);
            border-radius: 3px;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Card chứa form */
        .card.mb-4 {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(145deg, #ffffff, #f0f4f8);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card.mb-4:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }
        .card-body {
            padding: 30px;
        }

        /* Thông báo lỗi và thành công */
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

        /* Form chọn ngày */
        .row.g-3 {
            margin-top: 20px;
        }
        .form-label {
            font-size: 1.2rem;
            font-weight: 600;
            color: #0d6efd;
            margin-bottom: 15px;
            display: block;
        }
        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-radius: 10px;
            background-color: #f8f9fa;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .form-check:hover {
            background-color: #e9ecef;
            transform: scale(1.02);
        }
        .form-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid #0d6efd;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }
        .form-check-input:focus {
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
            outline: none;
        }
        .form-check-label {
            font-size: 1rem;
            color: #343a40;
            cursor: pointer;
        }

        /* Nút lưu */
        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #28a745);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            color: #fff;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #218838);
            transform: scale(1.05);
            color: #fff;
        }
        .btn-primary i {
            margin-right: 5px;
        }
        .text-center {
            margin-top: 20px;
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
                    <div class="card mb-4">
                        <h5 class="card-header text-center">Chọn Ngày Làm Việc</h5>
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
                            <form action="?action=nurse_availability" method="post" class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Chọn các ngày làm việc trong tuần:</label>
                                    <div class="row">
                                        <?php foreach ($daysOfWeek as $day): ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="days_of_week[]"
                                                           value="<?php echo htmlspecialchars($day); ?>"
                                                           class="form-check-input"
                                                           id="day-<?php echo htmlspecialchars($day); ?>"
                                                           <?php echo in_array($day, $availability['selected_days']) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="day-<?php echo htmlspecialchars($day); ?>">
                                                        <?php echo htmlspecialchars($day); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <input type="hidden" name="userId" value="<?php echo htmlspecialchars($user['user_id'] ?? ''); ?>">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Lưu Lịch Làm Việc
                                    </button>
                                </div>
                            </form>
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