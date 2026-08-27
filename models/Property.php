<?php
class Property extends Model {
    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO properties (owner_id, title, description, address_line, city, price_per_month, bedrooms, bathrooms, area_sqft, availability_status, is_approved, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)");
        $stmt->execute([
            $data['owner_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['address_line'],
            $data['city'],
            $data['price_per_month'],
            $data['bedrooms'] ?? null,
            $data['bathrooms'] ?? null,
            $data['area_sqft'] ?? null,
            $data['availability_status'] ?? 'available'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT p.*, u.name as owner_name, u.email as owner_email, u.phone as owner_phone FROM properties p JOIN users u ON u.user_id = p.owner_id WHERE p.property_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function search(?string $city = null, ?float $minPrice = null, ?float $maxPrice = null, ?int $bedrooms = null): array {
        $sql = "SELECT p.*, pi.image_url as cover_image FROM properties p LEFT JOIN property_images pi ON pi.property_id = p.property_id AND pi.is_cover = 1 WHERE p.is_approved = 1";
        $params = [];

        if (!empty($city)) {
            $sql .= " AND p.city LIKE ?";
            $params[] = "%{$city}%";
        }
        if ($minPrice !== null && $minPrice >= 0) {
            $sql .= " AND p.price_per_month >= ?";
            $params[] = $minPrice;
        }
        if ($maxPrice !== null && $maxPrice > 0) {
            $sql .= " AND p.price_per_month <= ?";
            $params[] = $maxPrice;
        }
        if ($bedrooms !== null && $bedrooms > 0) {
            $sql .= " AND p.bedrooms >= ?";
            $params[] = $bedrooms;
        }

        $sql .= " ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findByOwner(int $ownerId): array {
        $stmt = $this->db->prepare("SELECT p.*, pi.image_url as cover_image FROM properties p LEFT JOIN property_images pi ON pi.property_id = p.property_id AND pi.is_cover = 1 WHERE p.owner_id = ? ORDER BY p.created_at DESC");
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $propertyId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE properties SET availability_status = ? WHERE property_id = ?");
        return $stmt->execute([$status, $propertyId]);
    }

    public function updateApproval(int $propertyId, bool $isApproved, bool $isVerified = false): bool {
        $stmt = $this->db->prepare("UPDATE properties SET is_approved = ?, is_verified = ? WHERE property_id = ?");
        return $stmt->execute([(int)$isApproved, (int)$isVerified, $propertyId]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE properties SET title = ?, description = ?, address_line = ?, city = ?, price_per_month = ?, bedrooms = ?, bathrooms = ?, area_sqft = ?, availability_status = ? WHERE property_id = ?");
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['address_line'],
            $data['city'],
            $data['price_per_month'],
            $data['bedrooms'] ?? null,
            $data['bathrooms'] ?? null,
            $data['area_sqft'] ?? null,
            $data['availability_status'],
            $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM properties WHERE property_id = ?");
        return $stmt->execute([$id]);
    }

    public function getAllForAdmin(): array {
        $stmt = $this->db->prepare("SELECT p.*, u.name as owner_name FROM properties p JOIN users u ON u.user_id = p.owner_id ORDER BY p.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
