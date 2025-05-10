<?php
// Đặt file này vào thư mục controllers/

require_once __DIR__ . '/../models/MessageModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../views/messages.php';

class MessageController {
    private $messageModel;
    private $userModel;

    public function __construct() {
        // Kết nối database bằng MySQLi
        $host = 'localhost';
        $dbname = 'db_nurseborn';
        $username = 'root';
        $password = '';
        
        $conn = new mysqli($host, $username, $password, $dbname);
        if ($conn->connect_error) {
            error_log("Kết nối database thất bại: " . $conn->connect_error);
            die(json_encode(['error' => 'Không thể kết nối đến database']));
        }

        $this->messageModel = new MessageModel($conn);
        $this->userModel = new UserModel($conn);
    }

    // Action: messages
    public function messages() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: /index.php?action=login');
            exit();
        }

        $currentUserId = $_SESSION['user_id'];
        $currentUser = $this->userModel->getUserById($currentUserId);

        if (!$currentUser || !is_array($currentUser)) {
            error_log("Người dùng không tồn tại với user_id: $currentUserId");
            die("Người dùng không tồn tại. Vui lòng đăng nhập lại.");
        }

        if (!isset($currentUser['role']) || $currentUser['role'] === 'ADMIN') {
            error_log("Admin không có quyền truy cập vào nhắn tin. User ID: $currentUserId");
            die("Admin không có quyền truy cập vào nhắn tin");
        }

        try {
            $partners = $this->messageModel->findConversationPartners($currentUserId);
        } catch (Exception $e) {
            error_log("Lỗi khi lấy danh sách đối tác trò chuyện cho user_id $currentUserId: " . $e->getMessage());
            die("Lỗi khi lấy dữ liệu: " . $e->getMessage());
        }

        $selectedUserId = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : null;
        $messages = [];
        $selectedUser = null;

        if ($selectedUserId) {
            try {
                $messages = $this->messageModel->findConversationBetweenUsers($currentUserId, $selectedUserId);
                $selectedUser = $this->userModel->getUserById($selectedUserId);
                if (!$selectedUser) {
                    error_log("Không tìm thấy người dùng với ID: $selectedUserId");
                    die("Người nhận không tồn tại");
                }
            } catch (Exception $e) {
                error_log("Lỗi khi lấy cuộc trò chuyện giữa user_id $currentUserId và $selectedUserId: " . $e->getMessage());
                die("Lỗi khi lấy dữ liệu: " . $e->getMessage());
            }
        }

        $data = [
            'user' => $currentUser,
            'partners' => $partners,
            'messages' => $messages,
            'selectedUser' => $selectedUser,
            'action' => 'messages'
        ];

        $this->loadView('messages', $data);
    }

    // Action: get_conversation (trả về JSON cho JavaScript)
    public function get_conversation() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            die(json_encode(['error' => 'Bạn cần đăng nhập']));
        }

        // Kiểm tra CSRF token
        $token = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : null;
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            http_response_code(403);
            die(json_encode(['error' => 'CSRF token không hợp lệ']));
        }

        $currentUserId = $_SESSION['user_id'];
        $senderId = isset($_GET['sender_id']) ? (int)$_GET['sender_id'] : null;
        $receiverId = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : null;

        if (!$senderId || !$receiverId) {
            http_response_code(400);
            die(json_encode(['error' => 'Dữ liệu không hợp lệ']));
        }

        if ($senderId !== $currentUserId && $receiverId !== $currentUserId) {
            http_response_code(403);
            die(json_encode(['error' => 'Không có quyền truy cập cuộc trò chuyện này']));
        }

        try {
            $messages = $this->messageModel->findConversationBetweenUsers($senderId, $receiverId);
            echo json_encode($messages);
        } catch (Exception $e) {
            error_log("Lỗi khi lấy cuộc trò chuyện giữa sender_id $senderId và receiver_id $receiverId: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['error' => 'Lỗi khi lấy dữ liệu: ' . $e->getMessage()]));
        }
    }

    // Action: send_message
    public function send_message() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            die(json_encode(['error' => 'Bạn cần đăng nhập để gửi tin nhắn']));
        }

        // Kiểm tra CSRF token
        $token = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : null;
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            http_response_code(403);
            die(json_encode(['error' => 'CSRF token không hợp lệ']));
        }

        $currentUserId = $_SESSION['user_id'];
        $currentUser = $this->userModel->getUserById($currentUserId);

        if (!$currentUser || !is_array($currentUser)) {
            http_response_code(404);
            die(json_encode(['error' => 'Người dùng không tồn tại']));
        }

        if (!isset($currentUser['role']) || $currentUser['role'] === 'ADMIN') {
            http_response_code(403);
            die(json_encode(['error' => 'Admin không có quyền gửi tin nhắn']));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Đọc dữ liệu JSON từ body
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !is_array($input)) {
                http_response_code(400);
                die(json_encode(['error' => 'Dữ liệu JSON không hợp lệ']));
            }

            $receiverId = isset($input['receiver_id']) ? (int)$input['receiver_id'] : null;
            $content = isset($input['content']) ? trim($input['content']) : '';
            $sentAt = isset($input['sent_at']) ? $input['sent_at'] : date('Y-m-d H:i:s');
            $isRead = isset($input['is_read']) ? (bool)$input['is_read'] : false;

            if (!$receiverId || !$content) {
                http_response_code(400);
                die(json_encode(['error' => 'Dữ liệu không hợp lệ']));
            }

            $receiver = $this->userModel->getUserById($receiverId);
            if (!$receiver || !is_array($receiver)) {
                http_response_code(404);
                die(json_encode(['error' => 'Người nhận không tồn tại']));
            }

            if (!isset($receiver['role']) || $receiver['role'] === 'ADMIN') {
                http_response_code(403);
                die(json_encode(['error' => 'Không thể gửi tin nhắn đến Admin']));
            }

            $message = [
                'sender_id' => $currentUserId,
                'receiver_id' => $receiverId,
                'content' => $content,
                'sent_at' => $sentAt,
                'is_read' => $isRead ? 1 : 0
            ];

            try {
                $messageId = $this->messageModel->save($message);

                $newMessage = [
                    'message_id' => $messageId,
                    'sender_id' => $currentUserId,
                    'receiver_id' => $receiverId,
                    'content' => $content,
                    'sent_at' => $sentAt,
                    'is_read' => $isRead,
                    'sender_username' => $currentUser['username']
                ];

                echo json_encode($newMessage);
            } catch (Exception $e) {
                error_log("Lỗi khi gửi tin nhắn từ user_id $currentUserId đến receiver_id $receiverId: " . $e->getMessage());
                http_response_code(500);
                die(json_encode(['error' => 'Lỗi khi gửi tin nhắn: ' . $e->getMessage()]));
            }
        } else {
            http_response_code(405);
            die(json_encode(['error' => 'Phương thức không được hỗ trợ']));
        }
    }

    // Action: mark_message_as_read
    public function mark_message_as_read() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            die(json_encode(['error' => 'Bạn cần đăng nhập']));
        }

        // Kiểm tra CSRF token
        $token = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : null;
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            http_response_code(403);
            die(json_encode(['error' => 'CSRF token không hợp lệ']));
        }

        $currentUserId = $_SESSION['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Đọc dữ liệu JSON từ body
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !is_array($input)) {
                http_response_code(400);
                die(json_encode(['error' => 'Dữ liệu JSON không hợp lệ']));
            }

            $messageId = isset($input['message_id']) ? (int)$input['message_id'] : null;
        } else {
            $messageId = isset($_POST['message_id']) ? (int)$_POST['message_id'] : null;
        }

        if (!$messageId) {
            http_response_code(400);
            die(json_encode(['error' => 'Dữ liệu không hợp lệ']));
        }

        try {
            $message = $this->messageModel->findByMessageId($messageId);
            if (!$message) {
                http_response_code(404);
                die(json_encode(['error' => 'Tin nhắn không tồn tại']));
            }

            if ($message['receiver_id'] !== $currentUserId) {
                http_response_code(403);
                die(json_encode(['error' => 'Không có quyền đánh dấu tin nhắn này']));
            }

            $this->messageModel->markAsRead($messageId);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Lỗi khi đánh dấu tin nhắn đã đọc (message_id $messageId): " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['error' => 'Lỗi khi đánh dấu tin nhắn: ' . $e->getMessage()]));
        }
    }

    // Action: partners (để lấy danh sách đối tác trò chuyện)
    public function partners() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            die(json_encode(['error' => 'Bạn cần đăng nhập']));
        }

        // Kiểm tra CSRF token
        $token = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : null;
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            http_response_code(403);
            die(json_encode(['error' => 'CSRF token không hợp lệ']));
        }

        $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

        if (!$userId || $userId !== $_SESSION['user_id']) {
            http_response_code(403);
            die(json_encode(['error' => 'Không có quyền truy cập']));
        }

        try {
            $partners = $this->messageModel->findConversationPartners($userId);
            echo json_encode($partners);
        } catch (Exception $e) {
            error_log("Lỗi khi lấy danh sách đối tác trò chuyện cho user_id $userId: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['error' => 'Lỗi khi lấy dữ liệu: ' . $e->getMessage()]));
        }
    }

    // Hàm hỗ trợ để load view
    private function loadView($view, $data = []) {
        // Đảm bảo các biến mặc định luôn được định nghĩa
        $defaultData = [
            'user' => null,
            'partners' => [],
            'messages' => [],
            'selectedUser' => null,
            'action' => 'messages'
        ];
        $data = array_merge($defaultData, $data);
        extract($data);
        $viewPath = __DIR__ . "/../views/{$view}.php"; // Sử dụng đường dẫn tuyệt đối
        if (!file_exists($viewPath)) {
            error_log("View file not found: $viewPath");
            die("View file not found: $viewPath");
        }
        require_once $viewPath;
    }
}
?>