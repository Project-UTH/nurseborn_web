<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/MessageModel.php';
require_once __DIR__ . '/../models/UserModel.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');


// Tắt hiển thị lỗi PHP để tránh làm hỏng JSON
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Khởi tạo models
$messageModel = new MessageModel($conn);
$userModel = new UserModel($conn);

// Hàm kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm xác thực người dùng
function authenticateUser($redirect = true) {
    if (!isLoggedIn()) {
        error_log("Người dùng chưa đăng nhập");
        if ($redirect) {
            $_SESSION['error'] = 'Vui lòng đăng nhập';
            header('Location: ?action=login');
            exit;
        }
        throw new Exception("Vui lòng đăng nhập");
    }

    global $userModel;
    $user = $userModel->getUserById($_SESSION['user_id']);
    if (!$user) {
        error_log("User not found for ID: {$_SESSION['user_id']}");
        if ($redirect) {
            $_SESSION['error'] = 'Người dùng không tồn tại';
            session_destroy();
            header('Location: ?action=login');
            exit;
        }
        throw new Exception("Người dùng không tồn tại");
    }

    if ($user['role'] !== 'FAMILY' && $user['role'] !== 'NURSE') {
        error_log("User {$_SESSION['user_id']} không có vai trò FAMILY hoặc NURSE, role hiện tại: {$user['role']}");
        if ($redirect) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            header('Location: ?action=home');
            exit;
        }
        throw new Exception("Người dùng phải có vai trò FAMILY hoặc NURSE");
    }

    return $user;
}

// Hiển thị trang tin nhắn (action: messages)
function index($user, $messageModel) {
    header('Content-Type: text/html; charset=UTF-8');
    $username = $user['username'];
    try {
        $receiverId = isset($_GET['receiver_id']) ? (string)$_GET['receiver_id'] : null;
        $selectedPartner = null;
        if ($receiverId) {
            $selectedPartner = $messageModel->getUserById($receiverId);
            if ($selectedPartner) {
                $selectedPartner = [
                    'userId' => $selectedPartner['user_id'],
                    'username' => $selectedPartner['username'],
                    'fullName' => $selectedPartner['full_name'] ?? $selectedPartner['username']
                ];
            }
        }

        $data = [
            'user' => $user,
            'selectedPartner' => $selectedPartner,
            'error' => null
        ];
    } catch (Exception $e) {
        error_log("Error in index: " . $e->getMessage());
        $data = [
            'user' => null,
            'selectedPartner' => null,
            'error' => "Không thể tải thông tin người dùng: " . $e->getMessage()
        ];
    }

    include __DIR__ . '/../views/messages.php';
}

// Gửi tin nhắn (action: send_message)
function send($user, $messageModel) {
    header('Content-Type: application/json');
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['senderId']) || !isset($data['receiverId']) || !isset($data['content'])) {
            throw new Exception("Dữ liệu gửi tin nhắn không hợp lệ");
        }
        error_log("Send message data: " . print_r($data, true));
        $sender = $user;
        $username = $sender['username'];
        // Kiểm tra vai trò của người gửi và người nhận
        $receiver = $messageModel->getUserById($data['receiverId']);
        if (!$receiver) {
            throw new Exception("Người nhận không tồn tại");
        }
        if ($sender['role'] === 'FAMILY' && $receiver['role'] !== 'NURSE') {
            throw new Exception("Family chỉ có thể nhắn tin cho y tá");
        }
        if ($sender['role'] === 'NURSE' && $receiver['role'] !== 'FAMILY') {
            throw new Exception("Y tá chỉ có thể nhắn tin cho family");
        }
        $message = $messageModel->sendMessage($data, $username);
        if (!$message) {
            throw new Exception("Không thể lưu tin nhắn");
        }
        echo json_encode($message);
    } catch (Exception $e) {
        http_response_code(500);
        error_log("Error sending message: " . $e->getMessage());
        echo json_encode(['error' => "Không thể gửi tin nhắn: " . $e->getMessage()]);
    }
}

// Lấy cuộc trò chuyện (action: get_conversation)
function getConversation($user, $messageModel) {
    header('Content-Type: application/json');
    $senderId = isset($_GET['senderId']) ? (string)$_GET['senderId'] : 0;
    $receiverId = isset($_GET['receiverId']) ? (string)$_GET['receiverId'] : 0;

    error_log("Fetching conversation between senderId: $senderId and receiverId: $receiverId");

    if (!$senderId || !$receiverId) {
        http_response_code(400);
        echo json_encode(['error' => 'Thiếu senderId hoặc receiverId']);
        return;
    }

    try {
        $username = $user['username'];
        $messages = $messageModel->getConversation($senderId, $receiverId, $username);
        error_log("Conversation data: " . print_r($messages, true));
        echo json_encode($messages);
    } catch (Exception $e) {
        http_response_code(500);
        error_log("Error fetching conversation: " . $e->getMessage());
        echo json_encode(['error' => "Không thể lấy cuộc trò chuyện: " . $e->getMessage()]);
    }
}

// Lấy danh sách đối tác trò chuyện (action: get_partners)
function getPartners($user, $messageModel) {
    header('Content-Type: application/json');
    $userId = isset($_GET['userId']) ? (string)$_GET['userId'] : 0;

    if (!$userId) {
        http_response_code(400);
        echo json_encode(['error' => 'Thiếu userId']);
        return;
    }

    try {
        error_log("Fetching conversation partners for userId: $userId");
        $partners = $messageModel->getConversationPartners($userId);
        error_log("Partners data: " . print_r($partners, true));
        if ($partners === false) {
            throw new Exception("Không thể lấy danh sách đối tác do lỗi truy vấn");
        }
        echo json_encode($partners);
    } catch (Exception $e) {
        http_response_code(500);
        error_log("Error fetching partners: " . $e->getMessage());
        echo json_encode(['error' => "Không thể lấy danh sách đối tác: " . $e->getMessage()]);
    }
}

// Đánh dấu tin nhắn là đã đọc (action: mark_message_as_read)
function markMessageAsRead($user, $messageModel) {
    header('Content-Type: application/json');
    $messageId = isset($_GET['messageId']) ? (int)$_GET['messageId'] : 0;

    if (!$messageId) {
        http_response_code(400);
        echo json_encode(['error' => 'Thiếu messageId']);
        return;
    }

    try {
        $username = $user['username'];
        $messageModel->markAsRead($messageId, $username);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        http_response_code(500);
        error_log("Error marking message as read: " . $e->getMessage());
        echo json_encode(['error' => "Không thể đánh dấu tin nhắn đã đọc: " . $e->getMessage()]);
    }
}

// Xử lý yêu cầu
$action = $_GET['action'] ?? 'messages';

try {
    error_log("Action received: " . $action);
    $user = authenticateUser();

    switch ($action) {
        case 'messages':
            index($user, $messageModel);
            break;
        case 'send_message':
            send($user, $messageModel);
            break;
        case 'get_conversation':
            getConversation($user, $messageModel);
            break;
        case 'get_partners':
            getPartners($user, $messageModel);
            break;
        case 'mark_message_as_read':
            markMessageAsRead($user, $messageModel);
            break;
        default:
            header('Location: ?action=home');
            break;
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    error_log("Unexpected error in MessageController: " . $e->getMessage());
    echo json_encode(['error' => 'Lỗi server: ' . $e->getMessage()]);
}

$conn->close();
error_log("MessageController execution completed");
?>