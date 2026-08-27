<?php
class NotificationController extends Controller {
    private Notification $notificationModel;

    public function __construct() {
        $this->notificationModel = new Notification();
    }

    public function markAsRead(): void {
        Auth::requireRole('tenant', 'owner', 'broker', 'admin');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $id = (int)($_POST['notification_id'] ?? 0);
        $user = Auth::user();

        $this->notificationModel->markAsRead($id, $user['user_id']);
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}
