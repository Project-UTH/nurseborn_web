<?php
$pageTitle = 'Chọn vai trò';
$baseUrl = '/nurseborn';
?>
<!DOCTYPE html>
<html lang="vi" class="light-style customizer-hide" dir="ltr">
<head>
    <?php include __DIR__ . '/fragments/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn Vai Trò - NurseBorn</title>
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
        }

        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #dcfce7 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-dialog {
            max-width: 500px;
        }

        .modal-content {
            border: none;
            border-radius: var(--border-radius);
            background-color: var(--card-bg);
            box-shadow: var(--shadow);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: center;
        }

        .modal-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-color);
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-body {
            padding: 2rem;
            text-align: center;
        }

        .modal-footer {
            border-top: none;
            padding: 1rem 2rem;
            display: flex;
            justify-content: center;
        }

        .btn {
            font-size: 1rem;
            font-weight: 500;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 1rem;
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

        .btn-secondary {
            background: linear-gradient(45deg, #6b7280, #9ca3af);
            border: none;
            color: #fff;
        }

        .btn-secondary:hover {
            background: linear-gradient(45deg, #4b5563, #6b7280);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .btn-link:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        .btn i {
            margin-right: 0.5rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 1rem;
            }

            .modal-title {
                font-size: 1.5rem;
            }

            .btn {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .modal-body {
                padding: 1.5rem;
            }

            .modal-footer {
                padding: 0.75rem;
            }
        }
    </style>
</head>
<body>
<div id="roleModal" class="modal fade show" tabindex="-1" aria-hidden="true" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn vai trò của bạn</h5>
            </div>
            <div class="modal-body">
                <button class="btn btn-primary" onclick="redirectToRegister('nurse')"><i class="fas fa-user-nurse"></i> Tôi là Y tá</button>
                <button class="btn btn-secondary" onclick="redirectToRegister('family')"><i class="fas fa-users"></i> Tôi là Khách Hàng</button>
            </div>
            <div class="modal-footer">
                <a href="?action=login" class="btn btn-link">Quay lại đăng nhập</a>
            </div>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
<script>
    function redirectToRegister(role) {
        if (role === 'nurse') {
            window.location.href = '?action=register_nurse';
        } else if (role === 'family') {
            window.location.href = '?action=register_family';
        }
    }
</script>
</body>
</html>