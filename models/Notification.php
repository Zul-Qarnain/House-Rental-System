<?php
class Notification extends Model {
    public function create(int $userId, string $type, string $content, ?string $relatedEntityType = null, ?int $relatedEntityId = null): int {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, type, content, related_entity_type, related_entity_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $type, $content, $relatedEntityType, $relatedEntityId]);
        return (int)$this->db->lastInsertId();
    }

    public function findByUser(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function markAsRead(int $notificationId, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
        return $stmt->execute([$notificationId, $userId]);
    }
}
