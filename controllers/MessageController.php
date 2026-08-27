<?php
class MessageController extends Controller {
    private Message $messageModel;
    private Notification $notificationModel;

    public function __construct() {
        $this->messageModel = new Message();
        $this->notificationModel = new Notification();
    }

    public function index(): void {
        Auth::requireRole('tenant', 'owner', 'broker', 'admin');
        $user = Auth::user();
        $receiverId = isset($_GET['with']) ? (int)$_GET['with'] : null;

        $threads = $this->messageModel->getUserThreads($user['user_id']);
        $activeThread = null;

        if ($receiverId) {
            $activeThread = $this->messageModel->getThread($user['user_id'], $receiverId);
            $this->messageModel->markAsRead($receiverId, $user['user_id']);
        } elseif (!empty($threads)) {
            $receiverId = $threads[0]['other_user_id'];
            $activeThread = $this->messageModel->getThread($user['user_id'], $receiverId);
            $this->messageModel->markAsRead($receiverId, $user['user_id']);
        }

        $this->render('messages/index', [
            'user' => $user,
            'threads' => $threads,
            'receiver_id' => $receiverId,
            'messages' => $activeThread ?? [],
            'csrf_token' => Auth::csrfToken()
        ]);
    }

    public function send(): void {
        Auth::requireRole('tenant', 'owner', 'broker', 'admin');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $sender = Auth::user();
        $receiverId = (int)($_POST['receiver_id'] ?? 0);
        $propertyId = !empty($_POST['property_id']) ? (int)$_POST['property_id'] : null;
        $content = trim($_POST['content'] ?? '');

        if ($receiverId && !empty($content) && $sender['user_id'] !== $receiverId) {
            $messageId = $this->messageModel->send($sender['user_id'], $receiverId, $propertyId, $content);
            
            // Generate live Notification for the recipient when a new message arrives!
            $snippet = mb_strimwidth($content, 0, 50, "...");
            $this->notificationModel->create(
                $receiverId,
                'message',
                "New message from {$sender['name']}: \"{$snippet}\"",
                'message',
                $messageId
            );
        }

        $this->redirect("/messages?with={$receiverId}");
    }
}
