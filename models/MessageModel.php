<?php
// Đặt file này vào thư mục models/

class MessageModel {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Lấy cuộc trò chuyện giữa hai người dùng
    public function findConversationBetweenUsers($senderId, $receiverId) {
        $stmt = $this->conn->prepare(
            "SELECT m.*, 
             s.username AS sender_username, 
             r.username AS receiver_username 
             FROM messages m 
             JOIN users s ON m.sender_id = s.user_id 
             JOIN users r ON m.receiver_id = r.user_id 
             WHERE (m.sender_id = ? AND m.receiver_id = ?) 
             OR (m.sender_id = ? AND m.receiver_id = ?) 
             ORDER BY m.sent_at ASC"
        );
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
        }
        $stmt->bind_param("iiii", $senderId, $receiverId, $receiverId, $senderId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi thực thi truy vấn: " . $stmt->error);
        }
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy danh sách đối tác trò chuyện của một người dùng
    public function findConversationPartners($userId) {
        $stmt = $this->conn->prepare(
            "SELECT DISTINCT u.* 
             FROM users u 
             WHERE u.user_id IN (
                 SELECT DISTINCT m.receiver_id 
                 FROM messages m 
                 WHERE m.sender_id = ?
             ) 
             OR u.user_id IN (
                 SELECT DISTINCT m.sender_id 
                 FROM messages m 
                 WHERE m.receiver_id = ?
             )"
        );
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
        }
        $stmt->bind_param("ii", $userId, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi thực thi truy vấn: " . $stmt->error);
        }
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy tin nhắn theo ID
    public function findByMessageId($messageId) {
        $stmt = $this->conn->prepare("SELECT * FROM messages WHERE message_id = ?");
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
        }
        $stmt->bind_param("i", $messageId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi thực thi truy vấn: " . $stmt->error);
        }
        return $stmt->get_result()->fetch_assoc();
    }

    // Lưu tin nhắn mới
    public function save($message) {
        $stmt = $this->conn->prepare(
            "INSERT INTO messages (sender_id, receiver_id, booking_id, content, attachment, sent_at, is_read) 
             VALUES (?, ?, ?, ?, ?, NOW(), ?)"
        );
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
        }
        $bookingId = $message['booking_id'] ?? null;
        $attachment = $message['attachment'] ?? null;
        $isRead = $message['is_read'] ?? 0;
        $stmt->bind_param(
            "iissii",
            $message['sender_id'],
            $message['receiver_id'],
            $bookingId,
            $message['content'],
            $attachment,
            $isRead
        );
        if (!$stmt->execute()) {
            throw new Exception("Lỗi thực thi truy vấn: " . $stmt->error);
        }
        return $this->conn->insert_id;
    }

    // Cập nhật trạng thái đã đọc
    public function markAsRead($messageId) {
        $stmt = $this->conn->prepare("UPDATE messages SET is_read = 1 WHERE message_id = ?");
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
        }
        $stmt->bind_param("i", $messageId);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi thực thi truy vấn: " . $stmt->error);
        }
    }
}