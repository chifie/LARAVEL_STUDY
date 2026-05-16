<?php
declare(strict_types=1);

class User
{
    public static function findByEmail($email)
    {
        $stmt = Database::query(
            'SELECT * FROM users WHERE email = :email LIMIT 1',
            [':email' => trim(strtolower($email))]
        );

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? $user : null;
    }

    public static function findById($id)
    {
        $stmt = Database::query(
            'SELECT * FROM users WHERE id = :id LIMIT 1',
            [':id' => (int) $id]
        );

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? $user : null;
    }

    public static function existsByEmail($email)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total FROM users WHERE email = :email',
            [':email' => trim(strtolower($email))]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int) $row['total']) > 0;
    }

    public static function existsByEmailExcept($email, $userId)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total
             FROM users
             WHERE email = :email AND id <> :id',
            [
                ':email' => trim(strtolower($email)),
                ':id' => (int) $userId,
            ]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int) $row['total']) > 0;
    }

    public static function all()
    {
        $stmt = Database::query('SELECT * FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data)
    {
        $allowedRoles = ['super_admin', 'teacher', 'student'];

        if (!isset($data['email'], $data['full_name'], $data['role'])) {
            throw new InvalidArgumentException('Missing required user data.');
        }

        $email = trim(strtolower($data['email']));
        $fullName = trim($data['full_name']);
        $role = $data['role'];

        if (!in_array($role, $allowedRoles, true)) {
            throw new InvalidArgumentException('Invalid user role.');
        }

        if (self::existsByEmail($email)) {
            throw new RuntimeException('A user with this email already exists.');
        }

        if (isset($data['password'])) {
            $passwordHash = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        } elseif (isset($data['password_hash'])) {
            $passwordHash = (string) $data['password_hash'];
        } else {
            throw new InvalidArgumentException('Password is required.');
        }

        Database::query(
            'INSERT INTO users
            (full_name, email, phone, password_hash, role, must_change_password, is_active, created_at, updated_at)
            VALUES
            (:full_name, :email, :phone, :password_hash, :role, :must_change_password, :is_active, NOW(), NOW())',
            [
                ':full_name' => $fullName,
                ':email' => $email,
                ':phone' => isset($data['phone']) ? trim((string) $data['phone']) : null,
                ':password_hash' => $passwordHash,
                ':role' => $role,
                ':must_change_password' => isset($data['must_change_password']) ? (int) $data['must_change_password'] : 1,
                ':is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function updateBasicInfo($id, array $data)
    {
        Database::query(
            'UPDATE users
             SET full_name = :full_name,
                 email = :email,
                 phone = :phone,
                 updated_at = NOW()
             WHERE id = :id',
            [
                ':full_name' => trim((string) $data['full_name']),
                ':email' => trim(strtolower((string) $data['email'])),
                ':phone' => !empty($data['phone']) ? trim((string) $data['phone']) : null,
                ':id' => (int) $id,
            ]
        );

        return true;
    }

    public static function updateLastLogin($id)
    {
        return Database::query(
            'UPDATE users SET last_login_at = NOW(), updated_at = NOW() WHERE id = :id',
            [':id' => (int) $id]
        )->rowCount() > 0;
    }

    public static function updatePassword($id, $passwordHash)
    {
        return Database::query(
            'UPDATE users
             SET password_hash = :password_hash,
                 must_change_password = 0,
                 password_changed_at = NOW(),
                 password_reset_token = NULL,
                 password_reset_expires_at = NULL,
                 updated_at = NOW()
             WHERE id = :id',
            [
                ':password_hash' => $passwordHash,
                ':id' => (int) $id,
            ]
        )->rowCount() > 0;
    }

    public static function setPasswordResetToken($id, $tokenHash, $expiresAt)
    {
        return Database::query(
            'UPDATE users
             SET password_reset_token = :token,
                 password_reset_expires_at = :expires_at,
                 updated_at = NOW()
             WHERE id = :id',
            [
                ':token' => $tokenHash,
                ':expires_at' => $expiresAt,
                ':id' => (int) $id,
            ]
        )->rowCount() > 0;
    }

    public static function toggleActive($id)
    {
        $user = self::findById($id);
        if (!$user) {
            return false;
        }

        $newStatus = ((int) $user['is_active'] === 1) ? 0 : 1;

        return Database::query(
            'UPDATE users SET is_active = :is_active, updated_at = NOW() WHERE id = :id',
            [
                ':is_active' => $newStatus,
                ':id' => (int) $id,
            ]
        )->rowCount() > 0;
    }
}