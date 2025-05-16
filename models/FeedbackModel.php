<?php
class FeedbackModel {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function createFeedback($feedbackData) {
        $stmt = $this->conn->prepare(
            "INSERT INTO feedbacks (booking_id, nurse_user_id, family_user_id, rating, comment, attachment, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param(
            "iiiiss",
            $feedbackData['booking_id'],
            $feedbackData['nurse_user_id'],
            $feedbackData['family_user_id'],
            $feedbackData['rating'],
            $feedbackData['comment'],
            $feedbackData['attachment']
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    }

    // Lấy danh sách đánh giá của một y tá
    public function getFeedbackByNurseUserId($nurseUserId) {
        $stmt = $this->conn->prepare(
            "SELECT f.*, u.full_name AS family_full_name 
             FROM feedbacks f 
             JOIN users u ON f.family_user_id = u.user_id 
             WHERE f.nurse_user_id = ? 
             ORDER BY f.created_at DESC"
        );

        if (!$stmt) {
            error_log("Prepare failed in getFeedbackByNurseUserId: " . $this->conn->error);
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $nurseUserId);

        if (!$stmt->execute()) {
            error_log("Execute failed in getFeedbackByNurseUserId: " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $feedbacks = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $feedbacks;
    }

    // Tính số sao trung bình của một y tá
    public function getAverageRatingByNurseUserId($nurseUserId) {
        $stmt = $this->conn->prepare(
            "SELECT AVG(rating) AS average_rating 
             FROM feedbacks 
             WHERE nurse_user_id = ?"
        );

        if (!$stmt) {
            error_log("Prepare failed in getAverageRatingByNurseUserId: " . $this->conn->error);
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $nurseUserId);

        if (!$stmt->execute()) {
            error_log("Execute failed in getAverageRatingByNurseUserId: " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result()->fetch_assoc();
        $averageRating = $result['average_rating'] ? round($result['average_rating'], 1) : 0;

        $stmt->close();

        return $averageRating;
    }

    // Lấy tất cả đánh giá (dành cho admin)
    public function getAllFeedbacks($filters = []) {
        $query = "SELECT f.*, 
                         u1.full_name AS family_full_name, 
                         u2.full_name AS nurse_full_name 
                  FROM feedbacks f 
                  JOIN users u1 ON f.family_user_id = u1.user_id 
                  JOIN users u2 ON f.nurse_user_id = u2.user_id 
                  WHERE 1=1";

        $params = [];
        $types = "";

        if (!empty($filters['nurse_id'])) {
            $query .= " AND f.nurse_user_id = ?";
            $params[] = $filters['nurse_id'];
            $types .= "i";
        }
        if (!empty($filters['rating'])) {
            $query .= " AND f.rating = ?";
            $params[] = $filters['rating'];
            $types .= "i";
        }
        if (!empty($filters['start_date'])) {
            $query .= " AND f.created_at >= ?";
            $params[] = $filters['start_date'];
            $types .= "s";
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND f.created_at <= ?";
            $params[] = $filters['end_date'];
            $types .= "s";
        }

        $query .= " ORDER BY f.created_at DESC";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            error_log("Prepare failed in getAllFeedbacks: " . $this->conn->error);
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log("Execute failed in getAllFeedbacks: " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $feedbacks = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $feedbacks;
    }

    // Lấy đánh giá theo booking_id (dành cho admin-booking.php)
    public function getFeedbackByBookingId($bookingId) {
        $stmt = $this->conn->prepare(
            "SELECT f.*, 
                    u1.full_name AS family_full_name, 
                    u2.full_name AS nurse_full_name 
             FROM feedbacks f 
             JOIN users u1 ON f.family_user_id = u1.user_id 
             JOIN users u2 ON f.nurse_user_id = u2.user_id 
             WHERE f.booking_id = ? 
             ORDER BY f.created_at DESC"
        );

        if (!$stmt) {
            error_log("Prepare failed in getFeedbackByBookingId: " . $this->conn->error);
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $bookingId);

        if (!$stmt->execute()) {
            error_log("Execute failed in getFeedbackByBookingId: " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $feedbacks = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $feedbacks;
    }

    // Xóa đánh giá (dành cho admin)
    public function deleteFeedback($feedbackId) {
        $stmt = $this->conn->prepare("DELETE FROM feedbacks WHERE feedback_id = ?");

        if (!$stmt) {
            error_log("Prepare failed in deleteFeedback: " . $this->conn->error);
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $feedbackId);

        if (!$stmt->execute()) {
            error_log("Execute failed in deleteFeedback: " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    }
}
?>

