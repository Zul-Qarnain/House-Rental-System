<?php
class Review extends Model {
    public function create(int $agreementId, int $rating, ?string $feedback): int {
        $stmt = $this->db->prepare("INSERT INTO reviews (agreement_id, rating, feedback) VALUES (?, ?, ?)");
        $stmt->execute([$agreementId, $rating, $feedback]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT v.*, u.name as tenant_name, rr.reply_text, rr.created_at as reply_created_at FROM v_reviews_full v JOIN users u ON u.user_id = v.tenant_id LEFT JOIN review_replies rr ON rr.review_id = v.review_id WHERE v.review_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByProperty(int $propertyId): array {
        $stmt = $this->db->prepare("SELECT v.*, u.name as tenant_name, rr.reply_text, rr.created_at as reply_created_at FROM v_reviews_full v JOIN users u ON u.user_id = v.tenant_id LEFT JOIN review_replies rr ON rr.review_id = v.review_id WHERE v.property_id = ? ORDER BY v.created_at DESC");
        $stmt->execute([$propertyId]);
        return $stmt->fetchAll();
    }

    public function findByOwner(int $ownerId): array {
        $stmt = $this->db->prepare("SELECT v.*, p.title as property_title, u.name as tenant_name, rr.reply_text, rr.created_at as reply_created_at FROM v_reviews_full v JOIN properties p ON p.property_id = v.property_id JOIN users u ON u.user_id = v.tenant_id LEFT JOIN review_replies rr ON rr.review_id = v.review_id WHERE v.owner_id = ? ORDER BY v.created_at DESC");
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }
}
