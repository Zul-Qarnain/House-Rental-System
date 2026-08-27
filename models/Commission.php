<?php
class Commission extends Model {
    public function create(int $brokerId, int $agreementId, float $amount): int {
        $stmt = $this->db->prepare("INSERT INTO commissions (broker_id, agreement_id, amount) VALUES (?, ?, ?)");
        $stmt->execute([$brokerId, $agreementId, $amount]);
        return (int)$this->db->lastInsertId();
    }

    public function findByBroker(int $brokerId): array {
        $stmt = $this->db->prepare("SELECT c.*, p.title as property_title FROM commissions c JOIN rental_agreements ra ON ra.agreement_id = c.agreement_id JOIN rental_requests rr ON rr.request_id = ra.request_id JOIN properties p ON p.property_id = rr.property_id WHERE c.broker_id = ? ORDER BY c.created_at DESC");
        $stmt->execute([$brokerId]);
        return $stmt->fetchAll();
    }

    public function markPaid(int $commissionId): bool {
        $stmt = $this->db->prepare("UPDATE commissions SET status = 'paid', paid_at = NOW() WHERE commission_id = ?");
        return $stmt->execute([$commissionId]);
    }
}
