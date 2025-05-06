<?php
// Đảm bảo file được gọi trong ngữ cảnh có $_SESSION
$title = isset($pageTitle) ? htmlspecialchars($pageTitle) : 'NurseBorn';
$baseUrl = '/nurseborn'; // Điều chỉnh nếu dự án nằm trong thư mục con
?>
<head>
    <title><?php echo $title; ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $baseUrl; ?>/static/assets/img/favicon/favicon.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/static/assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Helpers -->
    <script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/helpers.js"></script>
    <script src="<?php echo $baseUrl; ?>/static/assets/js/config.js"></script>
</head>