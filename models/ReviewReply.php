<?php
class ReviewReply extends Model {
    public function create(int $reviewId, int $ownerId, string $replyText): int {
        $stmt = $this->db->prepare("INSERT INTO review_replies (review_id, owner_id, reply_text) VALUES (?, ?, ?)");
        $stmt->execute([$reviewId, $ownerId, $replyText]);
        return (int)$this->db->lastInsertId();
    }

    public function findByReview(int $reviewId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM review_replies WHERE review_id = ?");
        $stmt->execute([$reviewId]);
        return $stmt->fetch() ?: null;
    }
}
