<?php
class AdminAction extends Model {
    public function log(int $adminId, string $actionType, string $targetType, int $targetId, ?string $notes = null): int {
        $stmt = $this->db->prepare("INSERT INTO admin_actions (admin_id, action_type, target_type, target_id, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$adminId, $actionType, $targetType, $targetId, $notes]);
        return (int)$this->db->lastInsertId();
    }

    public function findAll(): array {
        $stmt = $this->db->prepare("SELECT a.*, u.name as admin_name FROM admin_actions a JOIN users u ON u.user_id = a.admin_id ORDER BY a.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
