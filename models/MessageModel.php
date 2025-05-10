<?php
class MessageModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy user theo ID
    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // Lấy user theo username
    public function getUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // Lấy danh sách đối tác trò chuyện (từ MessageRepository.java)
    public function findConversationPartners($userId) {
        $sql = "SELECT DISTINCT u.* FROM users u 
                WHERE u.user_id IN (
                    SELECT DISTINCT m.receiver_id FROM messages m WHERE m.sender_id = ?
                ) OR u.user_id IN (
                    SELECT DISTINCT m.sender_id FROM messages m WHERE m.receiver_id = ?
                )";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $partners = [];
        while ($row = $result->fetch_assoc()) {
            $partners[] = $row;
        }
        $stmt->close();
        return $partners;
    }

    // Lấy cuộc trò chuyện giữa 2 người dùng (từ MessageRepository.java)
    public function findConversationBetweenUsers($senderId, $receiverId) {
        $sql = "SELECT m.*, 
                sender.username AS sender_username, 
                receiver.username AS receiver_username 
                FROM messages m 
                JOIN users sender ON m.sender_id = sender.user_id 
                JOIN users receiver ON m.receiver_id = receiver.user_id 
                WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                   OR (m.sender_id = ? AND m.receiver_id = ?) 
                ORDER BY m.sent_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiii", $senderId, $receiverId, $receiverId, $senderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        $stmt->close();
        return $messages;
    }

    // Lấy thông tin tin nhắn theo ID (từ MessageRepository.java)
    public function findByMessageId($messageId) {
        $sql = "SELECT * FROM messages WHERE message_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $result = $stmt->get_result();
        $message = $result->fetch_assoc();
        $stmt->close();
        return $message;
    }

    // Lưu tin nhắn mới (từ MessageRepository.java)
    private function saveMessage($senderId, $receiverId, $content, $sentAt, $isRead = 0) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, content, sent_at, is_read) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iissi", $senderId, $receiverId, $content, $sentAt, $isRead);
        $stmt->execute();
        $messageId = $stmt->insert_id;
        $stmt->close();

        // Trả về thông tin tin nhắn vừa lưu
        $sql = "SELECT m.*, 
                sender.username AS sender_username, 
                receiver.username AS receiver_username 
                FROM messages m 
                JOIN users sender ON m.sender_id = sender.user_id 
                JOIN users receiver ON m.receiver_id = receiver.user_id 
                WHERE m.message_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $result = $stmt->get_result();
        $message = $result->fetch_assoc();
        $stmt->close();
        return $message;
    }

    // Đánh dấu tin nhắn là đã đọc (từ MessageRepository.java)
    private function updateMessageReadStatus($messageId) {
        $sql = "UPDATE messages SET is_read = 1 WHERE message_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $stmt->close();
    }

    // Gửi tin nhắn (logic từ MessageService.java)
    public function sendMessage($messageData, $currentUsername) {
        $currentUser = $this->getUserByUsername($currentUsername);
        if (!$currentUser) {
            throw new Exception("Không tìm thấy người dùng: $currentUsername");
        }

        if ($currentUser['role'] === 'ADMIN') {
            throw new Exception("Admin không có quyền gửi tin nhắn");
        }

        $senderId = $messageData['senderId'] ?? null;
        $receiverId = $messageData['receiverId'] ?? null;
        $content = $messageData['content'] ?? '';

        if (!$senderId || !$receiverId || !$content) {
            throw new Exception("Dữ liệu không hợp lệ");
        }

        if ($currentUser['user_id'] != $senderId) {
            throw new Exception("SenderId không hợp lệ");
        }

        $sender = $this->getUserById($senderId);
        $receiver = $this->getUserById($receiverId);
        if (!$sender || !$receiver) {
            throw new Exception("Không tìm thấy người gửi hoặc người nhận");
        }

        if ($receiver['role'] === 'ADMIN') {
            throw new Exception("Không thể gửi tin nhắn đến Admin");
        }

        $sentAt = date('Y-m-d H:i:s');
        return $this->saveMessage($senderId, $receiverId, $content, $sentAt);
    }

    // Lấy cuộc trò chuyện (logic từ MessageService.java)
    public function getConversation($senderId, $receiverId, $currentUsername) {
        $currentUser = $this->getUserByUsername($currentUsername);
        if (!$currentUser) {
            throw new Exception("Không tìm thấy người dùng: $currentUsername");
        }

        if ($currentUser['role'] === 'ADMIN') {
            throw new Exception("Admin không có quyền xem tin nhắn");
        }

        if ($currentUser['user_id'] != $senderId && $currentUser['user_id'] != $receiverId) {
            throw new Exception("Không có quyền truy cập cuộc trò chuyện này");
        }

        return $this->findConversationBetweenUsers($senderId, $receiverId);
    }

    // Lấy danh sách đối tác trò chuyện (logic từ MessageService.java)
    public function getConversationPartners($userId, $currentUsername) {
        $currentUser = $this->getUserByUsername($currentUsername);
        if (!$currentUser) {
            throw new Exception("Không tìm thấy người dùng: $currentUsername");
        }

        if ($currentUser['role'] === 'ADMIN') {
            throw new Exception("Admin không có quyền xem danh sách đối tác trò chuyện");
        }

        if ($currentUser['user_id'] != $userId) {
            throw new Exception("Không có quyền truy cập danh sách này");
        }

        $partners = $this->findConversationPartners($userId);
        return array_map(function($partner) {
            return [
                'userId' => $partner['user_id'],
                'username' => $partner['username'],
                'fullName' => $partner['full_name']
            ];
        }, $partners);
    }

    // Đánh dấu tin nhắn là đã đọc (logic từ MessageService.java)
    public function markAsRead($messageId, $currentUsername) {
        $currentUser = $this->getUserByUsername($currentUsername);
        if (!$currentUser) {
            throw new Exception("Không tìm thấy người dùng: $currentUsername");
        }

        $message = $this->findByMessageId($messageId);
        if (!$message) {
            throw new Exception("Không tìm thấy tin nhắn với ID: $messageId");
        }

        if ($message['receiver_id'] != $currentUser['user_id']) {
            throw new Exception("Không có quyền đánh dấu tin nhắn này");
        }

        $this->updateMessageReadStatus($messageId);
    }
}