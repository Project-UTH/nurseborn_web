<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/MessageModel.php';

class MessageController {
    private $conn;
    private $messageModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->messageModel = new MessageModel($conn);
    }

    // Hiển thị trang tin nhắn (action: messages)
    public function index() {
        // Debug session
        error_log("Session data: " . print_r($_SESSION, true));

        // Kiểm tra đăng nhập bằng $_SESSION['user_id']
        if (!isset($_SESSION['user_id'])) {
            error_log("Session user_id not set, redirecting to login");
            header('Location: /nurseborn/views/login.php');
            exit();
        }

        // Lấy thông tin người dùng từ user_id
        $user = $this->messageModel->getUserById($_SESSION['user_id']);
        if (!$user) {
            error_log("User not found for ID: {$_SESSION['user_id']}");
            session_destroy();
            header('Location: /nurseborn/views/login.php');
            exit();
        }

        $username = $user['username'];
        try {
            $nurseUserId = isset($_GET['nurseUserId']) ? (int)$_GET['nurseUserId'] : null;
            $selectedNurse = null;
            if ($nurseUserId) {
                $selectedNurse = $this->messageModel->getUserById($nurseUserId);
                if ($selectedNurse) {
                    $selectedNurse = [
                        'userId' => $selectedNurse['user_id'],
                        'username' => $selectedNurse['username'],
                        'fullName' => $selectedNurse['full_name']
                    ];
                }
            }

            $data = [
                'user' => $user,
                'selectedNurse' => $selectedNurse,
                'error' => null
            ];
        } catch (Exception $e) {
            $data = [
                'user' => null,
                'selectedNurse' => null,
                'error' => "Không thể tải thông tin người dùng: " . $e->getMessage()
            ];
        }

        include __DIR__ . '/../views/messages.php';
    }

    // Gửi tin nhắn (action: send_message)
    public function send() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn chưa đăng nhập']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $this->messageModel->getUserById($_SESSION['user_id'])['username'];
            $message = $this->messageModel->sendMessage($data, $username);
            echo json_encode($message);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => "Không thể gửi tin nhắn: " . $e->getMessage()]);
        }
    }

    // Lấy cuộc trò chuyện (action: get_conversation)
    public function conversation() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn chưa đăng nhập']);
            return;
        }

        $senderId = isset($_GET['senderId']) ? (int)$_GET['senderId'] : 0;
        $receiverId = isset($_GET['receiverId']) ? (int)$_GET['receiverId'] : 0;

        if (!$senderId || !$receiverId) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu senderId hoặc receiverId']);
            return;
        }

        try {
            $username = $this->messageModel->getUserById($_SESSION['user_id'])['username'];
            $messages = $this->messageModel->getConversation($senderId, $receiverId, $username);
            echo json_encode($messages);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => "Không thể lấy cuộc trò chuyện: " . $e->getMessage()]);
        }
    }

    // Lấy danh sách đối tác trò chuyện (action: get_partners)
    public function partners() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn chưa đăng nhập']);
            return;
        }

        $userId = isset($_GET['userId']) ? (int)$_GET['userId'] : 0;

        if (!$userId) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu userId']);
            return;
        }

        try {
            $username = $this->messageModel->getUserById($_SESSION['user_id'])['username'];
            $partners = $this->messageModel->getConversationPartners($userId, $username);
            echo json_encode($partners);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => "Không thể lấy danh sách đối tác: " . $e->getMessage()]);
        }
    }

    // Đánh dấu tin nhắn là đã đọc (action: mark_message_as_read)
    public function read() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn chưa đăng nhập']);
            return;
        }

        $messageId = isset($_GET['messageId']) ? (int)$_GET['messageId'] : 0;

        if (!$messageId) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu messageId']);
            return;
        }

        try {
            $username = $this->messageModel->getUserById($_SESSION['user_id'])['username'];
            $this->messageModel->markAsRead($messageId, $username);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => "Không thể đánh dấu tin nhắn đã đọc: " . $e->getMessage()]);
        }
    }
}

// Xử lý các yêu cầu dựa trên action
$controller = new MessageController($conn);

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) ?? 'messages';

switch ($action) {
    case 'messages':
        $controller->index();
        break;
    case 'send_message':
        $controller->send();
        break;
    case 'get_conversation':
        $controller->conversation();
        break;
    case 'get_partners':
        $controller->partners();
        break;
    case 'mark_message_as_read':
        $controller->read();
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Hành động không hợp lệ']);
        break;
}

$conn->close();