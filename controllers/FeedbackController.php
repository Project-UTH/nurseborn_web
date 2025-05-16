<?php
class FeedbackController {
    private $feedbackModel;
    private $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->feedbackModel = new FeedbackModel($dbConnection);
    }
    //case 'admin_feedback'
    // Hiển thị đánh giá cho admin trong admin-booking.php
    public function viewFeedback($bookingId) {
        try {
            // Lấy đánh giá theo booking_id
            $feedbacks = $this->feedbackModel->getFeedbackByBookingId($bookingId);
            return $feedbacks;
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi lấy đánh giá: " . $e->getMessage();
            return [];
        }
    }

    // Xóa đánh giá
    public function deleteFeedback($feedbackId) {
        try {
            $this->feedbackModel->deleteFeedback($feedbackId);
            $_SESSION['success'] = "Đã xóa đánh giá thành công.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi xóa đánh giá: " . $e->getMessage();
        }
        header("Location: ?action=admin_feedback");
        exit();
    }
}
?>

