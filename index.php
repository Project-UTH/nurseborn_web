<?php
// Khởi tạo session chỉ khi chưa active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Danh sách các action thuộc NurseAvailabilityController
$nurseActions = [
    'nurse_availability',
    'nurse_schedule',
    'complete_booking'
];

// Danh sách các action thuộc NotificationController
$notificationActions = [
    'notifications',
    'mark_as_read'
];

// Danh sách các action thuộc BookingController
$bookingActions = [
    'nurse_list',
    'set_service'
];

// Danh sách các action thuộc NurseController
$nurseControllerActions = [
    'pending_bookings',
    'accept_booking',
    'cancel_booking'
];

// Lấy action từ query string, mặc định là 'home'
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) ?? 'home';

// Định tuyến đến controller phù hợp
try {
    if (in_array($action, $nurseActions)) {
        $controllerFile = __DIR__ . '/controllers/NurseAvailabilityController.php';
    } elseif (in_array($action, $notificationActions)) {
        $controllerFile = __DIR__ . '/controllers/NotificationController.php';
    } elseif (in_array($action, $bookingActions)) {
        $controllerFile = __DIR__ . '/controllers/BookingController.php';
    } elseif (in_array($action, $nurseControllerActions)) {
        $controllerFile = __DIR__ . '/controllers/NurseController.php';
    } else {
        $controllerFile = __DIR__ . '/controllers/UserController.php';
    }

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
    } else {
        error_log("Controller file not found: $controllerFile");
        header('HTTP/1.1 404 Not Found');
        echo 'Controller not found';
        exit;
    }
} catch (Exception $e) {
    error_log("Error in index.php: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo 'Internal Server Error';
    exit;
}
?>