<?php
class Review extends Model {
    public function create(int $agreementId, int $rating, ?string $feedback): int {
        $stmt = $this->db->prepare("INSERT INTO reviews (agreement_id, rating, feedback) VALUES (?, ?, ?)");
        $stmt->execute([$agreementId, $rating, $feedback]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT r.*, req.tenant_id, p.property_id, p.owner_id, p.title as property_title,
                   u.name as tenant_name, u.role as tenant_role,
                   rr.reply_id, rr.reply_text, rr.created_at as reply_created_at
            FROM reviews r
            JOIN rental_agreements ra ON r.agreement_id = ra.agreement_id
            JOIN rental_requests req ON ra.request_id = req.request_id
            JOIN properties p ON req.property_id = p.property_id
            JOIN users u ON req.tenant_id = u.user_id
            LEFT JOIN review_replies rr ON r.review_id = rr.review_id
            WHERE r.review_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByProperty(int $propertyId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, req.tenant_id, p.property_id, p.owner_id, p.title as property_title,
                   u.name as tenant_name, u.role as tenant_role,
                   rr.reply_id, rr.reply_text, rr.created_at as reply_created_at
            FROM reviews r
            JOIN rental_agreements ra ON r.agreement_id = ra.agreement_id
            JOIN rental_requests req ON ra.request_id = req.request_id
            JOIN properties p ON req.property_id = p.property_id
            JOIN users u ON req.tenant_id = u.user_id
            LEFT JOIN review_replies rr ON r.review_id = rr.review_id
            WHERE p.property_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$propertyId]);
        return $stmt->fetchAll();
    }

    public function findByOwner(int $ownerId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, req.tenant_id, p.property_id, p.owner_id, p.title as property_title,
                   u.name as tenant_name, u.role as tenant_role,
                   rr.reply_id, rr.reply_text, rr.created_at as reply_created_at
            FROM reviews r
            JOIN rental_agreements ra ON r.agreement_id = ra.agreement_id
            JOIN rental_requests req ON ra.request_id = req.request_id
            JOIN properties p ON req.property_id = p.property_id
            JOIN users u ON req.tenant_id = u.user_id
            LEFT JOIN review_replies rr ON r.review_id = rr.review_id
            WHERE p.owner_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }
}
