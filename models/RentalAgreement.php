<?php
class RentalAgreement extends Model {
    public function create(int $requestId, ?int $brokerId, string $startDate, ?string $endDate, float $monthlyRent): int {
        $stmt = $this->db->prepare("INSERT INTO rental_agreements (request_id, broker_id, start_date, end_date, monthly_rent, status) VALUES (?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$requestId, $brokerId, $startDate, $endDate, $monthlyRent]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT ra.*, req.property_id, req.tenant_id, p.owner_id
            FROM rental_agreements ra
            JOIN rental_requests req ON ra.request_id = req.request_id
            JOIN properties p ON req.property_id = p.property_id
            WHERE ra.agreement_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByTenant(int $tenantId): array {
        $stmt = $this->db->prepare("
            SELECT ra.*, req.property_id, req.tenant_id, p.owner_id, p.title as property_title, p.address_line, p.city, pi.image_url as cover_image
            FROM rental_agreements ra
            JOIN rental_requests req ON ra.request_id = req.request_id
            JOIN properties p ON req.property_id = p.property_id
            LEFT JOIN property_images pi ON pi.property_id = p.property_id AND pi.is_cover = 1
            WHERE req.tenant_id = ?
            ORDER BY ra.confirmed_at DESC
        ");
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll();
    }

    public function findByOwner(int $ownerId): array {
        $stmt = $this->db->prepare("
            SELECT ra.*, req.property_id, req.tenant_id, p.owner_id, p.title as property_title, u.name as tenant_name
            FROM rental_agreements ra
            JOIN rental_requests req ON ra.request_id = req.request_id
            JOIN properties p ON req.property_id = p.property_id
            JOIN users u ON u.user_id = req.tenant_id
            WHERE p.owner_id = ?
            ORDER BY ra.confirmed_at DESC
        ");
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public function findByBroker(int $brokerId): array {
        $stmt = $this->db->prepare("
            SELECT ra.*, req.property_id, req.tenant_id, p.owner_id, p.title as property_title, t.name as tenant_name, o.name as owner_name
            FROM rental_agreements ra
            JOIN rental_requests req ON ra.request_id = req.request_id
            JOIN properties p ON req.property_id = p.property_id
            JOIN users t ON t.user_id = req.tenant_id
            JOIN users o ON o.user_id = p.owner_id
            WHERE ra.broker_id = ?
            ORDER BY ra.confirmed_at DESC
        ");
        $stmt->execute([$brokerId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $agreementId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE rental_agreements SET status = ? WHERE agreement_id = ?");
        return $stmt->execute([$status, $agreementId]);
    }
}
