<?php
// Khởi tạo session chỉ khi chưa active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Debug session ngay sau khi khởi tạo
error_log("Session in index.php (before routing): " . print_r($_SESSION, true));

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
    'set_service',
    'bookings',
    'cancel_booking',
    'feedback',
    'submit_feedback',
    'nurse_review'
];

// Danh sách các action thuộc NurseController
$nurseControllerActions = [
    'pending_bookings',
    'accept_booking',
    'cancel_booking'
];

// Danh sách các action thuộc MessageController
$messageActions = [
    'messages',
    'send_message',
    'mark_message_as_read',
    'get_conversation',
    'get_partners'
];

// Danh sách các action thuộc AdminController
$adminActions = [
    'admin_approved_nurses',
    'admin_family_accounts'
];

// Danh sách các action thuộc ReviewNurseController
$reviewNurseActions = [
    'review_nurse'
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
    } elseif (in_array($action, $messageActions)) {
        $controllerFile = __DIR__ . '/controllers/MessageController.php';
    } elseif (in_array($action, $adminActions)) {
        $controllerFile = __DIR__ . '/controllers/AdminController.php';
    } elseif (in_array($action, $reviewNurseActions)) {
        $controllerFile = __DIR__ . '/controllers/ReviewNurseController.php';
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