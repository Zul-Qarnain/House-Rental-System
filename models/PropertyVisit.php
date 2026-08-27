<?php
class PropertyVisit extends Model {
    public function schedule(int $propertyId, int $tenantId, ?int $brokerId, string $scheduledAt, ?string $notes): int {
        $stmt = $this->db->prepare("INSERT INTO property_visits (property_id, tenant_id, broker_id, scheduled_at, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$propertyId, $tenantId, $brokerId, $scheduledAt, $notes]);
        return (int)$this->db->lastInsertId();
    }

    public function findByBroker(int $brokerId): array {
        $stmt = $this->db->prepare("SELECT v.*, p.title as property_title, u.name as tenant_name, u.phone as tenant_phone FROM property_visits v JOIN properties p ON p.property_id = v.property_id JOIN users u ON u.user_id = v.tenant_id WHERE v.broker_id = ? ORDER BY v.scheduled_at ASC");
        $stmt->execute([$brokerId]);
        return $stmt->fetchAll();
    }

    public function findByTenant(int $tenantId): array {
        $stmt = $this->db->prepare("SELECT v.*, p.title as property_title, u.name as broker_name FROM property_visits v JOIN properties p ON p.property_id = v.property_id LEFT JOIN users u ON u.user_id = v.broker_id WHERE v.tenant_id = ? ORDER BY v.scheduled_at ASC");
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $visitId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE property_visits SET status = ? WHERE visit_id = ?");
        return $stmt->execute([$status, $visitId]);
    }
}
