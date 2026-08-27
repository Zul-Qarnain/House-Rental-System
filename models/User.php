<?php
class User extends Model {
    public function create(string $name, string $email, string $phone, string $password, string $role): int {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hash, $role]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus(int $userId, bool $isActive): bool {
        $stmt = $this->db->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
        return $stmt->execute([(int)$isActive, $userId]);
    }

    public function updateRole(int $userId, string $role): bool {
        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE user_id = ?");
        return $stmt->execute([$role, $userId]);
    }

    public function createPasswordReset(int $userId, string $tokenHash, string $expiresAt): bool {
        $stmt = $this->db->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $tokenHash, $expiresAt]);
    }

    public function findPasswordReset(string $tokenHash): ?array {
        $stmt = $this->db->prepare("SELECT * FROM password_resets WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()");
        $stmt->execute([$tokenHash]);
        return $stmt->fetch() ?: null;
    }

    public function markResetUsed(int $resetId): bool {
        $stmt = $this->db->prepare("UPDATE password_resets SET used_at = NOW() WHERE reset_id = ?");
        return $stmt->execute([$resetId]);
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        return $stmt->execute([$hash, $userId]);
    }
}
