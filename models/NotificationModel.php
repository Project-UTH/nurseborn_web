<?php
class NotificationModel {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Tạo thông báo mới
    public function createNotification($userId, $message, $bookingId = null) {
        $stmt = $this->conn->prepare(
            "INSERT INTO notifications (user_id, booking_id, message, is_read, created_at) 
             VALUES (?, ?, ?, 0, NOW())"
        );
        $stmt->bind_param("iis", $userId, $bookingId, $message);
        if (!$stmt->execute()) {
            error_log("Lỗi khi tạo thông báo: " . $stmt->error);
            throw new Exception("Lỗi khi tạo thông báo: " . $stmt->error);
        }
        error_log("Created notification for user_id {$userId}: {$message}");
    }

    // Lấy danh sách thông báo chưa đọc
    public function getUnreadNotifications($userId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        return $notifications;
    }

    // Lấy tất cả thông báo của người dùng, sắp xếp theo thời gian giảm dần
    public function getAllNotificationsForUser($userId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        return $notifications;
    }

    // Đánh dấu thông báo là đã đọc
    public function markAsRead($notificationId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM notifications WHERE notification_id = ?"
        );
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Notification not found with ID: " . $notificationId);
        }

        $stmt = $this->conn->prepare(
            "UPDATE notifications SET is_read = 1 WHERE notification_id = ?"
        );
        $stmt->bind_param("i", $notificationId);
        if (!$stmt->execute()) {
            error_log("Lỗi khi đánh dấu thông báo đã đọc: " . $stmt->error);
            throw new Exception("Lỗi khi đánh dấu thông báo đã đọc: " . $stmt->error);
        }
    }
}
?>