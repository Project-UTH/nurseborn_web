<?php
class MessageModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        if (!$this->conn) {
            error_log("MessageModel: Database connection is null");
            throw new Exception("Kết nối cơ sở dữ liệu thất bại");
        }
    }

    // Lấy thông tin người dùng theo user_id
    public function getUserById($userId) {
        $stmt = $this->conn->prepare("SELECT user_id, username, full_name, role FROM users WHERE user_id = ?");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for getUserById: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for getUserById: " . $stmt->error);
            $stmt->close();
            return false;
        }
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // Gửi tin nhắn
    public function sendMessage($data, $username) {
        $senderId = $data['senderId'];
        $receiverId = $data['receiverId'];
        $content = $data['content'];
        $attachment = isset($data['attachment']) ? $data['attachment'] : null;

        $stmt = $this->conn->prepare("INSERT INTO messages (sender_id, receiver_id, content, attachment, sent_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for sendMessage: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("iiss", $senderId, $receiverId, $content, $attachment);
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for sendMessage: " . $stmt->error);
            $stmt->close();
            return false;
        }

        if ($stmt->affected_rows > 0) {
            $messageId = $stmt->insert_id;
            $stmt->close();
            // Lấy thời gian sent_at từ cơ sở dữ liệu để đảm bảo tính chính xác
            $stmt = $this->conn->prepare("SELECT sent_at FROM messages WHERE message_id = ?");
            if (!$stmt) {
                error_log("MessageModel: Prepare failed for retrieving sent_at: " . $this->conn->error);
                return false;
            }
            $stmt->bind_param("i", $messageId);
            if (!$stmt->execute()) {
                error_log("MessageModel: Execute failed for retrieving sent_at: " . $stmt->error);
                $stmt->close();
                return false;
            }
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $sentAt = $row['sent_at'];
            $stmt->close();

            return [
                'message_id' => $messageId,
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'content' => $content,
                'attachment' => $attachment,
                'sent_at' => $sentAt, // Giữ nguyên giá trị từ cơ sở dữ liệu
                'is_read' => 0
            ];
        }
        $stmt->close();
        return false;
    }

    // Lấy cuộc trò chuyện giữa hai người dùng
    public function getConversation($senderId, $receiverId, $username) {
        $stmt = $this->conn->prepare("
            SELECT m.message_id, m.sender_id, m.receiver_id, m.content, m.attachment, m.sent_at, m.is_read
            FROM messages m
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.sent_at ASC
        ");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for getConversation: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("iiii", $senderId, $receiverId, $receiverId, $senderId);
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for getConversation: " . $stmt->error);
            $stmt->close();
            return [];
        }
        $result = $stmt->get_result();
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $row['is_read'] = (bool)$row['is_read'];
            // Không định dạng sent_at, giữ nguyên giá trị từ cơ sở dữ liệu
            $messages[] = $row;
        }
        $stmt->close();
        return $messages;
    }

    // Lấy danh sách đối tác trò chuyện
    public function getConversationPartners($userId) {
        $stmt = $this->conn->prepare("
            SELECT DISTINCT u.user_id, u.username, u.full_name
            FROM users u
            WHERE u.user_id IN (
                SELECT DISTINCT m.receiver_id FROM messages m WHERE m.sender_id = ?
                UNION
                SELECT DISTINCT m.sender_id FROM messages m WHERE m.receiver_id = ?
            )
        ");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for getConversationPartners: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("ii", $userId, $userId);
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for getConversationPartners: " . $stmt->error);
            $stmt->close();
            return [];
        }
        $result = $stmt->get_result();
        $partners = [];
        while ($row = $result->fetch_assoc()) {
            $partners[] = [
                'userId' => $row['user_id'],
                'username' => $row['username'],
                'fullName' => $row['full_name'] ?? $row['username']
            ];
        }
        $stmt->close();
        return $partners;
    }

    // Đánh dấu tin nhắn là đã đọc
    public function markAsRead($messageId, $username) {
        $stmt = $this->conn->prepare("UPDATE messages SET is_read = 1 WHERE message_id = ?");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for markAsRead: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $messageId);
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for markAsRead: " . $stmt->error);
            $stmt->close();
            return false;
        }
        $stmt->close();
        return true;
    }

    // Lấy tin nhắn theo message_id
    public function findByMessageId($messageId) {
        $stmt = $this->conn->prepare("SELECT message_id, sender_id, receiver_id, content, attachment, sent_at, is_read FROM messages WHERE message_id = ?");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for findByMessageId: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $messageId);
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for findByMessageId: " . $stmt->error);
            $stmt->close();
            return null;
        }
        $result = $stmt->get_result();
        $message = $result->fetch_assoc();
        if ($message) {
            $message['is_read'] = (bool)$message['is_read'];
            // Không định dạng sent_at, giữ nguyên giá trị từ cơ sở dữ liệu
        }
        $stmt->close();
        return $message;
    }

    // Lấy danh sách tin nhắn chưa đọc của người nhận
    public function findUnreadMessagesByReceiver($receiverId) {
        $stmt = $this->conn->prepare("SELECT message_id, sender_id, receiver_id, content, attachment, sent_at, is_read FROM messages WHERE receiver_id = ? AND is_read = 0");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for findUnreadMessagesByReceiver: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("i", $receiverId);
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for findUnreadMessagesByReceiver: " . $stmt->error);
            $stmt->close();
            return [];
        }
        $result = $stmt->get_result();
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $row['is_read'] = (bool)$row['is_read'];
            // Không định dạng sent_at, giữ nguyên giá trị từ cơ sở dữ liệu
            $messages[] = $row;
        }
        $stmt->close();
        return $messages;
    }

    // Lấy danh sách tin nhắn theo sender_id
    public function findBySenderUserId($senderId) {
        $stmt = $this->conn->prepare("SELECT message_id, sender_id, receiver_id, content, attachment, sent_at, is_read FROM messages WHERE sender_id = ?");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for findBySenderUserId: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("i", $senderId);
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for findBySenderUserId: " . $stmt->error);
            $stmt->close();
            return [];
        }
        $result = $stmt->get_result();
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $row['is_read'] = (bool)$row['is_read'];
            // Không định dạng sent_at, giữ nguyên giá trị từ cơ sở dữ liệu
            $messages[] = $row;
        }
        $stmt->close();
        return $messages;
    }

    // Lấy danh sách tin nhắn theo receiver_id
    public function findByReceiverUserId($receiverId) {
        $stmt = $this->conn->prepare("SELECT message_id, sender_id, receiver_id, content, attachment, sent_at, is_read FROM messages WHERE receiver_id = ?");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for findByReceiverUserId: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("i", $receiverId);
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for findByReceiverUserId: " . $stmt->error);
            $stmt->close();
            return [];
        }
        $result = $stmt->get_result();
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $row['is_read'] = (bool)$row['is_read'];
            // Không định dạng sent_at, giữ nguyên giá trị từ cơ sở dữ liệu
            $messages[] = $row;
        }
        $stmt->close();
        return $messages;
    }

    // Lấy danh sách tin nhắn theo booking_id
    public function findByBookingBookingId($bookingId) {
        $stmt = $this->conn->prepare("SELECT message_id, sender_id, receiver_id, content, attachment, sent_at, is_read FROM messages WHERE booking_id = ?");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for findByBookingBookingId: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("i", $bookingId);
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for findByBookingBookingId: " . $stmt->error);
            $stmt->close();
            return [];
        }
        $result = $stmt->get_result();
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $row['is_read'] = (bool)$row['is_read'];
            // Không định dạng sent_at, giữ nguyên giá trị từ cơ sở dữ liệu
            $messages[] = $row;
        }
        $stmt->close();
        return $messages;
    }

    // Lấy tất cả y tá (dành cho FAMILY)
    public function getConversationPartnersForFamily($userId, $username) {
        $stmt = $this->conn->prepare("
            SELECT u.user_id, u.username, u.full_name
            FROM users u
            WHERE u.role = 'NURSE'
        ");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for getConversationPartnersForFamily: " . $this->conn->error);
            return [];
        }
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for getConversationPartnersForFamily: " . $stmt->error);
            $stmt->close();
            return [];
        }
        $result = $stmt->get_result();
        $partners = [];
        while ($row = $result->fetch_assoc()) {
            $partners[] = [
                'userId' => $row['user_id'],
                'username' => $row['username'],
                'fullName' => $row['full_name'] ?? $row['username']
            ];
        }
        $stmt->close();
        return $partners;
    }

    // Lấy tất cả gia đình (dành cho NURSE)
    public function getConversationPartnersForNurse($userId, $username) {
        $stmt = $this->conn->prepare("
            SELECT u.user_id, u.username, u.full_name
            FROM users u
            WHERE u.role = 'FAMILY'
        ");
        if (!$stmt) {
            error_log("MessageModel: Prepare failed for getConversationPartnersForNurse: " . $this->conn->error);
            return [];
        }
        if (!$stmt->execute()) {
            error_log("MessageModel: Execute failed for getConversationPartnersForNurse: " . $stmt->error);
            $stmt->close();
            return [];
        }
        $result = $stmt->get_result();
        $partners = [];
        while ($row = $result->fetch_assoc()) {
            $partners[] = [
                'userId' => $row['user_id'],
                'username' => $row['username'],
                'fullName' => $row['full_name'] ?? $row['username']
            ];
        }
        $stmt->close();
        return $partners;
    }
}
?>