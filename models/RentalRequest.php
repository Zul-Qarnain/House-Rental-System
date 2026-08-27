<?php
class RentalRequest extends Model {
    public function create(int $propertyId, int $tenantId, ?string $moveInDate, ?string $message): int {
        $stmt = $this->db->prepare("INSERT INTO rental_requests (property_id, tenant_id, requested_move_in, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$propertyId, $tenantId, $moveInDate, $message]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT r.*, p.title as property_title, p.owner_id, u.name as tenant_name, u.email as tenant_email FROM rental_requests r JOIN properties p ON p.property_id = r.property_id JOIN users u ON u.user_id = r.tenant_id WHERE r.request_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByTenant(int $tenantId): array {
        $stmt = $this->db->prepare("SELECT r.*, p.title as property_title, p.city, p.price_per_month, pi.image_url as cover_image FROM rental_requests r JOIN properties p ON p.property_id = r.property_id LEFT JOIN property_images pi ON pi.property_id = p.property_id AND pi.is_cover = 1 WHERE r.tenant_id = ? ORDER BY r.requested_at DESC");
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll();
    }

    public function findByOwner(int $ownerId): array {
        $stmt = $this->db->prepare("SELECT r.*, p.title as property_title, u.name as tenant_name, u.email as tenant_email, u.phone as tenant_phone FROM rental_requests r JOIN properties p ON p.property_id = r.property_id JOIN users u ON u.user_id = r.tenant_id WHERE p.owner_id = ? ORDER BY r.requested_at DESC");
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $requestId, string $status, int $respondedBy): bool {
        $stmt = $this->db->prepare("UPDATE rental_requests SET status = ?, responded_at = NOW(), responded_by = ? WHERE request_id = ?");
        return $stmt->execute([$status, $respondedBy, $requestId]);
    }
}
