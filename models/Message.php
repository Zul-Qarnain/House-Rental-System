<?php
class Message extends Model {
    public function send(int $senderId, int $receiverId, ?int $propertyId, string $content): int {
        $stmt = $this->db->prepare("INSERT INTO messages (sender_id, receiver_id, property_id, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$senderId, $receiverId, $propertyId, $content]);
        return (int)$this->db->lastInsertId();
    }

    public function getThread(int $userId1, int $userId2): array {
        $stmt = $this->db->prepare("SELECT m.*, s.name as sender_name, r.name as receiver_name FROM messages m JOIN users s ON s.user_id = m.sender_id JOIN users r ON r.user_id = m.receiver_id WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) ORDER BY m.sent_at ASC");
        $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
        return $stmt->fetchAll();
    }

    public function getUserThreads(int $userId): array {
        $stmt = $this->db->prepare("SELECT DISTINCT CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as other_user_id, u.name as other_user_name, u.role as other_user_role FROM messages JOIN users u ON u.user_id = (CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END) WHERE sender_id = ? OR receiver_id = ?");
        $stmt->execute([$userId, $userId, $userId, $userId]);
        return $stmt->fetchAll();
    }

    public function markAsRead(int $senderId, int $receiverId): bool {
        $stmt = $this->db->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
        return $stmt->execute([$senderId, $receiverId]);
    }
}
