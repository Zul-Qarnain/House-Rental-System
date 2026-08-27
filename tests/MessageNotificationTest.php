<?php
class MessageNotificationTest {
    public function run(): void {
        $messageModel = new Message();
        $notificationModel = new Notification();
        $userModel = new User();

        $sender = $userModel->findByEmail('tenant@proptech.com');
        $receiver = $userModel->findByEmail('owner@proptech.com');

        assert_true(!empty($sender), "Sender tenant must exist");
        assert_true(!empty($receiver), "Receiver owner must exist");

        // 1. Send message
        $msgContent = "Hello, is this property available for immediate move-in?";
        $msgId = $messageModel->send($sender['user_id'], $receiver['user_id'], null, $msgContent);
        assert_true($msgId > 0, "Message ID should be a positive integer");

        // 2. Create notification for recipient
        $notifId = $notificationModel->create(
            $receiver['user_id'],
            'message',
            "New message from {$sender['name']}: \"{$msgContent}\"",
            'message',
            $msgId
        );
        assert_true($notifId > 0, "Notification ID should be positive integer");

        // 3. Verify notification received
        $notifications = $notificationModel->findByUser($receiver['user_id']);
        assert_true(!empty($notifications), "Receiver should have notifications");
        assert_equal('message', $notifications[0]['type'], "Notification type should be message");
    }
}
