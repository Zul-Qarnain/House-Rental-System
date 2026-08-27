<?php
class Complaint extends Model {
    public function create(int $filedBy, ?int $againstUserId, ?int $againstPropertyId, string $description): int {
        $stmt = $this->db->prepare("INSERT INTO complaints (filed_by, against_user_id, against_property_id, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$filedBy, $againstUserId, $againstPropertyId, $description]);
        return (int)$this->db->lastInsertId();
    }

    public function findAll(): array {
        $stmt = $this->db->prepare("SELECT c.*, f.name as filer_name, u.name as against_user_name, p.title as against_property_title, r.name as resolver_name FROM complaints c JOIN users f ON f.user_id = c.filed_by LEFT JOIN users u ON u.user_id = c.against_user_id LEFT JOIN properties p ON p.property_id = c.against_property_id LEFT JOIN users r ON r.user_id = c.resolved_by ORDER BY c.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function resolve(int $complaintId, int $resolvedBy, string $status = 'resolved'): bool {
        $stmt = $this->db->prepare("UPDATE complaints SET status = ?, resolved_by = ?, resolved_at = NOW() WHERE complaint_id = ?");
        return $stmt->execute([$status, $resolvedBy, $complaintId]);
    }
}
