<?php
class BrokerAssignment extends Model {
    public function assign(int $brokerId, int $propertyId): int {
        // Automatically deactivate any previous active broker assignment on this property
        $unassignStmt = $this->db->prepare("UPDATE broker_assignments SET unassigned_at = NOW() WHERE property_id = ? AND unassigned_at IS NULL");
        $unassignStmt->execute([$propertyId]);

        $stmt = $this->db->prepare("INSERT INTO broker_assignments (broker_id, property_id) VALUES (?, ?)");
        $stmt->execute([$brokerId, $propertyId]);
        return (int)$this->db->lastInsertId();
    }

    public function unassign(int $assignmentId): bool {
        $stmt = $this->db->prepare("UPDATE broker_assignments SET unassigned_at = NOW() WHERE assignment_id = ? AND unassigned_at IS NULL");
        return $stmt->execute([$assignmentId]);
    }

    public function findActiveByProperty(int $propertyId): ?array {
        $stmt = $this->db->prepare("SELECT ba.*, u.name as broker_name, u.email as broker_email, u.phone as broker_phone FROM broker_assignments ba JOIN users u ON u.user_id = ba.broker_id WHERE ba.property_id = ? AND ba.unassigned_at IS NULL");
        $stmt->execute([$propertyId]);
        return $stmt->fetch() ?: null;
    }

    public function findActiveByBroker(int $brokerId): array {
        $stmt = $this->db->prepare("SELECT ba.*, p.title, p.city, p.price_per_month, p.availability_status, u.name as owner_name FROM broker_assignments ba JOIN properties p ON p.property_id = ba.property_id JOIN users u ON u.user_id = p.owner_id WHERE ba.broker_id = ? AND ba.unassigned_at IS NULL");
        $stmt->execute([$brokerId]);
        return $stmt->fetchAll();
    }
}
