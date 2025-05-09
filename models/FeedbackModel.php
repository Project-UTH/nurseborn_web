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
}
?>