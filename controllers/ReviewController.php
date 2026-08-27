<?php
class ReviewController extends Controller {
    private Review $reviewModel;
    private ReviewReply $replyModel;
    private RentalAgreement $agreementModel;

    public function __construct() {
        $this->reviewModel = new Review();
        $this->replyModel = new ReviewReply();
        $this->agreementModel = new RentalAgreement();
    }

    public function processSubmit(): void {
        Auth::requireRole('tenant');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $agreementId = (int)($_POST['agreement_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 5);
        $feedback = trim($_POST['feedback'] ?? '');
        $user = Auth::user();

        $agreement = $this->agreementModel->findById($agreementId);
        if (!$agreement || $agreement['tenant_id'] !== $user['user_id']) {
            http_response_code(403);
            echo "You can only review property tied to your own agreement.";
            return;
        }

        try {
            $this->reviewModel->create($agreementId, $rating, $feedback);
            $_SESSION['success'] = 'Review submitted successfully!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to submit review: ' . $e->getMessage();
        }

        $this->redirect('/tenant/dashboard');
    }

    public function processReply(): void {
        Auth::requireRole('owner');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $reviewId = (int)($_POST['review_id'] ?? 0);
        $replyText = trim($_POST['reply_text'] ?? '');
        $owner = Auth::user();

        if (empty($replyText)) {
            $_SESSION['error'] = 'Reply text cannot be empty.';
            $this->redirect('/owner/dashboard');
        }

        try {
            $this->replyModel->create($reviewId, $owner['user_id'], $replyText);
            $_SESSION['success'] = 'Reply posted successfully!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to post reply: ' . $e->getMessage();
        }

        $this->redirect('/owner/dashboard');
    }
}
