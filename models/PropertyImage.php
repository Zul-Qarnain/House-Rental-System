<?php
class PropertyImage extends Model {
    public function addImage(int $propertyId, string $imageUrl, bool $isCover = false): int {
        if ($isCover) {
            $this->clearCover($propertyId);
        }
        $stmt = $this->db->prepare("INSERT INTO property_images (property_id, image_url, is_cover) VALUES (?, ?, ?)");
        $stmt->execute([$propertyId, $imageUrl, (int)$isCover]);
        return (int)$this->db->lastInsertId();
    }

    public function findByProperty(int $propertyId): array {
        $stmt = $this->db->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY is_cover DESC, uploaded_at ASC");
        $stmt->execute([$propertyId]);
        return $stmt->fetchAll();
    }

    public function setCover(int $imageId, int $propertyId): bool {
        $this->clearCover($propertyId);
        $stmt = $this->db->prepare("UPDATE property_images SET is_cover = 1 WHERE image_id = ? AND property_id = ?");
        return $stmt->execute([$imageId, $propertyId]);
    }

    public function clearCover(int $propertyId): void {
        $stmt = $this->db->prepare("UPDATE property_images SET is_cover = 0 WHERE property_id = ?");
        $stmt->execute([$propertyId]);
    }

    public function delete(int $imageId): bool {
        $stmt = $this->db->prepare("DELETE FROM property_images WHERE image_id = ?");
        return $stmt->execute([$imageId]);
    }
}
